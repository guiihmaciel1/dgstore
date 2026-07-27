<?php

declare(strict_types=1);

namespace App\Domain\System\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class ComprasParaguaiDollarService
{
    private const URL = 'https://www.comprasparaguai.com.br/';

    private const HISTORY_URL = 'https://www.comprasparaguai.com.br/historico-cotacao';

    private const HISTORY_CACHE_KEY = 'cp_dollar_history';

    private const HISTORY_CACHE_MINUTES = 30;

    private const XPATH = '/html/body/div/div[5]/div/div/div[3]/div[1]/span[1]/strong';

    private const CSS_FALLBACK = 'div.dolar-cotacao strong';

    public function fetchRate(): ?float
    {
        try {
            $response = $this->httpGet(self::URL);

            if ($response === null) {
                return null;
            }

            return $this->parseRate($response);
        } catch (\Throwable $e) {
            Log::error('ComprasParaguai: falha ao buscar cotação', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Busca e cacheia o histórico de cotações da página de histórico.
     * Retorna: ['currencies' => [...], 'history' => [...], 'updated_at' => '...']
     */
    public function fetchHistory(): ?array
    {
        try {
            $html = $this->httpGet(self::HISTORY_URL);

            if ($html === null) {
                return null;
            }

            $data = $this->parseHistoryPage($html);

            if ($data !== null) {
                Cache::put(self::HISTORY_CACHE_KEY, $data, now()->addMinutes(self::HISTORY_CACHE_MINUTES));
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('ComprasParaguai: falha ao buscar histórico', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function getCachedHistory(): ?array
    {
        return Cache::get(self::HISTORY_CACHE_KEY);
    }

    private function httpGet(string $url): ?string
    {
        $response = Http::timeout(15)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept'     => 'text/html,application/xhtml+xml',
            ])
            ->get($url);

        if (! $response->successful()) {
            Log::warning("ComprasParaguai: HTTP {$response->status()} ao acessar {$url}");

            return null;
        }

        return $response->body();
    }

    private function parseHistoryPage(string $html): ?array
    {
        $crawler = new Crawler($html);

        $currencies = $this->extractCurrencyCards($crawler);
        $history = $this->extractHistoryTable($crawler);

        if (empty($currencies) && empty($history)) {
            Log::warning('ComprasParaguai: nenhum dado extraído da página de histórico');

            return null;
        }

        return [
            'currencies' => $currencies,
            'history'    => $history,
            'updated_at' => now()->format('d/m/Y H:i'),
        ];
    }

    private function extractCurrencyCards(Crawler $crawler): array
    {
        $currencies = [];

        $mappings = [
            ['label' => 'Dólar → Real', 'symbol' => 'R$', 'key' => 'brl'],
            ['label' => 'Dólar → Guarani', 'symbol' => '₲', 'key' => 'pyg'],
            ['label' => 'Dólar → Peso', 'symbol' => '$', 'key' => 'ars'],
        ];

        try {
            // CSS-based extraction: cards com cotação
            $cards = $crawler->filter('section')->each(function (Crawler $section) {
                return $section;
            });

            // Fallback: buscar por texto forte com valores monetários
            $strongNodes = $crawler->filter('strong, b, span.value, div.value');
            $values = [];

            $strongNodes->each(function (Crawler $node) use (&$values) {
                $text = trim($node->text());
                if (preg_match('/^[R\$₲\$\s]*[\d.,]+$/', $text)) {
                    $values[] = $text;
                }
            });

            // Tentar extrair as 3 primeiras cotações encontradas na página
            foreach ($values as $index => $raw) {
                if ($index >= count($mappings)) {
                    break;
                }

                $numericStr = preg_replace('/[^\d,.]/', '', $raw);
                if ($numericStr === '' || $numericStr === null) {
                    continue;
                }

                // Guarani/Peso usam ponto como separador de milhar: 6.060 → 6060
                if ($mappings[$index]['key'] !== 'brl') {
                    $numericStr = str_replace('.', '', $numericStr);
                    $numericStr = str_replace(',', '.', $numericStr);
                } else {
                    $numericStr = str_replace(',', '.', $numericStr);
                }

                $value = (float) $numericStr;

                if ($value > 0) {
                    $currencies[$mappings[$index]['key']] = [
                        'label'  => $mappings[$index]['label'],
                        'symbol' => $mappings[$index]['symbol'],
                        'value'  => $value,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ComprasParaguai: falha ao extrair cards de moedas', ['error' => $e->getMessage()]);
        }

        return $currencies;
    }

    private function extractHistoryTable(Crawler $crawler): array
    {
        $history = [];

        try {
            // Procura linhas de tabela dentro de section[3] ou qualquer tabela
            $rows = $crawler->filter('table tr, section table tr');

            if ($rows->count() === 0) {
                // Fallback: tentar divs com padrão de data + valor
                $rows = $crawler->filter('section div');
            }

            $rows->each(function (Crawler $row) use (&$history) {
                $cells = $row->filter('td, th');

                if ($cells->count() < 3) {
                    return;
                }

                $dateText = trim($cells->eq(0)->text());
                $valueText = trim($cells->eq(1)->text());
                $changeText = trim($cells->eq(2)->text());

                // Validar que parece uma linha de dados (data + valor monetário)
                if (! preg_match('/\d{2}\/\d{2}/', $dateText)) {
                    return;
                }

                $numericStr = preg_replace('/[^\d,.]/', '', $valueText);
                if ($numericStr === '' || $numericStr === null) {
                    return;
                }
                $rate = (float) str_replace(',', '.', $numericStr);

                $history[] = [
                    'date'   => $dateText,
                    'rate'   => $rate,
                    'change' => $changeText,
                ];
            });
        } catch (\Throwable $e) {
            Log::warning('ComprasParaguai: falha ao extrair tabela de histórico', ['error' => $e->getMessage()]);
        }

        return $history;
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

        $cleaned = str_replace(',', '.', $cleaned);

        $value = (float) $cleaned;

        if ($value <= 0 || $value > 50) {
            Log::warning('ComprasParaguai: valor fora do esperado', ['raw' => $raw, 'parsed' => $value]);

            return null;
        }

        return round($value, 4);
    }
}
