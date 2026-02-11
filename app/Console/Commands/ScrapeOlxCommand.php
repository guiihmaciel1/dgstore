<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Valuation\Services\OlxScraperService;
use Illuminate\Console\Command;

class ScrapeOlxCommand extends Command
{
    protected $signature = 'valuation:scrape-olx
                            {--model= : Slug do modelo específico para raspar (ex: iphone-15-pro-max)}';

    protected $description = 'Coleta anúncios de iPhones no OLX para cálculo de preços de mercado';

    public function __construct(
        private readonly OlxScraperService $scraperService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $modelSlug = $this->option('model');

        $this->info('Iniciando coleta de anúncios do OLX...');
        $this->newLine();

        try {
            $result = $modelSlug
                ? $this->scraperService->scrapeBySlug($modelSlug)
                : $this->scraperService->scrapeAll();

            $this->info("Modelos processados: {$result['models_processed']}");
            $this->info("Anúncios coletados: {$result['total_listings']}");

            if (!empty($result['errors'])) {
                $this->newLine();
                $this->warn('Erros encontrados:');
                foreach ($result['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }

            $this->newLine();
            $this->info('Coleta finalizada com sucesso!');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Erro fatal: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
