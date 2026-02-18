<?php

declare(strict_types=1);

namespace App\Domain\B2B\Services;

use App\Domain\B2B\DTOs\CreateB2BProductDTO;
use App\Domain\B2B\Models\B2BProduct;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class B2BProductService
{
    public function listForAdmin(?string $search = null, ?string $condition = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = B2BProduct::query()->orderBy('sort_order')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%");
            });
        }

        if ($condition) {
            $query->where('condition', $condition);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function listForCatalog(?string $search = null, ?string $model = null, ?string $condition = null): LengthAwarePaginator
    {
        $query = B2BProduct::query()
            ->available()
            ->orderBy('sort_order')
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($model) {
            $query->where('model', $model);
        }

        if ($condition) {
            $query->where('condition', $condition);
        }

        return $query->paginate(24)->withQueryString();
    }

    public function create(CreateB2BProductDTO $dto): B2BProduct
    {
        return B2BProduct::create([
            'name' => $dto->name,
            'model' => $dto->model,
            'storage' => $dto->storage,
            'color' => $dto->color,
            'condition' => $dto->condition,
            'cost_price' => $dto->costPrice,
            'wholesale_price' => $dto->wholesalePrice,
            'stock_quantity' => $dto->stockQuantity,
            'photo' => $dto->photo,
            'sort_order' => $dto->sortOrder,
        ]);
    }

    public function update(B2BProduct $product, array $data): void
    {
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $this->deletePhotoFile($product->photo);

            $fileName = uniqid('b2b_') . '.' . $data['photo']->getClientOriginalExtension();
            $data['photo']->move(public_path('images/b2b-products'), $fileName);
            $data['photo'] = 'images/b2b-products/' . $fileName;
        }

        $product->update($data);
    }

    public function delete(B2BProduct $product): void
    {
        $this->deletePhotoFile($product->photo);
        $product->delete();
    }

    private function deletePhotoFile(?string $photo): void
    {
        if (!$photo) {
            return;
        }

        if (str_starts_with($photo, 'images/')) {
            $fullPath = public_path($photo);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        } else {
            Storage::disk('public')->delete($photo);
        }
    }

    public function getAvailableModels(): array
    {
        return B2BProduct::query()
            ->available()
            ->whereNotNull('model')
            ->distinct()
            ->pluck('model')
            ->sort()
            ->values()
            ->toArray();
    }
}
