<?php

declare(strict_types=1);

namespace App\Domain\CRM\Enums;

enum LeadSource: string
{
    case WhatsApp = 'whatsapp';
    case Instagram = 'instagram';
    case TrafegoPago = 'trafego_pago';
    case Indicacao = 'indicacao';
    case LojaFisica = 'loja_fisica';
    case Site = 'site';
    case Outro = 'outro';

    public function label(): string
    {
        return match ($this) {
            self::WhatsApp => 'WhatsApp',
            self::Instagram => 'Instagram',
            self::TrafegoPago => 'Tráfego Pago',
            self::Indicacao => 'Indicação',
            self::LojaFisica => 'Loja Física',
            self::Site => 'Site',
            self::Outro => 'Outro',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::WhatsApp => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
            self::Instagram => 'M4 4h16v16H4V4zm4 8a4 4 0 108 0 4 4 0 00-8 0zm9-5h.01',
            self::TrafegoPago => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z',
            self::Indicacao => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            self::LojaFisica => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            self::Site => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
            self::Outro => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::WhatsApp => '#16a34a',
            self::Instagram => '#c026d3',
            self::TrafegoPago => '#ea580c',
            self::Indicacao => '#0891b2',
            self::LojaFisica => '#4f46e5',
            self::Site => '#2563eb',
            self::Outro => '#6b7280',
        };
    }
}
