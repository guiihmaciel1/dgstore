<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use Exception;

class CancelSaleUseCase
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository
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

        // 3. Se houver motivo, adiciona às notas
        if ($reason) {
            $notes = $sale->notes ?? '';
            $notes .= ($notes ? "\n" : '') . "[CANCELAMENTO] {$reason}";
            $sale->update(['notes' => $notes]);
        }

        return $sale;
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
