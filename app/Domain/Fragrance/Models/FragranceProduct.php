<?php

declare(strict_types=1);

namespace App\Domain\Fragrance\Models;

use App\Domain\Fragrance\Enums\FragranceGender;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FragranceProduct extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'fragrantica_id',
        'fragrantica_url',
        'name',
        'brand',
        'slug',
        'gender',
        'description',
        'concentration',
        'year',
        'size_ml',
        'image_url',
        'brand_logo_url',
        'rating',
        'votes_count',
        'sale_price',
        'stock_quantity',
        'seasons',
        'day_night',
        'active',
        'sort_order',
        'scraped_at',
    ];

    protected function casts(): array
    {
        return [
            'gender'         => FragranceGender::class,
            'rating'         => 'decimal:2',
            'sale_price'     => 'decimal:2',
            'votes_count'    => 'integer',
            'stock_quantity' => 'integer',
            'seasons'        => 'array',
            'day_night'      => 'array',
            'active'         => 'boolean',
            'sort_order'     => 'integer',
            'scraped_at'     => 'datetime',
            'year'           => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name, $product->brand);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?string $brand = null): string
    {
        $base = Str::slug(($brand ? $brand . ' ' : '') . $name);
        $slug = $base;
        $counter = 1;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    // ---- Relationships ----

    public function accords(): HasMany
    {
        return $this->hasMany(FragranceAccord::class)->orderBy('sort_order');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(FragranceNote::class)->orderBy('sort_order');
    }

    public function topNotes(): HasMany
    {
        return $this->notes()->where('layer', 'top');
    }

    public function heartNotes(): HasMany
    {
        return $this->notes()->where('layer', 'heart');
    }

    public function baseNotes(): HasMany
    {
        return $this->notes()->where('layer', 'base');
    }

    // ---- Scopes ----

    public function scopeActive(Builder $query): void
    {
        $query->where('active', true);
    }

    public function scopeByGender(Builder $query, string $gender): void
    {
        $query->where('gender', $gender);
    }

    // ---- Accessors ----

    public function getCatalogUrlAttribute(): string
    {
        return route('catalogo.show', $this->slug);
    }

    public function getRatingStarsAttribute(): string
    {
        if (! $this->rating) {
            return '—';
        }

        $rating = (float) $this->rating;
        $full = (int) floor($rating);
        $half = ($rating - $full) >= 0.5 ? 1 : 0;
        $empty = 5 - $full - $half;

        return str_repeat('★', $full) . str_repeat('½', $half) . str_repeat('☆', $empty);
    }
}
