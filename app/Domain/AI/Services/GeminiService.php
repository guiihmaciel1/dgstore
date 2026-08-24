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

    private int $maxRequestsPerMinute = 12;

    private int $maxRequestsPerDay = 80;

    private ?string $lastError = null;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key');
        $this->model = (string) config('services.gemini.model', 'gemini-2.0-flash');
    }

    public function isAvailable(): bool
    {
        return $this->apiKey !== '';
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Retorna info de uso diário para feedback.
     */
    public function getDailyUsage(): array
    {
        $key = 'gemini_daily_limit:' . now()->format('Y-m-d');
        $count = (int) Cache::get($key, 0);

        return [
            'used' => $count,
            'limit' => $this->maxRequestsPerDay,
            'remaining' => max(0, $this->maxRequestsPerDay - $count),
            'exhausted' => $count >= $this->maxRequestsPerDay,
        ];
    }

    public function generateContent(string $prompt, ?string $systemInstruction = null): ?string
    {
        if (! $this->isAvailable()) {
            $this->lastError = 'API key não configurada.';
            Log::warning('GeminiService: API key não configurada.');

            return null;
        }

        if (! $this->checkRateLimit()) {
            $this->lastError = 'Limite de requisições por minuto atingido. Aguarde 1 minuto.';
            Log::warning('GeminiService: Rate limit por minuto atingido.');

            return null;
        }

        if (! $this->checkDailyLimit()) {
            $this->lastError = 'Limite diário atingido. Tente novamente amanhã ou digite o IMEI manualmente.';
            Log::warning('GeminiService: Rate limit diário atingido.');

            return null;
        }

        $body = $this->buildRequestBody($prompt, $systemInstruction);
        $response = $this->sendRequest($body);

        if ($response === null) {
            return null;
        }

        return $this->extractText($response);
    }

    public function analyzeImage(string $base64Image, string $mimeType, string $prompt, ?string $systemInstruction = null): ?array
    {
        if (! $this->isAvailable()) {
            $this->lastError = 'API key não configurada.';
            Log::warning('GeminiService: API key não configurada.');

            return null;
        }

        if (! $this->checkRateLimit()) {
            $this->lastError = 'Limite de requisições por minuto atingido. Aguarde 1 minuto.';
            Log::warning('GeminiService: Rate limit por minuto atingido.');

            return null;
        }

        if (! $this->checkDailyLimit()) {
            $this->lastError = 'Limite diário de consultas atingido. Digite o IMEI manualmente.';
            Log::warning('GeminiService: Rate limit diário atingido.');

            return null;
        }

        $jsonInstruction = ($systemInstruction ? $systemInstruction . "\n\n" : '')
            . 'Retorne APENAS JSON válido, sem markdown, sem ```json. Apenas o JSON puro e compacto.';

        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['inlineData' => ['mimeType' => $mimeType, 'data' => $base64Image]],
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 1024,
                'thinkingConfig' => ['thinkingBudget' => 0],
            ],
        ];

        if ($jsonInstruction) {
            $body['systemInstruction'] = ['parts' => [['text' => $jsonInstruction]]];
        }

        $response = $this->sendRequest($body);

        if ($response === null) {
            return null;
        }

        $text = $this->extractText($response);

        if ($text === null) {
            return null;
        }

        return $this->parseJsonResponse($text);
    }

    public function generateJson(string $prompt, ?string $systemInstruction = null): ?array
    {
        $jsonInstruction = ($systemInstruction ? $systemInstruction . "\n\n" : '')
            . 'Retorne APENAS JSON válido, sem markdown, sem ```json. Apenas o JSON puro e compacto.';

        $text = $this->generateContent($prompt, $jsonInstruction);

        if ($text === null) {
            return null;
        }

        return $this->parseJsonResponse($text);
    }

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

    private function sendRequest(array $body): ?array
    {
        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";
        $maxAttempts = 3;
        $baseDelay = 5;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::timeout(60)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $body);

                if ($response->successful()) {
                    $this->incrementRateLimit();
                    $this->incrementDailyLimit();

                    return $response->json();
                }

                $status = $response->status();
                $errorBody = $response->body();

                Log::warning("GeminiService: Erro HTTP {$status} (tentativa {$attempt}/{$maxAttempts})", [
                    'status' => $status,
                    'body' => mb_substr($errorBody, 0, 500),
                ]);

                if ($status === 429) {
                    $this->lastError = 'API sobrecarregada (limite Google). Tente novamente em alguns minutos ou digite o IMEI manualmente.';
                }

                if ($status >= 500) {
                    $this->lastError = 'Serviço do Google indisponível. Tente novamente em instantes.';
                }

                if ($attempt < $maxAttempts && ($status === 429 || $status >= 500)) {
                    $delay = min($baseDelay * pow(2, $attempt - 1), 45);
                    $retryFromHeader = $this->extractRetryDelay($errorBody);
                    $waitSeconds = max($delay, $retryFromHeader);

                    Log::info("GeminiService: Backoff {$waitSeconds}s antes do retry (tentativa {$attempt})...");
                    sleep($waitSeconds);

                    continue;
                }

                if ($status === 400 || $status === 403) {
                    $this->lastError = 'Erro na requisição à API. Verifique a configuração.';
                }

                return null;
            } catch (\Exception $e) {
                Log::error("GeminiService: Exceção na tentativa {$attempt}/{$maxAttempts}", [
                    'message' => $e->getMessage(),
                ]);

                $this->lastError = 'Erro de conexão com a API. Verifique sua internet.';

                if ($attempt < $maxAttempts) {
                    sleep($baseDelay * $attempt);

                    continue;
                }

                return null;
            }
        }

        return null;
    }

    private function extractRetryDelay(string $errorBody): int
    {
        if (preg_match('/retry in (\d+(?:\.\d+)?)s/i', $errorBody, $matches)) {
            return (int) ceil((float) $matches[1]);
        }

        return 5;
    }

    private function extractText(array $response): ?string
    {
        $parts = $response['candidates'][0]['content']['parts'] ?? [];

        foreach ($parts as $part) {
            $isThought = isset($part['thought']) && $part['thought'] === true;

            if (! $isThought && isset($part['text'])) {
                return $part['text'];
            }
        }

        foreach ($parts as $part) {
            if (isset($part['text']) && $part['text'] !== '') {
                return $part['text'];
            }
        }

        Log::warning('GeminiService: Resposta sem texto.', [
            'parts_count' => count($parts),
            'response_keys' => array_keys($response),
        ]);

        $this->lastError = 'API retornou resposta vazia. Tente novamente.';

        return null;
    }

    private function parseJsonResponse(string $text): ?array
    {
        $cleaned = $this->sanitizeJsonText($text);

        $decoded = json_decode($cleaned, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

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

        $this->lastError = 'Resposta da IA não pôde ser processada. Tente novamente.';

        return null;
    }

    private function sanitizeJsonText(string $text): string
    {
        $cleaned = trim($text);
        $cleaned = preg_replace('/^\x{FEFF}/u', '', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/\s*```\s*$/', '', $cleaned);
        $cleaned = trim($cleaned);
        $cleaned = preg_replace('/[\x00-\x1F\x7F]/u', '', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/[\x{200B}-\x{200F}\x{2028}-\x{202F}\x{2060}\x{FEFF}]/u', '', $cleaned) ?? $cleaned;

        return $cleaned;
    }

    private function checkRateLimit(): bool
    {
        $key = 'gemini_rate_limit:' . now()->format('Y-m-d-H-i');
        $count = (int) Cache::get($key, 0);

        return $count < $this->maxRequestsPerMinute;
    }

    private function incrementRateLimit(): void
    {
        $key = 'gemini_rate_limit:' . now()->format('Y-m-d-H-i');
        $count = (int) Cache::get($key, 0);
        Cache::put($key, $count + 1, 120);
    }

    private function checkDailyLimit(): bool
    {
        $key = 'gemini_daily_limit:' . now()->format('Y-m-d');
        $count = (int) Cache::get($key, 0);

        return $count < $this->maxRequestsPerDay;
    }

    private function incrementDailyLimit(): void
    {
        $key = 'gemini_daily_limit:' . now()->format('Y-m-d');
        $count = (int) Cache::get($key, 0);
        Cache::put($key, $count + 1, now()->endOfDay()->diffInSeconds(now()));
    }
}
