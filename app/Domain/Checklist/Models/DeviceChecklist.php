<?php

declare(strict_types=1);

namespace App\Domain\Checklist\Models;

use App\Domain\Product\Models\Product;
use App\Domain\Sale\Models\TradeIn;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DeviceChecklist extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'device_info',
        'sections',
        'total_items',
        'passed_items',
        'failed_items',
        'status',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'device_info' => 'array',
            'sections' => 'array',
            'total_items' => 'integer',
            'passed_items' => 'integer',
            'failed_items' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'checklist_id');
    }

    public function tradeIn(): HasOne
    {
        return $this->hasOne(TradeIn::class, 'checklist_id');
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getSummaryLabelAttribute(): string
    {
        $label = $this->passed_items . '/' . $this->total_items . ' OK';
        if ($this->failed_items > 0) {
            $label .= ', ' . $this->failed_items . ' falha(s)';
        }
        return $label;
    }

    public function getDeviceNameAttribute(): string
    {
        $info = $this->device_info;
        if ($info && isset($info['modelName'])) {
            $parts = [$info['modelName']];
            if (!empty($info['capacity'])) $parts[] = $info['capacity'];
            return implode(' ', $parts);
        }
        return $this->name;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'Aprovado',
            'failed' => 'Reprovado',
            default => 'Incompleto',
        };
    }

    public function isLinked(): bool
    {
        return $this->product()->exists() || $this->tradeIn()->exists();
    }
}
