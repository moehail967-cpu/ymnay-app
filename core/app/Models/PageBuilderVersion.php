<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageBuilderVersion extends Model
{
    protected $table = 'page_builder_versions';

    protected $fillable = [
        'page_id',
        'content',
        'widgets_data',
        'version_label',
        'is_pinned',
        'created_by',
    ];

    protected $casts = [
        'content'      => 'array',
        'widgets_data' => 'array',
        'is_pinned'    => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
