<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Finance\Services\FinanceService;
use Illuminate\Console\Command;

class MarkOverdueTransactionsCommand extends Command
{
    protected $signature = 'finance:mark-overdue';
    protected $description = 'Marca transações financeiras vencidas como overdue';

    public function handle(FinanceService $financeService): int
    {
        $count = $financeService->markOverdueTransactions();

        if ($count > 0) {
            $this->info("{$count} transação(ões) marcada(s) como vencida(s).");
        } else {
            $this->info('Nenhuma transação pendente vencida encontrada.');
        }

        return self::SUCCESS;
    }
}
