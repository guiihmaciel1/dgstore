<?php

declare(strict_types=1);

namespace App\Domain\Customer\Models;

use App\Domain\Sale\Models\Sale;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cpf',
        'address',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    // Métodos auxiliares

    public function getFormattedCpfAttribute(): ?string
    {
        if (!$this->cpf) {
            return null;
        }

        $cpf = preg_replace('/\D/', '', $this->cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->phone);
        
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        }
        
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
    }

    public function getTotalPurchasesAttribute(): float
    {
        return (float) $this->sales()
            ->whereNotNull('sold_at')
            ->where('payment_status', '!=', 'cancelled')
            ->sum('total');
    }

    public function getPurchasesCountAttribute(): int
    {
        return $this->sales()
            ->whereNotNull('sold_at')
            ->where('payment_status', '!=', 'cancelled')
            ->count();
    }
}
