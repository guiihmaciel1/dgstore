<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Valuation\Services\PriceCalculatorService;
use Illuminate\Console\Command;

class CalculateAveragesCommand extends Command
{
    protected $signature = 'valuation:calculate-averages';

    protected $description = 'Calcula médias de preços de mercado a partir dos anúncios coletados';

    public function __construct(
        private readonly PriceCalculatorService $calculatorService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Calculando médias de preços de mercado...');
        $this->newLine();

        try {
            $result = $this->calculatorService->calculateAll();

            $this->info("Médias calculadas: {$result['total_calculated']}");

            if (!empty($result['errors'])) {
                $this->newLine();
                $this->warn('Erros encontrados:');
                foreach ($result['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }

            $this->newLine();
            $this->info('Cálculo de médias finalizado!');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Erro fatal: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
