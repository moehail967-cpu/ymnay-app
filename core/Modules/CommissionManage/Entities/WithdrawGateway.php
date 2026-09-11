<?php

namespace Modules\CommissionManage\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_setting_id',
        'gateway_id',
        'name',
        'fields',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'fields' => 'array',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationship
    public function commissionSetting():BelongsTo
    {
        return $this->belongsTo(CommissionSetting::class);
    }

    // Helper Methods
    public function getRequiredFields(): array
    {
        if (!$this->fields) return [];

        return collect($this->fields)->filter(function ($field) {
            return $field['required'] ?? false;
        })->toArray();
    }

    public function getFieldByLabel(string $label): ?array
    {
        if (!$this->fields) return null;

        return collect($this->fields)->firstWhere('label', $label);
    }

    public function hasField(string $label): bool
    {
        return $this->getFieldByLabel($label) !== null;
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
