<?php

declare(strict_types=1);

namespace App\Domain\Fragrance\Models;

use App\Domain\Fragrance\Enums\FragranceNoteLayer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FragranceNote extends Model
{
    protected $fillable = [
        'fragrance_product_id',
        'layer',
        'name',
        'image_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'layer'      => FragranceNoteLayer::class,
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(FragranceProduct::class, 'fragrance_product_id');
    }
}
