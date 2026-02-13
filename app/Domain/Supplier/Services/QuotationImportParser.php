<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Services;

class QuotationImportParser
{
    /**
     * Parseia o texto bruto de cotação do fornecedor (formato WhatsApp).
     *
     * @return array<int, array{category: string, product_name: string, price_usd: float, quantity: int}>
     */
    public function parse(string $rawText): array
    {
        $lines = $this->normalizeLines($rawText);
        $items = [];
        $currentCategory = '';
        $currentProduct = '';

        foreach ($lines as $line) {
            $stripped = $this->stripEmojis($line);

            // Tenta detectar categoria (seções principais)
            $category = $this->detectCategory($stripped);
            if ($category !== null) {
                $currentCategory = $category;
                $currentProduct = '';
                continue;
            }

            // Tenta detectar cabeçalho de produto
            $product = $this->detectProduct($stripped);
            if ($product !== null) {
                $currentProduct = $product;
                continue;
            }

            // Tenta detectar variante (cor + preço)
            $variant = $this->detectVariant($stripped);
            if ($variant !== null && $currentProduct !== '') {
                $items[] = [
                    'category' => $currentCategory,
                    'product_name' => $currentProduct . ' - ' . $variant['color'],
                    'price_usd' => $variant['price'],
                    'quantity' => $variant['quantity'],
                ];
            }
        }

        return $items;
    }

    /**
     * Normaliza o texto em linhas limpas.
     */
    private function normalizeLines(string $text): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $text);

        return array_values(array_filter(
            array_map('trim', $lines),
            fn(string $line) => $line !== ''
        ));
    }

    /**
     * Remove emojis e caracteres Unicode decorativos.
     */
    private function stripEmojis(string $text): string
    {
        // Remove emojis, flags, símbolos decorativos e variation selectors
        $patterns = [
            '/[\x{1F000}-\x{1FFFF}]/u',   // Emoticons, símbolos, bandeiras
            '/[\x{2600}-\x{27BF}]/u',      // Misc symbols, dingbats
            '/[\x{FE00}-\x{FE0F}]/u',      // Variation selectors
            '/[\x{1F900}-\x{1F9FF}]/u',    // Supplemental symbols
            '/[\x{200D}]/u',               // Zero width joiner
            '/[\x{20E3}]/u',               // Combining enclosing keycap
            '/[\x{E0020}-\x{E007F}]/u',    // Tags
            '/[\x{1F1E0}-\x{1F1FF}]/u',    // Regional indicators (flags)
            '/[\x{00A9}\x{00AE}]/u',       // Copyright, registered
            '/[\x{203C}\x{2049}]/u',       // Exclamation marks
            '/[\x{2122}\x{2139}]/u',       // TM, info
            '/[\x{2194}-\x{21AA}]/u',      // Arrows
            '/[\x{231A}-\x{23FA}]/u',      // Misc technical
            '/[\x{25AA}-\x{25FE}]/u',      // Geometric shapes
            '/[\x{2934}-\x{2935}]/u',      // Arrows
            '/[\x{3030}\x{303D}]/u',       // Wavy dash, ideographic
            '/[\x{3297}\x{3299}]/u',       // CJK
            '/[\x{FE00}-\x{FEFF}]/u',      // Variation/format selectors
        ];

        $result = $text;
        foreach ($patterns as $pattern) {
            $result = preg_replace($pattern, '', $result) ?? $result;
        }

        return $result;
    }

    /**
     * Detecta se a linha é uma categoria (seção principal).
     */
    private function detectCategory(string $line): ?string
    {
        $clean = trim($line);
        if ($clean === '') {
            return null;
        }

        // Remove asteriscos para análise
        $inner = trim(str_replace('*', '', $clean));
        $inner = preg_replace('/\s+/u', ' ', $inner);

        // Se contém indicadores de produto (GB, TB, MM com número), NÃO é categoria
        if (preg_match('/\d+\s*(?:GB|TB|MM)\b/iu', $inner)) {
            return null;
        }

        // Se contém preço ($), NÃO é categoria
        if (str_contains($inner, '$')) {
            return null;
        }

        // Padrões específicos de categoria
        $categoryPatterns = [
            '/IPHONE\s+LACRADO/iu',
            '/IPHONE\s+SWAP/iu',
            '/IPHONE\s+CPO/iu',
            '/APPLE\s+ACCE?S+ORI?ES?/iu',
            '/L[IÍ]NEA?\s+\d+/iu',
            '/ASIS\s+PLUS/iu',
        ];

        foreach ($categoryPatterns as $pattern) {
            if (preg_match($pattern, $inner)) {
                return mb_strtoupper($inner);
            }
        }

        return null;
    }

    /**
     * Detecta se a linha é um cabeçalho de produto.
     */
    private function detectProduct(string $line): ?string
    {
        $clean = trim($line);

        // Remove textos extras como "Pcas PCs disponíveil"
        $clean = preg_replace('/\b(?:Pcas|PCs|pcs|disponíve[il]|disponivel)\b.*/iu', '', $clean);
        $clean = trim($clean);

        // Se contém preço ($), não é cabeçalho de produto
        if (preg_match('/\$\d/u', $clean)) {
            return null;
        }

        // Produto: geralmente envolvido em asteriscos, ou começa com IPHONE/APPLE/PHONE
        // Remove asteriscos e checa se parece produto
        $inner = trim(str_replace('*', '', $clean));
        $inner = preg_replace('/\s+/u', ' ', $inner);

        if ($inner !== '' && $this->looksLikeProductHeader($inner)) {
            return $this->normalizeProductName($inner);
        }

        return null;
    }

    /**
     * Verifica se o texto parece um cabeçalho de produto.
     */
    private function looksLikeProductHeader(string $text): bool
    {
        // Deve conter tamanho: nGB, nTB, nMM (obrigatório para produtos)
        if (preg_match('/\d+\s*(?:GB|TB|MM)\b/iu', $text)) {
            return true;
        }

        // Acessórios especiais sem tamanho
        if (preg_match('/AIR\s*TAG|CABO.*(?:C-C|USB|LIGHTNING)/iu', $text)) {
            return true;
        }

        return false;
    }

    /**
     * Normaliza o nome do produto.
     */
    private function normalizeProductName(string $name): string
    {
        $name = str_replace('*', '', $name);
        $name = preg_replace('/\s+/u', ' ', $name);

        return mb_strtoupper(trim($name));
    }

    /**
     * Detecta se a linha é uma variante de produto (cor + preço).
     */
    private function detectVariant(string $line): ?array
    {
        $clean = trim($line);

        // Remove textos extras
        $clean = preg_replace('/\b(?:Pcas|PCs|pcs|disponíve[il]|disponivel)\b.*/iu', '', $clean);
        $clean = trim($clean);

        if ($clean === '') {
            return null;
        }

        // Classe de caracteres para nomes de cores (inclui acentuados)
        $colorChar = '[A-ZÀ-ÿ]';
        $colorGroup = "({$colorChar}[{$colorChar[1]}-{$colorChar[strlen($colorChar)-2]}\\s]*?)";

        // Padrão 1: COR *$PREÇO* [Npc]
        // $ é opcional, asteriscos opcionais
        // Ex: "BLACK *$280*", "BLACK $565", "BLACK *680*", "GREEN *$605* 1pc", "BLACK TITÂNIO *$650*"
        if (preg_match('/^([A-ZÀ-ÿ][A-ZÀ-ÿ\s]*?)\s+\*?\$?(\d+(?:\.\d{1,2})?)\*?\s*(?:(\d+)\s*pc)?$/iu', $clean, $m)) {
            $color = mb_strtoupper(trim($m[1]));
            $price = (float) $m[2];
            $qty = isset($m[3]) && $m[3] !== '' ? (int) $m[3] : 1;

            if ($this->isValidColorPrice($color, $price)) {
                return ['color' => $color, 'price' => $price, 'quantity' => $qty];
            }
        }

        // Padrão 2: COR SKU/MODELO *$PREÇO* [Npc]
        // Ex: "BLACK MEQT4LW/SM *$355*", "GRAY MEQW4LW/SM *$375*"
        if (preg_match('/^([A-ZÀ-ÿ][A-ZÀ-ÿ\s]*?)\s+([A-Z0-9]+(?:\/[A-Z0-9]+)?)\s+\*?\$?(\d+(?:\.\d{1,2})?)\*?\s*(?:(\d+)\s*pc)?$/iu', $clean, $m)) {
            $color = mb_strtoupper(trim($m[1]));
            $sku = trim($m[2]);
            $price = (float) $m[3];
            $qty = isset($m[4]) && $m[4] !== '' ? (int) $m[4] : 1;

            if ($price > 0 && !preg_match('/\d+\s*(?:GB|TB|MM)/iu', $color)) {
                return ['color' => $color . ' ' . $sku, 'price' => $price, 'quantity' => $qty];
            }
        }

        // Padrão 3: SKU COR *$PREÇO* [Npc]
        // Ex: "MD4A4LL BLUE *$365*"
        if (preg_match('/^([A-Z0-9]+)\s+([A-ZÀ-ÿ]+)\s+\*?\$?(\d+(?:\.\d{1,2})?)\*?\s*(?:(\d+)\s*pc)?$/iu', $clean, $m)) {
            $sku = trim($m[1]);
            $color = mb_strtoupper(trim($m[2]));
            $price = (float) $m[3];
            $qty = isset($m[4]) && $m[4] !== '' ? (int) $m[4] : 1;

            if ($price > 0 && preg_match('/\d/', $sku)) {
                return ['color' => $sku . ' ' . $color, 'price' => $price, 'quantity' => $qty];
            }
        }

        // Padrão 4: Descrição mista com preço
        // Ex: "4 PACK A2187  *$75*", "1METRO C-C 60W MW493ZM/A *$14*"
        if (preg_match('/^(.+?)\s+\*?\$?(\d+(?:\.\d{1,2})?)\*?\s*(?:(\d+)\s*pc)?$/iu', $clean, $m)) {
            $desc = mb_strtoupper(trim($m[1]));
            $price = (float) $m[2];
            $qty = isset($m[3]) && $m[3] !== '' ? (int) $m[3] : 1;

            if ($price > 0 && preg_match('/^\d/', $desc) && !preg_match('/\d+\s*(?:GB|TB|MM)/iu', $desc)) {
                return ['color' => $desc, 'price' => $price, 'quantity' => $qty];
            }
        }

        return null;
    }

    /**
     * Valida se a cor e preço são válidos para uma variante.
     */
    private function isValidColorPrice(string $color, float $price): bool
    {
        if ($price <= 0) {
            return false;
        }

        // Se contém indicadores de produto, não é variante
        if (preg_match('/\d+\s*(?:GB|TB|MM)/iu', $color)) {
            return false;
        }

        // Cor não deve ser muito longa
        if (mb_strlen($color) > 30) {
            return false;
        }

        return true;
    }
}
