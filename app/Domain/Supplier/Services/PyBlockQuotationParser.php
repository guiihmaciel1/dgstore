<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Services;

/**
 * Parseia cotações PY no formato "em blocos" (ex.: Prime Shop - Vânia):
 *
 * Iphone 18 pro max 256Gb 🇺🇸
 * Burgundy                 $1450
 * Black                    $1440
 *
 * Iphone 18 pro 256Gb 🇺🇸. $1265      ← preço no cabeçalho vale para as cores sem preço
 * Burgundy
 * Black
 *
 * APPLE WATCH S11 46MM ... BLACK   $373   ← linha única vira um item
 *
 * A bandeira define a versão: 🇺🇸 fica sem sufixo; 🇮🇳 e 🇯🇵 ganham " (IN)" / " (JP)".
 */
class PyBlockQuotationParser
{
    private const HEADER = '/^(iphone|ipad|apple|macbook|airpods?|mac\b)/i';

    private const IGNORED_WORDS = ['AJUSTE', 'AJUSTES', 'PROMO', 'PROMOÇÃO', 'ESGOTADO', 'ATUALIZADO', 'NOVO', 'NOVOS'];

    /**
     * @return array<int, array{category: string, product_name: string, price_usd: float}>
     */
    public function parse(string $rawText): array
    {
        $items = [];
        $header = null;

        $flush = function () use (&$header, &$items) {
            if ($header && ! $header['emitted'] && $header['price'] > 0) {
                $items[] = $this->item($header, $header['name'], $header['price']);
            }
            $header = null;
        };

        foreach (preg_split('/\R/u', $rawText) as $rawLine) {
            $region = $this->region($rawLine);
            $line = trim(preg_replace('/\s+/u', ' ', $this->stripEmojis($rawLine)));

            if ($line === '') {
                continue;
            }

            [$text, $price] = $this->splitPrice($line);

            if (preg_match(self::HEADER, $text)) {
                $flush();
                $header = [
                    'name' => $this->cleanName($text),
                    'price' => $price,
                    'region' => $region,
                    'category' => $this->category($text),
                    'emitted' => false,
                ];
                continue;
            }

            if (! $header || ! $this->looksLikeColor($text)) {
                continue;
            }

            $colorPrice = $price ?? $header['price'];
            if ($colorPrice > 0) {
                $items[] = $this->item($header, $header['name'] . ' ' . mb_strtoupper($text), $colorPrice);
                $header['emitted'] = true;
            }
        }

        $flush();

        // Mesmo produto repetido na mensagem: vale o último preço.
        $unique = [];
        foreach ($items as $item) {
            $unique[$item['product_name']] = $item;
        }

        return array_values($unique);
    }

    private function item(array $header, string $name, float $price): array
    {
        $suffix = match ($header['region']) {
            'IN' => ' (IN)',
            'JP' => ' (JP)',
            default => '',
        };

        return [
            'category' => $header['category'],
            'product_name' => trim(preg_replace('/\s+/', ' ', $name)) . $suffix,
            'price_usd' => $price,
        ];
    }

    /** @return array{0: string, 1: ?float} */
    private function splitPrice(string $line): array
    {
        if (preg_match('/^(.*?)\s*\$\s*([\d][\d.,]*)\s*$/u', $line, $m)) {
            $digits = str_replace(',', '', $m[2]);
            // "1.250" com 3 casas depois do ponto é milhar, não decimal
            if (preg_match('/^\d{1,3}\.\d{3}$/', $digits)) {
                $digits = str_replace('.', '', $digits);
            }

            return [trim($m[1]), (float) $digits ?: null];
        }

        return [$line, null];
    }

    private function cleanName(string $text): string
    {
        $text = preg_replace('/(\d+)\s*gb\b/i', '$1GB', $text);
        $text = preg_replace('/(\d+)\s*tb\b/i', '$1TB', $text);
        $text = rtrim($text, " .-\t");

        return mb_strtoupper(trim(preg_replace('/\s+/', ' ', $text)));
    }

    private function looksLikeColor(string $text): bool
    {
        if (! preg_match('/^[\pL][\pL \/]{1,30}$/u', $text)) {
            return false;
        }

        return ! in_array(mb_strtoupper(trim($text)), self::IGNORED_WORDS, true)
            && str_word_count($text) <= 3;
    }

    private function category(string $text): string
    {
        $upper = mb_strtoupper($text);

        return match (true) {
            str_contains($upper, 'IPHONE') => 'IPHONE',
            str_contains($upper, 'WATCH') => 'APPLE WATCH',
            str_contains($upper, 'IPAD') => 'IPAD',
            str_contains($upper, 'MACBOOK') || str_starts_with($upper, 'MAC ') => 'MACBOOK',
            str_contains($upper, 'AIRPOD') => 'APPLE AIRPODS',
            default => 'APPLE',
        };
    }

    private function region(string $line): string
    {
        return match (true) {
            str_contains($line, "\u{1F1EE}\u{1F1F3}") => 'IN',
            str_contains($line, "\u{1F1EF}\u{1F1F5}") => 'JP',
            default => 'US',
        };
    }

    private function stripEmojis(string $text): string
    {
        return preg_replace('/[\x{1F000}-\x{1FFFF}\x{2600}-\x{27BF}\x{FE00}-\x{FE0F}\x{200D}\x{20E3}\x{E0020}-\x{E007F}]/u', '', $text) ?? $text;
    }
}
