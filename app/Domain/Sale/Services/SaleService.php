<?php

declare(strict_types=1);

namespace App\Domain\Sale\Services;

use App\Domain\Sale\DTOs\SaleData;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SaleService
{
    public function __construct(
        private readonly SaleRepositoryInterface $repository
    ) {}

    public function find(string $id): ?Sale
    {
        return $this->repository->find($id);
    }

    public function findBySaleNumber(string $saleNumber): ?Sale
    {
        return $this->repository->findBySaleNumber($saleNumber);
    }

    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(SaleData $data): Sale
    {
        return $this->repository->create($data);
    }

    public function updateStatus(Sale $sale, PaymentStatus $status): Sale
    {
        return $this->repository->updateStatus($sale, $status);
    }

    public function markAsPaid(Sale $sale): Sale
    {
        return $this->updateStatus($sale, PaymentStatus::Paid);
    }

    public function cancel(Sale $sale): Sale
    {
        return $this->repository->cancel($sale);
    }

    public function getByCustomer(string $customerId): Collection
    {
        return $this->repository->getByCustomer($customerId);
    }

    public function getByUser(string $userId): Collection
    {
        return $this->repository->getByUser($userId);
    }

    public function getTodaySales(): Collection
    {
        return $this->repository->getTodaySales();
    }

    public function getMonthSales(): Collection
    {
        return $this->repository->getMonthSales();
    }

    public function getTodayTotal(): float
    {
        return $this->repository->getTotalByDateRange(
            Carbon::today(),
            Carbon::today()->endOfDay()
        );
    }

    public function getMonthTotal(): float
    {
        return $this->repository->getTotalByDateRange(
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        );
    }

    public function getTodayCount(): int
    {
        return $this->repository->getCountByDateRange(
            Carbon::today(),
            Carbon::today()->endOfDay()
        );
    }

    public function getMonthCount(): int
    {
        return $this->repository->getCountByDateRange(
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        );
    }

    public function getTopSellingProducts(int $limit = 10): Collection
    {
        return $this->repository->getTopSellingProducts($limit);
    }

    public function getSalesChartData(int $days = 30): array
    {
        $sales = $this->repository->getSalesByDay($days);

        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d/m');
            $data[] = $sales->get($date, 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function formatMoney(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}
