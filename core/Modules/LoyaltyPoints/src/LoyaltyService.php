<?php

namespace Modules\LoyaltyPoints\src;

use Illuminate\Support\Facades\DB;
use Modules\LoyaltyPoints\Models\LoyaltyTransaction;

class LoyaltyService
{
    public function getBalance(int $userId): int
    {
        $row = LoyaltyTransaction::where('user_id', $userId)
            ->orderByDesc('id')
            ->value('balance_after');

        return (int) $row;
    }

    public function addPoints(int $userId, int $points, string $type, string $note = '', ?int $orderId = null, ?\Carbon\Carbon $expiresAt = null): int
    {
        if ($points <= 0) return $this->getBalance($userId);

        $balance = $this->getBalance($userId) + $points;

        LoyaltyTransaction::create([
            'user_id'       => $userId,
            'order_id'      => $orderId,
            'type'          => $type,
            'points'        => $points,
            'balance_after' => $balance,
            'note'          => $note,
            'expires_at'    => $expiresAt,
            'created_at'    => now(),
        ]);

        return $balance;
    }

    public function deductPoints(int $userId, int $points, string $type, string $note = '', ?int $orderId = null): bool
    {
        $balance = $this->getBalance($userId);
        if ($balance < $points) return false;

        LoyaltyTransaction::create([
            'user_id'       => $userId,
            'order_id'      => $orderId,
            'type'          => $type,
            'points'        => -$points,
            'balance_after' => $balance - $points,
            'note'          => $note,
            'expires_at'    => null,
            'created_at'    => now(),
        ]);

        return true;
    }

    public function expirePoints(): void
    {
        $users = LoyaltyTransaction::where('expires_at', '<=', now())
            ->where('type', 'earn')
            ->whereNotExists(function ($q) {
                $q->from('loyalty_transactions as lt2')
                    ->whereColumn('lt2.user_id', 'loyalty_transactions.user_id')
                    ->where('lt2.type', 'expire')
                    ->whereColumn('lt2.note', DB::raw('CAST(loyalty_transactions.id AS CHAR)'));
            })
            ->selectRaw('user_id, SUM(points) as expiring')
            ->groupBy('user_id')
            ->get();

        foreach ($users as $row) {
            $balance = $this->getBalance($row->user_id);
            $expire  = min((int) $row->expiring, $balance);
            if ($expire <= 0) continue;

            LoyaltyTransaction::create([
                'user_id'       => $row->user_id,
                'order_id'      => null,
                'type'          => 'expire',
                'points'        => -$expire,
                'balance_after' => $balance - $expire,
                'note'          => 'Points expired',
                'expires_at'    => null,
                'created_at'    => now(),
            ]);
        }
    }

    public function getTransactions(int $userId, int $limit = 50): \Illuminate\Support\Collection
    {
        return LoyaltyTransaction::where('user_id', $userId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function getAllCustomers(): \Illuminate\Support\Collection
    {
        return LoyaltyTransaction::selectRaw('
                user_id,
                SUM(CASE WHEN points > 0 THEN points ELSE 0 END) as total_earned,
                SUM(CASE WHEN points < 0 THEN ABS(points) ELSE 0 END) as total_redeemed,
                MAX(created_at) as last_activity
            ')
            ->groupBy('user_id')
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($row) {
                $row->balance = $this->getBalance($row->user_id);
                return $row;
            });
    }
}
