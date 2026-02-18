<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Models;

use App\Domain\Perfumes\Enums\PerfumeRetailerStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerfumeRetailer extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'owner_name',
        'document',
        'whatsapp',
        'city',
        'state',
        'email',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PerfumeRetailerStatus::class,
        ];
    }

    public function samples(): HasMany
    {
        return $this->hasMany(PerfumeSample::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(PerfumeOrder::class);
    }

    public function activeSamples(): HasMany
    {
        return $this->samples()->whereIn('status', ['delivered', 'with_retailer']);
    }

    public function getWhatsappLinkAttribute(): string
    {
        $number = preg_replace('/\D/', '', $this->whatsapp);

        return "https://wa.me/55{$number}";
    }
}
