<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Services;

use App\Domain\AI\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class AiQuotationParser
{
    public function __construct(
        private readonly GeminiService $gemini
    ) {}

    /**
     * Verifica se o parser com IA está disponível.
     */
    public function isAvailable(): bool
    {
        return $this->gemini->isAvailable();
    }

    /**
     * Parseia texto de cotação usando Gemini AI.
     *
     * Retorna o mesmo formato do QuotationImportParser:
     * array<int, array{category: string, product_name: string, price_usd: float, quantity: int}>
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
            Log::warning('AiQuotationParser: Gemini retornou null.');

            return [];
        }

        return $this->validateAndNormalize($result);
    }

    /**
     * Monta o prompt com o texto do fornecedor.
     */
    private function buildPrompt(string $rawText): string
    {
        return <<<PROMPT
Analise o texto abaixo que é uma lista de cotação de um fornecedor de produtos Apple (iPhones, iPads, MacBooks, Apple Watch, AirPods, acessórios).

Extraia TODOS os itens com seus dados. Cada item deve conter:
- category: categoria do produto (ex: "IPHONE LACRADO", "IPHONE SWAP", "APPLE ACCESSORIES", "IPAD", "MACBOOK", "APPLE WATCH", etc.)
- product_name: nome completo do produto incluindo modelo, capacidade, cor/variante (ex: "15 PRO MAX 256GB - BLACK TITANIUM")
- price_usd: preço em dólares americanos (número decimal, sem símbolo $)
- quantity: quantidade disponível (número inteiro, se não informado use 1)

Regras:
1. Se o texto tem seções/categorias (como "IPHONE LACRADO", "IPHONE SWAP"), use como category
2. Se um produto tem múltiplas variantes (cores), crie um item separado para CADA variante
3. O product_name deve incluir o cabeçalho do produto + a variante (ex: "16 PRO 256GB - DESERT TITANIUM")
4. Preços podem estar em formatos como: *$280*, $280, 280, US$ 280
5. Quantidades podem estar como: 1pc, 2 pcs, x3, ou implícita (1)
6. Se o texto contém preços em BRL (R$), IGNORE-os — extraia apenas USD
7. Se não conseguir identificar o preço em USD, IGNORE o item

Retorne um array JSON com os itens extraídos. Exemplo:
[
  {"category": "IPHONE LACRADO", "product_name": "16 PRO 256GB - DESERT TITANIUM", "price_usd": 650, "quantity": 1},
  {"category": "IPHONE LACRADO", "product_name": "16 PRO 256GB - BLACK TITANIUM", "price_usd": 655, "quantity": 2}
]

Se nenhum item for encontrado, retorne um array vazio: []

TEXTO DO FORNECEDOR:
---
{$rawText}
---
PROMPT;
    }

    /**
     * Instrução de sistema para o Gemini.
     */
    private function buildSystemInstruction(): string
    {
        return 'Você é um parser especializado em extrair dados estruturados de cotações de fornecedores de produtos Apple. '
            . 'Seu trabalho é transformar texto não estruturado (WhatsApp, e-mail, planilha colada) em dados JSON limpos. '
            . 'Seja preciso nos preços e nomes. Não invente dados que não estão no texto.';
    }

    /**
     * Valida e normaliza os itens retornados pela IA.
     */
    private function validateAndNormalize(array $items): array
    {
        // Se a IA retornou um objeto com chave "items", extraia o array
        if (isset($items['items']) && is_array($items['items'])) {
            $items = $items['items'];
        }

        $valid = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $productName = trim((string) ($item['product_name'] ?? ''));
            $priceUsd = (float) ($item['price_usd'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 1);
            $category = trim((string) ($item['category'] ?? ''));

            // Validações básicas
            if ($productName === '' || $priceUsd <= 0) {
                continue;
            }

            if ($quantity < 1) {
                $quantity = 1;
            }

            $valid[] = [
                'category' => mb_strtoupper($category),
                'product_name' => mb_strtoupper($productName),
                'price_usd' => $priceUsd,
                'quantity' => $quantity,
            ];
        }

        return $valid;
    }
}
