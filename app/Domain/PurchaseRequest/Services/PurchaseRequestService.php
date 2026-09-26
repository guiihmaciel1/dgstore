<?php

declare(strict_types=1);

namespace App\Domain\PurchaseRequest\Services;

use App\Domain\PurchaseRequest\Enums\PurchaseRequestStatus;
use App\Domain\PurchaseRequest\Models\PurchaseRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseRequestService
{
    public function create(array $data): PurchaseRequest
    {
        return DB::transaction(function () use ($data) {
            return PurchaseRequest::create($data);
        });
    }

    public function approve(PurchaseRequest $request, string $adminId, ?string $adminNotes = null): void
    {
        if (!$request->canBeApproved()) {
            throw new \DomainException('Esta solicitação não pode ser aprovada no status atual.');
        }

        $request->update([
            'status' => PurchaseRequestStatus::Approved,
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_notes' => $adminNotes,
        ]);

        Log::info('Solicitação de compra aprovada', [
            'request_id' => $request->id,
            'request_number' => $request->request_number,
            'approved_by' => $adminId,
        ]);
    }

    public function reject(PurchaseRequest $request, string $adminId, ?string $reason = null): void
    {
        if (!$request->canBeRejected()) {
            throw new \DomainException('Esta solicitação não pode ser rejeitada no status atual.');
        }

        $request->update([
            'status' => PurchaseRequestStatus::Rejected,
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_notes' => $reason,
        ]);

        Log::info('Solicitação de compra rejeitada', [
            'request_id' => $request->id,
            'request_number' => $request->request_number,
            'rejected_by' => $adminId,
            'reason' => $reason,
        ]);
    }

    public function markPurchased(PurchaseRequest $request): void
    {
        if (!$request->canBePurchased()) {
            throw new \DomainException('Esta solicitação precisa estar aprovada para marcar como comprada.');
        }

        $request->update([
            'status' => PurchaseRequestStatus::Purchased,
            'purchased_at' => now(),
        ]);
    }

    public function markDelivered(PurchaseRequest $request): void
    {
        if (!$request->canBeDelivered()) {
            throw new \DomainException('Esta solicitação precisa estar comprada para marcar como entregue.');
        }

        $request->update([
            'status' => PurchaseRequestStatus::Delivered,
            'delivered_at' => now(),
        ]);
    }

    public function cancel(PurchaseRequest $request, ?string $reason = null): void
    {
        if (!$request->canBeCancelled()) {
            throw new \DomainException('Esta solicitação não pode ser cancelada no status atual.');
        }

        $request->update([
            'status' => PurchaseRequestStatus::Cancelled,
            'cancelled_at' => now(),
            'cancelled_reason' => $reason,
        ]);

        Log::info('Solicitação de compra cancelada', [
            'request_id' => $request->id,
            'request_number' => $request->request_number,
            'reason' => $reason,
        ]);
    }

    public function markConverted(PurchaseRequest $request, string $saleId): void
    {
        if (!$request->canBeConverted()) {
            throw new \DomainException('Esta solicitação precisa estar entregue para ser efetivada.');
        }

        $request->update([
            'status' => PurchaseRequestStatus::Converted,
            'converted_sale_id' => $saleId,
            'converted_at' => now(),
        ]);
    }

    public function processExpired(): int
    {
        $expired = PurchaseRequest::expirable()->get();

        foreach ($expired as $request) {
            $request->update([
                'status' => PurchaseRequestStatus::Expired,
            ]);

            Log::info('Solicitação de compra expirada automaticamente', [
                'request_id' => $request->id,
                'request_number' => $request->request_number,
                'expired_at' => $request->expires_at,
            ]);
        }

        return $expired->count();
    }

    public function list(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = PurchaseRequest::with(['customer', 'seller']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['seller_id'])) {
            $query->where('seller_id', $filters['seller_id']);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();
    }

    public function pendingCount(): int
    {
        return PurchaseRequest::pending()->count();
    }
}
