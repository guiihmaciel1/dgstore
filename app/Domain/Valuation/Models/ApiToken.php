<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    use HasUlids;

    protected $fillable = [
        'provider',
        'access_token',
        'refresh_token',
        'expires_at',
        'external_user_id',
        'scopes',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'scopes' => 'array',
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
        ];
    }

    public function isValid(): bool
    {
        return $this->expires_at && $this->expires_at->isAfter(now()->addMinutes(5));
    }

    public function needsRefresh(): bool
    {
        return !$this->isValid() && !empty($this->refresh_token);
    }

    public static function forProvider(string $provider): ?self
    {
        return static::where('provider', $provider)->first();
    }
}
