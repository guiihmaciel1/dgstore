<?php

declare(strict_types=1);

namespace App\Domain\Valuation\DTOs;

use App\Domain\Valuation\Enums\AccessoryState;
use App\Domain\Valuation\Enums\BatteryHealth;
use App\Domain\Valuation\Enums\DeviceState;

class ValuationChecklistData
{
    public function __construct(
        public readonly string $iphoneModelId,
        public readonly string $storage,
        public readonly int $batteryPercentage,
        public readonly DeviceState $deviceState,
        public readonly AccessoryState $accessoryState,
        public readonly ?string $color = null,
        public readonly ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            iphoneModelId: $data['iphone_model_id'],
            storage: $data['storage'],
            batteryPercentage: (int) $data['battery_percentage'],
            deviceState: DeviceState::from($data['device_state']),
            accessoryState: AccessoryState::from($data['accessory_state']),
            color: $data['color'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    /**
     * Retorna a classificação da saúde da bateria.
     */
    public function batteryHealth(): BatteryHealth
    {
        return BatteryHealth::fromPercentage($this->batteryPercentage);
    }

    /**
     * Calcula o modificador total (soma de todos os fatores).
     */
    public function totalModifier(): float
    {
        return $this->batteryHealth()->priceModifier()
            + $this->deviceState->priceModifier()
            + $this->accessoryState->priceModifier();
    }
}
