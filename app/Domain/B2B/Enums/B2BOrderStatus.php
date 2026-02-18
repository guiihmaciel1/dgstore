<?php

declare(strict_types=1);

namespace App\Domain\B2B\Enums;

enum B2BOrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Separating = 'separating';
    case Ready = 'ready';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Aguardando Pagamento',
            self::Paid => 'Pago',
            self::Separating => 'Em Separação',
            self::Ready => 'Aguardando Retirada/Entrega',
            self::Completed => 'Concluído',
            self::Cancelled => 'Cancelado',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::PendingPayment => 'Aguardando PIX',
            self::Paid => 'Pago',
            self::Separating => 'Separando',
            self::Ready => 'Pronto p/ Retirada',
            self::Completed => 'Concluído',
            self::Cancelled => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PendingPayment => 'yellow',
            self::Paid => 'blue',
            self::Separating => 'indigo',
            self::Ready => 'purple',
            self::Completed => 'green',
            self::Cancelled => 'red',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PendingPayment => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            self::Paid => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            self::Separating => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
            self::Ready => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>',
            self::Completed => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
            self::Cancelled => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        };
    }

    public function nextStatuses(): array
    {
        return match ($this) {
            self::PendingPayment => [self::Paid, self::Cancelled],
            self::Paid => [self::Separating, self::Cancelled],
            self::Separating => [self::Ready, self::Cancelled],
            self::Ready => [self::Completed],
            self::Completed => [],
            self::Cancelled => [],
        };
    }
}
