<?php

namespace Modules\CommissionManage\Http\Services;

use App\Models\Order;
use App\Models\ProductOrder;
use App\Models\PricePlan;
use Illuminate\Support\Facades\Log;
use Modules\CommissionManage\Entities\CommissionSetting;

class CommissionCalculatorService
{
    public function handleSubscription(PricePlan $plan, CommissionSetting $setting)
    {
        switch ($setting->commission_type) {
            case CommissionSetting::COMMISSION_TYPE_SUBSCRIPTION_ONLY:
                return $plan->price;

            case CommissionSetting::COMMISSION_TYPE_SUBSCRIPTION_AND_COMMISSION:
                return $plan->price;

            case CommissionSetting::COMMISSION_TYPE_COMMISSION_ONLY:
                return 0;

        }

        return $plan->price;
    }

    public function calculateOrderCommission(ProductOrder $productOrder, CommissionSetting $setting): float
    {
        if (! $setting->hasCommission()) {
            return 0;
        }
        return round(($productOrder->total_amount * $setting->commission_rate) / 100, 2);
    }
}

