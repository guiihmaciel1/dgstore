<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerfumeCustomer extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'cpf',
        'email',
        'address',
        'birth_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(PerfumeReservation::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(PerfumeSale::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereNull('deleted_at');
    }

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('cpf', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->phone);
        
        if (strlen($phone) === 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7);
        }
        
        return $this->phone;
    }

    public function getFormattedCpfAttribute(): ?string
    {
        if (!$this->cpf) {
            return null;
        }

        $cpf = preg_replace('/\D/', '', $this->cpf);
        
        if (strlen($cpf) === 11) {
            return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
        }
        
        return $this->cpf;
    }

    public function getWhatsappLinkAttribute(): string
    {
        $number = preg_replace('/\D/', '', $this->phone);

        return "https://wa.me/55{$number}";
    }
}
