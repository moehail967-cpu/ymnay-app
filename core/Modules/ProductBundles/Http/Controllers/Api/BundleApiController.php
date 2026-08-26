<?php

namespace Modules\ProductBundles\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\ProductBundles\Src\BundleService;

class BundleApiController extends Controller
{
    private function tenantId(): ?int
    {
        try { return function_exists('tenant') && tenant() ? (int) tenant()->id : null; } catch (\Throwable) { return null; }
    }

    public function check()
    {
        $service = new BundleService($this->tenantId());
        $result  = $service->checkCartForBundle();

        if (! $result) {
            return response()->json(['applied' => false]);
        }

        [$bundle, $discount] = $result;

        return response()->json([
            'applied'   => true,
            'bundle'    => $bundle->name,
            'discount'  => $discount,
        ]);
    }

    public function forProduct(int $id)
    {
        $service = new BundleService($this->tenantId());
        $bundles = $service->getBundlesForProduct($id);

        return response()->json(['bundles' => $bundles]);
    }
}
