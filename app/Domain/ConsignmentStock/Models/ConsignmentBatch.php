<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Models;

use App\Domain\Supplier\Models\Supplier;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsignmentBatch extends Model
{
    use HasUlids;

    protected $fillable = [
        'supplier_id',
        'batch_code',
        'name',
        'model',
        'storage',
        'color',
        'condition',
        'supplier_cost',
        'suggested_price',
        'total_quantity',
        'notes',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'supplier_cost' => 'decimal:2',
            'suggested_price' => 'decimal:2',
            'total_quantity' => 'integer',
            'received_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $batch) {
            if (!$batch->batch_code) {
                $supplier = Supplier::find($batch->supplier_id);
                $batch->batch_code = static::generateBatchCode(
                    $supplier?->name ?? '',
                    $batch->received_at ?? now(),
                );
            }
        });
    }

    public static function generateBatchCode(string $supplierName, mixed $receivedAt): string
    {
        $initials = static::extractInitials($supplierName);
        $date = \Carbon\Carbon::parse($receivedAt)->format('ymd');
        $prefix = $initials . $date;

        $lastBatch = static::where('batch_code', 'like', $prefix . '-%')
            ->orderByDesc('batch_code')
            ->first();

        if ($lastBatch) {
            $lastSeq = (int) substr($lastBatch->batch_code, strrpos($lastBatch->batch_code, '-') + 1);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . '-' . str_pad((string) $nextSeq, 2, '0', STR_PAD_LEFT);
    }

    private static function extractInitials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $initials = '';

        foreach ($words as $word) {
            if ($word !== '') {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }
        }

        return $initials ?: 'XX';
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ConsignmentStockItem::class, 'batch_id');
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(ConsignmentPriceHistory::class, 'batch_id');
    }

    public function getProductSignatureAttribute(): string
    {
        return implode('|', [
            strtolower(trim($this->name ?? '')),
            strtolower(trim($this->model ?? '')),
            strtolower(trim($this->storage ?? '')),
            strtolower(trim($this->color ?? '')),
            $this->supplier_id,
        ]);
    }
}
