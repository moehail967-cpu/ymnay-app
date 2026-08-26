<?php

namespace Modules\TierPricing\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\TierPricing\Src\TierPricingService;

class TierPricingApiController extends Controller
{
    private function tenantId(): ?int
    {
        try { return function_exists('tenant') && tenant() ? (int) tenant()->id : null; } catch (\Throwable) { return null; }
    }

    public function forProduct(int $id)
    {
        $service = new TierPricingService($this->tenantId());
        $tiers   = $service->getTiersForProduct($id);

        return response()->json(['tiers' => $tiers]);
    }
}
