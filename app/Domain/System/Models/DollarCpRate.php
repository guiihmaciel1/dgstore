<?php

declare(strict_types=1);

namespace App\Domain\System\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DollarCpRate extends Model
{
    protected $fillable = ['rate', 'fetched_at'];

    protected function casts(): array
    {
        return [
            'rate'       => 'decimal:4',
            'fetched_at' => 'datetime',
        ];
    }

    public function scopeRecent(Builder $query, int $limit = 10): Builder
    {
        return $query->orderByDesc('fetched_at')->limit($limit);
    }

    public static function latestRate(): ?self
    {
        return static::orderByDesc('fetched_at')->first();
    }

    public static function lastTen(): \Illuminate\Database\Eloquent\Collection
    {
        return static::recent(10)->get();
    }
}
