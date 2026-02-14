<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Finance\Services\FinanceService;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\DTOs\SaleData;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Stock\Services\StockService;
use Exception;
use Illuminate\Support\Facades\Log;

class CreateSaleUseCase
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StockService $stockService,
        private readonly FinanceService $financeService,
    ) {}

    /**
     * Executa a criação de uma nova venda
     * 
     * @throws Exception se não houver estoque suficiente
     */
    public function execute(SaleData $data): Sale
    {
        // 1. Validar disponibilidade de estoque
        $this->validateStockAvailability($data);

        // 2. Criar a venda (o repository já cuida de criar items e movimentar estoque)
        $sale = $this->saleRepository->create($data);

        // 3. Registrar automaticamente no financeiro
        $this->registerInFinance($sale);

        return $sale;
    }

    /**
     * Registra a venda no módulo financeiro.
     * Não lança exceção para não bloquear a venda.
     */
    private function registerInFinance(Sale $sale): void
    {
        try {
            $description = "Venda #{$sale->sale_number}";

            // Registrar parcela à vista (dinheiro/PIX)
            if ((float) $sale->cash_payment > 0) {
                $method = $sale->cash_payment_method === 'pix' ? 'PIX' : 'Dinheiro';
                $this->financeService->registerSaleIncome(
                    userId: $sale->user_id,
                    amount: (float) $sale->cash_payment,
                    description: "{$description} ({$method})",
                    referenceId: $sale->id,
                    paymentMethod: $sale->cash_payment_method,
                );
            }

            // Registrar parcela no cartão
            if ((float) $sale->card_payment > 0) {
                $methodLabel = $sale->payment_method?->label() ?? 'Cartão';
                $installmentInfo = $sale->installments > 1 ? " {$sale->installments}x" : '';
                $this->financeService->registerSaleIncome(
                    userId: $sale->user_id,
                    amount: (float) $sale->card_payment,
                    description: "{$description} ({$methodLabel}{$installmentInfo})",
                    referenceId: $sale->id,
                    paymentMethod: $sale->payment_method?->value,
                );
            }

            // Se não for pagamento misto, registrar valor total com método principal
            if ((float) $sale->cash_payment <= 0 && (float) $sale->card_payment <= 0) {
                $this->financeService->registerSaleIncome(
                    userId: $sale->user_id,
                    amount: (float) $sale->total,
                    description: $description,
                    referenceId: $sale->id,
                    paymentMethod: $sale->payment_method?->value,
                );
            }

            // Registrar custo dos produtos (CMV)
            $sale->load('items');
            $totalCost = $sale->total_cost;
            if ($totalCost > 0) {
                $this->financeService->registerSaleCost(
                    userId: $sale->user_id,
                    amount: $totalCost,
                    description: "{$description} (Custo)",
                    referenceId: $sale->id,
                );
            }

            // Registrar trade-in como despesa
            if ((float) $sale->trade_in_value > 0) {
                $this->financeService->registerTradeInExpense(
                    userId: $sale->user_id,
                    amount: (float) $sale->trade_in_value,
                    description: "{$description} (Trade-in)",
                    referenceId: $sale->id,
                );
            }
        } catch (\Throwable $e) {
            Log::warning("Não foi possível registrar venda #{$sale->sale_number} no financeiro: {$e->getMessage()}");
        }
    }

    /**
     * Valida se há estoque disponível para todos os itens
     * 
     * @throws Exception
     */
    private function validateStockAvailability(SaleData $data): void
    {
        $unavailableItems = [];

        foreach ($data->items as $item) {
            $product = $this->productRepository->find($item->productId);

            if (!$product) {
                $unavailableItems[] = [
                    'product_id' => $item->productId,
                    'message' => 'Produto não encontrado',
                ];
                continue;
            }

            if (!$product->active) {
                $unavailableItems[] = [
                    'product_id' => $item->productId,
                    'product_name' => $product->name,
                    'message' => 'Produto inativo',
                ];
                continue;
            }

            if ($product->stock_quantity < $item->quantity) {
                $unavailableItems[] = [
                    'product_id' => $item->productId,
                    'product_name' => $product->name,
                    'requested' => $item->quantity,
                    'available' => $product->stock_quantity,
                    'message' => "Estoque insuficiente. Disponível: {$product->stock_quantity}",
                ];
            }
        }

        if (!empty($unavailableItems)) {
            throw new Exception(
                'Não foi possível completar a venda. Verifique o estoque dos produtos: ' .
                collect($unavailableItems)->pluck('message')->implode('; ')
            );
        }
    }
}
