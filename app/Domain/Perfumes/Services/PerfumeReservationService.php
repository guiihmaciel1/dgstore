<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Services;

use App\Domain\Perfumes\Enums\PerfumeReservationStatus;
use App\Domain\Perfumes\Models\PerfumeReservation;
use App\Domain\Perfumes\Models\PerfumeReservationPayment;
use Illuminate\Support\Facades\DB;

class PerfumeReservationService
{
    public function create(array $data): PerfumeReservation
    {
        return DB::transaction(function () use ($data) {
            $reservation = PerfumeReservation::create([
                'reservation_number'   => PerfumeReservation::generateReservationNumber(),
                'perfume_customer_id'  => $data['perfume_customer_id'],
                'perfume_product_id'   => $data['perfume_product_id'] ?? null,
                'product_description'  => $data['product_description'] ?? null,
                'user_id'              => $data['user_id'],
                'product_price'        => $data['product_price'],
                'deposit_amount'       => $data['deposit_amount'],
                'deposit_paid'         => 0,
                'status'               => PerfumeReservationStatus::Active,
                'expires_at'           => $data['expires_at'] ?? null,
                'notes'                => $data['notes'] ?? null,
            ]);

            // Se tem pagamento inicial
            if (!empty($data['initial_payment'])) {
                $this->addPayment($reservation, [
                    'user_id'        => $data['user_id'],
                    'amount'         => $data['initial_payment'],
                    'payment_method' => $data['payment_method'] ?? 'pix',
                    'paid_at'        => now(),
                    'notes'          => 'Pagamento inicial',
                ]);
            }

            return $reservation->fresh(['customer', 'product', 'user', 'payments']);
        });
    }

    public function addPayment(PerfumeReservation $reservation, array $data): PerfumeReservationPayment
    {
        return DB::transaction(function () use ($reservation, $data) {
            $payment = $reservation->payments()->create([
                'user_id'        => $data['user_id'],
                'amount'         => $data['amount'],
                'payment_method' => $data['payment_method'],
                'paid_at'        => $data['paid_at'] ?? now(),
                'notes'          => $data['notes'] ?? null,
            ]);

            // Atualiza o total pago
            $reservation->update([
                'deposit_paid' => $reservation->payments()->sum('amount'),
            ]);

            return $payment;
        });
    }

    public function removePayment(PerfumeReservationPayment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $reservation = $payment->reservation;
            $payment->delete();

            // Atualiza o total pago
            $reservation->update([
                'deposit_paid' => $reservation->payments()->sum('amount'),
            ]);
        });
    }

    public function convertToSale(PerfumeReservation $reservation): string
    {
        // Retorna o ID da reserva para ser usado na criação da venda
        // A conversão real acontecerá no controller de vendas
        return $reservation->id;
    }

    public function markAsConverted(PerfumeReservation $reservation, string $saleId): void
    {
        $reservation->update([
            'status'                     => PerfumeReservationStatus::Completed,
            'converted_perfume_sale_id'  => $saleId,
        ]);
    }

    public function cancel(PerfumeReservation $reservation): void
    {
        $reservation->update([
            'status' => PerfumeReservationStatus::Cancelled,
        ]);
    }

    public function markExpired(PerfumeReservation $reservation): void
    {
        $reservation->update([
            'status' => PerfumeReservationStatus::Expired,
        ]);
    }
}
