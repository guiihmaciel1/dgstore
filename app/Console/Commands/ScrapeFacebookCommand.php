<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Valuation\Services\FacebookMarketplaceService;
use Illuminate\Console\Command;

class ScrapeFacebookCommand extends Command
{
    protected $signature = 'valuation:scrape-facebook
                            {--model= : Slug do modelo específico (ex: iphone-15-pro-max)}
                            {--location= : Cidade para buscar (ex: São José do Rio Preto)}
                            {--pages=2 : Número de páginas de resultados (1 página = ~24 anúncios)}
                            {--test : Faz um teste de diagnóstico para verificar se o endpoint funciona}';

    protected $description = 'Coleta anúncios de iPhones usados no Facebook Marketplace via GraphQL';

    public function __construct(
        private readonly FacebookMarketplaceService $fbService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('test')) {
            return $this->runDiagnostic();
        }

        $modelSlug = $this->option('model');
        $location = $this->option('location') ?: config('services.facebook_marketplace.default_location', 'São José do Rio Preto');
        $pages = (int) $this->option('pages');

        $progressCallback = function (string $message, string $type = 'info') {
            match ($type) {
                'error' => $this->error($message),
                'warn' => $this->warn($message),
                default => $this->line($message),
            };
        };

        $this->info('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║      FACEBOOK MARKETPLACE                ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->newLine();
        $this->info("  Cidade: {$location}");
        $this->info("  Páginas: {$pages}");
        $this->newLine();

        $this->fbService->onProgress($progressCallback);

        $total = 0;

        try {
            $result = $modelSlug
                ? $this->fbService->scrapeBySlug($modelSlug, $location, $pages)
                : $this->fbService->scrapeAll($location, $pages);

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
        $this->info('══════════════════════════════════════════════');
        $this->info("  Coleta finalizada! Total: {$total} anúncios");
        $this->info('══════════════════════════════════════════════');

        return self::SUCCESS;
    }

    private function runDiagnostic(): int
    {
        $this->info('=== Diagnóstico Facebook Marketplace GraphQL ===');
        $this->newLine();

        $diagnostic = $this->fbService->diagnose();

        foreach ($diagnostic['steps'] as $step) {
            $icon = $step['success'] ? '✓' : '✗';
            $this->line("  {$icon} {$step['label']}");

            if (! empty($step['details'])) {
                foreach ($step['details'] as $detail) {
                    $this->line("    {$detail}");
                }
            }
        }

        $this->newLine();
        $this->info('=== Diagnóstico concluído ===');

        return self::SUCCESS;
    }
}
