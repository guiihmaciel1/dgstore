<?php

declare(strict_types=1);

namespace App\Domain\Sale\DTOs;

use App\Domain\Sale\Enums\TradeInCondition;

readonly class TradeInData
{
    public function __construct(
        public string $deviceName,
        public float $estimatedValue,
        public TradeInCondition $condition = TradeInCondition::Good,
        public ?string $deviceModel = null,
        public ?string $category = null,
        public ?string $storage = null,
        public ?string $color = null,
        public ?string $imei = null,
        public ?float $costPrice = null,
        public ?float $salePrice = null,
        public ?float $resalePrice = null,
        public ?int $batteryHealth = null,
        public bool $hasBox = false,
        public bool $hasCable = false,
        public ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            deviceName: $data['device_name'] ?? throw new \InvalidArgumentException('Campo "device_name" é obrigatório em TradeInData.'),
            estimatedValue: (float) ($data['estimated_value'] ?? throw new \InvalidArgumentException('Campo "estimated_value" é obrigatório em TradeInData.')),
            condition: isset($data['condition'])
                ? ($data['condition'] instanceof TradeInCondition
                    ? $data['condition']
                    : TradeInCondition::from($data['condition']))
                : TradeInCondition::Good,
            deviceModel: $data['device_model'] ?? null,
            category: $data['category'] ?? null,
            storage: $data['storage'] ?? null,
            color: $data['color'] ?? null,
            imei: $data['imei'] ?? null,
            costPrice: (float) ($data['estimated_value']),
            salePrice: isset($data['sale_price']) && $data['sale_price'] !== '' ? (float) $data['sale_price'] : null,
            resalePrice: isset($data['resale_price']) && $data['resale_price'] !== '' ? (float) $data['resale_price'] : null,
            batteryHealth: isset($data['battery_health']) && $data['battery_health'] !== '' ? (int) $data['battery_health'] : null,
            hasBox: (bool) ($data['has_box'] ?? false),
            hasCable: (bool) ($data['has_cable'] ?? false),
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'device_name' => $this->deviceName,
            'device_model' => $this->deviceModel,
            'category' => $this->category,
            'storage' => $this->storage,
            'color' => $this->color,
            'imei' => $this->imei,
            'estimated_value' => $this->estimatedValue,
            'cost_price' => $this->costPrice,
            'sale_price' => $this->salePrice,
            'resale_price' => $this->resalePrice,
            'condition' => $this->condition->value,
            'battery_health' => $this->batteryHealth,
            'has_box' => $this->hasBox,
            'has_cable' => $this->hasCable,
            'notes' => $this->notes,
        ];
    }
}
