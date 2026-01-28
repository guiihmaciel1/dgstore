<?php

declare(strict_types=1);

namespace App\Domain\Warranty\Services;

use App\Domain\Sale\Models\SaleItem;
use App\Domain\Warranty\Enums\WarrantyClaimStatus;
use App\Domain\Warranty\Enums\WarrantyClaimType;
use App\Domain\Warranty\Models\Warranty;
use App\Domain\Warranty\Models\WarrantyClaim;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class WarrantyService
{
    /**
     * Lista garantias com filtros
     */
    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Warranty::with(['saleItem.sale.customer', 'saleItem.product', 'claims']);

        // Filtro de busca
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('imei', 'like', "%{$search}%")
                  ->orWhereHas('saleItem.sale', function ($q2) use ($search) {
                      $q2->where('sale_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('saleItem.sale.customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('saleItem.product', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro de status
        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'expiring':
                    $query->expiringSoon(30);
                    break;
                case 'supplier_active':
                    $query->supplierActive();
                    break;
                case 'customer_active':
                    $query->customerActive();
                    break;
                case 'with_claims':
                    $query->withOpenClaims();
                    break;
            }
        }

        // Ordenação
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Cria garantia para um item de venda
     */
    public function createFromSaleItem(
        SaleItem $saleItem,
        int $supplierMonths,
        int $customerMonths,
        ?string $imei = null,
        ?string $notes = null
    ): Warranty {
        $saleDate = $saleItem->sale->sold_at;

        return Warranty::create([
            'sale_item_id' => $saleItem->id,
            'supplier_warranty_months' => $supplierMonths,
            'customer_warranty_months' => $customerMonths,
            'supplier_warranty_until' => $supplierMonths > 0 ? $saleDate->copy()->addMonths($supplierMonths) : null,
            'customer_warranty_until' => $customerMonths > 0 ? $saleDate->copy()->addMonths($customerMonths) : null,
            'imei' => $imei ?? $saleItem->product?->imei,
            'notes' => $notes,
        ]);
    }

    /**
     * Abre um acionamento de garantia
     */
    public function openClaim(
        Warranty $warranty,
        string $userId,
        WarrantyClaimType $type,
        string $reason
    ): WarrantyClaim {
        return WarrantyClaim::create([
            'warranty_id' => $warranty->id,
            'user_id' => $userId,
            'type' => $type,
            'status' => WarrantyClaimStatus::Opened,
            'reason' => $reason,
            'opened_at' => now(),
        ]);
    }

    /**
     * Atualiza status de um acionamento
     */
    public function updateClaimStatus(
        WarrantyClaim $claim,
        WarrantyClaimStatus $status,
        ?string $resolution = null
    ): WarrantyClaim {
        $data = ['status' => $status];

        if ($resolution) {
            $data['resolution'] = $resolution;
        }

        if ($status->isClosed()) {
            $data['resolved_at'] = now();
        }

        $claim->update($data);

        return $claim->fresh();
    }

    /**
     * Retorna garantias vencendo em X dias
     */
    public function getExpiringSoon(int $days = 30): Collection
    {
        return Warranty::with(['saleItem.sale.customer', 'saleItem.product'])
            ->expiringSoon($days)
            ->orderBy('customer_warranty_until')
            ->get();
    }

    /**
     * Retorna quantidade de garantias vencendo
     */
    public function countExpiringSoon(int $days = 30): int
    {
        return Warranty::expiringSoon($days)->count();
    }

    /**
     * Retorna acionamentos abertos
     */
    public function getOpenClaims(): Collection
    {
        return WarrantyClaim::with(['warranty.saleItem.sale.customer', 'warranty.saleItem.product', 'user'])
            ->open()
            ->orderBy('opened_at', 'desc')
            ->get();
    }

    /**
     * Retorna quantidade de acionamentos abertos
     */
    public function countOpenClaims(): int
    {
        return WarrantyClaim::open()->count();
    }

    /**
     * Busca garantia por IMEI
     */
    public function findByImei(string $imei): ?Warranty
    {
        return Warranty::with(['saleItem.sale.customer', 'saleItem.product', 'claims'])
            ->where('imei', $imei)
            ->first();
    }
}
