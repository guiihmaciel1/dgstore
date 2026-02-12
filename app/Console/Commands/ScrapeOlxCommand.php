<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Valuation\Services\MercadoLivreApiService;
use Illuminate\Console\Command;

class ScrapeOlxCommand extends Command
{
    protected $signature = 'valuation:scrape
                            {--model= : Slug do modelo específico (ex: iphone-15-pro-max)}';

    protected $description = 'Coleta preços de iPhones novos via catálogo do Mercado Livre';

    public function __construct(
        private readonly MercadoLivreApiService $mlApi,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $modelSlug = $this->option('model');

        $progressCallback = function (string $message, string $type = 'info') {
            match ($type) {
                'error' => $this->error($message),
                'warn' => $this->warn($message),
                default => $this->line($message),
            };
        };

        $this->mlApi->onProgress($progressCallback);

        if (! $this->mlApi->isConnected()) {
            $this->warn('  API do ML não conectada. Execute: php artisan valuation:ml-connect');

            return self::FAILURE;
        }

        $this->info('');
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║  MERCADO LIVRE — NOVOS (Catálogo)    ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        try {
            $result = $modelSlug
                ? $this->mlApi->scrapeBySlug($modelSlug)
                : $this->mlApi->scrapeAll();

            $this->newLine();
            $this->info("  Modelos: {$result['models_processed']} | Anúncios: {$result['total_listings']}");

            if (! empty($result['errors'])) {
                foreach ($result['errors'] as $error) {
                    $this->error("    - {$error}");
                }
            }
        } catch (\Throwable $e) {
            $this->error("  Erro: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('══════════════════════════════════════');
        $this->info("  Coleta finalizada! Total: {$result['total_listings']} anúncios");
        $this->info('══════════════════════════════════════');

        return self::SUCCESS;
    }
}
