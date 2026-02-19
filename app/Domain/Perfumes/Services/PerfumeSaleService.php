<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Services;

use App\Domain\Perfumes\Enums\PerfumeSalePaymentStatus;
use App\Domain\Perfumes\Models\PerfumeProduct;
use App\Domain\Perfumes\Models\PerfumeSale;
use Illuminate\Support\Facades\DB;

class PerfumeSaleService
{
    public function __construct(
        private PerfumeReservationService $reservationService
    ) {}

    public function create(array $data, array $items): PerfumeSale
    {
        return DB::transaction(function () use ($data, $items) {
            // Valida estoque antes
            foreach ($items as $item) {
                $product = PerfumeProduct::findOrFail($item['perfume_product_id']);
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Estoque insuficiente para o produto: {$product->name}");
                }
            }

            $sale = PerfumeSale::create([
                'sale_number'         => PerfumeSale::generateSaleNumber(),
                'perfume_customer_id' => $data['perfume_customer_id'],
                'user_id'             => $data['user_id'],
                'payment_method'      => $data['payment_method'],
                'payment_amount'      => $data['payment_amount'] ?? 0,
                'installments'        => $data['installments'] ?? 1,
                'payment_status'      => $data['payment_status'] ?? PerfumeSalePaymentStatus::Paid,
                'discount'            => $data['discount'] ?? 0,
                'sold_at'             => $data['sold_at'] ?? now(),
                'notes'               => $data['notes'] ?? null,
                'subtotal'            => 0,
                'total'               => 0,
            ]);

            $subtotal = 0;

            foreach ($items as $item) {
                $product = PerfumeProduct::findOrFail($item['perfume_product_id']);

                $qty = (int) $item['quantity'];
                $unitPrice = (float) ($item['unit_price'] ?? $product->sale_price);
                $costPrice = (float) $product->cost_price;
                $itemSubtotal = $unitPrice * $qty;

                $sale->items()->create([
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

                // Baixa estoque
                $product->decrement('stock_quantity', $qty);
                $subtotal += $itemSubtotal;
            }

            $discount = (float) ($data['discount'] ?? 0);
            $sale->update([
                'subtotal' => $subtotal,
                'total'    => max(0, $subtotal - $discount),
            ]);

            // Se veio de uma reserva, marca como convertida
            if (!empty($data['from_reservation_id'])) {
                $this->reservationService->markAsConverted(
                    \App\Domain\Perfumes\Models\PerfumeReservation::findOrFail($data['from_reservation_id']),
                    $sale->id
                );
            }

            return $sale->fresh(['items', 'customer', 'user']);
        });
    }

    public function cancel(PerfumeSale $sale): void
    {
        DB::transaction(function () use ($sale) {
            // Estorna estoque
            foreach ($sale->items as $item) {
                $item->product?->increment('stock_quantity', $item->quantity);
            }

            $sale->update([
                'payment_status' => PerfumeSalePaymentStatus::Cancelled,
            ]);

            $sale->delete();
        });
    }
}
