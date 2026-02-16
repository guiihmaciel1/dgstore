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

class WhatsAppWebhookService
{
    /**
     * Processa o payload do webhook do WhatsApp.
     * Extrai mensagens, salva no banco e cria/atualiza deals no CRM.
     */
    public function processWebhook(array $payload): void
    {
        $entries = $payload['entry'] ?? [];

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];

            foreach ($changes as $change) {
                if (($change['field'] ?? '') !== 'messages') {
                    continue;
                }

                $value = $change['value'] ?? [];
                $this->processMessages($value);
            }
        }
    }

    /**
     * Processa o bloco "value" de cada change que contém mensagens.
     */
    private function processMessages(array $value): void
    {
        $contacts = $this->indexContacts($value['contacts'] ?? []);
        $messages = $value['messages'] ?? [];

        foreach ($messages as $message) {
            $this->processMessage($message, $contacts);
        }
    }

    /**
     * Indexa contatos por wa_id para lookup rápido.
     */
    private function indexContacts(array $contacts): array
    {
        $indexed = [];

        foreach ($contacts as $contact) {
            $waId = $contact['wa_id'] ?? null;
            if ($waId) {
                $indexed[$waId] = $contact['profile']['name'] ?? null;
            }
        }

        return $indexed;
    }

    /**
     * Processa uma mensagem individual do webhook.
     */
    private function processMessage(array $message, array $contacts): void
    {
        $waMessageId = $message['id'] ?? null;
        $fromPhone = $message['from'] ?? null;
        $messageType = $message['type'] ?? 'text';

        if (! $waMessageId || ! $fromPhone) {
            return;
        }

        if (WhatsAppMessage::where('wa_message_id', $waMessageId)->exists()) {
            return;
        }

        $fromName = $contacts[$fromPhone] ?? null;
        $messageBody = $this->extractMessageBody($message);
        $referral = $message['referral'] ?? null;

        $whatsappMessage = WhatsAppMessage::create([
            'wa_message_id' => $waMessageId,
            'from_phone' => $fromPhone,
            'from_name' => $fromName,
            'message_type' => $messageType,
            'message_body' => $messageBody,
            'referral_source' => $referral['source_type'] ?? null,
            'referral_headline' => $referral['headline'] ?? $referral['body'] ?? null,
            'raw_payload' => $message,
        ]);

        $deal = $this->findOrCreateDeal($fromPhone, $fromName, $referral);

        $whatsappMessage->update(['deal_id' => $deal->id]);

        $this->logMessageActivity($deal, $messageBody, $messageType, $fromName);

        Log::info('WhatsApp: mensagem processada', [
            'wa_message_id' => $waMessageId,
            'from' => $fromPhone,
            'deal_id' => $deal->id,
        ]);
    }

    /**
     * Extrai o corpo da mensagem baseado no tipo.
     */
    private function extractMessageBody(array $message): ?string
    {
        $type = $message['type'] ?? 'text';

        return match ($type) {
            'text' => $message['text']['body'] ?? null,
            'image' => $message['image']['caption'] ?? '[Imagem]',
            'video' => $message['video']['caption'] ?? '[Vídeo]',
            'audio' => '[Áudio]',
            'document' => $message['document']['filename'] ?? '[Documento]',
            'location' => '[Localização]',
            'sticker' => '[Figurinha]',
            'contacts' => '[Contato]',
            'button' => $message['button']['text'] ?? '[Botão]',
            'interactive' => $this->extractInteractiveBody($message),
            default => "[{$type}]",
        };
    }

    /**
     * Extrai corpo de mensagens interativas (botões de resposta rápida, listas).
     */
    private function extractInteractiveBody(array $message): string
    {
        $interactive = $message['interactive'] ?? [];
        $type = $interactive['type'] ?? '';

        return match ($type) {
            'button_reply' => $interactive['button_reply']['title'] ?? '[Resposta rápida]',
            'list_reply' => $interactive['list_reply']['title'] ?? '[Seleção de lista]',
            default => '[Interativo]',
        };
    }

    /**
     * Busca deal aberto para o telefone ou cria um novo.
     */
    private function findOrCreateDeal(string $phone, ?string $contactName, ?array $referral): Deal
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

        $customer = $this->findCustomerByPhone($phone);
        $assignedUser = $this->getDefaultAssignee();
        $defaultStage = PipelineStage::where('is_default', true)->first()
            ?? PipelineStage::active()->ordered()->first();

        if (! $defaultStage) {
            Log::error('WhatsApp: nenhum pipeline stage configurado');
            throw new \RuntimeException('Nenhum pipeline stage configurado.');
        }

        $title = $contactName
            ? "WhatsApp - {$contactName}"
            : "WhatsApp - {$this->formatPhone($phone)}";

        $sourceMetadata = null;
        if ($referral) {
            $sourceMetadata = [
                'source_type' => $referral['source_type'] ?? null,
                'source_id' => $referral['source_id'] ?? null,
                'source_url' => $referral['source_url'] ?? null,
                'headline' => $referral['headline'] ?? null,
                'body' => $referral['body'] ?? null,
                'media_type' => $referral['media_type'] ?? null,
            ];
        }

        $maxPosition = Deal::where('pipeline_stage_id', $defaultStage->id)->max('position') ?? 0;

        $deal = Deal::create([
            'user_id' => $assignedUser->id,
            'customer_id' => $customer?->id,
            'pipeline_stage_id' => $defaultStage->id,
            'title' => $title,
            'phone' => $phone,
            'description' => $referral
                ? "Lead via anúncio WhatsApp: " . ($referral['headline'] ?? 'campanha')
                : 'Lead captado via WhatsApp',
            'position' => $maxPosition + 1,
            'source' => 'whatsapp',
            'source_metadata' => $sourceMetadata,
        ]);

        $referralType = $referral['source_type'] ?? 'ad';

        $deal->activities()->create([
            'user_id' => $assignedUser->id,
            'type' => DealActivityType::Created->value,
            'description' => $referral
                ? "Lead criado automaticamente via anúncio WhatsApp ({$referralType})"
                : 'Lead criado automaticamente via WhatsApp',
            'metadata' => $sourceMetadata,
        ]);

        Log::info('WhatsApp: novo deal criado', [
            'deal_id' => $deal->id,
            'phone' => $phone,
            'source' => $referral ? 'ad' : 'organic',
        ]);

        return $deal;
    }

    /**
     * Registra atividade de mensagem recebida no deal.
     */
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
     * Busca customer pelo telefone (tentando várias formatações).
     */
    private function findCustomerByPhone(string $phone): ?Customer
    {
        $normalized = $this->normalizePhoneForSearch($phone);

        return Customer::where('phone', $phone)
            ->orWhere('phone', $normalized)
            ->orWhere('phone', 'LIKE', "%{$normalized}")
            ->first();
    }

    /**
     * Retorna o usuário admin padrão para atribuir deals de WhatsApp.
     */
    private function getDefaultAssignee(): User
    {
        return User::where('active', true)
            ->where('role', 'admin')
            ->first()
            ?? User::where('active', true)->first()
            ?? User::first();
    }

    /**
     * Remove código do país e formata para busca local.
     * WhatsApp envia: 5517999998888 → queremos: 17999998888
     */
    private function normalizePhoneForSearch(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '55') && strlen($digits) >= 12) {
            $digits = substr($digits, 2);
        }

        return $digits;
    }

    /**
     * Formata telefone para exibição.
     */
    private function formatPhone(string $phone): string
    {
        $digits = $this->normalizePhoneForSearch($phone);

        if (strlen($digits) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $digits);
        }

        return $phone;
    }
}
