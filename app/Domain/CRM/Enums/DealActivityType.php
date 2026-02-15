<?php

declare(strict_types=1);

namespace App\Domain\CRM\Enums;

enum DealActivityType: string
{
    case Created = 'created';
    case StageChange = 'stage_change';
    case Note = 'note';
    case WhatsApp = 'whatsapp';
    case Call = 'call';
    case Won = 'won';
    case Lost = 'lost';
    case Reopened = 'reopened';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Criado',
            self::StageChange => 'Movido',
            self::Note => 'Nota',
            self::WhatsApp => 'WhatsApp',
            self::Call => 'Ligação',
            self::Won => 'Ganho',
            self::Lost => 'Perdido',
            self::Reopened => 'Reaberto',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Created => 'M12 4v16m8-8H4',
            self::StageChange => 'M13 7l5 5m0 0l-5 5m5-5H6',
            self::Note => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            self::WhatsApp => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
            self::Call => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
            self::Won => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            self::Lost => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            self::Reopened => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Created => '#3b82f6',
            self::StageChange => '#8b5cf6',
            self::Note => '#6b7280',
            self::WhatsApp => '#16a34a',
            self::Call => '#0891b2',
            self::Won => '#059669',
            self::Lost => '#dc2626',
            self::Reopened => '#d97706',
        };
    }
}
