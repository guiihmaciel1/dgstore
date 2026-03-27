<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Models;

use App\Domain\ConsignmentStock\Enums\ConsignmentMovementType;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignmentStockMovement extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = [
        'consignment_item_id',
        'user_id',
        'type',
        'quantity',
        'reason',
        'reference_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ConsignmentMovementType::class,
            'quantity' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $movement) {
            if (!$movement->created_at) {
                $movement->created_at = now();
            }
        });
    }

    public function consignmentItem(): BelongsTo
    {
        return $this->belongsTo(ConsignmentStockItem::class, 'consignment_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
