<?php

declare(strict_types=1);

namespace App\Domain\System\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

    public function scopeSince(Builder $query, \DateTimeInterface $date): Builder
    {
        return $query->where('fetched_at', '>=', $date)->orderByDesc('fetched_at');
    }

    public static function latestRate(): ?self
    {
        return static::orderByDesc('fetched_at')->first();
    }

    public static function lastTen(): Collection
    {
        return static::recent(10)->get();
    }

    public static function lastThirty(): Collection
    {
        return static::recent(30)->get();
    }

    /**
     * Retorna o min/max de um período para exibição no gráfico.
     */
    public static function statsForPeriod(int $days = 30): array
    {
        $since = now()->subDays($days);
        $rates = static::since($since)->get();

        if ($rates->isEmpty()) {
            return ['min' => null, 'max' => null, 'count' => 0, 'rates' => $rates];
        }

        return [
            'min'   => (float) $rates->min('rate'),
            'max'   => (float) $rates->max('rate'),
            'count' => $rates->count(),
            'rates' => $rates,
        ];
    }
}
