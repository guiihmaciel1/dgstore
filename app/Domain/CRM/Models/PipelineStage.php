<?php

declare(strict_types=1);

namespace App\Domain\CRM\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PipelineStage extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'color',
        'position',
        'is_default',
        'is_won',
        'is_lost',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_default' => 'boolean',
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
        ];
    }

    // Relacionamentos

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'pipeline_stage_id');
    }

    // Scopes

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    public function scopeActive($query)
    {
        return $query->where('is_won', false)->where('is_lost', false);
    }

    // Helpers

    public function isTerminal(): bool
    {
        return $this->is_won || $this->is_lost;
    }

    public function getDealsValueAttribute(): float
    {
        return (float) $this->deals()->sum('value');
    }
}
