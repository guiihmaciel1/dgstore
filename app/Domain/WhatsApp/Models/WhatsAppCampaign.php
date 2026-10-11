<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Models;

use App\Domain\Customer\Models\CustomerTag;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppCampaign extends Model
{
    protected $table = 'whatsapp_campaigns';

    protected $fillable = [
        'name',
        'message',
        'media_url',
        'status',
        'created_by',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'failed_count',
        'scheduled_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(CustomerTag::class, 'campaign_customer_tag', 'campaign_id', 'customer_tag_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class, 'campaign_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['sending', 'scheduled']);
    }
}
