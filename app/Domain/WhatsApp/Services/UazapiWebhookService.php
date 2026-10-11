<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\CRM\Enums\DealActivityType;
use App\Domain\CRM\Models\Deal;
use App\Domain\CRM\Models\PipelineStage;
use App\Domain\Customer\Models\Customer;
use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Log;

class UazapiWebhookService
{
    /**
     * Processa o payload do webhook da UazAPI.
     * Formato UazAPI: { EventType, owner, token, message: { ... } }
     */
    public function processWebhook(array $payload): void
    {
        $eventType = $payload['EventType'] ?? null;

        match ($eventType) {
            'messages' => $this->processMessage($payload),
            'messages_update' => $this->processMessageUpdate($payload),
            'connection' => $this->processConnectionEvent($payload),
            default => Log::debug('UazAPI webhook: evento ignorado', ['type' => $eventType]),
        };
    }

    /**
     * Valida o token do webhook para garantir que veio da instância correta.
     */
    public function isValidToken(array $payload): bool
    {
        $payloadToken = $payload['token'] ?? null;
        $expectedToken = config('services.uazapi.instance_token');

        if (empty($expectedToken)) {
            return true;
        }

        return $payloadToken === $expectedToken;
    }

    private function processMessage(array $payload): void
    {
        $message = $payload['message'] ?? null;

        if (!$message) {
            Log::warning('UazAPI processMessage: payload sem campo message');
            return;
        }

        $waMessageId = $message['id'] ?? $message['messageid'] ?? $message['key']['id'] ?? null;
        $fromMe = $message['fromMe'] ?? $message['key']['fromMe'] ?? false;
        $chatId = $message['chatid'] ?? $message['key']['remoteJid'] ?? null;
        $senderName = $message['senderName'] ?? $message['pushName'] ?? null;
        $messageType = $message['messageType'] ?? $message['type'] ?? 'text';
        $isGroup = $message['isGroup'] ?? false;

        Log::info('UazAPI processMessage: analisando', [
            'wa_message_id' => $waMessageId,
            'fromMe' => $fromMe,
            'chatid' => $chatId,
            'senderName' => $senderName,
            'messageType' => $messageType,
            'isGroup' => $isGroup,
        ]);

        if ($fromMe) {
            Log::debug('UazAPI processMessage: ignorado (fromMe=true)');
            return;
        }

        if (!$chatId || $isGroup || str_ends_with($chatId, '@g.us') || str_ends_with($chatId, '@newsletter')) {
            Log::debug('UazAPI processMessage: ignorado (grupo/newsletter/sem jid)', ['jid' => $chatId]);
            return;
        }

        $fromPhone = preg_replace('/@.*$/', '', $chatId);
        $messageBody = $message['text'] ?? $this->extractBody($message);

        if (!$waMessageId || !$fromPhone) {
            Log::warning('UazAPI processMessage: sem ID ou telefone', ['id' => $waMessageId, 'phone' => $fromPhone]);
            return;
        }

        if (WhatsAppMessage::where('wa_message_id', $waMessageId)->exists()) {
            Log::debug('UazAPI processMessage: mensagem duplicada', ['id' => $waMessageId]);
            return;
        }

        $whatsappMessage = WhatsAppMessage::create([
            'wa_message_id' => $waMessageId,
            'from_phone' => $fromPhone,
            'from_name' => $senderName,
            'message_type' => $messageType,
            'message_body' => $messageBody,
            'raw_payload' => $message,
        ]);

        $deal = $this->findOrCreateDeal($fromPhone, $senderName);
        $whatsappMessage->update(['deal_id' => $deal->id]);
        $this->logMessageActivity($deal, $messageBody, $messageType, $senderName);

        Log::info('UazAPI: mensagem processada com sucesso', [
            'wa_message_id' => $waMessageId,
            'from' => $fromPhone,
            'name' => $senderName,
            'deal_id' => $deal->id,
        ]);
    }

    private function processMessageUpdate(array $payload): void
    {
        Log::debug('UazAPI: message status update', [
            'event' => $payload['event'] ?? null,
        ]);
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

    private function findOrCreateDeal(string $phone, ?string $contactName): Deal
    {
        $normalizedPhone = $this->normalizePhoneForSearch($phone);

        $existingDeal = Deal::open()
            ->where(function ($query) use ($phone, $normalizedPhone) {
                $query->where('phone', $phone)
                    ->orWhere('phone', $normalizedPhone)
                    ->orWhere('phone', 'LIKE', "%{$normalizedPhone}");
            })
            ->latest()
            ->first();

        if ($existingDeal) {
            return $existingDeal;
        }

        $customer = $this->findOrCreateCustomer($phone, $contactName);
        $assignedUser = $this->getDefaultAssignee();
        $defaultStage = PipelineStage::where('is_default', true)->first()
            ?? PipelineStage::active()->ordered()->first();

        if (!$defaultStage) {
            Log::error('UazAPI: nenhum pipeline stage configurado');
            throw new \RuntimeException('Nenhum pipeline stage configurado.');
        }

        $displayName = $contactName ?: $this->formatPhone($phone);
        $title = "WhatsApp - {$displayName}";

        $maxPosition = Deal::where('pipeline_stage_id', $defaultStage->id)->max('position') ?? 0;

        $isNewCustomer = $customer->wasRecentlyCreated;

        $deal = Deal::create([
            'user_id' => $assignedUser->id,
            'customer_id' => $customer->id,
            'pipeline_stage_id' => $defaultStage->id,
            'title' => $title,
            'phone' => $phone,
            'description' => $isNewCustomer
                ? "Novo cliente captado via WhatsApp"
                : "Contato de cliente existente via WhatsApp",
            'position' => $maxPosition + 1,
            'source' => 'whatsapp',
        ]);

        $activityDesc = $isNewCustomer
            ? "📲 Novo cliente cadastrado automaticamente via WhatsApp: {$displayName}"
            : "📲 Cliente existente iniciou contato via WhatsApp";

        $deal->activities()->create([
            'user_id' => $assignedUser->id,
            'type' => DealActivityType::Created->value,
            'description' => $activityDesc,
        ]);

        Log::info('UazAPI: novo deal criado', [
            'deal_id' => $deal->id,
            'phone' => $phone,
            'new_customer' => $isNewCustomer,
        ]);

        return $deal;
    }

    private function logMessageActivity(Deal $deal, ?string $body, string $type, ?string $fromName): void
    {
        $prefix = $fromName ? "{$fromName}: " : '';
        $content = $body ?? "[{$type}]";
        $description = "📩 {$prefix}{$content}";

        if (mb_strlen($description) > 2000) {
            $description = mb_substr($description, 0, 1997) . '...';
        }

        $deal->activities()->create([
            'user_id' => $deal->user_id,
            'type' => DealActivityType::WhatsApp->value,
            'description' => $description,
        ]);
    }

    /**
     * Busca cliente pelo telefone. Se não existir, cria com nome e telefone.
     */
    private function findOrCreateCustomer(string $phone, ?string $contactName): Customer
    {
        $normalized = $this->normalizePhoneForSearch($phone);

        $customer = Customer::where('phone', $phone)
            ->orWhere('phone', $normalized)
            ->orWhere('phone', 'LIKE', "%{$normalized}")
            ->first();

        if ($customer) {
            $this->enrichCustomerData($customer, $contactName);
            return $customer;
        }

        $customer = Customer::create([
            'name' => $contactName ?: "WhatsApp {$this->formatPhone($phone)}",
            'phone' => $normalized,
        ]);

        Log::info('UazAPI: cliente criado automaticamente', [
            'customer_id' => $customer->id,
            'name' => $customer->name,
            'phone' => $normalized,
        ]);

        return $customer;
    }

    /**
     * Enriquece dados do cliente com info do WhatsApp.
     * Se o nome do cliente é genérico (ex: "WhatsApp ...") e temos um nome real, atualiza.
     */
    private function enrichCustomerData(Customer $customer, ?string $contactName): void
    {
        if (!$contactName) {
            return;
        }

        $isGenericName = str_starts_with($customer->name, 'WhatsApp ')
            || str_starts_with($customer->name, 'Cliente ')
            || $customer->name === $customer->phone;

        if ($isGenericName) {
            $customer->update(['name' => $contactName]);

            Log::info('UazAPI: nome do cliente atualizado via WhatsApp', [
                'customer_id' => $customer->id,
                'old_name' => $customer->getOriginal('name'),
                'new_name' => $contactName,
            ]);
        }
    }

    private function getDefaultAssignee(): User
    {
        return User::where('active', true)
            ->where('role', 'admin_geral')
            ->first()
            ?? User::where('active', true)->first()
            ?? User::first();
    }

    private function normalizePhoneForSearch(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '55') && strlen($digits) >= 12) {
            $digits = substr($digits, 2);
        }

        return $digits;
    }

    private function formatPhone(string $phone): string
    {
        $digits = $this->normalizePhoneForSearch($phone);

        if (strlen($digits) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $digits);
        }

        return $phone;
    }
}
