<?php

declare(strict_types=1);

namespace App\Domain\Marketing\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingPrice extends Model
{
    use HasUlids;

    public const CATEGORIES = [
        'iphone' => 'iPhone',
        'ipad' => 'iPad',
        'macbook' => 'MacBook',
        'applewatch' => 'Apple Watch',
        'airpods' => 'AirPods',
        'outros' => 'Outros',
    ];

    public const COLORS = [
        '#000000' => 'Preto',
        '#1c1c1e' => 'Meia-Noite',
        '#f5f5f7' => 'Branco',
        '#f9f3ee' => 'Estelar',
        '#3634a3' => 'Ultramarino',
        '#0071e3' => 'Azul',
        '#0d9488' => 'Teal',
        '#4caf50' => 'Verde',
        '#bf5af2' => 'Roxo',
        '#f472b6' => 'Rosa',
        '#dc2626' => 'Vermelho',
        '#f97316' => 'Laranja',
        '#f5d08e' => 'Dourado',
        '#c0c0c0' => 'Prata',
        '#5c5c5e' => 'Grafite',
        '#b0a898' => 'Titânio Natural',
        '#c2a97c' => 'Deserto',
        '#f0ede8' => 'Titânio Branco',
        '#3c3c3d' => 'Titânio Preto',
        '#7a1e3e' => 'Bordô',
        '#d1e8f0' => 'Glacial',
    ];

    protected $fillable = [
        'name',
        'category',
        'storage',
        'color',
        'color_hex',
        'price',
        'cost_price',
        'notes',
        'active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(MarketingPriceImage::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Disponibilidade vem do próprio nome: "(Encomenda)" / "(Sob Encomenda)".
     */
    public function isOnOrder(): bool
    {
        return (bool) preg_match('/encomenda/i', (string) $this->name);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->price, 2, ',', '.');
    }
}
