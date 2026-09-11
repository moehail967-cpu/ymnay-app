<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeMarketplace extends Model
{
    protected $table = 'theme_marketplace';

    protected $fillable = [
        'slug', 'name', 'description', 'niche', 'version',
        'preview_url', 'download_url', 'thumbnail_url',
        'price', 'is_free', 'is_installed', 'installed_version',
        'latest_version', 'update_available', 'screenshots',
        'tags', 'author', 'license_type',
    ];

    protected $casts = [
        'is_free'          => 'boolean',
        'is_installed'     => 'boolean',
        'update_available' => 'boolean',
        'screenshots'      => 'array',
        'tags'             => 'array',
        'price'            => 'decimal:2',
    ];

    public function getIsFreeAttribute(): bool
    {
        return (float) $this->price === 0.0;
    }

    public function scopeInstalled($query)
    {
        return $query->where('is_installed', true);
    }

    public function scopeWithUpdates($query)
    {
        return $query->where('update_available', true)->where('is_installed', true);
    }
}
