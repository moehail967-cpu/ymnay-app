<?php

namespace Modules\Campaign\Entities;

use App\Models\Admin;
use App\Models\MediaUploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'status',
        'start_date',
        'end_date',
        'admin_id',
        'discount_type',
        'discount_value',
    ];

    protected $casts = [
        'start_date'     => 'datetime',
        'end_date'       => 'datetime',
        'discount_value' => 'decimal:2',
    ];

    /**
     * Computed status: upcoming / active / ended — derived from dates regardless of stored status.
     * The stored status field is still used for 'draft' / manual 'ended' overrides.
     */
    public function getComputedStatusAttribute(): string
    {
        if ($this->status === 'draft') return 'draft';
        if ($this->status === 'ended') return 'ended';

        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) return 'upcoming';
        if ($this->end_date   && $now->gt($this->end_date))   return 'ended';

        return 'active';
    }

    public function campaignImage(): HasOne
    {
        return $this->hasOne(MediaUploader::class, 'id', 'image');
    }

    public function products(): HasMany
    {
        return $this->hasMany(CampaignProduct::class);
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }
}
