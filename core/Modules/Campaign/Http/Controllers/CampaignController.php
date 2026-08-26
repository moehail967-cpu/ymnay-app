<?php

namespace Modules\Campaign\Http\Controllers;

use App\Helpers\FlashMsg;
use App\Helpers\SanitizeInput;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Campaign\Entities\Campaign;
use Modules\Campaign\Entities\CampaignProduct;
use Modules\Product\Entities\Product;

class CampaignController extends Controller
{
    const BASE_URL = 'campaign::backend.';

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:campaign-list|campaign-create|campaign-edit|campaign-delete', ['only', ['index']]);
        $this->middleware('permission:campaign-create', ['only', ['store']]);
        $this->middleware('permission:campaign-edit', ['only', ['update']]);
        $this->middleware('permission:campaign-delete', ['only', ['destroy', 'bulk_action']]);
    }

    public function index()
    {
        $all_campaigns = Campaign::with(['campaignImage', 'products'])->get();
        return view(self::BASE_URL.'all', compact('all_campaigns'));
    }

    public function create()
    {
        // T16/T17: only exclude products in currently active published campaigns
        $all_campaign_products = CampaignProduct::whereHas('campaign', function ($q) {
            $q->where('status', 'publish')
              ->where('start_date', '<=', now())
              ->where('end_date', '>=', now());
        })->pluck('product_id')->toArray();

        $all_products = Product::with('inventory')->where('status_id', '1')->whereNotIn('id', $all_campaign_products)->get();
        return view(self::BASE_URL.'new', compact('all_products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_name'       => 'required|string|max:191',
            'campaign_start_date' => 'required',
            'campaign_end_date'   => 'required',
            'discount_type'       => 'nullable|in:percentage,flat',
            'discount_value'      => 'nullable|numeric|min:0',
            'product_id.*'        => 'required',
        ],
        [
            'product_id.*.required' => __('Products are required')
        ]);

        // T24: warn admin if start_date is in the past
        $pastDateWarning = null;
        if (now()->isAfter($request->campaign_start_date)) {
            $pastDateWarning = __('Note: campaign start date is in the past — the campaign is active immediately.');
        }

        // T22: ensure each campaign price is below the product regular price
        if (!empty($request->product_id) && !empty($request->campaign_price)) {
            foreach ($request->product_id as $key => $product_id) {
                $product = Product::find($product_id);
                if (!$product) continue;
                $regular_price = $product->sale_price ?: $product->price;
                $campaign_price = (float) ($request->campaign_price[$key] ?? 0);
                if ($campaign_price >= $regular_price) {
                    return back()->withErrors([
                        'campaign_price' => __('Campaign price must be less than the regular price for: ') . $product->name,
                    ])->withInput();
                }
            }
        }

        $validated_product_data = $this->getValidatedCampaignProducts($request);

        try{
            DB::beginTransaction();
            $campaign = Campaign::create([
                'title'          => SanitizeInput::esc_html($request->campaign_name),
                'image'          => $request->image,
                'status'         => $request->status,
                'start_date'     => $request->campaign_start_date,
                'end_date'       => $request->campaign_end_date,
                'admin_id'       => \Auth::guard('admin')->id(),
                'discount_type'  => $request->discount_type  ?? 'percentage',
                'discount_value' => $request->discount_value ?? 0,
            ]);

            if ($campaign->id && !empty($validated_product_data['product_id'])) {
                $this->insertCampaignProducts($campaign->id, $validated_product_data);
            }

            DB::commit();
            $response = back()->with(FlashMsg::create_succeed('Campaign'));
            if ($pastDateWarning) {
                $response = $response->with('warning', $pastDateWarning);
            }
            return $response;
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with(FlashMsg::create_failed('Campaign'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Campaign\Campaign  $campaign
     * @return Response
     */
    public function show(Campaign $campaign)
    {
        //
    }


    public function edit(Campaign $item)
    {
        $campaign = Campaign::with(['products', 'products.product','admin'])->findOrFail($item->id);

        // T16/T17: exclude products only from OTHER active published campaigns
        $other_campaign_products = CampaignProduct::where('campaign_id', '!=', $campaign->id)
            ->whereHas('campaign', function ($q) {
                $q->where('status', 'publish')
                  ->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
            })->pluck('product_id')->toArray();

        $all_products = Product::with('inventory')->where('status_id', 1)->whereNotIn('id', $other_campaign_products)->get();

        return view(self::BASE_URL.'edit', compact('campaign', 'all_products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'campaign_name'       => 'required|string|max:191',
            'image'               => 'required|string',
            'status'              => 'required|string',
            'campaign_start_date' => 'required',
            'campaign_end_date'   => 'required',
            'discount_type'       => 'nullable|in:percentage,flat',
            'discount_value'      => 'nullable|numeric|min:0',
            'product_id.*'        => 'required',
        ], [
            'product_id.*.required' => __('Products are required')
        ]);

        $validated_product_data = $this->getValidatedCampaignProducts($request);

        // T24: warn if start_date is in the past
        $pastDateWarning = null;
        if (now()->isAfter($request->campaign_start_date)) {
            $pastDateWarning = __('Note: campaign start date is in the past — the campaign is active immediately.');
        }

        // T22: ensure each campaign price is below the product regular price
        if (!empty($request->product_id) && !empty($request->campaign_price)) {
            foreach ($request->product_id as $key => $product_id) {
                $product = Product::find($product_id);
                if (!$product) continue;
                $regular_price = $product->sale_price ?: $product->price;
                $campaign_price = (float) ($request->campaign_price[$key] ?? 0);
                if ($campaign_price >= $regular_price) {
                    return back()->withErrors([
                        'campaign_price' => __('Campaign price must be less than the regular price for: ') . $product->name,
                    ])->withInput();
                }
            }
        }

        DB::beginTransaction();
        try{
            Campaign::findOrFail($request->id)->update([
                'title'          => SanitizeInput::esc_html($request->campaign_name),
                'image'          => $request->image,
                'status'         => $request->status,
                'start_date'     => $request->campaign_start_date,
                'end_date'       => $request->campaign_end_date,
                'discount_type'  => $request->discount_type  ?? 'percentage',
                'discount_value' => $request->discount_value ?? 0,
            ]);

            $this->updateCampaignProducts($request->id, $request, $validated_product_data);

            DB::commit();
            $response = back()->with(FlashMsg::update_succeed('Campaign'));
            if ($pastDateWarning) {
                $response = $response->with('warning', $pastDateWarning);
            }
            return $response;
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with(FlashMsg::update_failed('Campaign'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Campaign\Campaign  $campaign
     * @return Response
     */
    public function destroy(Campaign $item)
    {
        try {
            DB::beginTransaction();
            $products = $item->products;
            if ($products->count()) {
                foreach ($products as $product) {
                    $product->delete();
                }
            }
            $item_deleted = $item->delete();
            DB::commit();

            return back()->with(FlashMsg::delete_succeed('Campaign'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function bulk_action(Request $request)
    {
        try {
            DB::beginTransaction();
            $all_campaigns = Campaign::whereIn('id', $request->ids)->delete();
            $campaign_products = CampaignProduct::whereIn('campaign_id', $request->ids)->delete();
            DB::commit();
            return 'ok';
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function getProductPrice(Request $request)
    {
        $price = Product::findOrFail($request->id)->price;
        return response()->json(['price' => $price], 200);
    }

    public function deleteProductSingle(Request $request)
    {
        return (bool) CampaignProduct::findOrFail($request->id)->delete();
    }

    /**====================================================================
     *                  CAMPAIGN PRODUCT FUNCTIONS
    ==================================================================== */
    public function updateCampaignProducts($campaign_id, $request, $validated_product_data)
    {
//        try {
//            DB::beginTransaction();

            $pastCampaignProducts = CampaignProduct::where('campaign_id', $campaign_id)->pluck('product_id')->toArray() ?? [];
            if (!empty($pastCampaignProducts))
            {
                $unused_product = array_diff($pastCampaignProducts, $validated_product_data['product_id']);
            }

            $this->deleteCampaignProducts($campaign_id);
            if (!empty($validated_product_data['product_id'])) {
                $this->insertCampaignProducts($campaign_id, $validated_product_data);
            }

//            DB::commit();
//        }catch(\Throwable $th) {
//            DB::rollBack();
//
//            return false;
//        }
    }

    public function getValidatedCampaignProducts(Request $request): array
    {
        return $request->validate([
            'product_id'       => 'nullable|array',
            'campaign_price'   => 'nullable|array',
            'units_for_sale'   => 'nullable|array',
            'product_id.*'     => 'nullable|exists:products,id',
            'campaign_price.*' => 'nullable|numeric|min:0',
            'units_for_sale.*' => 'nullable|integer|min:0',
        ]);
    }

    public function insertCampaignProducts(int $campaign_id, array $products_data): bool
    {
        $insert_data = [];

        foreach ($products_data['product_id'] as $key => $product_id) {
            $insert_data[] = [
                'campaign_id'    => $campaign_id,
                'product_id'     => $product_id,
                'campaign_price' => $products_data['campaign_price'][$key] ?? 0,
                'units_for_sale' => ($products_data['units_for_sale'][$key] ?: null),
                'sold_count'     => 0,
            ];
        }

        return !empty($insert_data) && (bool) CampaignProduct::insert($insert_data);
    }

    public function deleteCampaignProducts($all_product_id): bool
    {
        return (bool) CampaignProduct::where('campaign_id', $all_product_id)->delete();
    }

    public function suggestions(Request $request)
    {
        if (! \Schema::hasTable('campaign_suggestions')) {
            $suggestions = collect();
        } else {
            $tenantId    = function_exists('tenant') && tenant() ? (int) tenant()->id : null;
            $suggestions = DB::table('campaign_suggestions')
                ->where('tenant_id', $tenantId)
                ->orderBy('status')
                ->orderByDesc('stock_count')
                ->paginate(20);
        }

        return view(self::BASE_URL . 'suggestions', compact('suggestions'));
    }

    public function dismissSuggestion(Request $request, int $id)
    {
        if (\Schema::hasTable('campaign_suggestions')) {
            DB::table('campaign_suggestions')->where('id', $id)->update(['status' => 'dismissed', 'updated_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function actSuggestion(Request $request, int $id)
    {
        if (\Schema::hasTable('campaign_suggestions')) {
            DB::table('campaign_suggestions')->where('id', $id)->update(['status' => 'acted', 'updated_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

}
