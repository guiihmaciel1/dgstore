<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro de uma troca de aparelho com outro lojista.
 *
 * Cada exchange e imutavel apos criacao - guarda o snapshot completo do
 * estado anterior e do estado novo do item, alem dos dados da troca
 * (lojista, ajuste financeiro, motivo).
 *
 * O ConsignmentStockItem mantem seu ID original e tem seus campos atualizados
 * para refletir o estado pos-troca - este registro preserva o historico.
 */
class ConsignmentItemExchange extends Model
{
    use HasUlids;

    protected $fillable = [
        'consignment_item_id',
        'user_id',
        'old_imei',
        'old_serial_number',
        'old_name',
        'old_model',
        'old_storage',
        'old_color',
        'old_condition',
        'new_imei',
        'new_serial_number',
        'new_name',
        'new_model',
        'new_storage',
        'new_color',
        'new_condition',
        'partner_name',
        'cost_adjustment',
        'reason',
        'exchanged_at',
    ];

    protected function casts(): array
    {
        return [
            'cost_adjustment' => 'decimal:2',
            'exchanged_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ConsignmentStockItem::class, 'consignment_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getOldFullNameAttribute(): string
    {
        return $this->buildFullName($this->old_name, $this->old_storage, $this->old_color);
    }

    public function getNewFullNameAttribute(): string
    {
        return $this->buildFullName($this->new_name, $this->new_storage, $this->new_color);
    }

    public function getFormattedCostAdjustmentAttribute(): string
    {
        $value = (float) $this->cost_adjustment;

        if ($value === 0.0) {
            return 'Sem ajuste';
        }

        $prefix = $value > 0 ? '+ R$ ' : '- R$ ';

        return $prefix . number_format(abs($value), 2, ',', '.');
    }

    public function changedProduct(): bool
    {
        return $this->old_name !== $this->new_name
            || $this->old_storage !== $this->new_storage
            || $this->old_model !== $this->new_model;
    }

    private function buildFullName(?string $name, ?string $storage, ?string $color): string
    {
        $parts = array_filter([$name, $storage, $color]);

        return implode(' - ', $parts);
    }
}
