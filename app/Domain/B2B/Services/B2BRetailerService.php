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

    public function create(CreateRetailerDTO $dto): B2BRetailer
    {
        return B2BRetailer::create([
            'store_name' => $dto->storeName,
            'owner_name' => $dto->ownerName,
            'document' => $dto->document,
            'whatsapp' => $dto->whatsapp,
            'city' => $dto->city,
            'state' => $dto->state,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);
    }

    public function updateStatus(B2BRetailer $retailer, RetailerStatus $status): void
    {
        $retailer->update(['status' => $status->value]);
    }
}
