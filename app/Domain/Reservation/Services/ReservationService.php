<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Services;

use App\Domain\Product\Models\Product;
use App\Domain\Reservation\Enums\ReservationStatus;
use App\Domain\Reservation\Models\Reservation;
use App\Domain\Reservation\Models\ReservationPayment;
use App\Domain\Sale\Enums\PaymentMethod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * Lista reservas com filtros
     */
    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Reservation::with(['customer', 'product', 'user', 'payments']);

        // Filtro de busca
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('reservation_number', 'like', "%{$search}%")
                  ->orWhere('product_description', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('product', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro de status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtro apenas ativas
        if (!empty($filters['active_only'])) {
            $query->active();
        }

        // Filtro vencendo
        if (!empty($filters['expiring'])) {
            $query->expiringSoon($filters['expiring']);
        }

        // Ordenação
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Cria nova reserva
     */
    public function create(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            $product = null;
            $source = $data['source'] ?? 'stock';

            // Se tiver product_id, é do estoque
            if (!empty($data['product_id'])) {
                $product = Product::find($data['product_id']);

                if ($product && $product->reserved) {
                    throw new \Exception('Este produto já está reservado.');
                }
            }

            // Cria a reserva
            $reservation = Reservation::create([
                'customer_id' => $data['customer_id'],
                'product_id' => $product?->id,
                'product_description' => $data['product_description'] ?? $product?->full_name ?? $product?->name,
                'source' => $source,
                'user_id' => $data['user_id'],
                'status' => ReservationStatus::Active,
                'product_price' => $data['product_price'] ?? $product?->sale_price ?? 0,
                'deposit_amount' => $data['deposit_amount'] ?? 0,
                'deposit_paid' => 0,
                'expires_at' => $data['expires_at'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Marca produto como reservado (apenas se for do estoque)
            if ($product) {
                $product->update([
                    'reserved' => true,
                    'reserved_by' => $reservation->id,
                ]);
            }

            // Se houver pagamento inicial
            if (!empty($data['initial_payment']) && $data['initial_payment'] > 0) {
                $this->addPayment(
                    $reservation,
                    $data['user_id'],
                    $data['initial_payment'],
                    PaymentMethod::from($data['payment_method'] ?? 'cash')
                );
            }

            return $reservation->load(['customer', 'product', 'payments']);
        });
    }

    /**
     * Adiciona pagamento à reserva
     */
    public function addPayment(
        Reservation $reservation,
        string $userId,
        float $amount,
        PaymentMethod $paymentMethod,
        ?string $notes = null
    ): ReservationPayment {
        if (!$reservation->canReceivePayment()) {
            throw new \Exception('Esta reserva não pode receber mais pagamentos.');
        }

        $payment = ReservationPayment::create([
            'reservation_id' => $reservation->id,
            'user_id' => $userId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
            'notes' => $notes,
        ]);

        // Atualiza total pago na reserva
        $reservation->addPayment($amount);

        return $payment;
    }

    /**
     * Converte reserva em venda
     */
    public function convert(Reservation $reservation, string $saleId): Reservation
    {
        if (!$reservation->canConvert()) {
            throw new \Exception('Esta reserva não pode ser convertida.');
        }

        $reservation->markAsConverted($saleId);

        return $reservation->fresh();
    }

    /**
     * Cancela reserva
     */
    public function cancel(Reservation $reservation): Reservation
    {
        if (!$reservation->canCancel()) {
            throw new \Exception('Esta reserva não pode ser cancelada.');
        }

        $reservation->cancel();

        return $reservation->fresh();
    }

    /**
     * Processa reservas expiradas
     */
    public function processExpiredReservations(): int
    {
        $expired = Reservation::where('status', ReservationStatus::Active)
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $reservation) {
            $reservation->markAsExpired();
        }

        return $expired->count();
    }

    /**
     * Retorna reservas ativas
     */
    public function getActive(): Collection
    {
        return Reservation::with(['customer', 'product', 'user'])
            ->active()
            ->orderBy('expires_at')
            ->get();
    }

    /**
     * Retorna reservas vencendo em X dias
     */
    public function getExpiringSoon(int $days = 3): Collection
    {
        return Reservation::with(['customer', 'product'])
            ->expiringSoon($days)
            ->orderBy('expires_at')
            ->get();
    }

    /**
     * Conta reservas ativas
     */
    public function countActive(): int
    {
        return Reservation::active()->count();
    }

    /**
     * Conta reservas vencendo
     */
    public function countExpiringSoon(int $days = 3): int
    {
        return Reservation::expiringSoon($days)->count();
    }

    /**
     * Conta reservas vencidas que ainda estão ativas
     */
    public function countOverdue(): int
    {
        return Reservation::overdue()->count();
    }

    /**
     * Verifica se produto está reservado
     */
    public function isProductReserved(string $productId): bool
    {
        return Reservation::where('product_id', $productId)
            ->where('status', ReservationStatus::Active)
            ->exists();
    }

    /**
     * Retorna reserva ativa do produto
     */
    public function getActiveReservationForProduct(string $productId): ?Reservation
    {
        return Reservation::with(['customer'])
            ->where('product_id', $productId)
            ->where('status', ReservationStatus::Active)
            ->first();
    }
}
