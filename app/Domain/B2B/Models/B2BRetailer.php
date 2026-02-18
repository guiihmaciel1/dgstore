<?php

declare(strict_types=1);

namespace App\Domain\B2B\Models;

use App\Domain\B2B\Enums\RetailerStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class B2BRetailer extends Authenticatable
{
    use HasUlids, Notifiable;

    protected $table = 'b2b_retailers';

    protected $fillable = [
        'store_name',
        'owner_name',
        'document',
        'whatsapp',
        'city',
        'state',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'status' => RetailerStatus::class,
            'password' => 'hashed',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(B2BOrder::class, 'b2b_retailer_id');
    }

    public function isApproved(): bool
    {
        return $this->status === RetailerStatus::Approved;
    }

    public function isPending(): bool
    {
        return $this->status === RetailerStatus::Pending;
    }

    public function isBlocked(): bool
    {
        return $this->status === RetailerStatus::Blocked;
    }

    public function getFormattedWhatsappAttribute(): string
    {
        $number = preg_replace('/\D/', '', $this->whatsapp);

        if (strlen($number) <= 11) {
            $number = '55' . $number;
        }

        return $number;
    }
}
