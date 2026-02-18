<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Services;

use App\Domain\AI\Services\GeminiService;
use App\Domain\Perfumes\Models\PerfumeProduct;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class PerfumeImportService
{
    private const LINES_PER_CHUNK = 80;

    public function __construct(
        private readonly GeminiService $gemini
    ) {}

    public function importFromPdf(UploadedFile $file): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getRealPath());
        $text = $this->sanitizeEncoding($pdf->getText());

        $products = $this->gemini->isAvailable()
            ? $this->parseWithAi($text)
            : $this->parseFallbackRegex($text);

        if (empty($products)) {
            Log::warning('PerfumeImportService: Nenhum produto extraído do PDF.');

            return ['created' => 0, 'updated' => 0, 'skipped' => 0];
        }

        return $this->upsertProducts($products);
    }

    // ----------------------------------------------------------------
    // Parsing via Gemini AI
    // ----------------------------------------------------------------

    private function parseWithAi(string $text): array
    {
        $chunks = $this->splitIntoChunks($text);
        $allProducts = [];

        foreach ($chunks as $chunk) {
            $result = $this->gemini->generateJson(
                $this->buildPrompt($chunk),
                $this->buildSystemInstruction(),
            );

            if (is_array($result)) {
                $items = $result['products'] ?? $result;
                $allProducts = array_merge($allProducts, $this->normalizeAiItems($items));
            }
        }

        return $allProducts;
    }

    private function splitIntoChunks(string $text): array
    {
        $lines = explode("\n", $text);

        if (count($lines) <= self::LINES_PER_CHUNK) {
            return [$text];
        }

        return array_map(
            fn (array $group) => implode("\n", $group),
            array_chunk($lines, self::LINES_PER_CHUNK)
        );
    }

    private function buildPrompt(string $text): string
    {
        return <<<PROMPT
Analise o texto abaixo extraído de um PDF de lista de preços de perfumes.
Extraia TODOS os produtos e retorne um array JSON.

FORMATO DE SAÍDA:
[{"name":"NOME","brand":"MARCA","barcode":"CÓDIGO DE BARRAS","size_ml":"ML","sale_price":99.99,"category":"masculino|feminino|unissex"}]

CAMPOS:
- name: Nome completo do perfume, sem código interno, sem pontos de preenchimento, em MAIÚSCULO
- brand: Marca principal extraída do nome (ex: LATTAFA, MAISON, AURORA SCENT, BANDERAS, ABERCROMBIE & FITCH, AXIS). Se incerto, deixe vazio ""
- barcode: Código de barras (sequência longa de dígitos, geralmente 10-13 dígitos, coluna "Referencia")
- size_ml: Apenas o número em ML do item principal (ex: "200" para 200ML). Se não identificável, null
- sale_price: Preço em US$ como número decimal (coluna "PrecioE US$")
- category: "masculino" para homem (indicadores: H, Homme, Man, Men, Him, King), "feminino" para mulher (indicadores: F, Femme, Woman, Women, Her, Lady, Queen), "unissex" quando não for possível identificar

REGRAS:
1. IGNORE cabeçalhos, separadores, dados da loja, linhas de página
2. O primeiro dígito colado ao nome (ex: "1MAISON", "2AURORA") é um prefixo de classificação - NÃO inclua no nome
3. NÃO invente dados que não estejam no texto
4. Retorne [] se nenhum produto for encontrado

TEXTO:
{$text}
PROMPT;
    }

    private function buildSystemInstruction(): string
    {
        return 'Você é um parser especializado em listas de preços de perfumes. '
            . 'Converta o texto do PDF em JSON puro. '
            . 'Retorne APENAS o array JSON, sem explicações, sem markdown. '
            . 'Seja preciso nos preços, nomes e códigos de barras. Não invente dados.';
    }

    private function normalizeAiItems(array $items): array
    {
        $valid = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $name = trim((string) ($item['name'] ?? ''));
            $barcode = trim((string) ($item['barcode'] ?? ''));

            if ($name === '' || $barcode === '') {
                continue;
            }

            $category = mb_strtolower(trim((string) ($item['category'] ?? 'unissex')));
            if (! in_array($category, ['masculino', 'feminino', 'unissex'])) {
                $category = 'unissex';
            }

            $sizeMl = $item['size_ml'] ?? null;
            if ($sizeMl !== null) {
                $sizeMl = preg_replace('/\D/', '', (string) $sizeMl);
                $sizeMl = $sizeMl !== '' ? $sizeMl : null;
            }

            $valid[] = [
                'name'       => mb_strtoupper($name),
                'brand'      => ($item['brand'] ?? '') !== '' ? mb_strtoupper(trim((string) $item['brand'])) : null,
                'barcode'    => $barcode,
                'size_ml'    => $sizeMl,
                'sale_price' => (float) ($item['sale_price'] ?? 0),
                'cost_price' => 0,
                'category'   => $category,
                'active'     => true,
            ];
        }

        return $valid;
    }

    // ----------------------------------------------------------------
    // Fallback: parsing por regex (quando Gemini indisponível)
    // ----------------------------------------------------------------

    private function parseFallbackRegex(string $text): array
    {
        Log::info('PerfumeImportService: Gemini indisponível, usando parser regex.');

        $lines = array_filter(
            array_map('trim', explode("\n", $text)),
            fn ($line) => mb_strlen($line) > 3
        );

        $products = [];

        foreach ($lines as $line) {
            if ($this->isHeaderLine($line)) {
                continue;
            }

            $parsed = $this->parseRegexLine($line);

            if ($parsed) {
                $products[] = $parsed;
            }
        }

        return $products;
    }

    private function isHeaderLine(string $line): bool
    {
        return (bool) preg_match('/^-{3,}$/', $line)
            || (bool) preg_match('/LOJA\s+\d/i', $line)
            || str_contains($line, 'Lista de Precios')
            || str_contains($line, 'CENTER SEIKO')
            || (bool) preg_match('/Nivel:/i', $line)
            || (bool) preg_match('/Usuario:/i', $line)
            || (bool) preg_match('/Codigo\s+Descripcion/i', $line)
            || (bool) preg_match('/Pagina:/i', $line)
            || (bool) preg_match('/Tipo\s*Iva/i', $line)
            || (bool) preg_match('/Estacion/i', $line)
            || (bool) preg_match('/^(codigo|nome|produto|marca|\*\*|#|pagina|total|subtotal)/i', $line);
    }

    private function parseRegexLine(string $line): ?array
    {
        if (! preg_match('/^\s*(\d+)\s+(.+?)(?:\.{2,}|\s{3,})(\d{4,})\s+([\d.,]+)\s+(\d{1,2})\s*$/', $line, $matches)) {
            return null;
        }

        $description = mb_substr(trim(rtrim($matches[2], '. ')), 0, 255);
        $barcode = $matches[3];
        $price = $this->parseBrazilianPrice($matches[4]);

        $sizeMl = null;
        if (preg_match('/(\d+)\s*ML/i', $description, $m)) {
            $sizeMl = $m[1];
        }

        return [
            'name'       => mb_strtoupper($description),
            'barcode'    => $barcode,
            'size_ml'    => $sizeMl,
            'sale_price' => $price,
            'cost_price' => 0,
            'category'   => 'unissex',
            'active'     => true,
        ];
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

    // ----------------------------------------------------------------
    // Upsert de produtos (usado por ambos os parsers)
    // ----------------------------------------------------------------

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
                : PerfumeProduct::where('name', $name)->first();

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

    // ----------------------------------------------------------------
    // Encoding
    // ----------------------------------------------------------------

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
        $text = preg_replace('/[^\x20-\x7E\xC0-\xFF\n\r\t]/u', '', $text);

        return $text;
    }
}
