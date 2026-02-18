<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Services;

use App\Domain\Perfumes\Models\PerfumeProduct;
use Illuminate\Http\UploadedFile;
use Smalot\PdfParser\Parser;

class PerfumeImportService
{
    public function importFromPdf(UploadedFile $file): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getRealPath());
        $text = $pdf->getText();

        $lines = array_filter(
            array_map('trim', explode("\n", $text)),
            fn ($line) => strlen($line) > 3
        );

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($lines as $line) {
            $parsed = $this->parseLine($line);

            if (! $parsed) {
                $skipped++;
                continue;
            }

            $product = PerfumeProduct::where('name', $parsed['name'])
                ->where('brand', $parsed['brand'])
                ->where('size_ml', $parsed['size_ml'])
                ->first();

            if ($product) {
                $product->update($parsed);
                $updated++;
            } else {
                PerfumeProduct::create($parsed);
                $created++;
            }
        }

        return compact('created', 'updated', 'skipped');
    }

    /**
     * Tenta parsear uma linha do PDF em dados de produto.
     * Suporta formatos comuns: "Nome - Marca - 100ml - R$ 99,90"
     */
    protected function parseLine(string $line): ?array
    {
        // Ignora linhas de cabeçalho / separadores
        if (preg_match('/^(codigo|nome|produto|marca|---|\*\*|#)/i', $line)) {
            return null;
        }

        // Tenta extrair preço no padrão BR: R$ 99,90 ou 99.90
        $price = null;
        if (preg_match('/R?\$?\s*([\d.,]+)/', $line, $m)) {
            $price = (float) str_replace(['.', ','], ['', '.'], $m[1]);
        }

        // Tenta extrair ML
        $sizeMl = null;
        if (preg_match('/(\d+)\s*ml/i', $line, $m)) {
            $sizeMl = $m[1];
        }

        // Remove preço e ML da linha para pegar nome/marca
        $clean = preg_replace('/R?\$?\s*[\d.,]+/', '', $line);
        $clean = preg_replace('/\d+\s*ml/i', '', $clean);
        $clean = trim(preg_replace('/\s{2,}/', ' ', $clean));

        // Tenta separar nome e marca por delimitadores comuns
        $parts = preg_split('/\s*[-–|\/]\s*/', $clean, 2);
        $name = trim($parts[0] ?? '');
        $brand = trim($parts[1] ?? '');

        if (strlen($name) < 2) {
            return null;
        }

        // Detecta categoria pelo nome/marca
        $category = $this->detectCategory($name . ' ' . $brand);

        return [
            'name'       => $name,
            'brand'      => $brand ?: null,
            'size_ml'    => $sizeMl,
            'sale_price' => $price ?? 0,
            'cost_price' => 0,
            'category'   => $category,
            'active'     => true,
        ];
    }

    protected function detectCategory(string $text): string
    {
        $lower = mb_strtolower($text);

        $femWords = ['feminino', 'woman', 'femme', 'her', 'donna', 'girl', 'lady'];
        $mascWords = ['masculino', 'homme', 'man', 'him', 'pour homme', 'boy'];

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

        return 'unissex';
    }
}
