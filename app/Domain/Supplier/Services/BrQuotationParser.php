<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Services;

/**
 * Parseia cotações de fornecedores brasileiros no formato WhatsApp.
 *
 * Formato esperado (exemplo Santa Fé):
 * *18 pro max 512gb*
 * 🟦AZUL *9.900*
 * 🟥BORDO *10.500*
 * ⬜️BRANCO ❌
 * ⬛️preto *9.900*
 */
class BrQuotationParser
{
    /**
     * @return array<int, array{category: string, product_name: string, unit_price: float, available: bool}>
     */
    public function parse(string $rawText): array
    {
        $lines = $this->normalizeLines($rawText);
        $items = [];
        $currentCategory = '';
        $currentProduct = '';

        foreach ($lines as $line) {
            $stripped = $this->stripEmojis($line);

            if ($this->isSeparator($stripped)) {
                continue;
            }

            $category = $this->detectCategory($stripped, $line);
            if ($category !== null) {
                $currentCategory = $category;
                $currentProduct = '';
                continue;
            }

            $product = $this->detectProduct($stripped);
            if ($product !== null) {
                $currentProduct = $product;
                continue;
            }

            $inlineProduct = $this->detectProductWithPrice($stripped, $line);
            if ($inlineProduct !== null) {
                $items[] = [
                    'category' => $currentCategory,
                    'product_name' => $inlineProduct['name'],
                    'unit_price' => $inlineProduct['price'],
                    'available' => $inlineProduct['available'],
                ];
                continue;
            }

            if ($currentProduct === '') {
                continue;
            }

            $variant = $this->detectVariant($stripped, $line);
            if ($variant !== null) {
                $items[] = [
                    'category' => $currentCategory,
                    'product_name' => $currentProduct . ' - ' . $variant['color'],
                    'unit_price' => $variant['price'],
                    'available' => $variant['available'],
                ];
            }
        }

        return $items;
    }

    private function normalizeLines(string $text): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $text);

        return array_values(array_filter(
            array_map('trim', $lines),
            fn(string $line) => $line !== ''
        ));
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
            '/[\x{23CF}\x{23ED}-\x{23EF}]/u',
        ];

        $result = $text;
        foreach ($patterns as $pattern) {
            $result = preg_replace($pattern, '', $result) ?? $result;
        }

        return trim($result);
    }

    private function isSeparator(string $line): bool
    {
        $clean = trim(str_replace(['—', '—', '_', '-', '*'], '', $line));
        return $clean === '' || preg_match('/^[\s\-_—]+$/u', $line);
    }

    private function detectCategory(string $stripped, string $originalLine): ?string
    {
        $inner = trim(str_replace('*', '', $stripped));
        $inner = preg_replace('/\s+/u', ' ', $inner);

        if ($inner === '' || mb_strlen($inner) < 3) {
            return null;
        }

        if (preg_match('/\d+\s*(?:GB|TB|MM)\b/iu', $inner)) {
            return null;
        }

        $patterns = [
            '/REPASSE\s+APPLE/iu',
            '/IPHONE\s+1[5-9]/iu',
            '/IPHONE\s+\d+/iu',
            '/ACESS[OÓ]RIOS?\s+APPLE/iu',
            '/L\s*A\s*N\s*[ÇC]\s*A\s*M\s*E\s*N\s*T\s*O/iu',
            '/DISPONÍVEL/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $inner)) {
                return mb_strtoupper($inner);
            }
        }

        if (preg_match('/^IPHONE/iu', $inner) && !preg_match('/\d+\s*gb/iu', $inner)) {
            return mb_strtoupper($inner);
        }

        return null;
    }

    private function detectProduct(string $stripped): ?string
    {
        $clean = trim(str_replace('*', '', $stripped));
        $clean = preg_replace('/\s+/u', ' ', $clean);

        if ($clean === '' || mb_strlen($clean) < 5) {
            return null;
        }

        if (preg_match('/\d+\s*(?:GB|gb|TB|tb|MM|mm)\b/u', $clean)) {
            if (!$this->hasPrice($clean)) {
                return $this->normalizeProductName($clean);
            }
        }

        $productWithSizePatterns = [
            '/(?:iPhone|iPad|Mac\s*Book|Apple\s*Watch)/iu',
        ];
        foreach ($productWithSizePatterns as $pattern) {
            if (preg_match($pattern, $clean) && preg_match('/\d+\s*(?:gb|mm)/iu', $clean) && !$this->hasPrice($clean)) {
                return $this->normalizeProductName($clean);
            }
        }

        $standaloneProdPatterns = [
            '/^(?:Airpods?\s+(?:Pro\s*)?\d|JBL\s+\w+\s*\d|Apple\s*Watch\s+(?:SE|ULTRA)\s+\d)/iu',
        ];
        foreach ($standaloneProdPatterns as $pattern) {
            if (preg_match($pattern, $clean) && !$this->hasPrice($clean)) {
                return $this->normalizeProductName($clean);
            }
        }

        return null;
    }

    private function hasPrice(string $text): bool
    {
        if (preg_match('/\*[\d.,]+\*/u', $text)) {
            return true;
        }

        $clean = trim(str_replace('*', '', $text));
        if (preg_match('/\b(\d{1,3}(?:\.\d{3})*)\s*$/u', $clean, $m)) {
            return (float) str_replace('.', '', $m[1]) >= 100;
        }

        return false;
    }

    /**
     * Detecta variante: COR + preço ou ❌.
     */
    private function detectVariant(string $stripped, string $originalLine): ?array
    {
        $hasX = str_contains($originalLine, '❌') || str_contains($originalLine, '✖');

        $clean = trim(str_replace('*', '', $stripped));
        $clean = preg_replace('/\s+/u', ' ', $clean);

        if ($clean === '' || mb_strlen($clean) < 2) {
            return null;
        }

        if (preg_match('/\d+\s*(?:GB|TB|MM)\b/iu', $clean) && !preg_match('/\d{1,3}(?:\.\d{3})*$/u', $clean)) {
            return null;
        }

        if (preg_match('/^([A-ZÀ-ÿa-z][A-ZÀ-ÿa-z\s]*?)\s+([\d.,]+)\s*$/u', $clean, $m)) {
            $color = mb_strtoupper(trim($m[1]));
            $priceStr = str_replace('.', '', $m[2]);
            $priceStr = str_replace(',', '.', $priceStr);
            $price = (float) $priceStr;

            if ($price >= 100 && mb_strlen($color) <= 30) {
                return ['color' => $color, 'price' => $price, 'available' => true];
            }
        }

        if (preg_match('/^([A-ZÀ-ÿa-z][A-ZÀ-ÿa-z\s]*?)\s*=\s*([\d.,]+)\s*$/u', $clean, $m)) {
            $color = mb_strtoupper(trim($m[1]));
            $priceStr = str_replace('.', '', $m[2]);
            $priceStr = str_replace(',', '.', $priceStr);
            $price = (float) $priceStr;

            if ($price >= 100 && mb_strlen($color) <= 30) {
                return ['color' => $color, 'price' => $price, 'available' => true];
            }
        }

        if ($hasX) {
            $colorPart = preg_replace('/[\d.,=]+/', '', $clean);
            $colorPart = trim($colorPart);
            if ($colorPart !== '' && mb_strlen($colorPart) <= 30) {
                return ['color' => mb_strtoupper($colorPart), 'price' => 0.0, 'available' => false];
            }
        }

        return null;
    }

    /**
     * Detecta produtos standalone com preço na mesma linha.
     * Ex: *Airpods 4*  850 | *Airpods 4 c/ ANC* 1.200 | *Airpods Pro 3* ❌
     */
    private function detectProductWithPrice(string $stripped, string $originalLine): ?array
    {
        $hasX = str_contains($originalLine, '❌');

        $clean = trim(str_replace('*', '', $stripped));
        $clean = preg_replace('/\s+/u', ' ', $clean);

        $patterns = [
            '/^(Airpods?\s+(?:Pro\s*)?\d[^\d]*?)\s+([\d.,]+)\s*$/iu',
            '/^(Airpods?\s+\d\s+c\/[^\d]*?)\s+([\d.,]+)\s*$/iu',
            '/^(JBL\s+\w+[^\d]*?)\s+([\d.,]+)\s*$/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $clean, $m)) {
                $name = trim($m[1]);
                $priceStr = str_replace(['.', ','], ['', '.'], $m[2]);
                $price = (float) $priceStr;
                if ($price >= 100) {
                    return ['name' => $name, 'price' => $price, 'available' => true];
                }
            }
        }

        if ($hasX) {
            $standaloneProdPatterns = [
                '/^(Airpods?\s+(?:Pro\s*)?\d.*?)$/iu',
                '/^(JBL\s+\w+.*?)$/iu',
            ];
            foreach ($standaloneProdPatterns as $pattern) {
                $cleanNoDigitEnd = preg_replace('/\s+[\d.,]+\s*$/', '', $clean);
                if (preg_match($pattern, $cleanNoDigitEnd, $m)) {
                    return ['name' => trim($m[1]), 'price' => 0.0, 'available' => false];
                }
            }
        }

        return null;
    }

    private function normalizeProductName(string $name): string
    {
        $name = str_replace('*', '', $name);
        $name = preg_replace('/\s+/u', ' ', $name);

        return trim($name);
    }
}
