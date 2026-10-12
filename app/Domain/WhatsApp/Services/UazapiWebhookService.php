<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\CRM\Enums\DealActivityType;
use App\Domain\CRM\Enums\LeadSource;
use App\Domain\CRM\Models\Deal;
use App\Domain\CRM\Models\PipelineStage;
use App\Domain\Customer\Models\Customer;
use App\Domain\Supplier\Models\Supplier;
use App\Domain\Supplier\Services\WhatsAppQuotationService;
use App\Domain\System\Models\SystemSetting;
use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Cache;
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
            'presence' => $this->processPresence($payload),
            'call' => $this->processCall($payload),
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

        if (!$chatId || $isGroup || str_ends_with($chatId, '@g.us') || str_ends_with($chatId, '@newsletter')) {
            Log::debug('UazAPI processMessage: ignorado (grupo/newsletter/sem jid)', ['jid' => $chatId]);
            return;
        }

        $remotePhone = preg_replace('/@.*$/', '', $chatId);
        $messageBody = $message['text'] ?? $this->extractBody($message);

        if (!$waMessageId || !$remotePhone) {
            Log::warning('UazAPI processMessage: sem ID ou telefone', ['id' => $waMessageId, 'phone' => $remotePhone]);
            return;
        }

        if (!$this->isBrazilianPhone($remotePhone)) {
            Log::debug('UazAPI processMessage: ignorado (número não-brasileiro)', ['phone' => $remotePhone]);
            return;
        }

        if (WhatsAppMessage::where('wa_message_id', $waMessageId)->exists()) {
            Log::debug('UazAPI processMessage: mensagem duplicada', ['id' => $waMessageId]);
            return;
        }

        if ($fromMe) {
            $this->processOutboundFromPhone($waMessageId, $remotePhone, $messageBody, $messageType, $message);
            return;
        }

        $this->processInboundMessage($waMessageId, $remotePhone, $senderName, $messageBody, $messageType, $message);
    }

    /**
     * Processa mensagem enviada pelo celular (fromMe=true).
     * Salva como outbound vinculada ao deal existente, sem criar deal/customer.
     */
    private function processOutboundFromPhone(
        string $waMessageId,
        string $remotePhone,
        ?string $messageBody,
        string $messageType,
        array $rawMessage,
    ): void {
        $normalizedPhone = $this->normalizePhoneForSearch($remotePhone);

        $deal = Deal::open()
            ->where(function ($query) use ($remotePhone, $normalizedPhone) {
                $query->where('phone', $remotePhone)
                    ->orWhere('phone', $normalizedPhone)
                    ->orWhere('phone', 'LIKE', "%{$normalizedPhone}");
            })
            ->latest()
            ->first();

        if (!$deal) {
            Log::debug('UazAPI outbound: nenhum deal aberto para este número', ['phone' => $remotePhone]);
            return;
        }

        $msgData = [
            'wa_message_id' => $waMessageId,
            'from_phone'    => $remotePhone,
            'from_name'     => 'DG Store',
            'message_type'  => $messageType,
            'message_body'  => $messageBody,
            'raw_payload'   => $rawMessage,
            'deal_id'       => $deal->id,
            'direction'     => 'outbound',
            'delivery_status' => 'sent',
        ];

        $quotedId = $rawMessage['contextInfo']['stanzaId']
            ?? $rawMessage['quotedMsg']['id']
            ?? $rawMessage['quotedMessage']['id']
            ?? null;

        if ($quotedId) {
            $msgData['reply_to_id'] = $quotedId;
            $quotedBody = $rawMessage['contextInfo']['quotedMessage']['conversation']
                ?? $rawMessage['quotedMsg']['body']
                ?? $rawMessage['quotedMsg']['text']
                ?? null;
            $msgData['reply_to_body'] = $quotedBody ? mb_substr($quotedBody, 0, 200) : null;
        }

        $whatsappMessage = WhatsAppMessage::create($msgData);

        $this->downloadMediaIfNeeded($whatsappMessage, $messageType);
        $deal->updateLastInteraction();

        Log::info('UazAPI: mensagem outbound (celular) salva', [
            'wa_message_id' => $waMessageId,
            'phone' => $remotePhone,
            'deal_id' => $deal->id,
        ]);
    }

    /**
     * Processa mensagem recebida (inbound).
     * Fornecedores cadastrados NÃO geram lead/deal — apenas salvam cotação.
     */
    private function processInboundMessage(
        string $waMessageId,
        string $fromPhone,
        ?string $senderName,
        ?string $messageBody,
        string $messageType,
        array $rawMessage,
    ): void {
        $msgData = [
            'wa_message_id' => $waMessageId,
            'from_phone' => $fromPhone,
            'from_name' => $senderName,
            'message_type' => $messageType,
            'message_body' => $messageBody,
            'raw_payload' => $rawMessage,
            'direction' => 'inbound',
        ];

        $quotedId = $rawMessage['contextInfo']['stanzaId']
            ?? $rawMessage['quotedMsg']['id']
            ?? $rawMessage['quotedMessage']['id']
            ?? null;

        if ($quotedId) {
            $msgData['reply_to_id'] = $quotedId;
            $quotedBody = $rawMessage['contextInfo']['quotedMessage']['conversation']
                ?? $rawMessage['quotedMsg']['body']
                ?? $rawMessage['quotedMsg']['text']
                ?? null;
            $msgData['reply_to_body'] = $quotedBody ? mb_substr($quotedBody, 0, 200) : null;
        }

        if ($this->isSupplierPhone($fromPhone)) {
            $this->processSupplierInbound($msgData, $fromPhone, $messageBody);
            return;
        }

        $whatsappMessage = WhatsAppMessage::create($msgData);

        $deal = $this->findOrCreateDeal($fromPhone, $senderName, $messageBody);
        $whatsappMessage->update(['deal_id' => $deal->id]);
        $this->logMessageActivity($deal, $messageBody, $messageType, $senderName);

        $this->downloadMediaIfNeeded($whatsappMessage, $messageType);

        $deal->increment('unread_wa_count');
        $deal->updateLastInteraction();

        $this->sendAwayMessageIfNeeded($deal, $fromPhone);

        Log::info('UazAPI: mensagem inbound processada', [
            'wa_message_id' => $waMessageId,
            'from' => $fromPhone,
            'name' => $senderName,
            'deal_id' => $deal->id,
            'unread' => $deal->unread_wa_count,
        ]);
    }

    /**
     * Verifica se o telefone pertence a um fornecedor ativo.
     */
    private function isSupplierPhone(string $phone): bool
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) > 11) {
            $digits = substr($digits, 2);
        }

        return Supplier::active()
            ->where(function ($q) use ($digits, $phone) {
                $q->where('phone', 'LIKE', "%{$digits}%")
                  ->orWhere('phone', 'LIKE', "%{$phone}%");
            })
            ->exists();
    }

    /**
     * Processa mensagem de fornecedor: salva a mensagem sem criar deal/lead,
     * e tenta extrair cotação se houver.
     */
    private function processSupplierInbound(array $msgData, string $fromPhone, ?string $messageBody): void
    {
        WhatsAppMessage::create($msgData);

        $this->processSupplierQuotation($fromPhone, $messageBody);

        Log::info('UazAPI: mensagem de fornecedor processada (sem deal)', [
            'wa_message_id' => $msgData['wa_message_id'],
            'from' => $fromPhone,
            'name' => $msgData['from_name'],
        ]);
    }

    private function processMessageUpdate(array $payload): void
    {
        $update = $payload['message'] ?? $payload['event'] ?? null;

        if (!$update) {
            return;
        }

        $messageId = $update['id'] ?? $update['key']['id'] ?? null;
        $status = $update['status'] ?? $update['ack'] ?? null;

        if (!$messageId || !$status) {
            Log::debug('UazAPI: message_update sem id/status', ['payload' => $update]);
            return;
        }

        $statusMap = [
            'PENDING' => 'pending',
            'SENT' => 'sent',
            'SERVER_ACK' => 'sent',
            'DELIVERY_ACK' => 'delivered',
            'READ' => 'read',
            'PLAYED' => 'read',
            0 => 'pending',
            1 => 'sent',
            2 => 'delivered',
            3 => 'read',
            4 => 'read',
        ];

        $normalizedStatus = $statusMap[$status] ?? (is_string($status) ? strtolower($status) : 'sent');

        $message = WhatsAppMessage::where('wa_message_id', $messageId)->first();

        if ($message) {
            $message->update(['delivery_status' => $normalizedStatus]);

            Log::debug('UazAPI: status atualizado', [
                'wa_message_id' => $messageId,
                'status' => $normalizedStatus,
            ]);
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

    private function findOrCreateDeal(string $phone, ?string $contactName, ?string $messageBody = null): Deal
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
        $detectedSource = $this->detectLeadSource($messageBody);

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
            'lead_source' => $detectedSource,
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

    /**
     * Detecta a origem do lead com base no conteúdo da primeira mensagem.
     */
    private function detectLeadSource(?string $messageBody): ?string
    {
        if (!$messageBody) {
            return LeadSource::WhatsApp->value;
        }

        $text = mb_strtolower($messageBody);

        $adPatterns = [
            'vi no anuncio', 'vi o anuncio', 'vi pelo anuncio',
            'vim pelo anuncio', 'vim do anuncio',
            'vi na propaganda', 'vi a propaganda',
            'vi no ad', 'vi pelo ad',
            'vi no instagram', 'vi pelo instagram', 'achei no instagram',
            'vi no facebook', 'vi pelo facebook',
            'vi no face', 'vi pelo face',
            'vi na internet', 'vi no google',
            'vi no site', 'achei no site',
            'vi no olx', 'vi no mercado livre',
            'cliquei no link', 'link do anuncio',
            'anuncio do iphone', 'anuncio do celular',
        ];

        foreach ($adPatterns as $pattern) {
            if (str_contains($text, $pattern)) {
                if (str_contains($text, 'instagram') || str_contains($text, 'insta')) {
                    return LeadSource::Instagram->value;
                }
                return LeadSource::TrafegoPago->value;
            }
        }

        $igPatterns = ['instagram', 'insta', 'direct', 'story', 'stories', 'reels'];
        foreach ($igPatterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return LeadSource::Instagram->value;
            }
        }

        $referralPatterns = [
            'indicacao', 'indicação', 'indicou', 'amigo indicou',
            'fulano indicou', 'me indicaram', 'recomendou', 'recomendaram',
        ];
        foreach ($referralPatterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return LeadSource::Indicacao->value;
            }
        }

        $sitePatterns = ['site', 'www', '.com', 'loja online'];
        foreach ($sitePatterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return LeadSource::Site->value;
            }
        }

        return LeadSource::WhatsApp->value;
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
            $this->enrichWhatsAppProfile($customer);
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

        $this->enrichWhatsAppProfile($customer);

        return $customer;
    }

    private function enrichWhatsAppProfile(Customer $customer): void
    {
        try {
            app(CustomerEnrichmentService::class)->enrich($customer);
        } catch (\Throwable $e) {
            Log::warning('UazAPI: falha ao enriquecer perfil WhatsApp', ['customer_id' => $customer->id]);
        }
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

    /**
     * Tenta baixar e armazenar mídia recebida.
     */
    private function downloadMediaIfNeeded(WhatsAppMessage $message, string $messageType): void
    {
        $mediaTypes = ['image', 'imageMessage', 'video', 'videoMessage', 'audio', 'audioMessage', 'ptt', 'pttMessage', 'document', 'documentMessage'];

        if (!in_array($messageType, $mediaTypes)) {
            return;
        }

        try {
            $mediaService = app(MediaDownloadService::class);
            $mediaService->downloadAndStore($message);
        } catch (\Throwable $e) {
            Log::warning('UazAPI: falha ao baixar mídia (será tentado depois)', ['msg_id' => $message->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Envia mensagem de ausência apenas entre 00:00 e 06:00 (madrugada).
     * Fora desse horário, não envia resposta automática.
     */
    private function sendAwayMessageIfNeeded(Deal $deal, string $phone): void
    {
        $awayEnabled = SystemSetting::get('wa_away_enabled', 'false');
        if ($awayEnabled !== 'true') {
            return;
        }

        $hour = now()->setTimezone('America/Sao_Paulo')->hour;
        if ($hour >= 6) {
            return;
        }

        $lastAway = $deal->source_metadata['last_away_sent'] ?? null;
        if ($lastAway && now()->diffInMinutes(\Carbon\Carbon::parse($lastAway)) < 30) {
            return;
        }

        $awayMessage = SystemSetting::get(
            'wa_away_message',
            'Olá! No momento estamos fora do horário de atendimento. Responderemos assim que possível. Obrigado!'
        );

        try {
            $uazapi = app(UazapiService::class);
            $jid = str_contains($phone, '@') ? $phone : $phone . '@s.whatsapp.net';
            $result = $uazapi->sendText($jid, $awayMessage);

            WhatsAppMessage::create([
                'wa_message_id' => $result['key']['id'] ?? ('away_' . uniqid()),
                'from_phone'    => $phone,
                'from_name'     => 'DG Store',
                'message_type'  => 'text',
                'message_body'  => $awayMessage,
                'raw_payload'   => $result,
                'deal_id'       => $deal->id,
                'direction'     => 'outbound',
                'delivery_status' => 'sent',
            ]);

            $metadata = $deal->source_metadata ?? [];
            $metadata['last_away_sent'] = now()->toISOString();
            $deal->update(['source_metadata' => $metadata]);

            Log::info('UazAPI: mensagem de ausência enviada', ['deal_id' => $deal->id, 'phone' => $phone]);
        } catch (\Throwable $e) {
            Log::error('UazAPI: falha ao enviar mensagem de ausência', ['error' => $e->getMessage()]);
        }
    }

    private function isWithinBusinessHours(): bool
    {
        $now = now()->setTimezone('America/Sao_Paulo');
        $dayOfWeek = $now->dayOfWeek;

        if ($dayOfWeek === 0) {
            return false;
        }

        $startHour = (int) SystemSetting::get('wa_business_start', '9');
        $endHour = (int) SystemSetting::get('wa_business_end', '18');

        if ($dayOfWeek === 6) {
            $endHour = min($endHour, 13);
        }

        $hour = $now->hour;
        return $hour >= $startHour && $hour < $endHour;
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

    private function getDefaultAssignee(): User
    {
        return User::where('active', true)
            ->where('role', 'admin_geral')
            ->first()
            ?? User::where('active', true)->first()
            ?? User::first();
    }

    /**
     * Verifica se o telefone é brasileiro.
     * Aceita: 55XXXXXXXXXXX (com código do país) ou XXXXXXXXXXX (DDD + número sem 55).
     * Rejeita: qualquer outro código de país (ex: 595 = Paraguai).
     */
    private function isBrazilianPhone(string $phone): bool
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '55') && strlen($digits) >= 12) {
            return true;
        }

        if (!str_starts_with($digits, '55') && strlen($digits) <= 11) {
            return true;
        }

        return false;
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

    // ─── Presence (digitando/gravando) ──────────────────────

    private function processPresence(array $payload): void
    {
        try {
            $chatId = $payload['chatId'] ?? $payload['id'] ?? $payload['jid'] ?? null;
            $type = $payload['type'] ?? $payload['presences'] ?? null;

            if (!$chatId) {
                if (isset($payload['presences']) && is_array($payload['presences'])) {
                    foreach ($payload['presences'] as $jid => $presenceData) {
                        $phone = preg_replace('/\D/', '', explode('@', $jid)[0] ?? '');
                        $phone = $this->normalizePhoneForSearch($phone);
                        $lastKnown = $presenceData['lastKnownPresence'] ?? $presenceData['type'] ?? 'paused';
                        Cache::put("wa_presence:{$phone}", $lastKnown, 15);
                    }
                    return;
                }
                return;
            }

            $phone = preg_replace('/\D/', '', explode('@', $chatId)[0] ?? '');
            $phone = $this->normalizePhoneForSearch($phone);

            if (is_string($type)) {
                Cache::put("wa_presence:{$phone}", $type, 15);
            }
        } catch (\Throwable $e) {
            Log::warning('Erro ao processar presença WhatsApp', ['error' => $e->getMessage()]);
        }
    }

    // ─── Chamadas WhatsApp ──────────────────────────────────

    private function processCall(array $payload): void
    {
        try {
            $callData = $payload['call'] ?? $payload['event'] ?? $payload;

            $chatId = $callData['from'] ?? $callData['chatId'] ?? $callData['id'] ?? null;
            if (!$chatId) {
                Log::debug('UazAPI call webhook: sem chatId', ['payload' => array_keys($payload)]);
                return;
            }

            $phone = preg_replace('/\D/', '', explode('@', $chatId)[0] ?? '');
            if (strlen($phone) < 10) {
                return;
            }

            $callStatus = $callData['status'] ?? $callData['type'] ?? 'unknown';
            $isOfferer = $callData['isOfferer'] ?? $callData['fromMe'] ?? false;
            $duration = $callData['duration'] ?? null;

            if ($isOfferer) {
                return;
            }

            $normalizedPhone = $this->normalizePhoneForSearch($phone);
            $deal = Deal::where('is_open', true)
                ->where(function ($q) use ($normalizedPhone, $phone) {
                    $q->where('phone', 'like', "%{$normalizedPhone}%")
                      ->orWhere('phone', 'like', "%{$phone}%");
                })
                ->first();

            if (!$deal) {
                Log::debug('UazAPI call: nenhum deal encontrado', ['phone' => $phone]);
                return;
            }

            $description = match (true) {
                in_array($callStatus, ['accepted', 'answered', 'accept']) =>
                    $duration
                        ? "📞 Chamada WhatsApp recebida (atendida, " . gmdate('i\m s\s', (int) $duration) . ")"
                        : "📞 Chamada WhatsApp recebida (atendida)",
                in_array($callStatus, ['rejected', 'reject']) =>
                    "📞 Chamada WhatsApp rejeitada",
                in_array($callStatus, ['timeout', 'missed', 'unanswered']) =>
                    "📞 Chamada WhatsApp perdida",
                in_array($callStatus, ['offer', 'ringing']) =>
                    null,
                default =>
                    "📞 Chamada WhatsApp ({$callStatus})",
            };

            if ($description) {
                $deal->logActivity(DealActivityType::Call, $description);
                $deal->updateLastInteraction();
            }
        } catch (\Throwable $e) {
            Log::warning('Erro ao processar chamada WhatsApp', ['error' => $e->getMessage()]);
        }
    }
}
