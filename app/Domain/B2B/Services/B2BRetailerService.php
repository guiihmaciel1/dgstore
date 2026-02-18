<?php

declare(strict_types=1);

namespace App\Domain\B2B\Services;

use App\Domain\B2B\DTOs\CreateRetailerDTO;
use App\Domain\B2B\Enums\RetailerStatus;
use App\Domain\B2B\Models\B2BRetailer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class B2BRetailerService
{
    public function list(?string $search = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = B2BRetailer::query()->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function create(CreateRetailerDTO $dto, ?string $status = null): B2BRetailer
    {
        $data = [
            'store_name' => $dto->storeName,
            'owner_name' => $dto->ownerName,
            'document' => $dto->document,
            'whatsapp' => $dto->whatsapp,
            'city' => $dto->city,
            'state' => $dto->state,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ];

        if ($status) {
            $data['status'] = $status;
        }

        return B2BRetailer::create($data);
    }

    public function update(B2BRetailer $retailer, array $data): void
    {
        $updateData = collect($data)->only([
            'store_name', 'owner_name', 'document', 'whatsapp',
            'city', 'state', 'email',
        ])->toArray();

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $retailer->update($updateData);
    }

    public function updateStatus(B2BRetailer $retailer, RetailerStatus $status): void
    {
        $retailer->update(['status' => $status->value]);
    }
}
