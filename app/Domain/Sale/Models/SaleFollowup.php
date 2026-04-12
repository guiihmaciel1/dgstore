<?php

declare(strict_types=1);

namespace App\Domain\Sale\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleFollowup extends Model
{
    use HasUlids;

    protected $fillable = [
        'sale_id',
        'user_id',
        'contacted_at',
        'method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at' => 'datetime',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
