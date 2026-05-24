<?php

namespace App\Domain\Supplier\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SupplierUser extends Authenticatable
{
    use HasFactory, HasUlids, Notifiable;

    protected $fillable = [
        'supplier_id',
        'email',
        'password',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
