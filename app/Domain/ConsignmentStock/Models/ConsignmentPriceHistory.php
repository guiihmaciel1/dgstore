<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignmentPriceHistory extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $table = 'consignment_price_history';

    protected $fillable = [
        'batch_id',
        'user_id',
        'old_supplier_cost',
        'new_supplier_cost',
        'old_suggested_price',
        'new_suggested_price',
        'reason',
        'affected_items_count',
        'affected_item_ids',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_supplier_cost' => 'decimal:2',
            'new_supplier_cost' => 'decimal:2',
            'old_suggested_price' => 'decimal:2',
            'new_suggested_price' => 'decimal:2',
            'affected_items_count' => 'integer',
            'affected_item_ids' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $history) {
            if (!$history->created_at) {
                $history->created_at = now();
            }
        });
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ConsignmentBatch::class, 'batch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
