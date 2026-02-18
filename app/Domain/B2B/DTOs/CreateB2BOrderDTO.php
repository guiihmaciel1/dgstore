<?php

declare(strict_types=1);

namespace App\Domain\B2B\DTOs;

class CreateB2BOrderDTO
{
    public function __construct(
        public readonly string $retailerId,
        public readonly array $items,
        public readonly ?string $notes = null,
    ) {}
}
