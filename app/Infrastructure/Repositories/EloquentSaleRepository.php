<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Product\Models\Product;
use App\Domain\Sale\DTOs\SaleData;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Enums\TradeInStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Models\SaleItem;
use App\Domain\Sale\Models\TradeIn;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Stock\Enums\StockMovementType;
use App\Domain\Stock\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentSaleRepository implements SaleRepositoryInterface
{
    public function find(string $id): ?Sale
    {
        return Sale::with(['items.product', 'customer', 'user'])->find($id);
    }

    public function findBySaleNumber(string $saleNumber): ?Sale
    {
        return Sale::with(['items.product', 'customer', 'user'])
            ->where('sale_number', $saleNumber)
            ->first();
    }

    public function all(): Collection
    {
        return Sale::with(['customer', 'user'])
            ->orderBy('sold_at', 'desc')
            ->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Sale::with(['customer', 'user']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('sold_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('sold_at', '<=', $filters['date_to']);
        }

        $sortField = $filters['sort'] ?? 'sold_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    public function create(SaleData $data): Sale
    {
        return DB::transaction(function () use ($data) {
            // Calcula os totais
            $subtotal = $data->calculateSubtotal();
            $total = $data->calculateTotal();

            // Cria a venda
            $sale = Sale::create([
                'customer_id' => $data->customerId,
                'user_id' => $data->userId,
                'subtotal' => $subtotal,
                'discount' => $data->discount,
                'trade_in_value' => $data->tradeInValue,
                'cash_payment' => $data->cashPayment,
                'card_payment' => $data->cardPayment,
                'cash_payment_method' => $data->cashPaymentMethod,
                'total' => $total,
                'payment_method' => $data->paymentMethod,
                'payment_status' => $data->paymentStatus,
                'installments' => $data->installments,
                'notes' => $data->notes,
                'sold_at' => $data->soldAt ?? now(),
            ]);

            // Cria os itens da venda
            foreach ($data->items as $itemData) {
                $product = Product::findOrFail($itemData->productId);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_snapshot' => $product->toSnapshot(),
                    'quantity' => $itemData->quantity,
                    'unit_price' => $itemData->unitPrice,
                    'subtotal' => $itemData->subtotal(),
                ]);

                // Registra saída de estoque
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $data->userId,
                    'type' => StockMovementType::Out,
                    'quantity' => $itemData->quantity,
                    'reason' => "Venda #{$sale->sale_number}",
                    'reference_id' => $sale->id,
                ]);

                // Decrementa estoque do produto
                $product->decrement('stock_quantity', $itemData->quantity);
            }

            // Cria o trade-in se houver
            if ($data->hasTradeIn() && $data->tradeIn !== null) {
                TradeIn::create([
                    'sale_id' => $sale->id,
                    'device_name' => $data->tradeIn->deviceName,
                    'device_model' => $data->tradeIn->deviceModel,
                    'imei' => $data->tradeIn->imei,
                    'estimated_value' => $data->tradeIn->estimatedValue,
                    'condition' => $data->tradeIn->condition,
                    'notes' => $data->tradeIn->notes,
                    'status' => TradeInStatus::Pending,
                ]);
            }

            return $sale->load(['items.product', 'customer', 'user', 'tradeIn']);
        });
    }

    public function updateStatus(Sale $sale, PaymentStatus $status): Sale
    {
        $sale->update(['payment_status' => $status]);
        return $sale->fresh();
    }

    public function cancel(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            // Atualiza status da venda
            $sale->update(['payment_status' => PaymentStatus::Cancelled]);

            // Devolve os itens ao estoque
            foreach ($sale->items as $item) {
                // Registra devolução de estoque
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'user_id' => auth()->id(),
                    'type' => StockMovementType::Return,
                    'quantity' => $item->quantity,
                    'reason' => "Cancelamento da venda #{$sale->sale_number}",
                    'reference_id' => $sale->id,
                ]);

                // Incrementa estoque do produto
                $item->product->increment('stock_quantity', $item->quantity);
            }

            return $sale->fresh();
        });
    }

    public function delete(Sale $sale): bool
    {
        return (bool) $sale->delete();
    }

    public function getByCustomer(string $customerId): Collection
    {
        return Sale::with(['items.product', 'user'])
            ->where('customer_id', $customerId)
            ->orderBy('sold_at', 'desc')
            ->get();
    }

    public function getByUser(string $userId): Collection
    {
        return Sale::with(['items.product', 'customer'])
            ->where('user_id', $userId)
            ->orderBy('sold_at', 'desc')
            ->get();
    }

    public function getByDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return Sale::with(['customer', 'user'])
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->orderBy('sold_at', 'desc')
            ->get();
    }

    public function getTodaySales(): Collection
    {
        return $this->getByDateRange(
            Carbon::today(),
            Carbon::today()->endOfDay()
        );
    }

    public function getMonthSales(): Collection
    {
        return $this->getByDateRange(
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        );
    }

    public function getTotalByDateRange(Carbon $startDate, Carbon $endDate): float
    {
        return (float) Sale::whereBetween('sold_at', [$startDate, $endDate])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->sum('total');
    }

    public function getCountByDateRange(Carbon $startDate, Carbon $endDate): int
    {
        return Sale::whereBetween('sold_at', [$startDate, $endDate])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->count();
    }

    public function getTopSellingProducts(int $limit = 10, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.payment_status', '!=', PaymentStatus::Cancelled);

        if ($startDate && $endDate) {
            $query->whereBetween('sales.sold_at', [$startDate, $endDate]);
        }

        return $query->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'product' => $item->product,
                    'total_sold' => $item->total_sold,
                ];
            });
    }

    public function getSalesByDay(int $days = 30): Collection
    {
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        $sales = Sale::select(
            DB::raw('DATE(sold_at) as date'),
            DB::raw('SUM(total) as total')
        )
            ->where('sold_at', '>=', $startDate)
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->groupBy('date')
            ->get();

        return $sales->pluck('total', 'date');
    }
}
