<?php

namespace Modules\MultiCurrency\Http\Controllers\Frontend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MultiCurrency\Models\Currency;
use Modules\MultiCurrency\Models\TenantCurrency;

class CurrencySwitchController extends Controller
{
    public function switch(Request $request): JsonResponse
    {
        $code = strtoupper(trim($request->input('code', '')));

        if (!$code) {
            return response()->json(['error' => 'Missing currency code.'], 422);
        }

        // Must exist in the global registry and be active
        if (!Currency::findByCode($code)) {
            return response()->json(['error' => 'Unknown currency.'], 422);
        }

        // Must be enabled by this specific tenant
        if (!TenantCurrency::isEnabled($code)) {
            return response()->json(['error' => 'Currency not available in this store.'], 422);
        }

        session(['mc_currency' => $code]);

        return response()->json(['success' => true, 'code' => $code]);
    }
}
