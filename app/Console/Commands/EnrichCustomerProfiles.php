<?php

namespace App\Console\Commands;

use App\Domain\WhatsApp\Services\CustomerEnrichmentService;
use Illuminate\Console\Command;

class EnrichCustomerProfiles extends Command
{
    protected $signature = 'whatsapp:enrich-customers {--limit=30}';
    protected $description = 'Busca avatares e dados business dos clientes via WhatsApp';

    public function handle(CustomerEnrichmentService $service): int
    {
        $limit = (int) $this->option('limit');
        $processed = $service->processUnprocessed($limit);

        $this->info("Enriquecidos {$processed} clientes.");

        return self::SUCCESS;
    }
}
