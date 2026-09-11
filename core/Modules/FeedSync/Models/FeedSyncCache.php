<?php

namespace Modules\FeedSync\Models;

use Illuminate\Database\Eloquent\Model;

class FeedSyncCache extends Model
{
    protected $table = 'feed_sync_cache';
    public $timestamps = false;

    protected $fillable = ['tenant_id', 'feed_type', 'content', 'generated_at'];

    protected $casts = [
        'generated_at' => 'datetime',
    ];
}
