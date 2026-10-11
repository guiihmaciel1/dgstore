<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UazapiService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.uazapi.base_url', ''), '/');
        $this->token = config('services.uazapi.instance_token', '');
    }

    /**
     * Verifica se a UazAPI está configurada.
     */
    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->token);
    }

    /**
     * Status da instância.
     */
    public function status(): array
    {
        return $this->get('/instance/status');
    }

    /**
     * Envia mensagem de texto.
     */
    public function sendText(string $number, string $text): array
    {
        return $this->post('/send/text', [
            'number' => $this->normalizeNumber($number),
            'text' => $text,
        ]);
    }

    /**
     * Envia imagem com legenda opcional.
     */
    public function sendImage(string $number, string $imageUrl, ?string $caption = null): array
    {
        $payload = [
            'number' => $this->normalizeNumber($number),
            'file' => $imageUrl,
        ];

        if ($caption) {
            $payload['caption'] = $caption;
        }

        return $this->post('/send/media', $payload);
    }

    /**
     * Envia documento/arquivo.
     */
    public function sendDocument(string $number, string $fileUrl, ?string $filename = null): array
    {
        $payload = [
            'number' => $this->normalizeNumber($number),
            'file' => $fileUrl,
        ];

        if ($filename) {
            $payload['fileName'] = $filename;
        }

        return $this->post('/send/media', $payload);
    }

    /**
     * Configura o webhook da instância.
     */
    public function setWebhook(string $url, array $events = ['messages', 'messages_update', 'connection']): array
    {
        return $this->post('/webhook', [
            'enabled' => true,
            'url' => $url,
            'events' => $events,
            'excludeMessages' => ['wasSentByApi'],
        ]);
    }

    /**
     * Consulta configuração atual do webhook.
     */
    public function getWebhook(): array
    {
        return $this->get('/webhook');
    }

    /**
     * Busca chats com paginação.
     */
    public function findChats(int $limit = 50, int $offset = 0): array
    {
        return $this->post('/chat/find', [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    /**
     * Busca mensagens de um chat.
     */
    public function findMessages(string $number, int $limit = 50, int $offset = 0): array
    {
        return $this->post('/message/find', [
            'number' => $this->normalizeNumber($number),
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    /**
     * Marca mensagem como lida.
     */
    public function markRead(string $messageId): array
    {
        return $this->post('/message/markread', [
            'id' => $messageId,
        ]);
    }

    /**
     * Normaliza número para formato internacional sem +.
     */
    private function normalizeNumber(string $number): string
    {
        $digits = preg_replace('/\D/', '', $number);

        if (strlen($digits) === 11 || strlen($digits) === 10) {
            $digits = '55' . $digits;
        }

        return $digits;
    }

    /**
     * GET request.
     */
    private function get(string $endpoint): array
    {
        try {
            $response = Http::withHeaders([
                'token' => $this->token,
            ])->get("{$this->baseUrl}{$endpoint}");

            return $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('UazAPI GET error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return ['error' => $e->getMessage()];
        }
    }

    /**
     * POST request.
     */
    private function post(string $endpoint, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'token' => $this->token,
            ])->post("{$this->baseUrl}{$endpoint}", $data);

            return $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('UazAPI POST error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return ['error' => $e->getMessage()];
        }
    }
}
