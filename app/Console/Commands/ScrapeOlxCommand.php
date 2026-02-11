<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Valuation\Services\MercadoLivreApiService;
use App\Domain\Valuation\Services\MercadoLivreScraperService;
use Illuminate\Console\Command;

class ScrapeOlxCommand extends Command
{
    protected $signature = 'valuation:scrape
                            {--model= : Slug do modelo específico (ex: iphone-15-pro-max)}
                            {--source= : Forçar fonte: api, scraper}';

    protected $description = 'Coleta anúncios de iPhones via API ou scraping do Mercado Livre';

    public function __construct(
        private readonly MercadoLivreApiService $mlApi,
        private readonly MercadoLivreScraperService $mlScraper,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $modelSlug = $this->option('model');
        $forceSource = $this->option('source');

        $progressCallback = function (string $message, string $type = 'info') {
            match ($type) {
                'error' => $this->error($message),
                'warn' => $this->warn($message),
                default => $this->line($message),
            };
        };

        $this->info('');
        $this->info('╔══════════════════════════════╗');
        $this->info('║      MERCADO LIVRE           ║');
        $this->info('╚══════════════════════════════╝');
        $this->newLine();

        $total = 0;

        try {
            $total = $this->scrapeMercadoLivre($modelSlug, $forceSource, $progressCallback);
        } catch (\Throwable $e) {
            $this->error("  Erro fatal: {$e->getMessage()}");
        }

        $this->newLine();
        $this->info('══════════════════════════════════');
        $this->info("  Coleta finalizada! Total: {$total} anúncios");
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
