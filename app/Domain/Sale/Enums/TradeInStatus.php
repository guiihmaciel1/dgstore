<?php

declare(strict_types=1);

namespace App\Domain\Sale\Enums;

enum TradeInStatus: string
{
    case Pending = 'pending';
    case Processed = 'processed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Processed => 'Processado',
            self::Rejected => 'Rejeitado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Processed => 'green',
            self::Rejected => 'red',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Pending => 'Aguardando cadastro no estoque',
            self::Processed => 'Cadastrado como produto',
            self::Rejected => 'Não será cadastrado',
        };
    }
}
