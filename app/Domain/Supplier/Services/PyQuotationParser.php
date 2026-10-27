<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Services;

/**
 * Parseia cotações de fornecedores PY (Paraguai) no formato WhatsApp.
 *
 * Formato esperado (Star Company):
 * 🍎🎧 APPLE AIRPODS 🍎🎧
 * ▪️ 4 MXP63LL/A WHITE - $121.00
 *
 * 🍎📱🆕 IPHONE LACRADO 🍎📱🆕
 * ▪️ IPHONE 17 PRO MAX 256GB AMERICANO BLUE - $1220.00
 */
class PyQuotationParser
{
    private const APPLE_CATEGORIES = [
        'APPLE AIRPODS',
        'APPLE WATCH',
        'IPHONE LACRADO',
        'IPAD',
        'MACBOOK',
        'APPLE',
    ];

    /**
     * @return array<int, array{category: string, product_name: string, price_usd: float}>
     */
    public function parse(string $rawText): array
    {
        $appleSection = $this->extractAppleSection($rawText);
        if (!$appleSection) {
            return [];
        }

        $lines = $this->normalizeLines($appleSection);
        $items = [];
        $currentCategory = '';

        foreach ($lines as $line) {
            $category = $this->detectCategory($line);
            if ($category !== null) {
                $currentCategory = $category;
                continue;
            }

            $item = $this->parsePriceLine($line);
            if ($item !== null) {
                $items[] = [
                    'category' => $currentCategory,
                    'product_name' => $item['name'],
                    'price_usd' => $item['price'],
                ];
            }
        }

        return $items;
    }

    /**
     * Extrai apenas a seção Apple da mensagem (ignora Amazon, etc).
     */
    private function extractAppleSection(string $text): ?string
    {
        if (preg_match('/🍎🍏\s*APPLE\s*🍎🍏(.+)$/isu', $text, $m)) {
            return $m[1];
        }

        if (preg_match('/APPLE\s+AIRPODS|APPLE\s+WATCH|IPHONE\s+LACRADO|MACBOOK/iu', $text)) {
            return $text;
        }

        return null;
    }

    private function normalizeLines(string $text): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $text);

        return array_values(array_filter(
            array_map('trim', $lines),
            fn (string $line) => $line !== ''
        ));
    }

    private function detectCategory(string $line): ?string
    {
        if (str_contains($line, '$')) {
            return null;
        }

        $stripped = $this->stripEmojis($line);
        $clean = trim($stripped);

        if ($clean === '' || mb_strlen($clean) < 3) {
            return null;
        }

        if (str_starts_with($clean, '▪') || str_starts_with($clean, '•') || str_starts_with($clean, '-')) {
            return null;
        }

        $upper = mb_strtoupper($clean);

        foreach (self::APPLE_CATEGORIES as $cat) {
            if (str_contains($upper, $cat)) {
                return $clean;
            }
        }

        return null;
    }

    /**
     * Parseia linha de produto com preço em USD.
     * Formato: ▪️ PRODUCT NAME - $PRICE
     */
    private function parsePriceLine(string $line): ?array
    {
        $stripped = $this->stripEmojis($line);
        $clean = trim($stripped);

        $clean = preg_replace('/^[▪️▫️•\-\*\s]+/u', '', $clean);
        $clean = trim($clean);

        if ($clean === '' || !str_contains($clean, '$')) {
            return null;
        }

        if (preg_match('/^(.+?)\s*-\s*\$\s*([\d.,]+)\s*$/u', $clean, $m)) {
            $name = trim($m[1]);
            $priceStr = str_replace(',', '', $m[2]);
            $price = (float) $priceStr;

            if ($price > 0 && mb_strlen($name) >= 3) {
                return [
                    'name' => $this->normalizeProductName($name),
                    'price' => $price,
                ];
            }
        }

        return null;
    }

    private function normalizeProductName(string $name): string
    {
        $name = preg_replace('/\s+/', ' ', $name);
        $name = preg_replace('/\bAMERICANO\b/i', '', $name);
        $name = preg_replace('/\bINDIAN\b/i', '', $name);
        $name = preg_replace('/\bLACRADO\b/i', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);

        return trim($name);
    }

    private function stripEmojis(string $text): string
    {
        $patterns = [
            '/[\x{1F000}-\x{1FFFF}]/u',
            '/[\x{2600}-\x{27BF}]/u',
            '/[\x{FE00}-\x{FE0F}]/u',
            '/[\x{1F900}-\x{1F9FF}]/u',
            '/[\x{200D}]/u',
            '/[\x{20E3}]/u',
            '/[\x{E0020}-\x{E007F}]/u',
            '/[\x{1F1E0}-\x{1F1FF}]/u',
            '/[\x{00A9}\x{00AE}]/u',
            '/[\x{203C}\x{2049}]/u',
            '/[\x{2122}\x{2139}]/u',
            '/[\x{2194}-\x{21AA}]/u',
            '/[\x{231A}-\x{23FA}]/u',
            '/[\x{25AA}-\x{25FE}]/u',
            '/[\x{2934}-\x{2935}]/u',
            '/[\x{3030}\x{303D}]/u',
            '/[\x{3297}\x{3299}]/u',
            '/[\x{FE00}-\x{FEFF}]/u',
            '/[\x{2B05}-\x{2B55}]/u',
            '/[\x{2716}\x{274C}\x{274E}\x{2705}]/u',
        ];

        $result = $text;
        foreach ($patterns as $pattern) {
            $result = preg_replace($pattern, '', $result) ?? $result;
        }

        return trim($result);
    }
}
