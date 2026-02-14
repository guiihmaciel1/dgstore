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
                'temperature' => 0.2,
                'maxOutputTokens' => 4096,
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
     * Envia request para a API Gemini com retry.
     */
    private function sendRequest(array $body): ?array
    {
        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";
        $maxAttempts = 2;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::timeout(30)
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

                // Rate limit (429) ou erro de servidor (5xx): retry
                if ($attempt < $maxAttempts && ($status === 429 || $status >= 500)) {
                    sleep(2);

                    continue;
                }

                return null;
            } catch (\Exception $e) {
                Log::error("GeminiService: Exceção na tentativa {$attempt}/{$maxAttempts}", [
                    'message' => $e->getMessage(),
                ]);

                if ($attempt < $maxAttempts) {
                    sleep(1);

                    continue;
                }

                return null;
            }
        }

        return null;
    }

    /**
     * Extrai o texto da resposta da API.
     */
    private function extractText(array $response): ?string
    {
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($text === null) {
            Log::warning('GeminiService: Resposta sem texto.', [
                'response' => array_keys($response),
            ]);
        }

        return $text;
    }

    /**
     * Parseia texto como JSON, removendo possíveis wrappers markdown.
     */
    private function parseJsonResponse(string $text): ?array
    {
        // Remove wrapper ```json ... ``` se presente
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $cleaned = preg_replace('/\s*```\s*$/', '', $cleaned);
        $cleaned = trim($cleaned);

        $decoded = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('GeminiService: Falha ao parsear JSON da resposta.', [
                'error' => json_last_error_msg(),
                'text' => mb_substr($text, 0, 500),
            ]);

            return null;
        }

        return $decoded;
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
