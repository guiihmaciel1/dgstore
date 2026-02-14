<?php

declare(strict_types=1);

namespace App\Domain\AI\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;

    private string $model;

    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    /**
     * Máximo de requests por minuto (margem sobre o limite de 15 RPM do free tier).
     */
    private int $maxRequestsPerMinute = 12;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key');
        $this->model = (string) config('services.gemini.model', 'gemini-2.0-flash');
    }

    /**
     * Verifica se o serviço está configurado e disponível.
     */
    public function isAvailable(): bool
    {
        return $this->apiKey !== '';
    }

    /**
     * Gera conteúdo textual via Gemini.
     */
    public function generateContent(string $prompt, ?string $systemInstruction = null): ?string
    {
        if (! $this->isAvailable()) {
            Log::warning('GeminiService: API key não configurada.');

            return null;
        }

        if (! $this->checkRateLimit()) {
            Log::warning('GeminiService: Rate limit atingido.');

            return null;
        }

        $body = $this->buildRequestBody($prompt, $systemInstruction);

        $response = $this->sendRequest($body);

        if ($response === null) {
            return null;
        }

        return $this->extractText($response);
    }

    /**
     * Gera conteúdo e parseia como JSON.
     * Envia instrução para retornar apenas JSON válido.
     */
    public function generateJson(string $prompt, ?string $systemInstruction = null): ?array
    {
        $jsonInstruction = ($systemInstruction ? $systemInstruction . "\n\n" : '')
            . 'IMPORTANTE: Retorne APENAS um JSON válido, sem markdown, sem ```json, sem explicações. Apenas o JSON puro.';

        $text = $this->generateContent($prompt, $jsonInstruction);

        if ($text === null) {
            return null;
        }

        return $this->parseJsonResponse($text);
    }

    /**
     * Monta o body do request para a API Gemini.
     */
    private function buildRequestBody(string $prompt, ?string $systemInstruction = null): array
    {
        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 16384,
                // Desabilita "thinking" do gemini-2.5+ para economia de tokens e menor latência
                'thinkingConfig' => [
                    'thinkingBudget' => 0,
                ],
            ],
        ];

        if ($systemInstruction) {
            $body['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction],
                ],
            ];
        }

        return $body;
    }

    /**
     * Envia request para a API Gemini com retry inteligente.
     */
    private function sendRequest(array $body): ?array
    {
        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";
        $maxAttempts = 2;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::timeout(60)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $body);

                if ($response->successful()) {
                    $this->incrementRateLimit();

                    return $response->json();
                }

                $status = $response->status();
                $errorBody = $response->body();

                Log::warning("GeminiService: Erro HTTP {$status} (tentativa {$attempt}/{$maxAttempts})", [
                    'status' => $status,
                    'body' => mb_substr($errorBody, 0, 500),
                ]);

                // Rate limit (429) ou erro de servidor (5xx): retry com backoff
                if ($attempt < $maxAttempts && ($status === 429 || $status >= 500)) {
                    $waitSeconds = min($this->extractRetryDelay($errorBody), 30);
                    Log::info("GeminiService: Aguardando {$waitSeconds}s antes do retry...");
                    sleep($waitSeconds);

                    continue;
                }

                return null;
            } catch (\Exception $e) {
                Log::error("GeminiService: Exceção na tentativa {$attempt}/{$maxAttempts}", [
                    'message' => $e->getMessage(),
                ]);

                if ($attempt < $maxAttempts) {
                    sleep(2);

                    continue;
                }

                return null;
            }
        }

        return null;
    }

    /**
     * Extrai o tempo de espera sugerido da resposta 429 do Gemini.
     */
    private function extractRetryDelay(string $errorBody): int
    {
        // Tenta extrair "Please retry in Xs" da mensagem
        if (preg_match('/retry in (\d+(?:\.\d+)?)s/i', $errorBody, $matches)) {
            return (int) ceil((float) $matches[1]);
        }

        return 5; // Fallback: espera 5 segundos
    }

    /**
     * Extrai o texto da resposta da API.
     * Gemini 2.5+ pode retornar "thought" parts antes do texto real.
     * Percorre todas as parts e retorna o texto da primeira part que NÃO é thought.
     */
    private function extractText(array $response): ?string
    {
        $parts = $response['candidates'][0]['content']['parts'] ?? [];

        // Percorre parts: pula "thought" e pega o primeiro texto real
        foreach ($parts as $part) {
            $isThought = isset($part['thought']) && $part['thought'] === true;

            if (! $isThought && isset($part['text'])) {
                return $part['text'];
            }
        }

        // Fallback: tenta qualquer part com texto (caso a flag thought não exista)
        foreach ($parts as $part) {
            if (isset($part['text']) && $part['text'] !== '') {
                return $part['text'];
            }
        }

        Log::warning('GeminiService: Resposta sem texto.', [
            'parts_count' => count($parts),
            'response_keys' => array_keys($response),
        ]);

        return null;
    }

    /**
     * Parseia texto como JSON, removendo possíveis wrappers markdown e caracteres inválidos.
     */
    private function parseJsonResponse(string $text): ?array
    {
        $cleaned = $this->sanitizeJsonText($text);

        // Tentativa 1: JSON direto
        $decoded = json_decode($cleaned, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Tentativa 2: Extrair JSON entre [ ] ou { }
        if (preg_match('/(\[[\s\S]*\])\s*$/', $cleaned, $matches)) {
            $decoded = json_decode($matches[1], true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        if (preg_match('/(\{[\s\S]*\})\s*$/', $cleaned, $matches)) {
            $decoded = json_decode($matches[1], true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        Log::warning('GeminiService: Falha ao parsear JSON da resposta.', [
            'error' => json_last_error_msg(),
            'text' => mb_substr($text, 0, 500),
        ]);

        return null;
    }

    /**
     * Sanitiza o texto para parsing JSON seguro.
     * Remove wrappers markdown, caracteres de controle e BOM.
     */
    private function sanitizeJsonText(string $text): string
    {
        $cleaned = trim($text);

        // Remove BOM (Byte Order Mark)
        $cleaned = preg_replace('/^\x{FEFF}/u', '', $cleaned) ?? $cleaned;

        // Remove wrapper ```json ... ``` se presente
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/\s*```\s*$/', '', $cleaned);
        $cleaned = trim($cleaned);

        // Remove caracteres de controle (exceto \n, \r, \t que são válidos em JSON strings)
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $cleaned) ?? $cleaned;

        // Remove caracteres Unicode invisíveis problemáticos
        $cleaned = preg_replace('/[\x{200B}-\x{200F}\x{2028}-\x{202F}\x{2060}\x{FEFF}]/u', '', $cleaned) ?? $cleaned;

        return $cleaned;
    }

    /**
     * Verifica se o rate limit não foi atingido.
     */
    private function checkRateLimit(): bool
    {
        $key = 'gemini_rate_limit:' . now()->format('Y-m-d-H-i');
        $count = (int) Cache::get($key, 0);

        return $count < $this->maxRequestsPerMinute;
    }

    /**
     * Incrementa o contador de rate limit.
     */
    private function incrementRateLimit(): void
    {
        $key = 'gemini_rate_limit:' . now()->format('Y-m-d-H-i');
        $count = (int) Cache::get($key, 0);
        Cache::put($key, $count + 1, 120);
    }
}
