<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Services;

use App\Domain\Perfumes\Models\PerfumeProduct;
use Illuminate\Http\UploadedFile;
use Smalot\PdfParser\Parser;

class PerfumeImportService
{
    /**
     * Extrai texto do PDF e parseia produtos via regex.
     *
     * @return array{created: int, updated: int, skipped: int}
     */
    public function importFromPdf(UploadedFile $file): array
    {
        $text = $this->extractText($file);
        $items = $this->parse($text);

        return $this->upsertProducts($items);
    }

    /**
     * Parseia o texto bruto em array de produtos.
     *
     * @return array<int, array{name: string, brand: ?string, barcode: ?string, size_ml: ?string, sale_price: float, cost_price: float, category: string, active: bool}>
     */
    public function parse(string $text): array
    {
        $lines = array_filter(
            array_map('trim', explode("\n", $text)),
            fn ($line) => mb_strlen($line) > 3
        );

        $isSeikoFormat = str_contains($text, 'SEIKO') || str_contains($text, 'Lista de Precios');

        $products = [];

        foreach ($lines as $line) {
            if ($this->isHeaderLine($line)) {
                continue;
            }

            $parsed = $this->parseSeikoLine($line);

            if (! $parsed && ! $isSeikoFormat) {
                $parsed = $this->parseGenericLine($line);
            }

            if ($parsed) {
                $products[] = $parsed;
            }
        }

        return $products;
    }

    /**
     * Extrai e sanitiza o texto de um PDF.
     */
    public function extractText(UploadedFile $file): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getRealPath());

        return $this->sanitizeEncoding($pdf->getText());
    }

    // ----------------------------------------------------------------
    // Formato Seiko Center (tabular)
    // Usa o barcode (10-14 dígitos) como âncora em vez de dots, pois
    // algumas linhas não têm pontos de preenchimento.
    // ----------------------------------------------------------------

    private function parseSeikoLine(string $line): ?array
    {
        if (! preg_match('/^\s*(\d{1,6})\s+(.+?)\s+(\d{10,14})[A-Z]?\s+([\d.,]+)\s+(\d{1,2})\s*$/', $line, $matches)) {
            return null;
        }

        $description = trim(rtrim($matches[2], '.* '));

        $description = preg_replace('/^\d+/', '', $description);
        $description = trim($description);

        if ($description === '') {
            return null;
        }

        $barcode = $matches[3];
        $price = $this->parseBrazilianPrice($matches[4]);

        $sizeMl = null;
        if (preg_match('/(\d+)\s*ML/i', $description, $m)) {
            $sizeMl = $m[1];
        }

        $brand = null;
        if (preg_match('/^([A-Z][A-Z&.\'\s]+?)\s{1,2}[A-Z]/', $description, $bm)) {
            $candidate = trim(rtrim($bm[1], ' .'));
            if (mb_strlen($candidate) >= 2 && mb_strlen($candidate) <= 50) {
                $brand = $candidate;
            }
        }

        return [
            'name'       => $description,
            'brand'      => $brand,
            'barcode'    => $barcode,
            'size_ml'    => $sizeMl,
            'sale_price' => $price,
            'cost_price' => 0,
            'category'   => $this->detectCategory($description),
            'active'     => true,
        ];
    }

    // ----------------------------------------------------------------
    // Formato genérico (linhas livres)
    // ----------------------------------------------------------------

    private function parseGenericLine(string $line): ?array
    {
        if (preg_match('/^(codigo|nome|produto|marca|---|\*\*|#|pagina|total|subtotal)/i', $line)) {
            return null;
        }

        $price = null;
        if (preg_match('/R\$\s*([\d.,]+)/', $line, $m)) {
            $price = $this->parseBrazilianPrice($m[1]);
        } elseif (preg_match('/(?:US?\$|USD)\s*([\d.,]+)/', $line, $m)) {
            $price = $this->parseBrazilianPrice($m[1]);
        } elseif (preg_match('/(?<!\d)([\d]{1,3}(?:\.\d{3})*,\d{2})(?!\d)/', $line, $m)) {
            $price = $this->parseBrazilianPrice($m[1]);
        }

        $sizeMl = null;
        if (preg_match('/(\d+)\s*ml/i', $line, $m)) {
            $sizeMl = $m[1];
        }

        $clean = preg_replace('/(?:R\$|US?\$|USD)\s*[\d.,]+/', '', $line);
        $clean = preg_replace('/(?<!\w)[\d]{1,3}(?:\.\d{3})*,\d{2}(?!\w)/', '', $clean);
        $clean = preg_replace('/\d+\s*ml/i', '', $clean);
        $clean = trim(preg_replace('/\s{2,}/', ' ', $clean));

        $parts = preg_split('/\s*[-\x{2013}|\/]\s*/u', $clean, 2);
        $name = mb_substr(trim($parts[0] ?? ''), 0, 255);
        $brand = mb_substr(trim($parts[1] ?? ''), 0, 255);

        if (mb_strlen($name) < 2) {
            return null;
        }

        return [
            'name'       => $name,
            'brand'      => $brand ?: null,
            'barcode'    => null,
            'size_ml'    => $sizeMl,
            'sale_price' => $price ?? 0,
            'cost_price' => 0,
            'category'   => $this->detectCategory($name . ' ' . $brand),
            'active'     => true,
        ];
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function isHeaderLine(string $line): bool
    {
        return (bool) preg_match('/^-{2,}/', $line)
            || (bool) preg_match('/^--\s*\d+\s+of\s+\d+\s*--$/i', $line)
            || str_contains($line, '/home/')
            || str_contains($line, 'perfumes.pdf')
            || (bool) preg_match('/LOJA\s+\d/i', $line)
            || str_contains($line, 'Lista de Precios')
            || str_contains($line, 'CENTER SEIKO')
            || (bool) preg_match('/Nivel:/i', $line)
            || (bool) preg_match('/Usuario:/i', $line)
            || (bool) preg_match('/Codigo\s+Descripcion/i', $line)
            || (bool) preg_match('/Pagina:/i', $line)
            || (bool) preg_match('/Tipo\s*Iva/i', $line)
            || (bool) preg_match('/Estacion/i', $line);
    }

    private function detectCategory(string $text): string
    {
        $lower = mb_strtolower($text);

        $femWords = ['feminino', 'woman', 'women', 'femme', 'her', 'donna', 'girl', 'lady'];
        $mascWords = ['masculino', 'homme', 'man', 'men', 'him', 'pour homme', 'boy'];

        foreach ($femWords as $w) {
            if (str_contains($lower, $w)) {
                return 'feminino';
            }
        }

        foreach ($mascWords as $w) {
            if (str_contains($lower, $w)) {
                return 'masculino';
            }
        }

        if (preg_match('/\bH\s+(EDT|EDP|EDC|V\d|SPRAY|SPARY)/i', $text)
            || preg_match('/\b(EDT|EDP|EDC|DEO)\s+H\b/i', $text)) {
            return 'masculino';
        }

        if (preg_match('/\bF\s+(EDT|EDP|EDC|V\d|SPRAY|SPARY)/i', $text)
            || preg_match('/\b(EDT|EDP|EDC|DEO)\s+F\b/i', $text)) {
            return 'feminino';
        }

        return 'unissex';
    }

    private function parseBrazilianPrice(string $raw): float
    {
        $raw = trim($raw);

        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d{2})?$/', $raw)) {
            return (float) str_replace(['.', ','], ['', '.'], $raw);
        }

        if (preg_match('/^\d+,\d{2}$/', $raw)) {
            return (float) str_replace(',', '.', $raw);
        }

        if (preg_match('/^\d+\.\d{2}$/', $raw)) {
            return (float) $raw;
        }

        return (float) str_replace(['.', ','], ['', '.'], $raw);
    }

    /**
     * Insere ou atualiza produtos no banco.
     */
    private function upsertProducts(array $products): array
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($products as $data) {
            $barcode = $data['barcode'] ?? null;
            $name = $data['name'] ?? '';

            if (! $barcode && ! $name) {
                $skipped++;

                continue;
            }

            $existing = $barcode
                ? PerfumeProduct::where('barcode', $barcode)->first()
                : PerfumeProduct::where('name', $name)
                    ->where('brand', $data['brand'] ?? null)
                    ->where('size_ml', $data['size_ml'] ?? null)
                    ->first();

            if ($existing) {
                $existing->update($data);
                $updated++;
            } else {
                PerfumeProduct::create($data);
                $created++;
            }
        }

        return compact('created', 'updated', 'skipped');
    }

    private function sanitizeEncoding(string $text): string
    {
        $encoding = mb_detect_encoding($text, ['UTF-8', 'Windows-1252', 'ISO-8859-1', 'ASCII'], true);

        if ($encoding && $encoding !== 'UTF-8') {
            $text = mb_convert_encoding($text, 'UTF-8', $encoding);
        }

        if (! mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
        }

        $replacements = [
            "\xC2\x91" => "'", "\xC2\x92" => "'",
            "\xC2\x93" => '"', "\xC2\x94" => '"',
            "\xC2\x96" => '-', "\xC2\x97" => '-',
            "\xE2\x80\x98" => "'", "\xE2\x80\x99" => "'",
            "\xE2\x80\x9C" => '"', "\xE2\x80\x9D" => '"',
            "\xE2\x80\x93" => '-', "\xE2\x80\x94" => '-',
            "\xE2\x80\xA6" => '...',
        ];

        $text = str_replace(array_keys($replacements), array_values($replacements), $text);

        // NBSP (U+00A0) → espaço regular — PDFs frequentemente usam NBSP em vez de space
        $text = str_replace("\xC2\xA0", ' ', $text);

        $text = preg_replace('/[^\x20-\x7E\xC0-\xFF\n\r\t]/u', '', $text);

        return $text;
    }
}
