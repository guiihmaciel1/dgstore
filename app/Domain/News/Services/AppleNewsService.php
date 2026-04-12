<?php

declare(strict_types=1);

namespace App\Domain\News\Services;

use App\Domain\News\DTOs\NewsItemData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AppleNewsService
{
    private const CACHE_KEY = 'apple_news_items';
    private const CACHE_TTL_MINUTES = 120;
    private const MAX_ITEMS = 20;
    private const TIMEOUT_SECONDS = 10;

    private array $feeds = [
        'https://feeds.macrumors.com/MacRumors-All' => 'MacRumors',
        'https://9to5mac.com/feed/' => '9to5Mac',
        'https://macmagazine.com.br/feed/' => 'MacMagazine',
    ];

    public function fetchAndCache(): array
    {
        $items = $this->fetchFromAllFeeds();

        Cache::put(self::CACHE_KEY, $items, now()->addMinutes(self::CACHE_TTL_MINUTES));

        return $items;
    }

    public function getCached(): array
    {
        return Cache::get(self::CACHE_KEY, []);
    }

    private function fetchFromAllFeeds(): array
    {
        $allItems = [];

        foreach ($this->feeds as $url => $sourceName) {
            try {
                $items = $this->parseFeed($url, $sourceName);
                $allItems = array_merge($allItems, $items);
            } catch (\Throwable $e) {
                Log::warning("Apple News: falha ao buscar feed {$sourceName}", [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        usort($allItems, fn (array $a, array $b) => strtotime($b['date']) <=> strtotime($a['date']));

        return array_slice($allItems, 0, self::MAX_ITEMS);
    }

    private function parseFeed(string $url, string $sourceName): array
    {
        $response = Http::timeout(self::TIMEOUT_SECONDS)
            ->withHeaders(['Accept' => 'application/rss+xml, application/xml, text/xml'])
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException("HTTP {$response->status()} ao buscar {$url}");
        }

        $xml = @simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($xml === false) {
            throw new \RuntimeException("XML inválido do feed {$url}");
        }

        $items = [];
        $feedItems = $xml->channel->item ?? [];

        foreach ($feedItems as $entry) {
            $imageUrl = $this->extractImage($entry);
            $date = $this->parseDate((string) $entry->pubDate);

            $items[] = (new NewsItemData(
                title: html_entity_decode((string) $entry->title, ENT_QUOTES, 'UTF-8'),
                link: (string) $entry->link,
                date: $date,
                source: $sourceName,
                summary: Str::limit(
                    strip_tags(html_entity_decode((string) $entry->description, ENT_QUOTES, 'UTF-8')),
                    200
                ),
                imageUrl: $imageUrl,
            ))->toArray();
        }

        return $items;
    }

    private function extractImage(\SimpleXMLElement $entry): ?string
    {
        $namespaces = $entry->getNamespaces(true);

        if (isset($namespaces['media'])) {
            $media = $entry->children($namespaces['media']);
            if (isset($media->content)) {
                $attrs = $media->content->attributes();
                if (isset($attrs['url'])) {
                    return (string) $attrs['url'];
                }
            }
            if (isset($media->thumbnail)) {
                $attrs = $media->thumbnail->attributes();
                if (isset($attrs['url'])) {
                    return (string) $attrs['url'];
                }
            }
        }

        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', (string) $entry->description, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function parseDate(string $dateString): string
    {
        try {
            return Carbon::parse($dateString)->toIso8601String();
        } catch (\Throwable) {
            return now()->toIso8601String();
        }
    }
}
