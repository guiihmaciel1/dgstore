<?php

declare(strict_types=1);

namespace App\Domain\Sale\Services;

use App\Domain\Product\DTOs\ProductData;
use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Services\ProductService;
use App\Domain\Sale\Models\TradeIn;
use App\Domain\Stock\Services\StockService;

class TradeInProcessingService
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly StockService $stockService,
    ) {}

    /**
     * Cria um produto a partir do trade-in e registra entrada no estoque.
     */
    public function createProductFromTradeIn(TradeIn $tradeIn, string $userId): Product
    {
        $category = $tradeIn->category ?? ProductCategory::Smartphone->value;
        $sku = $this->productService->generateSku($category, $tradeIn->device_model ?? '');

        $saleNumber = $tradeIn->sale?->sale_number;
        $formattedValue = number_format((float) $tradeIn->estimated_value, 2, ',', '.');

        $costPrice = $tradeIn->cost_price !== null
            ? (float) $tradeIn->cost_price
            : (float) $tradeIn->estimated_value;

        $notes = trim("Origem: Trade-in da venda #{$saleNumber}. Valor negociado: R$ {$formattedValue}. {$tradeIn->notes}");

        $productData = ProductData::fromArray([
            'name' => $tradeIn->device_name,
            'sku' => $sku,
            'category' => $category,
            'model' => $tradeIn->device_model,
            'storage' => $tradeIn->storage,
            'color' => $tradeIn->color,
            'condition' => ProductCondition::Used->value,
            'imei' => $tradeIn->imei,
            'cost_price' => $costPrice,
            'sale_price' => $tradeIn->sale_price !== null ? (float) $tradeIn->sale_price : null,
            'resale_price' => $tradeIn->resale_price !== null ? (float) $tradeIn->resale_price : null,
            'stock_quantity' => 0,
            'battery_health' => $tradeIn->battery_health,
            'has_box' => $tradeIn->has_box ?? false,
            'has_cable' => $tradeIn->has_cable ?? false,
            'notes' => $notes,
        ]);

        $product = $this->productService->create($productData);

        $this->stockService->registerEntry(
            $product,
            1,
            $userId,
            "Trade-in processado (venda #{$saleNumber})"
        );

        $tradeIn->markAsProcessed($product->id);

        return $product;
    }
}
