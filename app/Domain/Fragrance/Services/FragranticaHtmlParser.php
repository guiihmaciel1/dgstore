<?php

declare(strict_types=1);

namespace App\Domain\Fragrance\Services;

use App\Domain\Fragrance\DTOs\FragranceScrapedData;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Parseia o HTML de uma página de perfume do Fragrantica,
 * extraindo todos os dados estruturados (nome, marca, acordes, pirâmide, etc).
 */
class FragranticaHtmlParser
{
    public function parse(string $url, string $html): FragranceScrapedData
    {
        $this->validateUrl($url);

        $crawler = new Crawler($html);

        return new FragranceScrapedData(
            fragranticaId: $this->extractId($url),
            fragranticaUrl: $url,
            name: $this->extractName($crawler),
            brand: $this->extractBrand($crawler),
            gender: $this->extractGender($crawler),
            description: $this->extractDescription($crawler),
            inspiredBy: $this->extractInspiredBy($crawler),
            concentration: $this->extractConcentration($crawler),
            year: $this->extractYear($crawler),
            imageUrl: $this->extractImage($crawler),
            brandLogoUrl: $this->extractBrandLogo($crawler),
            rating: $this->extractRating($crawler),
            votesCount: $this->extractVotesCount($crawler),
            accords: $this->extractAccords($crawler),
            notes: $this->extractNotes($crawler),
            seasons: $this->extractSeasons($crawler),
            dayNight: $this->extractDayNight($crawler),
        );
    }

    private function validateUrl(string $url): void
    {
        if (! preg_match('#^https?://(www\.)?fragrantica\.com(\.\w+)?/perfume/.+-\d+\.html#i', $url)) {
            throw new InvalidArgumentException(
                'URL inválida. Use o formato: https://www.fragrantica.com.br/perfume/Marca/Nome-12345.html'
            );
        }
    }

    private function extractId(string $url): int
    {
        if (! preg_match('/-(\d+)\.html/i', $url, $matches)) {
            throw new InvalidArgumentException('Não foi possível extrair o ID do perfume da URL.');
        }

        return (int) $matches[1];
    }

    // ---- Data Extractors ----

    private function extractName(Crawler $crawler): string
    {
        try {
            $h1 = $crawler->filter('h1[itemprop="name"]');
            if ($h1->count() === 0) {
                $h1 = $crawler->filter('h1');
            }

            $fullText = trim($h1->text(''));
            $fullText = preg_replace('/\s*(Masculino|Feminino|Unissex|para Homens e Mulheres|para Homens|para Mulheres)\s*/iu', '', $fullText);

            $brand = $this->extractBrand($crawler);
            if ($brand && str_ends_with($fullText, $brand)) {
                $fullText = trim(mb_substr($fullText, 0, -mb_strlen($brand)));
            }

            return trim($fullText) ?: 'Sem Nome';
        } catch (\Throwable $e) {
            Log::warning("Fragrantica parser: erro ao extrair nome - {$e->getMessage()}");

            return 'Sem Nome';
        }
    }

    private function extractBrand(Crawler $crawler): ?string
    {
        try {
            $brandNode = $crawler->filter('[itemprop="brand"] [itemprop="name"]');
            if ($brandNode->count() > 0) {
                return trim($brandNode->text(''));
            }

            $brandLink = $crawler->filter('[itemprop="brand"] a');
            if ($brandLink->count() > 0) {
                return trim($brandLink->text(''));
            }
        } catch (\Throwable) {}

        return null;
    }

    private function extractGender(Crawler $crawler): string
    {
        try {
            $h1Text = $crawler->filter('h1')->text('');
            $lower = mb_strtolower($h1Text);

            if (str_contains($lower, 'masculino') || str_contains($lower, 'para homens')) {
                return 'masculino';
            }
            if (str_contains($lower, 'feminino') || str_contains($lower, 'para mulheres')) {
                return 'feminino';
            }
            if (str_contains($lower, 'unissex') || str_contains($lower, 'homens e mulheres')) {
                return 'unissex';
            }

            $desc = $crawler->filter('#perfume-description-content')->text('');
            $descLower = mb_strtolower($desc);

            if (str_contains($descLower, 'masculino')) {
                return 'masculino';
            }
            if (str_contains($descLower, 'feminino')) {
                return 'feminino';
            }
        } catch (\Throwable) {}

        return 'unissex';
    }

    private function extractDescription(Crawler $crawler): ?string
    {
        try {
            $node = $crawler->filter('#perfume-description-content');
            if ($node->count() > 0) {
                return trim(strip_tags($node->html()));
            }

            $node = $crawler->filter('[itemprop="description"]');
            if ($node->count() > 0) {
                return trim(strip_tags($node->html()));
            }
        } catch (\Throwable) {}

        return null;
    }

    /**
     * A seção "Este Perfume me Lembra do" do Fragrantica é renderizada via JavaScript
     * (dados criptografados em `similar_perfumes`), portanto não está disponível no
     * HTML estático obtido via Ctrl+U. Este campo deve ser preenchido manualmente
     * na tela de edição após a importação.
     */
    private function extractInspiredBy(Crawler $crawler): ?string
    {
        return null;
    }

    private function extractConcentration(Crawler $crawler): ?string
    {
        $patterns = [
            '/Eau de Parfum/i', '/Eau de Toilette/i', '/Eau de Cologne/i',
            '/Extrait de Parfum/i', '/Parfum/i', '/Colônia/i', '/Cologne/i',
        ];

        try {
            $desc = $crawler->filter('#perfume-description-content')->text('');
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $desc, $m)) {
                    return $m[0];
                }
            }

            $title = $crawler->filter('title')->text('');
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $title, $m)) {
                    return $m[0];
                }
            }
        } catch (\Throwable) {}

        return null;
    }

    private function extractYear(Crawler $crawler): ?int
    {
        try {
            $desc = $crawler->filter('#perfume-description-content')->text('');
            if (preg_match('/lançado em (\d{4})/iu', $desc, $m)) {
                return (int) $m[1];
            }

            $title = $crawler->filter('title')->text('');
            if (preg_match('/\b(19|20)\d{2}\b/', $title, $m)) {
                return (int) $m[0];
            }
        } catch (\Throwable) {}

        return null;
    }

    private function extractImage(Crawler $crawler): ?string
    {
        try {
            $img = $crawler->filter('img[itemprop="image"]');
            if ($img->count() > 0) {
                $srcset = $img->attr('srcset');
                if ($srcset && preg_match('/(\S+\.2x\.\w+)/', $srcset, $m)) {
                    return $m[1];
                }

                return $img->attr('src');
            }

            $picture = $crawler->filter('picture img');
            if ($picture->count() > 0) {
                return $picture->attr('src');
            }
        } catch (\Throwable) {}

        return null;
    }

    private function extractBrandLogo(Crawler $crawler): ?string
    {
        try {
            $logo = $crawler->filter('img[alt*="logo"]');
            if ($logo->count() > 0) {
                return $logo->attr('src');
            }
        } catch (\Throwable) {}

        return null;
    }

    private function extractRating(Crawler $crawler): ?float
    {
        try {
            $node = $crawler->filter('[itemprop="ratingValue"]');
            if ($node->count() > 0) {
                $val = $node->attr('content') ?? $node->text('');

                return $val ? round((float) $val, 2) : null;
            }
        } catch (\Throwable) {}

        return null;
    }

    private function extractVotesCount(Crawler $crawler): int
    {
        try {
            $node = $crawler->filter('[itemprop="ratingCount"]');
            if ($node->count() > 0) {
                $val = $node->attr('content') ?? $node->text('');

                return (int) preg_replace('/\D/', '', $val);
            }
        } catch (\Throwable) {}

        return 0;
    }

    // ---- Accords & Notes ----

    /**
     * @return array<int, array{name: string, percentage: float, color: string}>
     */
    private function extractAccords(Crawler $crawler): array
    {
        $accords = [];

        try {
            $crawler->filter('h6')->each(function (Crawler $h6) use (&$accords) {
                if (! str_contains(mb_strtolower($h6->text('')), 'principais acordes')) {
                    return;
                }

                $container = $h6->nextAll()->first();
                if ($container->count() === 0) {
                    return;
                }

                $container->filter('div[style]')->each(function (Crawler $bar) use (&$accords) {
                    $style = $bar->attr('style') ?? '';

                    if (! preg_match('/width:\s*([\d.]+)%/', $style, $wm)) {
                        return;
                    }

                    $span = $bar->filter('span');
                    $name = $span->count() > 0 ? trim($span->text('')) : '';
                    if (empty($name)) {
                        return;
                    }

                    $color = '#999999';
                    if (preg_match('/background(?:-color)?:\s*([^;]+)/i', $style, $cm)) {
                        $color = trim($cm[1]);
                    }

                    $accords[] = [
                        'name'       => $name,
                        'percentage' => (float) $wm[1],
                        'color'      => $color,
                    ];
                });
            });
        } catch (\Throwable $e) {
            Log::warning("Fragrantica parser: erro ao extrair acordes - {$e->getMessage()}");
        }

        return $accords;
    }

    /**
     * @return array<int, array{layer: string, name: string, image_url: ?string}>
     */
    private function extractNotes(Crawler $crawler): array
    {
        try {
            $pyramid = $crawler->filter('#pyramid');
            if ($pyramid->count() === 0) {
                return $this->extractNotesFromDescription($crawler);
            }

            $notes = [];
            $layerOrder = ['top', 'heart', 'base'];
            $containers = $pyramid->filter('.pyramid-level-container');

            if ($containers->count() === 0) {
                return $this->extractNotesFromDescription($crawler);
            }

            $containers->each(function (Crawler $container, int $i) use (&$notes, $layerOrder) {
                $layer = $layerOrder[$i] ?? 'base';

                $container->filter('a')->each(function (Crawler $link) use (&$notes, $layer) {
                    $span = $link->filter('span');
                    $name = $span->count() > 0 ? trim($span->text('')) : trim($link->text(''));

                    if (mb_strlen($name) < 2) {
                        return;
                    }

                    $lower = mb_strtolower($name);
                    if (str_contains($lower, 'notas de') || str_contains($lower, 'buscar')) {
                        return;
                    }

                    $img = $link->filter('img');
                    $imageUrl = $img->count() > 0 ? $img->attr('src') : null;

                    $notes[] = [
                        'layer'     => $layer,
                        'name'      => $name,
                        'image_url' => $imageUrl,
                    ];
                });
            });

            return ! empty($notes) ? $notes : $this->extractNotesFromDescription($crawler);
        } catch (\Throwable $e) {
            Log::warning("Fragrantica parser: erro ao extrair notas - {$e->getMessage()}");

            return $this->extractNotesFromDescription($crawler);
        }
    }

    /**
     * Fallback: extrai notas da descrição textual.
     */
    private function extractNotesFromDescription(Crawler $crawler): array
    {
        $notes = [];

        try {
            $desc = $crawler->filter('#perfume-description-content')->text('');

            $patterns = [
                'top'   => '/notas de topo s[aã]o:?\s*(.+?)\.?\s*(?:As notas|A nota|$)/iu',
                'heart' => '/notas de cora[çc][aã]o s[aã]o:?\s*(.+?)\.?\s*(?:As notas|A nota|$)/iu',
                'base'  => '/notas de (?:fundo|base) s[aã]o:?\s*(.+?)\.?\s*$/iu',
            ];

            foreach ($patterns as $layer => $pattern) {
                if (preg_match($pattern, $desc, $m)) {
                    $noteNames = preg_split('/\s*[,e]\s*/u', trim($m[1]));
                    foreach ($noteNames as $name) {
                        $name = trim($name, ' .');
                        if (mb_strlen($name) >= 2) {
                            $notes[] = ['layer' => $layer, 'name' => $name, 'image_url' => null];
                        }
                    }
                }
            }
        } catch (\Throwable) {}

        return $notes;
    }

    // ---- Voting Cards (Seasons / Day-Night) ----

    /**
     * @return array<string, int>|null
     */
    private function extractSeasons(Crawler $crawler): ?array
    {
        return $this->extractVotingCard($crawler, [
            'inverno'   => ['inverno', 'winter'],
            'primavera' => ['primavera', 'spring'],
            'verao'     => ['verão', 'verâo', 'summer', 'verao'],
            'outono'    => ['outono', 'fall', 'autumn'],
        ]);
    }

    /**
     * @return array<string, int>|null
     */
    private function extractDayNight(Crawler $crawler): ?array
    {
        return $this->extractVotingCard($crawler, [
            'dia'   => ['dia', 'day'],
            'noite' => ['noite', 'night'],
        ]);
    }

    /**
     * @param  array<string, array<string>>  $keywordMap
     * @return array<string, int>|null
     */
    private function extractVotingCard(Crawler $crawler, array $keywordMap): ?array
    {
        $result = [];

        try {
            $allText = $crawler->filter('body')->text('');

            foreach ($keywordMap as $key => $keywords) {
                foreach ($keywords as $kw) {
                    if (preg_match('/\b' . preg_quote($kw, '/') . '\b\s*([\d.,k]+)/iu', $allText, $m)) {
                        $result[$key] = $this->parseVoteCount($m[1]);
                        break;
                    }
                }
            }
        } catch (\Throwable) {}

        return ! empty($result) ? $result : null;
    }

    private function parseVoteCount(string $raw): int
    {
        $raw = mb_strtolower(trim($raw));

        if (str_contains($raw, 'k')) {
            return (int) ((float) str_replace([',', 'k'], ['.', ''], $raw) * 1000);
        }

        return (int) preg_replace('/\D/', '', $raw);
    }
}
