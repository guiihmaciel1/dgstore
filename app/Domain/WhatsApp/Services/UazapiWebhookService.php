<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\Supplier\Models\Supplier;
use App\Domain\Supplier\Services\WhatsAppQuotationService;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Log;

/**
 * Webhook da UazAPI.
 *
 * Escopo atual: processa SOMENTE mensagens recebidas de fornecedores cadastrados
 * (leitura de cotações de repasse). Qualquer outro contato é ignorado — o sistema
 * NÃO cria cliente, lead ou deal automaticamente a partir do WhatsApp.
 */
class UazapiWebhookService
{
    private const MAX_BODY_LENGTH = 10000;
    private const MAX_NAME_LENGTH = 100;

    /**
     * Processa o payload do webhook da UazAPI.
     * Formato UazAPI: { EventType, owner, token, message: { ... } }
     */
    public function processWebhook(array $payload): void
    {
        $eventType = $payload['EventType'] ?? null;

        match ($eventType) {
            'messages' => $this->processMessage($payload),
            'connection' => $this->processConnectionEvent($payload),
            default => Log::debug('UazAPI webhook: evento ignorado', ['type' => $eventType]),
        };
    }

    /**
     * Valida o token do webhook para garantir que veio da instância correta.
     */
    public function isValidToken(array $payload): bool
    {
        $payloadToken = $payload['token'] ?? '';
        $expectedToken = config('services.uazapi.instance_token', '');

        if (empty($expectedToken)) {
            return true;
        }

        return hash_equals($expectedToken, (string) $payloadToken);
    }

    private function processMessage(array $payload): void
    {
        $message = $payload['message'] ?? null;

        if (!is_array($message)) {
            Log::warning('UazAPI processMessage: payload sem campo message');
            return;
        }

        $fromMe = $message['fromMe'] ?? $message['key']['fromMe'] ?? false;
        $isGroup = $message['isGroup'] ?? false;
        $chatId = $message['chatid'] ?? $message['key']['remoteJid'] ?? null;

        if ($fromMe || $isGroup || !is_string($chatId)
            || str_ends_with($chatId, '@g.us') || str_ends_with($chatId, '@newsletter')) {
            return;
        }

        $remotePhone = preg_replace('/@.*$/', '', $chatId);
        $waMessageId = $message['id'] ?? $message['messageid'] ?? $message['key']['id'] ?? null;

        if (!is_string($waMessageId) || $waMessageId === '' || !$this->isSupplierPhone($remotePhone)) {
            Log::debug('UazAPI processMessage: ignorado (remetente não é fornecedor cadastrado)');
            return;
        }

        if (WhatsAppMessage::where('wa_message_id', $waMessageId)->exists()) {
            Log::debug('UazAPI processMessage: mensagem duplicada', ['id' => $waMessageId]);
            return;
        }

        $senderName = $message['senderName'] ?? $message['pushName'] ?? null;
        $messageBody = $message['text'] ?? $this->extractBody($message);
        $messageType = $message['messageType'] ?? $message['type'] ?? 'text';

        $this->processSupplierInbound(
            $waMessageId,
            $remotePhone,
            is_string($senderName) ? mb_substr(strip_tags($senderName), 0, self::MAX_NAME_LENGTH) : null,
            is_string($messageBody) ? mb_substr($messageBody, 0, self::MAX_BODY_LENGTH) : null,
            is_string($messageType) ? $messageType : 'text',
            $message,
        );
    }

    /**
     * Verifica se o telefone pertence a um fornecedor ativo.
     */
    private function isSupplierPhone(string $phone): bool
    {
        $allDigits = preg_replace('/\D/', '', $phone);

        // Telefone inválido (ex.: "status@broadcast" vira ''): LIKE '%%' casaria qualquer fornecedor.
        if (strlen($allDigits) < 10 || strlen($allDigits) > 15) {
            return false;
        }

        $digits = strlen($allDigits) > 11 ? substr($allDigits, 2) : $allDigits;

        return Supplier::active()
            ->where(function ($q) use ($digits, $allDigits) {
                $q->where('phone', 'LIKE', "%{$digits}%")
                  ->orWhere('phone', 'LIKE', "%{$allDigits}%");
            })
            ->exists();
    }

    /**
     * Salva a mensagem do fornecedor (sem criar cliente/deal) e extrai a cotação, se houver.
     */
    private function processSupplierInbound(
        string $waMessageId,
        string $fromPhone,
        ?string $senderName,
        ?string $messageBody,
        string $messageType,
        array $rawMessage,
    ): void {
        WhatsAppMessage::create([
            'wa_message_id' => $waMessageId,
            'from_phone' => $fromPhone,
            'from_name' => $senderName,
            'message_type' => $messageType,
            'message_body' => $messageBody,
            'raw_payload' => $rawMessage,
            'direction' => 'inbound',
        ]);

        $this->processSupplierQuotation($fromPhone, $messageBody);

        Log::info('UazAPI: mensagem de fornecedor processada', [
            'wa_message_id' => $waMessageId,
            'from' => $fromPhone,
            'name' => $senderName,
        ]);
    }

    /**
     * Detecta e processa cotações de fornecedores recebidas via WhatsApp.
     */
    private function processSupplierQuotation(string $fromPhone, ?string $messageBody): void
    {
        if (!$messageBody || mb_strlen($messageBody) < 50) {
            return;
        }

        try {
            $service = app(WhatsAppQuotationService::class);
            $service->processIfQuotation($fromPhone, $messageBody);
        } catch (\Throwable $e) {
            Log::warning('Erro ao processar cotação de fornecedor', ['error' => $e->getMessage()]);
        }
    }

    private function processConnectionEvent(array $payload): void
    {
        $event = $payload['event'] ?? [];
        $status = $event['status'] ?? $event['state'] ?? 'unknown';

        Log::info('UazAPI: connection event', ['status' => $status]);
    }

    private function extractBody(array $message): ?string
    {
        $msg = $message['message'] ?? $message;
        $type = $message['messageType'] ?? 'text';

        if (isset($msg['conversation'])) {
            return $msg['conversation'];
        }

        if (isset($msg['extendedTextMessage']['text'])) {
            return $msg['extendedTextMessage']['text'];
        }

        if (isset($msg['imageMessage'])) {
            return $msg['imageMessage']['caption'] ?? '[Imagem]';
        }

        if (isset($msg['videoMessage'])) {
            return $msg['videoMessage']['caption'] ?? '[Vídeo]';
        }

        if (isset($msg['audioMessage']) || isset($msg['pttMessage'])) {
            return '[Áudio]';
        }

        if (isset($msg['documentMessage'])) {
            return $msg['documentMessage']['fileName'] ?? '[Documento]';
        }

        if (isset($msg['stickerMessage'])) {
            return '[Figurinha]';
        }

        if (isset($msg['contactMessage']) || isset($msg['contactsArrayMessage'])) {
            return '[Contato]';
        }

        if (isset($msg['locationMessage'])) {
            return '[Localização]';
        }

        if (isset($msg['buttonsResponseMessage'])) {
            return $msg['buttonsResponseMessage']['selectedDisplayText'] ?? '[Botão]';
        }

        if (isset($msg['listResponseMessage'])) {
            return $msg['listResponseMessage']['title'] ?? '[Seleção de lista]';
        }

        return "[{$type}]";
    }
}
