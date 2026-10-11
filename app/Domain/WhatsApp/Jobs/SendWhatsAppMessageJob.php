<?php

namespace App\Domain\WhatsApp\Jobs;

use App\Domain\WhatsApp\Services\UazapiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public string $phone,
        public string $message,
        public bool $useAsync = true,
    ) {}

    public function handle(UazapiService $uazapi): void
    {
        try {
            if ($this->useAsync) {
                $uazapi->sendAsync($this->phone, $this->message);
            } else {
                $uazapi->sendText($this->phone, $this->message);
            }

            Log::debug('SendWhatsAppMessageJob: enviado', [
                'phone' => $this->phone,
                'async' => $this->useAsync,
            ]);
        } catch (\Throwable $e) {
            Log::error('SendWhatsAppMessageJob: falha', [
                'phone' => $this->phone,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
