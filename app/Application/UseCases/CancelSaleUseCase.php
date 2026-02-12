<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\CashRegister\Enums\CashEntryType;
use App\Domain\CashRegister\Models\CashRegisterEntry;
use App\Domain\CashRegister\Services\CashRegisterService;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class CancelSaleUseCase
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly CashRegisterService $cashRegisterService,
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

        // 3. Estornar lançamentos do caixa
        $this->reverseCashRegisterEntries($sale);

        // 4. Se houver motivo, adiciona às notas
        if ($reason) {
            $notes = $sale->notes ?? '';
            $notes .= ($notes ? "\n" : '') . "[CANCELAMENTO] {$reason}";
            $sale->update(['notes' => $notes]);
        }

        return $sale;
    }

    /**
     * Estorna os lançamentos do caixa relacionados a esta venda.
     */
    private function reverseCashRegisterEntries(Sale $sale): void
    {
        try {
            $register = $this->cashRegisterService->getOpenRegister();

            if (!$register) {
                return;
            }

            // Buscar lançamentos de venda vinculados
            $entries = CashRegisterEntry::where('reference_id', $sale->id)
                ->where('type', CashEntryType::Sale)
                ->get();

            foreach ($entries as $entry) {
                $this->cashRegisterService->addEntry(
                    register: $register,
                    userId: auth()->id() ?? $sale->user_id,
                    type: CashEntryType::Withdrawal,
                    amount: (float) $entry->amount,
                    description: "Estorno: Venda #{$sale->sale_number} cancelada",
                    paymentMethod: $entry->payment_method,
                    referenceId: $sale->id,
                );
            }

            // Estornar trade-in (se houver, devolver como suprimento)
            $tradeInEntries = CashRegisterEntry::where('reference_id', $sale->id)
                ->where('type', CashEntryType::TradeIn)
                ->get();

            foreach ($tradeInEntries as $entry) {
                $this->cashRegisterService->addEntry(
                    register: $register,
                    userId: auth()->id() ?? $sale->user_id,
                    type: CashEntryType::Supply,
                    amount: (float) $entry->amount,
                    description: "Estorno trade-in: Venda #{$sale->sale_number} cancelada",
                    referenceId: $sale->id,
                );
            }
        } catch (\Throwable $e) {
            Log::warning("Não foi possível estornar caixa da venda #{$sale->sale_number}: {$e->getMessage()}");
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
