<?php

namespace App\Console\Commands;

use App\Domain\WhatsApp\Services\LabelSyncService;
use Illuminate\Console\Command;

class SyncWhatsAppLabels extends Command
{
    protected $signature = 'whatsapp:sync-labels {--import : Importa labels do WhatsApp para o CRM}';
    protected $description = 'Sincroniza etiquetas entre CRM e WhatsApp';

    public function handle(LabelSyncService $service): int
    {
        if ($this->option('import')) {
            $result = $service->importFromWhatsApp();

            if (isset($result['error'])) {
                $this->error("Erro: {$result['error']}");
                return self::FAILURE;
            }

            $this->info('Etiquetas importadas: ' . implode(', ', $result['imported'] ?? []));
            return self::SUCCESS;
        }

        $this->info('Use --import para importar labels do WhatsApp.');
        return self::SUCCESS;
    }
}
