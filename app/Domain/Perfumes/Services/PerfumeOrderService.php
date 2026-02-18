<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Services;

use App\Domain\Perfumes\Enums\PerfumeOrderStatus;
use App\Domain\Perfumes\Models\PerfumeOrder;
use App\Domain\Perfumes\Models\PerfumeProduct;
use Illuminate\Support\Facades\DB;

class PerfumeOrderService
{
    public function create(array $data, array $items): PerfumeOrder
    {
        return DB::transaction(function () use ($data, $items) {
            $order = PerfumeOrder::create([
                'order_number'        => PerfumeOrder::generateOrderNumber(),
                'perfume_retailer_id' => $data['perfume_retailer_id'],
                'payment_method'      => $data['payment_method'],
                'discount'            => $data['discount'] ?? 0,
                'notes'               => $data['notes'] ?? null,
                'subtotal'            => 0,
                'total'               => 0,
            ]);

            $subtotal = 0;

            foreach ($items as $item) {
                $product = PerfumeProduct::findOrFail($item['perfume_product_id']);

                $qty = (int) $item['quantity'];
                $unitPrice = (float) $product->sale_price;
                $costPrice = (float) $product->cost_price;
                $itemSubtotal = $unitPrice * $qty;

                $order->items()->create([
                    'perfume_product_id' => $product->id,
                    'product_snapshot'   => [
                        'name'     => $product->name,
                        'brand'    => $product->brand,
                        'size_ml'  => $product->size_ml,
                        'category' => $product->category?->value,
                    ],
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                    'cost_price' => $costPrice,
                    'subtotal'   => $itemSubtotal,
                ]);

                $product->decrement('stock_quantity', $qty);
                $subtotal += $itemSubtotal;
            }

            $discount = (float) ($data['discount'] ?? 0);
            $order->update([
                'subtotal' => $subtotal,
                'total'    => max(0, $subtotal - $discount),
            ]);

            return $order->fresh(['items', 'retailer']);
        });
    }

    public function updateStatus(PerfumeOrder $order, PerfumeOrderStatus $status): PerfumeOrder
    {
        $order->update(['status' => $status]);

        if ($status === PerfumeOrderStatus::Cancelled) {
            foreach ($order->items as $item) {
                $item->product?->increment('stock_quantity', $item->quantity);
            }
        }

        return $order->fresh();
    }

    public function buildWhatsAppMessage(PerfumeOrder $order): string
    {
        $lines = ["*Pedido {$order->order_number}*"];
        $lines[] = "Status: {$order->status->label()}";
        $lines[] = '';

        foreach ($order->items as $item) {
            $snap = $item->product_snapshot;
            $name = $snap['name'] ?? 'Produto';
            $lines[] = "• {$item->quantity}x {$name} - R$ " . number_format((float) $item->subtotal, 2, ',', '.');
        }

        $lines[] = '';
        $lines[] = "*Total: R$ " . number_format((float) $order->total, 2, ',', '.') . '*';
        $lines[] = "Pagamento: {$order->payment_method->label()}";

        return implode("\n", $lines);
    }
}
