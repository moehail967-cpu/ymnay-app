<?php

namespace Modules\YemeniWallets\Entities;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class WalletPaymentProof extends Model
{
    use TenantConnection;
    protected $table = 'wallet_payment_proofs';

    protected $fillable = [
        'order_id',
        'catalog_wallet_id',
        'wallet_name',
        'screenshot_path',
        'verification_status',
        'admin_note',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function approve(?string $note = null): void
    {
        $this->update([
            'verification_status' => 'approved',
            'admin_note' => $note,
            'verified_at' => now(),
        ]);
    }

    public function reject(?string $note = null): void
    {
        $this->update([
            'verification_status' => 'rejected',
            'admin_note' => $note,
            'verified_at' => now(),
        ]);
    }
}
