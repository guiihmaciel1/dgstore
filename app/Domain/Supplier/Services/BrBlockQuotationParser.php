<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Services;

/**
 * Parseia listas de repasse BR em blocos com preço em reais (ex.: MT Imports):
 *
 * 📱 IPHONE 18 PRO MAX • 256GB      ← cabeçalho (ícone de produto)
 * ⬛️Preto — R$ 9.200,00            ← cor + preço
 *
 * 💻 MACBOOK AIR M5 • 13”
 * 512GB / 16GB                      ← linha de especificação entra no nome
 * 🩶 Prata — R$ 8.200,00
 *
 * 🎧 AIRPODS 5 ANC
 * 💰 R$ 1.200,00                    ← preço sem cor vale para o próprio produto
 *
 * ⌚ APPLE WATCH SE 2
 * ⬛ Midnight • 40mm - R$ 1.200,00   ← detalhe depois do "•" entra na variante
 *
 * Nome gerado: "IPHONE 18 PRO MAX 256GB - PRETO".
 */
class BrBlockQuotationParser
{
    /** Ícones que abrem um bloco de produto. */
    private const HEADER_ICONS = ['📱', '💻', '🎧', '⌚', '🔊', '📲', '🖥', '⌨', '🖱', '🔋', '🎮'];

    private const PRODUCT_WORDS = '/^(?:IPHONE|IPAD|MACBOOK|MAC\b|AIRPODS?|APPLE\s*WATCH|JBL|CHARGE|FLIP|GO\s*\d|CLIP|BOOMBOX|XTREME|PULSE)/iu';

    /**
     * @return array<int, array{category: string, product_name: string, unit_price: float, available: bool}>
     */
    public function parse(string $rawText): array
    {
        $items = [];
        $header = null;

        foreach (preg_split('/\R/u', $rawText) as $rawLine) {
            $rawLine = trim($rawLine);
            $line = $this->clean($rawLine);

            if ($line === '' || preg_match('/^[\-—━_=•\s]+$/u', $line)) {
                continue;
            }

            [$text, $price] = $this->splitPrice($line);

            if ($price === null && $this->isHeader($rawLine, $text)) {
                $category = $this->category($text);
                $name = $this->normalizeName($text);
                $header = [
                    'name' => $category === 'JBL' && ! str_starts_with($name, 'JBL') ? 'JBL ' . $name : $name,
                    'category' => $category,
                ];
                continue;
            }

            if (! $header) {
                continue;
            }

            // "512GB / 16GB": especificação logo abaixo do cabeçalho
            if ($price === null) {
                if (preg_match('/^\d+\s*(?:GB|TB)\b/iu', $text)) {
                    $header['name'] .= ' ' . $this->normalizeName($text);
                }
                continue;
            }

            $variant = $this->normalizeName($text);
            $name = $variant === '' ? $header['name'] : $header['name'] . ' - ' . $variant;

            $items[] = [
                'category' => $header['category'],
                'product_name' => $name,
                'unit_price' => $price,
                'available' => true,
            ];
        }

        $unique = [];
        foreach ($items as $item) {
            $unique[$item['product_name']] = $item;
        }

        return array_values($unique);
    }

    private function isHeader(string $rawLine, string $text): bool
    {
        foreach (self::HEADER_ICONS as $icon) {
            if (str_starts_with($rawLine, $icon)) {
                return preg_match('/\pL/u', $text) === 1;
            }
        }

        return preg_match(self::PRODUCT_WORDS, $text) === 1;
    }

    /** @return array{0: string, 1: ?float} */
    private function splitPrice(string $line): array
    {
        if (! preg_match('/^(.*?)\s*[—–\-:=]?\s*R\$\s*([\d.]+(?:,\d{1,2})?)\s*$/u', $line, $m)) {
            return [$line, null];
        }

        $price = (float) str_replace(['.', ','], ['', '.'], $m[2]);

        return [trim($m[1], " \t—–-:=•"), $price >= 50 ? $price : null];
    }

    private function normalizeName(string $text): string
    {
        $text = str_replace(['•', '”', '″', 'Ô', 'ô'], [' ', '"', '"', 'O', 'o'], $text);
        $text = preg_replace('/(\d+)\s*gb\b/iu', '$1GB', $text);
        $text = preg_replace('/(\d+)\s*tb\b/iu', '$1TB', $text);
        $text = preg_replace('/(\d+)\s*mm\b/iu', '$1MM', $text);

        return mb_strtoupper(trim(preg_replace('/\s+/u', ' ', $text), " \t-—–/"));
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
            (bool) preg_match('/JBL|CHARGE|FLIP|\bGO\s*\d|CLIP|BOOMBOX|XTREME|PULSE/u', $upper) => 'JBL',
            default => 'OUTROS',
        };
    }

    private function clean(string $text): string
    {
        $text = str_replace('*', '', $text);
        $text = preg_replace('/[\x{1F000}-\x{1FFFF}\x{2300}-\x{23FF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{25A0}-\x{25FF}\x{FE00}-\x{FE0F}\x{200D}\x{20E3}\x{E0020}-\x{E007F}]/u', '', $text) ?? $text;

        return trim(preg_replace('/\s+/u', ' ', $text));
    }
}
