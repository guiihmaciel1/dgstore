<?php

declare(strict_types=1);

namespace App\Domain\Warranty\Models;

use App\Domain\User\Models\User;
use App\Domain\Warranty\Enums\WarrantyClaimStatus;
use App\Domain\Warranty\Enums\WarrantyClaimType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class WarrantyClaim extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'warranty_id',
        'user_id',
        'type',
        'status',
        'reason',
        'resolution',
        'opened_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => WarrantyClaimType::class,
            'status' => WarrantyClaimStatus::class,
            'opened_at' => 'datetime',
            'resolved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [WarrantyClaimStatus::Opened, WarrantyClaimStatus::InProgress]);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereIn('status', [WarrantyClaimStatus::Resolved, WarrantyClaimStatus::Denied]);
    }

    public function scopeSupplier(Builder $query): Builder
    {
        return $query->where('type', WarrantyClaimType::Supplier);
    }

    public function scopeCustomer(Builder $query): Builder
    {
        return $query->where('type', WarrantyClaimType::Customer);
    }

    // Métodos auxiliares

    public function isOpen(): bool
    {
        return $this->status->isOpen();
    }

    public function isClosed(): bool
    {
        return $this->status->isClosed();
    }

    public function markAsInProgress(): void
    {
        $this->update([
            'status' => WarrantyClaimStatus::InProgress,
        ]);
    }

    public function resolve(string $resolution): void
    {
        $this->update([
            'status' => WarrantyClaimStatus::Resolved,
            'resolution' => $resolution,
            'resolved_at' => now(),
        ]);
    }

    public function deny(string $resolution): void
    {
        $this->update([
            'status' => WarrantyClaimStatus::Denied,
            'resolution' => $resolution,
            'resolved_at' => now(),
        ]);
    }

    public function getDurationInDaysAttribute(): ?int
    {
        if (!$this->resolved_at) {
            return $this->opened_at->diffInDays(now());
        }

        return $this->opened_at->diffInDays($this->resolved_at);
    }
}
