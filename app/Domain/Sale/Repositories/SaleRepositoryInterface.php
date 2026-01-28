<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Domain\Sale\DTOs\SaleData;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SaleRepositoryInterface
{
    public function find(string $id): ?Sale;

    public function findBySaleNumber(string $saleNumber): ?Sale;

    public function all(): Collection;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function create(SaleData $data): Sale;

    public function updateStatus(Sale $sale, PaymentStatus $status): Sale;

    public function cancel(Sale $sale): Sale;

    public function delete(Sale $sale): bool;

    public function getByCustomer(string $customerId): Collection;

    public function getByUser(string $userId): Collection;

    public function getByDateRange(Carbon $startDate, Carbon $endDate): Collection;

    public function getTodaySales(): Collection;

    public function getMonthSales(): Collection;

    public function getTotalByDateRange(Carbon $startDate, Carbon $endDate): float;

    public function getCountByDateRange(Carbon $startDate, Carbon $endDate): int;

    public function getTopSellingProducts(int $limit = 10, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection;

    public function getSalesByDay(int $days = 30): Collection;
}
