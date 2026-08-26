<?php

namespace Modules\TrackingPixel\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class TrackingPixelEventLog extends Model
{
    use TenantConnection;

    public $timestamps = false;

    protected $table = 'tracking_pixel_event_log';

    protected $fillable = [
        'tenant_id',
        'channel',
        'event_type',
        'order_id',
        'http_status',
        'response_snippet',
        'event_id',
        'fired_at',
    ];

    protected $casts = [
        'fired_at' => 'datetime',
    ];
}
