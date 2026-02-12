<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Reservation\Services\ReservationService;
use Illuminate\Console\Command;

class ProcessExpiredReservationsCommand extends Command
{
    protected $signature = 'reservations:process-expired';
    protected $description = 'Processa reservas expiradas e libera os produtos';

    public function handle(ReservationService $reservationService): int
    {
        $count = $reservationService->processExpiredReservations();

        if ($count > 0) {
            $this->info("{$count} reserva(s) expirada(s) processada(s). Produtos liberados.");
        } else {
            $this->info('Nenhuma reserva expirada encontrada.');
        }

        return self::SUCCESS;
    }
}
