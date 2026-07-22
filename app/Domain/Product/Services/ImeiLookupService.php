<?php

declare(strict_types=1);

namespace App\Domain\Product\Services;

use App\Domain\Product\Models\TacDevice;

class ImeiLookupService
{
    /**
     * Consulta o banco TAC pelo IMEI e retorna dados do dispositivo.
     *
     * @return array{found: bool, brand?: string, model?: string, device_type?: string, suggested_category?: string, suggested_name?: string}
     */
    public function lookup(string $imei): array
    {
        $cleanImei = preg_replace('/\D/', '', $imei);

        if (! $this->isValidImei($cleanImei)) {
            return ['found' => false, 'error' => 'IMEI inválido'];
        }

        $device = TacDevice::findByImei($cleanImei);

        if (! $device) {
            return ['found' => false];
        }

        return [
            'found' => true,
            'brand' => $device->brand,
            'model' => $device->model,
            'device_type' => $device->device_type,
            'suggested_category' => $device->suggestedCategory(),
            'suggested_name' => $device->suggestedName(),
        ];
    }

    /**
     * Valida o IMEI (15 dígitos + Luhn check digit).
     */
    private function isValidImei(string $imei): bool
    {
        if (strlen($imei) < 14 || strlen($imei) > 16) {
            return false;
        }

        if (! ctype_digit($imei)) {
            return false;
        }

        return true;
    }
}
