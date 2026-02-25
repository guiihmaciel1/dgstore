<?php

declare(strict_types=1);

namespace App\Domain\Payment\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class CardMdrRate extends Model
{
    use HasUlids;

    protected $fillable = [
        'payment_type',
        'installments',
        'mdr_rate',
        'is_active',
    ];

    protected $casts = [
        'mdr_rate' => 'decimal:4',
        'is_active' => 'boolean',
        'installments' => 'integer',
    ];

    /**
     * Scope para taxas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Busca a taxa MDR para um tipo de pagamento e parcelas específicos
     * 
     * @param string $type 'debit' ou 'credit'
     * @param int $installments Número de parcelas (1-18)
     * @return float|null Taxa MDR em percentual (ex: 9.99)
     */
    public static function getRateFor(string $type, int $installments): ?float
    {
        $rate = self::active()
            ->where('payment_type', $type)
            ->where('installments', $installments)
            ->first();

        return $rate ? (float) $rate->mdr_rate : null;
    }

    /**
     * Retorna todas as taxas ativas organizadas por tipo
     * 
     * @return array ['debit' => [...], 'credit' => [...]]
     */
    public static function getAllActiveRates(): array
    {
        $rates = self::active()
            ->orderBy('payment_type')
            ->orderBy('installments')
            ->get();

        return [
            'debit' => $rates->where('payment_type', 'debit')->values()->toArray(),
            'credit' => $rates->where('payment_type', 'credit')->values()->toArray(),
        ];
    }
}
