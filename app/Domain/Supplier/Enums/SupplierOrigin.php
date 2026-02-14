<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Enums;

enum SupplierOrigin: string
{
    case Py = 'py';
    case Br = 'br';

    public function label(): string
    {
        return match ($this) {
            self::Py => 'Paraguai (PY)',
            self::Br => 'Brasil (BR)',
        };
    }

    /**
     * Percentual de frete aplicado ao custo.
     */
    public function freightPercent(): float
    {
        return match ($this) {
            self::Py => 0.04, // 4% de frete
            self::Br => 0.0,  // Sem frete
        };
    }

    /**
     * Indica se o fornecedor tem frete.
     */
    public function hasFreight(): bool
    {
        return $this->freightPercent() > 0;
    }
}
