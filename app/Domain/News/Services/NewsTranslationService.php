<?php

declare(strict_types=1);

namespace App\Domain\News\Services;

use App\Domain\AI\Services\GeminiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NewsTranslationService
{
    private const CACHE_PREFIX = 'news_translation:';
    private const CACHE_TTL_HOURS = 48;
    private const BATCH_SIZE = 10;

    public function __construct(
        private readonly GeminiService $geminiService,
    ) {}

    /**
     * Traduz títulos de notícias em lote para pt-BR.
     * Itens do MacMagazine (já em pt-BR) são ignorados.
     * Resultados ficam cacheados por 48h para economizar requests do Gemini.
     */
    public function translateNewsItems(array $items): array
    {
        if (! $this->geminiService->isAvailable()) {
            return $items;
        }

        $toTranslate = [];
        $translatedMap = [];

        foreach ($items as $index => $item) {
            if ($item['source'] === 'MacMagazine') {
                continue;
            }

            $cacheKey = self::CACHE_PREFIX . md5($item['title']);
            $cached = Cache::get($cacheKey);

            if ($cached !== null) {
                $translatedMap[$index] = $cached;

                continue;
            }

            $toTranslate[$index] = $item['title'];
        }

        if (empty($toTranslate)) {
            return $this->applyTranslations($items, $translatedMap);
        }

        $batches = array_chunk($toTranslate, self::BATCH_SIZE, true);

        foreach ($batches as $batch) {
            $translations = $this->translateBatch($batch);

            foreach ($translations as $index => $translated) {
                $translatedMap[$index] = $translated;

                $cacheKey = self::CACHE_PREFIX . md5($items[$index]['title']);
                Cache::put($cacheKey, $translated, now()->addHours(self::CACHE_TTL_HOURS));
            }
        }

        return $this->applyTranslations($items, $translatedMap);
    }

    private function translateBatch(array $titles): array
    {
        $numbered = [];
        $indexMap = [];
        $seq = 1;

        foreach ($titles as $originalIndex => $title) {
            $numbered[] = "{$seq}. {$title}";
            $indexMap[$seq] = $originalIndex;
            $seq++;
        }

        $titlesText = implode("\n", $numbered);

        $prompt = <<<PROMPT
Traduza os seguintes títulos de notícias de tecnologia do inglês para o português brasileiro.
Mantenha a numeração original. Retorne APENAS as traduções numeradas, sem explicações.
Mantenha nomes próprios (Apple, iPhone, iPad, Mac, etc.) sem traduzir.

{$titlesText}
PROMPT;

        try {
            $result = $this->geminiService->generateContent(
                $prompt,
                'Você é um tradutor especializado em tecnologia. Traduza de forma natural e fluente para pt-BR.'
            );

            if ($result === null) {
                return [];
            }

            return $this->parseTranslationResult($result, $indexMap);
        } catch (\Throwable $e) {
            Log::warning('NewsTranslation: falha ao traduzir lote', [
                'error' => $e->getMessage(),
                'count' => count($titles),
            ]);

            return [];
        }
    }

    private function parseTranslationResult(string $text, array $indexMap): array
    {
        $translations = [];
        $lines = array_filter(array_map('trim', explode("\n", $text)));

        foreach ($lines as $line) {
            if (preg_match('/^(\d+)\.\s*(.+)$/', $line, $matches)) {
                $seq = (int) $matches[1];
                $translated = trim($matches[2]);

                if (isset($indexMap[$seq]) && $translated !== '') {
                    $translations[$indexMap[$seq]] = $translated;
                }
            }
        }

        return $translations;
    }

    private function applyTranslations(array $items, array $translatedMap): array
    {
        foreach ($translatedMap as $index => $translatedTitle) {
            if (isset($items[$index])) {
                $items[$index]['title_original'] = $items[$index]['title'];
                $items[$index]['title'] = $translatedTitle;
            }
        }

        return $items;
    }
}
