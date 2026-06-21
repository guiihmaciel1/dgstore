<?php

declare(strict_types=1);

namespace App\Domain\CRM\Models;

use App\Domain\CRM\Enums\DealActivityType;
use App\Domain\CRM\Enums\LeadSource;
use App\Domain\Customer\Models\Customer;
use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
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
        'source',
        'source_metadata',
        'lead_source',
        'temperature',
        'next_action',
        'next_action_at',
        'last_interaction_at',
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
            'source_metadata' => 'array',
            'lead_source' => LeadSource::class,
            'next_action_at' => 'datetime',
            'last_interaction_at' => 'datetime',
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

    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function productInterests(): HasMany
    {
        return $this->hasMany(ProductInterest::class);
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

    public function scopeFromWhatsApp(Builder $query): Builder
    {
        return $query->where('source', 'whatsapp');
    }

    public function scopeStale(Builder $query, int $hours = 24): Builder
    {
        return $query->open()->where(function (Builder $q) use ($hours) {
            $q->where('last_interaction_at', '<', now()->subHours($hours))
                ->orWhereNull('last_interaction_at');
        });
    }

    public function scopeNeedsFollowup(Builder $query): Builder
    {
        return $query->open()
            ->whereNotNull('next_action_at')
            ->where('next_action_at', '<=', now());
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
        if ($this->last_interaction_at) {
            return (int) $this->last_interaction_at->diffInDays(now());
        }

        $lastActivity = $this->activities()->latest()->first();

        if (! $lastActivity) {
            return (int) $this->created_at->diffInDays(now());
        }

        return (int) $lastActivity->created_at->diffInDays(now());
    }

    public function updateLastInteraction(): void
    {
        $this->update(['last_interaction_at' => now()]);
    }

    public function getIsStaleAttribute(): bool
    {
        $ref = $this->last_interaction_at ?? $this->created_at;

        return $this->isOpen() && $ref->diffInHours(now()) >= 24;
    }

    public function getWaitingHoursAttribute(): float
    {
        $ref = $this->last_interaction_at ?? $this->created_at;

        return round($ref->diffInHours(now(), true), 1);
    }

    public function getWaitingTimeLabelAttribute(): string
    {
        $hours = $this->waiting_hours;

        if ($hours < 1) {
            $minutes = (int) round($hours * 60);

            return $minutes <= 1 ? 'agora' : "{$minutes}min";
        }

        if ($hours < 24) {
            return (int) $hours . 'h';
        }

        $days = (int) floor($hours / 24);

        return $days . 'd';
    }

    public function getWaitingUrgencyAttribute(): string
    {
        $hours = $this->waiting_hours;

        if ($hours < 1) {
            return 'green';
        }
        if ($hours < 4) {
            return 'yellow';
        }
        if ($hours < 24) {
            return 'orange';
        }

        return 'red';
    }

    public function getIsFollowupOverdueAttribute(): bool
    {
        return $this->isOpen()
            && $this->next_action_at
            && $this->next_action_at->lt(now());
    }
}
