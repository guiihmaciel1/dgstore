<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Services;

use App\Domain\AI\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class AiPerfumeImportParser
{
    public function __construct(
        private readonly GeminiService $gemini
    ) {}

    public function isAvailable(): bool
    {
        return $this->gemini->isAvailable();
    }

    /**
     * Parseia texto de lista de preços de perfumes usando Gemini AI.
     *
     * Retorna o mesmo formato do PerfumeImportService::parse():
     * array<int, array{name: string, brand: ?string, barcode: ?string, size_ml: ?string, sale_price: float, cost_price: float, category: string, active: bool}>
     */
    public function parse(string $rawText): array
    {
        if (! $this->isAvailable()) {
            return [];
        }

        $prompt = $this->buildPrompt($rawText);
        $systemInstruction = $this->buildSystemInstruction();

        $result = $this->gemini->generateJson($prompt, $systemInstruction);

        if ($result === null) {
            Log::warning('AiPerfumeImportParser: Gemini retornou null.');

            return [];
        }

        return $this->validateAndNormalize($result);
    }

    private function buildPrompt(string $rawText): string
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
{$rawText}
PROMPT;
    }

    private function buildSystemInstruction(): string
    {
        return 'Você é um parser especializado em listas de preços de perfumes. '
            . 'Converta o texto do PDF em JSON puro. '
            . 'Retorne APENAS o array JSON, sem explicações, sem markdown. '
            . 'Seja preciso nos preços, nomes e códigos de barras. Não invente dados.';
    }

    private function validateAndNormalize(array $items): array
    {
        if (isset($items['products']) && is_array($items['products'])) {
            $items = $items['products'];
        }

        $valid = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $name = trim((string) ($item['name'] ?? ''));
            $barcode = trim((string) ($item['barcode'] ?? ''));

            if ($name === '') {
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

            $brand = trim((string) ($item['brand'] ?? ''));

            $valid[] = [
                'name'       => mb_strtoupper($name),
                'brand'      => $brand !== '' ? mb_strtoupper($brand) : null,
                'barcode'    => $barcode !== '' ? $barcode : null,
                'size_ml'    => $sizeMl,
                'sale_price' => (float) ($item['sale_price'] ?? 0),
                'cost_price' => 0,
                'category'   => $category,
                'active'     => true,
            ];
        }

        return $valid;
    }
}
