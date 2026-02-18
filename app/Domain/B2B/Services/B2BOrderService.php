<?php

declare(strict_types=1);

namespace App\Domain\B2B\Services;

use App\Domain\B2B\DTOs\CreateB2BOrderDTO;
use App\Domain\B2B\Enums\B2BOrderStatus;
use App\Domain\B2B\Models\B2BOrder;
use App\Domain\B2B\Models\B2BProduct;
use App\Domain\B2B\Models\B2BSetting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class B2BOrderService
{
    public function listForRetailer(string $retailerId, int $perPage = 15): LengthAwarePaginator
    {
        return B2BOrder::where('b2b_retailer_id', $retailerId)
            ->with('items')
            ->latest()
            ->paginate($perPage);
    }

    public function listForAdmin(?string $search = null, ?string $status = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = B2BOrder::with(['retailer', 'items'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('retailer', function ($rq) use ($search) {
                        $rq->where('store_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function createFromCart(CreateB2BOrderDTO $dto): B2BOrder
    {
        return DB::transaction(function () use ($dto) {
            $subtotal = 0;
            $pixCode = B2BOrder::generatePixCode();

            $order = B2BOrder::create([
                'order_number' => B2BOrder::generateOrderNumber(),
                'b2b_retailer_id' => $dto->retailerId,
                'subtotal' => 0,
                'total' => 0,
                'status' => B2BOrderStatus::PendingPayment,
                'payment_method' => 'pix',
                'pix_code' => $pixCode,
                'notes' => $dto->notes,
            ]);

            foreach ($dto->items as $item) {
                $product = B2BProduct::findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \RuntimeException("Estoque insuficiente para {$product->full_name}. Disponível: {$product->stock_quantity}");
                }

                $itemSubtotal = (float) $product->wholesale_price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $order->items()->create([
                    'b2b_product_id' => $product->id,
                    'product_snapshot' => $product->toSnapshot(),
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->wholesale_price,
                    'cost_price' => $product->cost_price,
                    'subtotal' => $itemSubtotal,
                ]);

                $product->decrement('stock_quantity', $item['quantity']);
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);

            return $order->fresh(['items', 'retailer']);
        });
    }

    public function confirmPayment(B2BOrder $order): void
    {
        if ($order->status !== B2BOrderStatus::PendingPayment) {
            throw new \RuntimeException('Este pedido não está aguardando pagamento.');
        }

        $order->update([
            'status' => B2BOrderStatus::Paid,
            'paid_at' => now(),
        ]);
    }

    public function updateStatus(B2BOrder $order, B2BOrderStatus $newStatus): void
    {
        $allowedNext = $order->status->nextStatuses();

        if (!in_array($newStatus, $allowedNext)) {
            throw new \RuntimeException("Transição de status inválida: {$order->status->label()} → {$newStatus->label()}");
        }

        if ($newStatus === B2BOrderStatus::Cancelled) {
            $this->restoreStock($order);
        }

        $updateData = ['status' => $newStatus->value];

        if ($newStatus === B2BOrderStatus::Paid) {
            $updateData['paid_at'] = now();
        }

        $order->update($updateData);
    }

    private function restoreStock(B2BOrder $order): void
    {
        foreach ($order->items as $item) {
            $product = B2BProduct::find($item->b2b_product_id);
            if ($product) {
                $product->increment('stock_quantity', $item->quantity);
            }
        }
    }

    public function getMinimumOrderAmount(): float
    {
        return B2BSetting::getMinimumOrderAmount();
    }
}
