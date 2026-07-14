<?php

declare(strict_types=1);

namespace App\Domain\System\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class ComprasParaguaiDollarService
{
    private const URL = 'https://www.comprasparaguai.com.br/';

    private const XPATH = '/html/body/div/div[5]/div/div/div[3]/div[1]/span[1]/strong';

    private const CSS_FALLBACK = 'div.dolar-cotacao strong';

    public function fetchRate(): ?float
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept'     => 'text/html,application/xhtml+xml',
                ])
                ->get(self::URL);

            if (! $response->successful()) {
                Log::warning('ComprasParaguai: HTTP ' . $response->status());

                return null;
            }

            return $this->parseRate($response->body());
        } catch (\Throwable $e) {
            Log::error('ComprasParaguai: falha ao buscar cotação', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function parseRate(string $html): ?float
    {
        $crawler = new Crawler($html);

        $value = $this->extractByXpath($crawler)
              ?? $this->extractByCss($crawler);

        if ($value === null) {
            Log::warning('ComprasParaguai: não foi possível extrair a cotação do HTML');
        }

        return $value;
    }

    private function extractByXpath(Crawler $crawler): ?float
    {
        try {
            $node = $crawler->filterXPath(self::XPATH);

            if ($node->count() === 0) {
                return null;
            }

            return $this->sanitizeValue($node->text());
        } catch (\Throwable) {
            return null;
        }
    }

    private function extractByCss(Crawler $crawler): ?float
    {
        try {
            $node = $crawler->filter(self::CSS_FALLBACK);

            if ($node->count() === 0) {
                return null;
            }

            return $this->sanitizeValue($node->first()->text());
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Converte "R$ 5,23" ou "5.23" para float 5.23
     */
    private function sanitizeValue(string $raw): ?float
    {
        $cleaned = preg_replace('/[^\d,.]/', '', trim($raw));

        if ($cleaned === '' || $cleaned === null) {
            return null;
        }

        // Formato brasileiro: 5,23 → 5.23
        $cleaned = str_replace(',', '.', $cleaned);

        $value = (float) $cleaned;

        if ($value <= 0 || $value > 50) {
            Log::warning('ComprasParaguai: valor fora do esperado', ['raw' => $raw, 'parsed' => $value]);

            return null;
        }

        return round($value, 4);
    }
}
