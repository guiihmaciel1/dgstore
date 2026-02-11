<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Valuation\Services\MercadoLivreApiService;
use Illuminate\Console\Command;

class ScrapeOlxCommand extends Command
{
    protected $signature = 'valuation:scrape
                            {--model= : Slug do modelo específico (ex: iphone-15-pro-max)}';

    protected $description = 'Coleta anúncios de iPhones via API do Mercado Livre (catálogo de produtos)';

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

        if (! $this->mlApi->isConnected()) {
            $this->error('API do ML não conectada. Execute: php artisan valuation:ml-connect');

            return self::FAILURE;
        }

        $this->info('');
        $this->info('╔══════════════════════════════╗');
        $this->info('║      MERCADO LIVRE           ║');
        $this->info('╚══════════════════════════════╝');
        $this->newLine();

        $this->mlApi->onProgress($progressCallback);

        $total = 0;

        try {
            $result = $modelSlug
                ? $this->mlApi->scrapeBySlug($modelSlug)
                : $this->mlApi->scrapeAll();

            $total = $result['total_listings'];

            $this->newLine();
            $this->info("  Modelos processados: {$result['models_processed']}");
            $this->info("  Total anúncios: {$total}");

            if (! empty($result['errors'])) {
                $this->newLine();
                foreach ($result['errors'] as $error) {
                    $this->error("    - {$error}");
                }
            }
        } catch (\Throwable $e) {
            $this->error("  Erro fatal: {$e->getMessage()}");
        }

        $this->newLine();
        $this->info('══════════════════════════════════');
        $this->info("  Coleta finalizada! Total: {$total} anúncios");
        $this->info('══════════════════════════════════');

        return self::SUCCESS;
    }
}
