<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Models;

use App\Domain\CRM\Models\Deal;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    use HasUlids;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'wa_message_id',
        'from_phone',
        'from_name',
        'message_type',
        'message_body',
        'transcription',
        'referral_source',
        'referral_headline',
        'raw_payload',
        'deal_id',
        'delivery_status',
        'direction',
        'local_media_path',
        'media_mime_type',
        'reply_to_id',
        'reply_to_body',
    ];

    protected function casts(): array
    {
        return [
            'raw_payload' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
