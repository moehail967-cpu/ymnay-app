<?php

namespace Modules\CommissionManage\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class CommissionSelectedGateway extends Model
{
use HasFactory , CentralConnection;
    protected $fillable = [
        'commission_setting_id',
        'original_gateway_id',
        'name',
        'image',
        'description',
        'status',
        'test_mode',
        'credentials',
    ];

    public function commissionSetting()
    {
        return $this->belongsTo(CommissionSetting::class, 'commission_setting_id');
    }
}
