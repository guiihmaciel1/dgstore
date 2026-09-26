<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\PurchaseRequest\Services\PurchaseRequestService;
use Illuminate\Console\Command;

class ExpirePurchaseRequestsCommand extends Command
{
    protected $signature = 'purchase-requests:expire';
    protected $description = 'Expira solicitações de compra pendentes que ultrapassaram o prazo de 48h';

    public function handle(PurchaseRequestService $service): int
    {
        $count = $service->processExpired();

        if ($count > 0) {
            $this->info("{$count} solicitação(ões) de compra expirada(s).");
        } else {
            $this->info('Nenhuma solicitação expirada encontrada.');
        }

        return self::SUCCESS;
    }
}
