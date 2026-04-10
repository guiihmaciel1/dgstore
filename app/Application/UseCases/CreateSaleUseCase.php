<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Commission\Models\Commission;
use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Finance\Services\FinanceService;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\DTOs\SaleData;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Stock\Services\StockService;
use App\Domain\User\Enums\UserRole;
use App\Domain\Warranty\Services\WarrantyService;
use Exception;
use Illuminate\Support\Facades\Log;

class CreateSaleUseCase
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StockService $stockService,
        private readonly FinanceService $financeService,
        private readonly WarrantyService $warrantyService,
    ) {}

    /**
     * Executa a criação de uma nova venda
     * 
     * @throws Exception se não houver estoque suficiente
     */
    public function execute(SaleData $data): Sale
    {
        $this->validateStockAvailability($data);

        $sale = $this->saleRepository->create($data);

        $this->registerWarranties($sale);
        $this->registerInFinance($sale);
        $this->registerCommission($sale);

        return $sale;
    }

    /**
     * Registra garantia de 3 meses (cliente) para itens seminovos (condition=used).
     * Não lança exceção para não bloquear a venda.
     */
    private function registerWarranties(Sale $sale): void
    {
        try {
            foreach ($sale->items as $item) {
                $condition = $item->product_snapshot['condition'] ?? null;

                if ($condition === 'used') {
                    $this->warrantyService->createFromSaleItem(
                        saleItem: $item,
                        supplierMonths: 0,
                        customerMonths: 3,
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Não foi possível registrar garantias da venda #{$sale->sale_number}: {$e->getMessage()}");
        }
    }

    /**
     * Registra a venda no módulo financeiro.
     * Não lança exceção para não bloquear a venda.
     */
    private function registerInFinance(Sale $sale): void
    {
        try {
            $description = "Venda #{$sale->sale_number}";
            $saleDate = $sale->sold_at ?? now();

            // Registrar parcela em dinheiro
            if ((float) $sale->cash_payment > 0) {
                $this->financeService->registerSaleIncome(
                    userId: $sale->user_id,
                    amount: (float) $sale->cash_payment,
                    description: "{$description} (Dinheiro)",
                    referenceId: $sale->id,
                    paymentMethod: 'cash',
                    date: $saleDate,
                );
            }

            // Registrar parcela em PIX
            if ((float) $sale->pix_payment > 0) {
                $this->financeService->registerSaleIncome(
                    userId: $sale->user_id,
                    amount: (float) $sale->pix_payment,
                    description: "{$description} (PIX)",
                    referenceId: $sale->id,
                    paymentMethod: 'pix',
                    date: $saleDate,
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
                    date: $saleDate,
                );
            }

            // Se não for pagamento misto, registrar valor total com método principal
            if ((float) $sale->cash_payment <= 0 && (float) $sale->pix_payment <= 0 && (float) $sale->card_payment <= 0) {
                $this->financeService->registerSaleIncome(
                    userId: $sale->user_id,
                    amount: (float) $sale->total,
                    description: $description,
                    referenceId: $sale->id,
                    paymentMethod: $sale->payment_method?->value,
                    date: $saleDate,
                );
            }

            // Registrar custo dos produtos (CMV) — contábil, não movimenta caixa
            $sale->load('items');
            $totalCost = $sale->total_cost;
            if ($totalCost > 0) {
                $this->financeService->registerSaleCost(
                    userId: $sale->user_id,
                    amount: $totalCost,
                    description: "{$description} (Custo)",
                    referenceId: $sale->id,
                    date: $saleDate,
                );
            }

            // Registrar trade-in — contábil, não movimenta caixa
            if ((float) $sale->trade_in_value > 0) {
                $this->financeService->registerTradeInExpense(
                    userId: $sale->user_id,
                    amount: (float) $sale->trade_in_value,
                    description: "{$description} (Trade-in)",
                    referenceId: $sale->id,
                    date: $saleDate,
                );
            }
        } catch (\Throwable $e) {
            Log::warning("Não foi possível registrar venda #{$sale->sale_number} no financeiro: {$e->getMessage()}");
        }
    }

    private function registerCommission(Sale $sale): void
    {
        try {
            $user = $sale->user;

            if (!$user || $user->role !== UserRole::Intern) {
                return;
            }

            $rate = (float) $user->commission_rate;
            if ($rate <= 0) {
                return;
            }

            $type = $user->commission_type ?? 'percentage';
            $amount = $type === 'fixed'
                ? $rate
                : round((float) $sale->total * $rate / 100, 2);

            if ($amount <= 0) {
                return;
            }

            Commission::create([
                'user_id' => $user->id,
                'sale_id' => $sale->id,
                'sale_number' => $sale->sale_number,
                'sale_total' => $sale->total,
                'commission_rate' => $rate,
                'commission_type' => $type,
                'commission_amount' => $amount,
                'status' => 'approved',
            ]);
        } catch (\Throwable $e) {
            Log::warning("Não foi possível registrar comissão da venda #{$sale->sale_number}: {$e->getMessage()}");
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
            if ($item->isConsignment()) {
                $consignmentItem = ConsignmentStockItem::find($item->consignmentItemId);

                if (!$consignmentItem) {
                    $unavailableItems[] = ['message' => 'Item consignado não encontrado'];
                    continue;
                }

                if (!$consignmentItem->isAvailable()) {
                    $unavailableItems[] = [
                        'message' => "Item consignado '{$consignmentItem->name}' não está disponível",
                    ];
                    continue;
                }

                if ($consignmentItem->available_quantity < $item->quantity) {
                    $unavailableItems[] = [
                        'message' => "Item consignado '{$consignmentItem->name}': estoque insuficiente. Disponível: {$consignmentItem->available_quantity}",
                    ];
                }

                continue;
            }

            if (!$item->productId) {
                continue;
            }

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
