<?php

declare(strict_types=1);

namespace App\Domain\Stock\Services;

use App\Domain\Product\Models\Product;
use App\Domain\Stock\Enums\StockMovementType;
use App\Domain\Stock\Models\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Registra uma entrada de estoque
     */
    public function registerEntry(
        Product $product,
        int $quantity,
        ?string $userId = null,
        ?string $reason = null
    ): StockMovement {
        return $this->createMovement(
            product: $product,
            type: StockMovementType::In,
            quantity: $quantity,
            userId: $userId,
            reason: $reason ?? 'Entrada de estoque'
        );
    }

    /**
     * Registra uma saída de estoque
     *
     * @throws \Exception se não houver estoque suficiente
     */
    public function registerExit(
        Product $product,
        int $quantity,
        ?string $userId = null,
        ?string $reason = null,
        ?string $referenceId = null
    ): StockMovement {
        if ($product->stock_quantity < $quantity) {
            throw new \Exception(
                "Estoque insuficiente para {$product->name}. Disponível: {$product->stock_quantity}, Solicitado: {$quantity}"
            );
        }

        return $this->createMovement(
            product: $product,
            type: StockMovementType::Out,
            quantity: $quantity,
            userId: $userId,
            reason: $reason ?? 'Saída de estoque',
            referenceId: $referenceId
        );
    }

    /**
     * Registra um ajuste de estoque
     */
    public function registerAdjustment(
        Product $product,
        int $newQuantity,
        ?string $userId = null,
        ?string $reason = null
    ): StockMovement {
        $difference = $newQuantity - $product->stock_quantity;

        return $this->createMovement(
            product: $product,
            type: StockMovementType::Adjustment,
            quantity: abs($difference),
            userId: $userId,
            reason: $reason ?? "Ajuste de estoque: {$product->stock_quantity} → {$newQuantity}"
        );
    }

    /**
     * Registra uma devolução ao estoque
     */
    public function registerReturn(
        Product $product,
        int $quantity,
        ?string $userId = null,
        ?string $reason = null,
        ?string $referenceId = null
    ): StockMovement {
        return $this->createMovement(
            product: $product,
            type: StockMovementType::Return,
            quantity: $quantity,
            userId: $userId,
            reason: $reason ?? 'Devolução ao estoque',
            referenceId: $referenceId
        );
    }

    /**
     * Cria um movimento de estoque e atualiza o produto
     */
    private function createMovement(
        Product $product,
        StockMovementType $type,
        int $quantity,
        ?string $userId = null,
        ?string $reason = null,
        ?string $referenceId = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $type, $quantity, $userId, $reason, $referenceId) {
            $movement = StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'type' => $type,
                'quantity' => $quantity,
                'reason' => $reason,
                'reference_id' => $referenceId,
            ]);

            // Atualiza o estoque do produto
            if ($type->isAddition()) {
                $product->increment('stock_quantity', $quantity);
            } else {
                $product->decrement('stock_quantity', $quantity);
            }

            return $movement;
        });
    }

    /**
     * Obtém o histórico de movimentações de um produto
     */
    public function getProductHistory(string $productId, int $limit = 50): Collection
    {
        return StockMovement::where('product_id', $productId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtém movimentações recentes
     */
    public function getRecentMovements(int $days = 30, int $limit = 100): Collection
    {
        return StockMovement::with(['product', 'user'])
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtém produtos com estoque baixo
     */
    public function getLowStockProducts(): Collection
    {
        return Product::active()
            ->lowStock()
            ->orderBy('stock_quantity')
            ->get();
    }

    /**
     * Verifica se há estoque suficiente
     */
    public function hasStock(Product $product, int $quantity): bool
    {
        return $product->stock_quantity >= $quantity;
    }

    /**
     * Verifica disponibilidade de estoque (considerando reservas)
     */
    public function checkAvailability(array $items): array
    {
        $unavailable = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                $unavailable[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => 'Produto não encontrado',
                    'requested' => $item['quantity'],
                    'available' => 0,
                ];
                continue;
            }

            // Considera produtos reservados como indisponíveis
            $available = $product->stock_quantity;
            if ($product->reserved) {
                $available = max(0, $available - 1);
            }

            if ($available < $item['quantity']) {
                $unavailable[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'requested' => $item['quantity'],
                    'available' => $available,
                    'reserved' => $product->reserved,
                ];
            }
        }

        return $unavailable;
    }
}
