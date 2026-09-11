<?php

namespace Modules\Campaign\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Campaign\Entities\Campaign;
use Modules\Product\Entities\Product;

class FrontendCampaignController extends Controller
{
    public function campaignPage($id, $any = null)
    {
        $campaign = Campaign::with([
            'campaignImage',
            'products.product.inventory',
            'products.product.campaign_product.campaign',
        ])->findOrFail($id);

        // Only show published, currently active campaigns on frontend
        if ($campaign->computed_status === 'draft') {
            abort(404);
        }

        // Build a flat product collection with campaign prices injected
        $products = $campaign->products->map(function ($cp) {
            $product = $cp->product;
            if (!$product) return null;

            // Ensure campaign_product relation is accessible from product
            $product->setRelation('campaign_product', $cp);
            return $product;
        })->filter()->values();

        return themeView('shop.campaign', compact('campaign', 'products'));
    }
}
