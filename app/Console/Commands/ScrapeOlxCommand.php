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

        // === Mercado Livre ===
        $this->info('=== Mercado Livre ===');
        $this->newLine();

        try {
            $mlResult = $modelSlug
                ? $this->mlScraper->scrapeBySlug($modelSlug)
                : $this->mlScraper->scrapeAll();

            $this->info("Modelos processados: {$mlResult['models_processed']}");
            $this->info("Anúncios coletados: {$mlResult['total_listings']}");
            $grandTotal += $mlResult['total_listings'];

            if (!empty($mlResult['errors'])) {
                $this->warn('Erros:');
                foreach ($mlResult['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }
        } catch (\Throwable $e) {
            $this->error("Erro no Mercado Livre: {$e->getMessage()}");
        }

        $this->newLine();

        // === OLX ===
        $this->info('=== OLX ===');
        $this->newLine();

        try {
            $olxResult = $modelSlug
                ? $this->olxScraper->scrapeBySlug($modelSlug)
                : $this->olxScraper->scrapeAll();

            $this->info("Modelos processados: {$olxResult['models_processed']}");
            $this->info("Anúncios coletados: {$olxResult['total_listings']}");
            $grandTotal += $olxResult['total_listings'];

            if (!empty($olxResult['errors'])) {
                $this->warn('Erros:');
                foreach ($olxResult['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }
        } catch (\Throwable $e) {
            $this->error("Erro no OLX: {$e->getMessage()}");
        }

        $this->newLine();
        $this->info("=== Coleta finalizada! Total geral: {$grandTotal} anúncios ===");

        return self::SUCCESS;
    }
}
