<?php

namespace Modules\CommissionManage\Listeners;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;
use App\Helpers\PaymentGatewayRenderHelper;
use Modules\CommissionManage\Events\OrderCompleted;
use Modules\CommissionManage\Entities\CommissionHistory;
use Modules\CommissionManage\Entities\CommissionSetting;
use Modules\CommissionManage\Http\Services\CommissionCalculatorService;

class CreateCommissionRecord
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
public function handle(OrderCompleted $event): void
{

    $productOrderId = $event->productOrder;
    $tenant = $event->tenant;

    $productOrder = \App\Models\ProductOrder::find($productOrderId);

    if (!$productOrder) {
        return;
    }

    $setting = CommissionSetting::first();
    if (!$setting || !$setting->hasCommission()) {
        return;
    }

    $service = app(CommissionCalculatorService::class);
    $commissionAmount = $service->calculateOrderCommission($productOrder, $setting);
    if ($commissionAmount <= 0) {
        return;
    }

    $tenantId = strtolower(optional($tenant)->id ?? 'MAIN');
    $unique_order_id = $tenantId . '-' . strtoupper(Str::random(3)) . $productOrder->id;

    $useLandlordGateway = commissionSettingType(); // landlord gateway enabled ?


    $pendingGateways = ['manual_payment', 'cash_on_delivery'];

    if (in_array($productOrder->payment_gateway, $pendingGateways)) {
        $commissionStatus = 'pending';
    } elseif (!commissionSettingType()) {
        $commissionStatus = 'pending';
    } else {
        $commissionStatus = 'complete';
    }


    $commission_history = CommissionHistory::create([
        'tenant_id' => $tenant->id,
        'user_id' => $tenant->user_id,
        'order_id' => $productOrder->id,
        'cus_id' => $productOrder->user_id,
        'order_total' => $productOrder->total_amount,
        'commission_rate' => $setting->commission_rate,
        'commission_amount' => $commissionAmount,
        'payment_status' => $productOrder->payment_status,
        'uid' => $unique_order_id,
        'status' => $commissionStatus,
        'payment_gateway' => $productOrder->payment_gateway,
    ]);
    /**
     * ============================
     *  NOW MERGE creditWalletAfterOrder HERE
     * ============================
     */
    try {
        DB::beginTransaction();

        $activeGateways = PaymentGatewayRenderHelper::getActiveGateways();
        $useLandlord = !empty($activeGateways) && ($activeGateways[0]['from_landlord'] ?? false);

        // Only wallet credit when landlord gateway is ON
        if (!$useLandlord) {
            DB::rollBack();
            return;
        }

        // Commission setting check
        if (!$setting) {
            DB::rollBack();
            return;
        }

        // Calculate wallet credit amount
        $walletAmount = walletAmountAfterOrder($productOrder, $setting);

        if ($walletAmount <= 0) {
            DB::rollBack();
            return;
        }

        $user_id = tenant()->user_id;

        // Find or create wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user_id],
            ['balance' => 0, 'status' => 1]
        );

        // Add amount
        $wallet->increment('balance', $walletAmount);

        // History
        WalletHistory::create([
            'user_id' => $user_id,
            'amount' => $walletAmount,
            'payment_gateway' => $productOrder->payment_gateway,
            'payment_status' => 'complete',
            'status' => 1,
            'order_id' => $productOrder->id,
        ]);

        DB::commit();

    } catch (\Exception $e) {
        DB::rollBack();
    }
}


}
