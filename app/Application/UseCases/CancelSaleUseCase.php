<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Finance\Services\FinanceService;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class CancelSaleUseCase
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly FinanceService $financeService,
    ) {}

    /**
     * Executa o cancelamento de uma venda
     * 
     * @throws Exception se a venda já estiver cancelada
     */
    public function execute(Sale $sale, ?string $reason = null): Sale
    {
        // 1. Validar se pode ser cancelada
        $this->validateCancellation($sale);

        // 2. Cancelar a venda (o repository cuida de devolver o estoque)
        $sale = $this->saleRepository->cancel($sale);

        // 3. Cancelar transações financeiras
        $this->cancelFinancialTransactions($sale);

        // 4. Se houver motivo, adiciona às notas
        if ($reason) {
            $notes = $sale->notes ?? '';
            $notes .= ($notes ? "\n" : '') . "[CANCELAMENTO] {$reason}";
            $sale->update(['notes' => $notes]);
        }

        return $sale;
    }

    /**
     * Cancela as transações financeiras vinculadas à venda.
     */
    private function cancelFinancialTransactions(Sale $sale): void
    {
        try {
            $this->financeService->cancelSaleTransactions($sale->id);
        } catch (\Throwable $e) {
            Log::warning("Não foi possível cancelar transações financeiras da venda #{$sale->sale_number}: {$e->getMessage()}");
        }
    }

    /**
     * Valida se a venda pode ser cancelada
     * 
     * @throws Exception
     */
    private function validateCancellation(Sale $sale): void
    {
        if ($sale->payment_status === PaymentStatus::Cancelled) {
            throw new Exception('Esta venda já está cancelada.');
        }
    }
}
