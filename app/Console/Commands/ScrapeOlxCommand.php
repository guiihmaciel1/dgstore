<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Valuation\Services\MercadoLivreApiService;
use App\Domain\Valuation\Services\MercadoLivreScraperService;
use App\Domain\Valuation\Services\OlxScraperService;
use Illuminate\Console\Command;

class ScrapeOlxCommand extends Command
{
    protected $signature = 'valuation:scrape
                            {--model= : Slug do modelo específico (ex: iphone-15-pro-max)}
                            {--source= : Forçar fonte: api, scraper, olx}';

    protected $description = 'Coleta anúncios de iPhones via API do ML, scraping ou OLX';

    public function __construct(
        private readonly MercadoLivreApiService $mlApi,
        private readonly MercadoLivreScraperService $mlScraper,
        private readonly OlxScraperService $olxScraper,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $modelSlug = $this->option('model');
        $forceSource = $this->option('source');
        $grandTotal = 0;

        $progressCallback = function (string $message, string $type = 'info') {
            match ($type) {
                'error' => $this->error($message),
                'warn' => $this->warn($message),
                default => $this->line($message),
            };
        };

        // === MERCADO LIVRE ===
        $this->info('');
        $this->info('╔══════════════════════════════╗');
        $this->info('║      MERCADO LIVRE           ║');
        $this->info('╚══════════════════════════════╝');
        $this->newLine();

        try {
            $mlTotal = $this->scrapeMercadoLivre($modelSlug, $forceSource, $progressCallback);
            $grandTotal += $mlTotal;
        } catch (\Throwable $e) {
            $this->error("  Erro fatal ML: {$e->getMessage()}");
        }

        $this->newLine();

        // === OLX ===
        if (!$forceSource || $forceSource === 'olx') {
            $this->info('╔══════════════════════════════╗');
            $this->info('║           OLX                ║');
            $this->info('╚══════════════════════════════╝');
            $this->newLine();

            $this->olxScraper->onProgress($progressCallback);

            try {
                $olxResult = $modelSlug
                    ? $this->olxScraper->scrapeBySlug($modelSlug)
                    : $this->olxScraper->scrapeAll();

                $this->newLine();
                $this->info("  Modelos processados: {$olxResult['models_processed']}");
                $this->info("  Total anúncios: {$olxResult['total_listings']}");
                $grandTotal += $olxResult['total_listings'];

                if (!empty($olxResult['errors'])) {
                    foreach ($olxResult['errors'] as $error) {
                        $this->error("    - {$error}");
                    }
                }
            } catch (\Throwable $e) {
                $this->error("  Erro fatal OLX: {$e->getMessage()}");
            }

            $this->newLine();
        }

        $this->info('══════════════════════════════════');
        $this->info("  Coleta finalizada! Total: {$grandTotal} anúncios");
        $this->info('══════════════════════════════════');

        return self::SUCCESS;
    }

    /**
     * Prioridade: API > Scraper (proxy/direto).
     */
    private function scrapeMercadoLivre(
        ?string $modelSlug,
        ?string $forceSource,
        \Closure $progressCallback,
    ): int {
        // 1) API oficial (se conectada)
        if ((!$forceSource || $forceSource === 'api') && $this->mlApi->isConnected()) {
            $this->mlApi->onProgress($progressCallback);

            $result = $modelSlug
                ? $this->mlApi->scrapeBySlug($modelSlug)
                : $this->mlApi->scrapeAll();

            $this->newLine();
            $this->info("  Modelos processados: {$result['models_processed']}");
            $this->info("  Total anúncios: {$result['total_listings']}");

            if (!empty($result['errors'])) {
                foreach ($result['errors'] as $error) {
                    $this->error("    - {$error}");
                }
            }

            return $result['total_listings'];
        }

        if ($forceSource === 'api') {
            $this->warn('  API do ML não conectada. Execute: php artisan valuation:ml-connect');
            return 0;
        }

        // 2) Fallback: Scraper (proxy ou direto)
        $this->mlScraper->onProgress($progressCallback);

        $result = $modelSlug
            ? $this->mlScraper->scrapeBySlug($modelSlug)
            : $this->mlScraper->scrapeAll();

        $this->newLine();
        $this->info("  Modelos processados: {$result['models_processed']}");
        $this->info("  Total anúncios: {$result['total_listings']}");

        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $error) {
                $this->error("    - {$error}");
            }
        }

        return $result['total_listings'];
    }
}
