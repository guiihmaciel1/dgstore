<?php

namespace App\Console\Commands;

use App\Domain\WhatsApp\Services\MediaDownloadService;
use Illuminate\Console\Command;

class ProcessPendingMedia extends Command
{
    protected $signature = 'whatsapp:process-media {--limit=20}';
    protected $description = 'Processa download de mídias WhatsApp pendentes';

    public function handle(MediaDownloadService $service): int
    {
        $limit = (int) $this->option('limit');
        $processed = $service->processPending($limit);

        $this->info("Processadas {$processed} mídias pendentes.");

        return self::SUCCESS;
    }
}
