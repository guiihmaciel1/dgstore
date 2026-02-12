<?php

declare(strict_types=1);

namespace App\Domain\Product\DTOs;

use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;

readonly class ProductData
{
    public function __construct(
        public string $name,
        public string $sku,
        public ProductCategory $category,
        public float $costPrice,
        public float $salePrice,
        public ?string $model = null,
        public ?string $storage = null,
        public ?string $color = null,
        public ProductCondition $condition = ProductCondition::New,
        public ?string $imei = null,
        public int $stockQuantity = 0,
        public int $minStockAlert = 1,
        public ?string $supplier = null,
        public ?string $notes = null,
        public bool $active = true,
    ) {}

    public static function fromArray(array $data): self
    {
        $name = $data['name'] ?? throw new \InvalidArgumentException('Campo "name" é obrigatório em ProductData.');
        $sku = $data['sku'] ?? throw new \InvalidArgumentException('Campo "sku" é obrigatório em ProductData.');
        $category = $data['category'] ?? throw new \InvalidArgumentException('Campo "category" é obrigatório em ProductData.');

        return new self(
            name: $name,
            sku: $sku,
            category: $category instanceof ProductCategory 
                ? $category 
                : ProductCategory::from($category),
            costPrice: (float) ($data['cost_price'] ?? 0),
            salePrice: (float) ($data['sale_price'] ?? 0),
            model: $data['model'] ?? null,
            storage: $data['storage'] ?? null,
            color: $data['color'] ?? null,
            condition: isset($data['condition']) 
                ? ($data['condition'] instanceof ProductCondition 
                    ? $data['condition'] 
                    : ProductCondition::from($data['condition']))
                : ProductCondition::New,
            imei: $data['imei'] ?? null,
            stockQuantity: (int) ($data['stock_quantity'] ?? 0),
            minStockAlert: (int) ($data['min_stock_alert'] ?? 1),
            supplier: $data['supplier'] ?? null,
            notes: $data['notes'] ?? null,
            active: (bool) ($data['active'] ?? true),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'sku' => $this->sku,
            'category' => $this->category->value,
            'model' => $this->model,
            'storage' => $this->storage,
            'color' => $this->color,
            'condition' => $this->condition->value,
            'imei' => $this->imei,
            'cost_price' => $this->costPrice,
            'sale_price' => $this->salePrice,
            'stock_quantity' => $this->stockQuantity,
            'min_stock_alert' => $this->minStockAlert,
            'supplier' => $this->supplier,
            'notes' => $this->notes,
            'active' => $this->active,
        ];
    }
}
