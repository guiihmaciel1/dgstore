<?php

namespace App\Domain\WhatsApp\Jobs;

use App\Domain\WhatsApp\Models\WhatsAppCampaign;
use App\Domain\WhatsApp\Services\CampaignService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCampaignBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public int $campaignId,
        public int $batchSize = 10,
    ) {}

    public function handle(CampaignService $service): void
    {
        $campaign = WhatsAppCampaign::find($this->campaignId);

        if (!$campaign || $campaign->status === 'completed') {
            return;
        }

        $result = $service->send($campaign, $this->batchSize);

        Log::info('CampaignBatch: batch processado', [
            'campaign_id' => $this->campaignId,
            'sent' => $result['sent'] ?? 0,
            'failed' => $result['failed'] ?? 0,
            'remaining' => $result['remaining'] ?? 0,
        ]);

        if (($result['remaining'] ?? 0) > 0) {
            self::dispatch($this->campaignId, $this->batchSize)
                ->delay(now()->addSeconds(30));
        }
    }
}
