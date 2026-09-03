<?php

namespace Modules\YmnayCustom\Entities;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class ManualWallet extends Model
{
    use CentralConnection;

    protected $table = 'ymnay_manual_wallets';
    protected $fillable = ['name', 'description', 'logo', 'status', 'sort_order'];
    protected $casts = ['status' => 'boolean', 'sort_order' => 'integer'];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? global_asset('assets/landlord/uploads/ymnay-wallets/' . $this->logo) : null;
    }
}
