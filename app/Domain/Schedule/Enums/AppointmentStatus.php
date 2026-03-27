<?php

declare(strict_types=1);

namespace App\Domain\Schedule\Enums;

enum AppointmentStatus: string
{
    case Scheduled = 'scheduled';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow    = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Agendado',
            self::Confirmed => 'Confirmado',
            self::Completed => 'Concluído',
            self::Cancelled => 'Cancelado',
            self::NoShow    => 'Não Compareceu',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'blue',
            self::Confirmed => 'green',
            self::Completed => 'gray',
            self::Cancelled => 'red',
            self::NoShow    => 'yellow',
        };
    }

    public function bgClass(): string
    {
        return match ($this) {
            self::Scheduled => 'bg-blue-100 text-blue-800 border-blue-300',
            self::Confirmed => 'bg-green-100 text-green-800 border-green-300',
            self::Completed => 'bg-gray-100 text-gray-800 border-gray-300',
            self::Cancelled => 'bg-red-100 text-red-800 border-red-300',
            self::NoShow    => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        };
    }

    public function slotBgClass(): string
    {
        return match ($this) {
            self::Scheduled => 'bg-blue-50 border-l-4 border-blue-500',
            self::Confirmed => 'bg-green-50 border-l-4 border-green-500',
            self::Completed => 'bg-gray-50 border-l-4 border-gray-400',
            self::Cancelled => 'bg-red-50 border-l-4 border-red-400',
            self::NoShow    => 'bg-yellow-50 border-l-4 border-yellow-500',
        };
    }
}
