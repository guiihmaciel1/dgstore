<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Valuation\Services\MercadoLivreScraperService;
use App\Domain\Valuation\Services\OlxScraperService;
use Illuminate\Console\Command;

class ScrapeOlxCommand extends Command
{
    protected $signature = 'valuation:scrape
                            {--model= : Slug do modelo específico para raspar (ex: iphone-15-pro-max)}';

    protected $description = 'Coleta anúncios de iPhones no Mercado Livre e OLX para cálculo de preços de mercado';

    public function __construct(
        private readonly MercadoLivreScraperService $mlScraper,
        private readonly OlxScraperService $olxScraper,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $modelSlug = $this->option('model');
        $grandTotal = 0;

        // Configura callbacks de progresso
        $progressCallback = function (string $message, string $type = 'info') {
            match ($type) {
                'error' => $this->error($message),
                'warn' => $this->warn($message),
                default => $this->line($message),
            };
        };

        $this->mlScraper->onProgress($progressCallback);
        $this->olxScraper->onProgress($progressCallback);

        // === Mercado Livre ===
        $this->info('');
        $this->info('╔══════════════════════════════╗');
        $this->info('║      MERCADO LIVRE           ║');
        $this->info('╚══════════════════════════════╝');
        $this->newLine();

        try {
            $mlResult = $modelSlug
                ? $this->mlScraper->scrapeBySlug($modelSlug)
                : $this->mlScraper->scrapeAll();

            $this->newLine();
            $this->info("  Modelos processados: {$mlResult['models_processed']}");
            $this->info("  Total anúncios: {$mlResult['total_listings']}");
            $grandTotal += $mlResult['total_listings'];

            if (!empty($mlResult['errors'])) {
                $this->warn('  Erros:');
                foreach ($mlResult['errors'] as $error) {
                    $this->error("    - {$error}");
                }
            }
        } catch (\Throwable $e) {
            $this->error("  Erro fatal no Mercado Livre: {$e->getMessage()}");
        }

        $this->newLine();

        // === OLX ===
        $this->info('╔══════════════════════════════╗');
        $this->info('║           OLX                ║');
        $this->info('╚══════════════════════════════╝');
        $this->newLine();

        try {
            $olxResult = $modelSlug
                ? $this->olxScraper->scrapeBySlug($modelSlug)
                : $this->olxScraper->scrapeAll();

            $this->newLine();
            $this->info("  Modelos processados: {$olxResult['models_processed']}");
            $this->info("  Total anúncios: {$olxResult['total_listings']}");
            $grandTotal += $olxResult['total_listings'];

            if (!empty($olxResult['errors'])) {
                $this->warn('  Erros:');
                foreach ($olxResult['errors'] as $error) {
                    $this->error("    - {$error}");
                }
            }
        } catch (\Throwable $e) {
            $this->error("  Erro fatal no OLX: {$e->getMessage()}");
        }

        $this->newLine();
        $this->info("══════════════════════════════════");
        $this->info("  Coleta finalizada! Total: {$grandTotal} anúncios");
        $this->info("══════════════════════════════════");

        return self::SUCCESS;
    }
}
