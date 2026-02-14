<?php

declare(strict_types=1);

namespace App\Domain\Sale\Services;

use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SaleService
{
    public function __construct(
        private readonly SaleRepositoryInterface $repository
    ) {}

    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function updateStatus(Sale $sale, PaymentStatus $status): Sale
    {
        return $this->repository->updateStatus($sale, $status);
    }
}
