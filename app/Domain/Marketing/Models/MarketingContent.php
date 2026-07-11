<?php

declare(strict_types=1);

namespace App\Domain\Marketing\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MarketingContent extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'platform',
        'status',
        'scheduled_at',
        'image_path',
        'ai_generated',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'date',
            'ai_generated' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'reels' => 'Reels',
            'stories' => 'Stories',
            'post' => 'Post',
            'carousel' => 'Carrossel',
            default => $this->type,
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'idea' => 'Ideia',
            'production' => 'Em Produção',
            'published' => 'Publicado',
            default => $this->status,
        };
    }

    public function getPlatformLabel(): string
    {
        return match ($this->platform) {
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'whatsapp' => 'WhatsApp',
            'all' => 'Todas',
            default => $this->platform,
        };
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }
}
