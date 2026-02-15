<?php

declare(strict_types=1);

namespace App\Domain\CRM\Models;

use App\Domain\CRM\Enums\DealActivityType;
use App\Domain\Customer\Models\Customer;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deal extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'customer_id',
        'pipeline_stage_id',
        'title',
        'description',
        'product_interest',
        'value',
        'phone',
        'expected_close_date',
        'position',
        'won_at',
        'lost_at',
        'lost_reason',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expected_close_date' => 'date',
            'position' => 'integer',
            'won_at' => 'datetime',
            'lost_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DealActivity::class)->orderBy('created_at', 'desc');
    }

    // Scopes

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('won_at')->whereNull('lost_at');
    }

    public function scopeWon(Builder $query): Builder
    {
        return $query->whereNotNull('won_at');
    }

    public function scopeLost(Builder $query): Builder
    {
        return $query->whereNotNull('lost_at');
    }

    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // Helpers

    public function isOpen(): bool
    {
        return $this->won_at === null && $this->lost_at === null;
    }

    public function isWon(): bool
    {
        return $this->won_at !== null;
    }

    public function isLost(): bool
    {
        return $this->lost_at !== null;
    }

    public function isOverdue(): bool
    {
        return $this->isOpen()
            && $this->expected_close_date
            && $this->expected_close_date->lt(today());
    }

    public function markAsWon(): void
    {
        $wonStage = PipelineStage::where('is_won', true)->first();

        $this->update([
            'won_at' => now(),
            'lost_at' => null,
            'lost_reason' => null,
            'pipeline_stage_id' => $wonStage?->id ?? $this->pipeline_stage_id,
        ]);

        $this->logActivity(DealActivityType::Won, 'Negócio ganho!');
    }

    public function markAsLost(string $reason = ''): void
    {
        $lostStage = PipelineStage::where('is_lost', true)->first();

        $this->update([
            'lost_at' => now(),
            'won_at' => null,
            'lost_reason' => $reason,
            'pipeline_stage_id' => $lostStage?->id ?? $this->pipeline_stage_id,
        ]);

        $this->logActivity(DealActivityType::Lost, $reason ?: 'Negócio perdido.');
    }

    public function reopen(string $stageId): void
    {
        $this->update([
            'won_at' => null,
            'lost_at' => null,
            'lost_reason' => null,
            'pipeline_stage_id' => $stageId,
        ]);

        $this->logActivity(DealActivityType::Reopened, 'Negócio reaberto.');
    }

    public function moveToStage(PipelineStage $newStage): void
    {
        $oldStage = $this->stage;

        $this->update(['pipeline_stage_id' => $newStage->id]);

        $this->logActivity(
            DealActivityType::StageChange,
            "Movido de \"{$oldStage->name}\" para \"{$newStage->name}\"",
            ['from_stage_id' => $oldStage->id, 'to_stage_id' => $newStage->id]
        );
    }

    public function logActivity(DealActivityType $type, ?string $description = null, ?array $metadata = null): DealActivity
    {
        return $this->activities()->create([
            'user_id' => auth()->id() ?? $this->user_id,
            'type' => $type->value,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        $phone = $this->phone ?? $this->customer?->phone;

        if (! $phone) {
            return null;
        }

        $phone = preg_replace('/\D/', '', $phone);

        return "https://wa.me/{$phone}";
    }

    public function getDaysSinceLastActivityAttribute(): int
    {
        $lastActivity = $this->activities()->latest()->first();

        if (! $lastActivity) {
            return (int) $this->created_at->diffInDays(now());
        }

        return (int) $lastActivity->created_at->diffInDays(now());
    }
}
