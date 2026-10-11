<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\Customer\Models\Customer;
use App\Domain\WhatsApp\Models\CampaignRecipient;
use App\Domain\WhatsApp\Models\WhatsAppCampaign;
use Illuminate\Support\Facades\Log;

class CampaignService
{
    private UazapiService $uazapi;

    public function __construct(UazapiService $uazapi)
    {
        $this->uazapi = $uazapi;
    }

    /**
     * Popula a lista de destinatários da campanha com base nas tags selecionadas.
     */
    public function populateRecipients(WhatsAppCampaign $campaign): int
    {
        $tagIds = $campaign->tags()->pluck('customer_tags.id');

        $customers = Customer::whereHas('tags', fn ($q) => $q->whereIn('customer_tags.id', $tagIds))
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        $count = 0;
        foreach ($customers as $customer) {
            $exists = $campaign->recipients()
                ->where('customer_id', $customer->id)
                ->exists();

            if (!$exists) {
                $campaign->recipients()->create([
                    'customer_id' => $customer->id,
                    'phone' => $customer->phone,
                    'status' => 'pending',
                ]);
                $count++;
            }
        }

        $campaign->update(['total_recipients' => $campaign->recipients()->count()]);

        return $count;
    }

    /**
     * Executa o envio da campanha (batch).
     */
    public function send(WhatsAppCampaign $campaign, int $batchSize = 10): array
    {
        if (!$this->uazapi->isConfigured()) {
            return ['error' => 'UazAPI não configurada'];
        }

        $campaign->update([
            'status' => 'sending',
            'started_at' => $campaign->started_at ?? now(),
        ]);

        $pending = $campaign->recipients()
            ->where('status', 'pending')
            ->limit($batchSize)
            ->get();

        $sent = 0;
        $failed = 0;

        foreach ($pending as $recipient) {
            try {
                if ($campaign->media_url) {
                    $this->uazapi->sendImage($recipient->phone, $campaign->media_url, $campaign->message);
                } else {
                    $this->uazapi->sendText($recipient->phone, $campaign->message);
                }

                $recipient->update(['status' => 'sent', 'sent_at' => now()]);
                $sent++;

                usleep(1500000);
            } catch (\Throwable $e) {
                $recipient->update([
                    'status' => 'failed',
                    'error' => mb_substr($e->getMessage(), 0, 500),
                ]);
                $failed++;
                Log::warning('Campaign send error', [
                    'campaign_id' => $campaign->id,
                    'recipient_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $campaign->increment('sent_count', $sent);
        $campaign->increment('failed_count', $failed);

        $remaining = $campaign->recipients()->where('status', 'pending')->count();
        if ($remaining === 0) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'remaining' => $remaining,
        ];
    }
}
