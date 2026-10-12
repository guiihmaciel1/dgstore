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
     * Envia mensagem de texto, opcionalmente citando uma mensagem (quote reply).
     */
    public function sendText(string $number, string $text, ?string $replyId = null): array
    {
        $payload = [
            'number' => $this->normalizeNumber($number),
            'text' => $text,
        ];

        if ($replyId) {
            $payload['replyid'] = $replyId;
        }

        return $this->post('/send/text', $payload);
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
    public function setWebhook(string $url, array $events = ['messages', 'messages_update', 'connection', 'presence', 'call']): array
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
     * Marca todo o chat como lido no WhatsApp.
     */
    public function markChatRead(string $number): array
    {
        return $this->post('/chat/read', [
            'number' => $this->normalizeNumber($number) . '@s.whatsapp.net',
            'read' => true,
        ]);
    }

    /**
     * Download de mídia de uma mensagem. Com transcribe=true, transcreve áudios.
     */
    public function downloadMedia(string $messageId, bool $transcribe = false): array
    {
        $payload = ['id' => $messageId];
        if ($transcribe) {
            $payload['transcribe'] = true;
        }

        return $this->post('/message/download', $payload);
    }

    /**
     * Busca avatar (foto de perfil) de um chat.
     */
    public function getChatAvatar(string $number): array
    {
        return $this->post('/chat/avatar', [
            'number' => $this->normalizeNumber($number),
        ]);
    }

    /**
     * Busca detalhes do chat (nome business, etc).
     */
    public function getChatDetails(string $number): array
    {
        return $this->post('/chat/details', [
            'number' => $this->normalizeNumber($number),
        ]);
    }

    /**
     * Verifica se um número existe no WhatsApp.
     */
    public function checkNumber(string $number): array
    {
        return $this->post('/contacts', [
            'number' => $this->normalizeNumber($number),
        ]);
    }

    /**
     * Busca etiquetas (labels) do WhatsApp.
     */
    public function getLabels(): array
    {
        return $this->get('/chat/labels');
    }

    /**
     * Aplica etiqueta a um chat.
     */
    public function setLabel(string $number, string $labelId): array
    {
        return $this->post('/chat/labels', [
            'number' => $this->normalizeNumber($number),
            'labelId' => $labelId,
        ]);
    }

    /**
     * Envia menu interativo com botões.
     * UazAPI espera: number, title, text, footer (opcional), buttons [{buttonId, buttonText}]
     */
    public function sendMenu(string $number, string $title, string $body, array $buttons, ?string $footer = null): array
    {
        $formattedButtons = array_map(function ($btn, $index) {
            return [
                'buttonId' => $btn['id'] ?? $btn['buttonId'] ?? 'btn_' . ($index + 1),
                'buttonText' => ['displayText' => $btn['text'] ?? $btn['buttonText'] ?? $btn['displayText'] ?? ''],
                'type' => 1,
            ];
        }, $buttons, array_keys($buttons));

        $payload = [
            'number' => $this->normalizeNumber($number),
            'title' => $title,
            'text' => $body,
            'buttons' => $formattedButtons,
        ];

        if ($footer) {
            $payload['footer'] = $footer;
        }

        return $this->post('/send/menu', $payload);
    }

    /**
     * Envia mensagem de lista.
     * UazAPI espera: number, title, text, buttonText, sections [{title, rows [{rowId, title, description}]}]
     */
    public function sendList(string $number, string $title, string $body, string $buttonText, array $sections, ?string $footer = null): array
    {
        $payload = [
            'number' => $this->normalizeNumber($number),
            'title' => $title,
            'text' => $body,
            'buttonText' => $buttonText,
            'sections' => $sections,
        ];

        if ($footer) {
            $payload['footer'] = $footer;
        }

        return $this->post('/send/menu', $payload);
    }

    /**
     * Envio simples para campanhas (sender/simple).
     */
    public function senderSimple(array $numbers, string $text, ?string $mediaUrl = null): array
    {
        $payload = [
            'numbers' => array_map(fn ($n) => $this->normalizeNumber($n), $numbers),
            'text' => $text,
        ];

        if ($mediaUrl) {
            $payload['file'] = $mediaUrl;
        }

        return $this->post('/sender/simple', $payload);
    }

    /**
     * Atualiza dados de lead no chat do WhatsApp.
     */
    public function editLead(string $number, string $name, ?string $responsible = null, ?string $notes = null): array
    {
        $payload = [
            'number' => $this->normalizeNumber($number),
            'name' => $name,
        ];

        if ($responsible) {
            $payload['responsible'] = $responsible;
        }
        if ($notes) {
            $payload['notes'] = $notes;
        }

        return $this->post('/chat/editLead', $payload);
    }

    /**
     * Envia mensagem assíncrona (para filas).
     */
    public function sendAsync(string $number, string $text): array
    {
        return $this->post('/message/async', [
            'number' => $this->normalizeNumber($number),
            'text' => $text,
        ]);
    }

    // ─── Reações ────────────────────────────────────────────

    /**
     * Reage a uma mensagem com emoji. Texto vazio remove a reação.
     */
    public function reactToMessage(string $messageId, string $emoji): array
    {
        return $this->post('/message/react', [
            'id' => $messageId,
            'text' => $emoji,
        ]);
    }

    // ─── Localização ────────────────────────────────────────

    /**
     * Envia localização geográfica.
     */
    public function sendLocation(string $number, float $lat, float $lng, string $name, string $address): array
    {
        return $this->post('/send/location', [
            'number' => $this->normalizeNumber($number),
            'latitude' => $lat,
            'longitude' => $lng,
            'name' => $name,
            'address' => $address,
        ]);
    }

    // ─── PIX ────────────────────────────────────────────────

    /**
     * Envia botão PIX. pixType: CPF, CNPJ, PHONE, EMAIL, EVP.
     */
    public function sendPixButton(string $number, string $pixType, string $pixKey, string $pixName = 'Pix'): array
    {
        return $this->post('/send/pix-button', [
            'number' => $this->normalizeNumber($number),
            'pixType' => strtoupper($pixType),
            'pixKey' => $pixKey,
            'pixName' => $pixName,
        ]);
    }

    // ─── Editar / Apagar Mensagem ───────────────────────────

    /**
     * Edita o texto de uma mensagem já enviada.
     */
    public function editMessage(string $messageId, string $newText): array
    {
        return $this->post('/message/edit', [
            'id' => $messageId,
            'text' => $newText,
        ]);
    }

    /**
     * Apaga mensagem para todos.
     */
    public function deleteMessage(string $messageId): array
    {
        return $this->post('/message/delete', [
            'id' => $messageId,
        ]);
    }

    // ─── Presença ───────────────────────────────────────────

    /**
     * Envia indicador de presença (composing/recording/paused).
     */
    public function sendPresence(string $number, string $presence = 'composing', int $delay = 5000): array
    {
        return $this->post('/message/presence', [
            'number' => $this->normalizeNumber($number),
            'presence' => $presence,
            'delay' => min($delay, 300000),
        ]);
    }

    // ─── Contagem de Chats ──────────────────────────────────

    /**
     * Retorna contadores de chats ativos.
     */
    public function countChats(): array
    {
        return $this->get('/chat/totalcount');
    }

    // ─── Resolver Leads (concluir conversas) ────────────────

    /**
     * Marca conversas como resolvidas.
     */
    public function resolveLeads(array $numbers): array
    {
        $chats = array_map(fn (string $n) => [
            'number' => $this->normalizeNumber($n) . '@s.whatsapp.net',
        ], $numbers);

        return $this->post('/lead/resolve', ['chats' => $chats]);
    }

    // ─── Tags de Lead ───────────────────────────────────────

    /**
     * Lista todas as tags de lead do WhatsApp.
     */
    public function getLeadTags(): array
    {
        return $this->get('/lead/tags');
    }

    /**
     * Cria, edita ou apaga uma tag de lead.
     */
    public function editLeadTag(array $data): array
    {
        return $this->post('/lead/tags/edit', $data);
    }

    // ─── Respostas Rápidas ──────────────────────────────────

    /**
     * Lista respostas rápidas com paginação.
     */
    public function listQuickReplies(int $limit = 100, int $offset = 0): array
    {
        return $this->get("/quick-replies?limit={$limit}&offset={$offset}");
    }

    /**
     * Cria uma resposta rápida.
     */
    public function createQuickReply(string $shortcut, array $message): array
    {
        return $this->post('/quick-replies', [
            'shortCut' => $shortcut,
            'message' => $message,
        ]);
    }

    /**
     * Atualiza uma resposta rápida.
     */
    public function updateQuickReply(string $id, array $data): array
    {
        return $this->patch("/quick-replies/{$id}", $data);
    }

    /**
     * Deleta uma resposta rápida.
     */
    public function deleteQuickReply(string $id): array
    {
        return $this->httpDelete("/quick-replies/{$id}");
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

    /**
     * PATCH request.
     */
    private function patch(string $endpoint, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'token' => $this->token,
            ])->patch("{$this->baseUrl}{$endpoint}", $data);

            return $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('UazAPI PATCH error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return ['error' => $e->getMessage()];
        }
    }

    /**
     * DELETE request.
     */
    private function httpDelete(string $endpoint): array
    {
        try {
            $response = Http::withHeaders([
                'token' => $this->token,
            ])->delete("{$this->baseUrl}{$endpoint}");

            return $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('UazAPI DELETE error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return ['error' => $e->getMessage()];
        }
    }
}
