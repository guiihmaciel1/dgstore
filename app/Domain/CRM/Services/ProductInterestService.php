<?php

declare(strict_types=1);

namespace App\Domain\CRM\Services;

use App\Domain\CRM\Models\ProductInterest;
use App\Domain\Product\Models\Product;

class ProductInterestService
{
    public function checkMatchesForProduct(Product $product): int
    {
        if ($product->stock_quantity <= 0 || ! $product->active) {
            return 0;
        }

        $pendingInterests = ProductInterest::pending()
            ->whereHas('deal', fn ($q) => $q->open())
            ->where('model', 'LIKE', '%' . $product->model . '%')
            ->when($product->storage, fn ($q) => $q->where(function ($q2) use ($product) {
                $q2->whereNull('storage')->orWhere('storage', $product->storage);
            }))
            ->when($product->color, fn ($q) => $q->where(function ($q2) use ($product) {
                $q2->whereNull('color')->orWhere('color', $product->color);
            }))
            ->get();

        $matched = 0;
        foreach ($pendingInterests as $interest) {
            if ($interest->condition) {
                $conditionMap = ['novo' => 'new', 'seminovo' => 'used'];
                $mapped = $conditionMap[$interest->condition] ?? $interest->condition;
                if ($product->condition && $product->condition->value !== $mapped) {
                    continue;
                }
            }

            $interest->markAsNotified();
            $matched++;
        }

        return $matched;
    }
}
