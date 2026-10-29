<?php

declare(strict_types=1);

namespace App\Domain\Product\Services;

use App\Domain\Product\Models\Product;
use App\Domain\Product\Models\ProductRepair;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductRepairService
{
    public const LATE_AFTER_DAYS = 7;

    /**
     * Assistências em andamento (mais antigas primeiro), para o painel inicial.
     */
    public function openRepairs(): Collection
    {
        return ProductRepair::open()
            ->whereHas('product', fn ($q) => $q->whereNull('products.deleted_at'))
            ->with('product')
            ->orderBy('sent_at')
            ->get();
    }

    public function send(Product $product, ?string $provider = null, ?string $reason = null, ?string $userId = null): ProductRepair
    {
        return DB::transaction(function () use ($product, $provider, $reason, $userId) {
            $locked = $this->lockProduct($product);

            if (! $locked->canGoToRepair()) {
                throw new InvalidArgumentException('Esta categoria de produto não vai para assistência.');
            }

            if ($locked->stock_quantity <= 0) {
                throw new InvalidArgumentException('Apenas aparelhos em estoque podem ir para a assistência.');
            }

            if ($locked->openRepair()->exists()) {
                throw new InvalidArgumentException('Este aparelho já está na assistência.');
            }

            $repair = $locked->repairs()->create([
                'provider' => $provider,
                'reason' => $reason,
                'sent_by' => $userId,
                'sent_at' => now(),
            ]);

            $product->unsetRelation('openRepair');

            return $repair;
        });
    }

    public function markReturned(Product $product, float $cost, ?string $notes = null, ?string $userId = null): ProductRepair
    {
        return DB::transaction(function () use ($product, $cost, $notes, $userId) {
            $locked = $this->lockProduct($product);
            $repair = $locked->openRepair()->first();

            if (! $repair) {
                throw new InvalidArgumentException('Este aparelho não está na assistência.');
            }

            $repair->update([
                'cost' => $cost,
                'notes' => $notes,
                'returned_by' => $userId,
                'returned_at' => now(),
            ]);

            $locked->increment('repair_cost', $cost);
            $product->unsetRelation('openRepair');

            return $repair;
        });
    }

    /**
     * Trava a linha do produto para evitar envio/retorno duplicado em cliques simultâneos.
     */
    private function lockProduct(Product $product): Product
    {
        return Product::whereKey($product->getKey())->lockForUpdate()->firstOrFail();
    }
}
