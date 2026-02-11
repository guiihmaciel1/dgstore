<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Valuation\Services\MercadoLivreApiService;
use Illuminate\Console\Command;

class ScrapeOlxCommand extends Command
{
    protected $signature = 'valuation:scrape
                            {--model= : Slug do modelo específico (ex: iphone-15-pro-max)}
                            {--used : Buscar anúncios de iPhones USADOS via proxy (ao invés de novos via catálogo)}
                            {--all : Buscar tanto novos (catálogo) quanto usados (proxy)}';

    protected $description = 'Coleta anúncios de iPhones via API do Mercado Livre';

    public function __construct(
        private readonly MercadoLivreApiService $mlApi,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $modelSlug = $this->option('model');
        $usedOnly = $this->option('used');
        $both = $this->option('all');

        $progressCallback = function (string $message, string $type = 'info') {
            match ($type) {
                'error' => $this->error($message),
                'warn' => $this->warn($message),
                default => $this->line($message),
            };
        };

        $this->mlApi->onProgress($progressCallback);

        $totalNew = 0;
        $totalUsed = 0;

        // ── Novos (catálogo) ──
        if (! $usedOnly || $both) {
            if (! $this->mlApi->isConnected()) {
                $this->warn('  API do ML não conectada (novos). Execute: php artisan valuation:ml-connect');
            } else {
                $this->info('');
                $this->info('╔══════════════════════════════════════╗');
                $this->info('║  MERCADO LIVRE — NOVOS (Catálogo)    ║');
                $this->info('╚══════════════════════════════════════╝');
                $this->newLine();

                try {
                    $result = $modelSlug
                        ? $this->mlApi->scrapeBySlug($modelSlug)
                        : $this->mlApi->scrapeAll();

                    $totalNew = $result['total_listings'];

                    $this->newLine();
                    $this->info("  Modelos: {$result['models_processed']} | Anúncios novos: {$totalNew}");

                    if (! empty($result['errors'])) {
                        foreach ($result['errors'] as $error) {
                            $this->error("    - {$error}");
                        }
                    }
                } catch (\Throwable $e) {
                    $this->error("  Erro (novos): {$e->getMessage()}");
                }
            }
        }

        // ── Usados (proxy) ──
        if ($usedOnly || $both) {
            $this->info('');
            $this->info('╔══════════════════════════════════════╗');
            $this->info('║  MERCADO LIVRE — USADOS (via Proxy)  ║');
            $this->info('╚══════════════════════════════════════╝');
            $this->newLine();

            try {
                $result = $modelSlug
                    ? $this->mlApi->scrapeUsedBySlug($modelSlug)
                    : $this->mlApi->scrapeUsedAll();

                $totalUsed = $result['total_listings'];

                $this->newLine();
                $this->info("  Modelos: {$result['models_processed']} | Anúncios usados: {$totalUsed}");

                if (! empty($result['errors'])) {
                    foreach ($result['errors'] as $error) {
                        $this->error("    - {$error}");
                    }
                }
            } catch (\Throwable $e) {
                $this->error("  Erro (usados): {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info('══════════════════════════════════════');
        $total = $totalNew + $totalUsed;
        if ($totalNew > 0 && $totalUsed > 0) {
            $this->info("  Coleta finalizada! Novos: {$totalNew} | Usados: {$totalUsed} | Total: {$total}");
        } else {
            $this->info("  Coleta finalizada! Total: {$total} anúncios");
        }
        $this->info('══════════════════════════════════════');

        return self::SUCCESS;
    }
}
