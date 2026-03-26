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
        $sku = $this->productService->generateSku('iphone', $tradeIn->device_model ?? '');

        $saleNumber = $tradeIn->sale?->sale_number;
        $formattedValue = number_format((float) $tradeIn->estimated_value, 2, ',', '.');

        $productData = ProductData::fromArray([
            'name' => $tradeIn->device_name,
            'sku' => $sku,
            'category' => ProductCategory::Smartphone->value,
            'model' => $tradeIn->device_model,
            'condition' => ProductCondition::Used->value,
            'imei' => $tradeIn->imei,
            'cost_price' => (float) $tradeIn->estimated_value,
            'stock_quantity' => 0,
            'notes' => "Origem: Trade-in da venda #{$saleNumber}. Valor estimado: R$ {$formattedValue}. {$tradeIn->notes}",
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
