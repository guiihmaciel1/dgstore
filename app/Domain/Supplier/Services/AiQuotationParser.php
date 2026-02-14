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
        // Limpa o texto antes de enviar (remove emojis e caracteres problemáticos)
        $cleanText = $this->cleanRawText($rawText);

        return <<<PROMPT
Converta a lista de cotação abaixo em JSON. O texto vem de WhatsApp de um fornecedor de produtos Apple.

FORMATO DE SAÍDA (array JSON, sem markdown):
[{"category":"CATEGORIA","product_name":"NOME COMPLETO","price_usd":999.99,"quantity":1}]

CAMPOS:
- category: seção/grupo do texto (ex: "IPHONE LACRADO", "IPHONE SWAP", "APPLE ACCESSORIES", "IPAD", "MACBOOK", "APPLE WATCH"). Se não houver seção, use "GERAL"
- product_name: modelo + capacidade + cor/variante em MAIÚSCULO (ex: "16 PRO 256GB - BLACK TITANIUM")
- price_usd: preço em USD como número (sem $)
- quantity: quantidade (número inteiro, padrão 1)

REGRAS IMPORTANTES:
1. Cada variante (cor) é um item SEPARADO
2. product_name = cabeçalho do produto + " - " + variante (ex: header "15 128GB IN" + cor "BLACK" = "15 128GB IN - BLACK")
3. Formatos de preço aceitos: *$610*, $610, US$610, 610 (quando claramente USD)
4. Formatos de quantidade: 1pc, 2pcs, x3, ou padrão 1
5. IGNORE preços em BRL (R$)
6. IGNORE itens sem preço USD
7. Tudo em MAIÚSCULO
8. NÃO invente dados que não estão no texto

TEXTO:
{$cleanText}
PROMPT;
    }

    /**
     * Instrução de sistema para o Gemini.
     */
    private function buildSystemInstruction(): string
    {
        return 'Você é um parser de cotações de produtos Apple. '
            . 'Converta texto de WhatsApp em JSON puro. '
            . 'Retorne APENAS o array JSON, sem explicações, sem markdown. '
            . 'Se não encontrar itens, retorne []. '
            . 'Seja preciso nos preços e nomes. Não invente dados.';
    }

    /**
     * Limpa o texto bruto antes de enviar ao Gemini.
     * Remove emojis, caracteres especiais e normaliza espaçamento.
     */
    private function cleanRawText(string $text): string
    {
        // Remove emojis e caracteres Unicode decorativos
        $patterns = [
            '/[\x{1F000}-\x{1FFFF}]/u',
            '/[\x{2600}-\x{27BF}]/u',
            '/[\x{FE00}-\x{FE0F}]/u',
            '/[\x{1F900}-\x{1F9FF}]/u',
            '/[\x{200D}]/u',
            '/[\x{20E3}]/u',
            '/[\x{E0020}-\x{E007F}]/u',
            '/[\x{1F1E0}-\x{1F1FF}]/u',
            '/[\x{200B}-\x{200F}]/u',
            '/[\x{2028}-\x{202F}]/u',
            '/[\x{2060}]/u',
            '/[\x{FEFF}]/u',
        ];

        $result = $text;
        foreach ($patterns as $pattern) {
            $result = preg_replace($pattern, '', $result) ?? $result;
        }

        // Normaliza quebras de linha
        $result = str_replace(["\r\n", "\r"], "\n", $result);

        // Remove linhas vazias consecutivas
        $result = preg_replace('/\n{3,}/', "\n\n", $result) ?? $result;

        return trim($result);
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
