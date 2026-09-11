<?php

namespace Modules\FomoNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class FomoSyntheticEntry extends Model
{
    protected $table = 'fomo_synthetic_entries';

    protected $fillable = [
        'tenant_id', 'product_id', 'variant_label',
        'display_name', 'location', 'active', 'sort_order',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
