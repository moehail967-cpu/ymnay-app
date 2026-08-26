<?php

namespace App\Http\Services;

use App\Actions\Payment\Tenant\PaymentGatewayIpn;
use App\Enums\PaymentRouteEnum;
use App\Enums\ProductTypeEnum;
use App\Helpers\FlashMsg;
use App\Helpers\Payment\PaymentGatewayCredential;
use App\Helpers\PaymentGatewayRenderHelper;
use App\Mail\StockOutEmail;
use App\Models\OrderProducts;
use App\Models\PaymentGateway;
use App\Models\ProductOrder;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\Campaign\Entities\Campaign;
use Modules\Campaign\Entities\CampaignProduct;
use Modules\CommissionManage\Entities\CommissionSelectedGateway;
use Modules\DigitalProduct\Entities\DigitalProductDownload;
use Modules\Product\Entities\ProductInventory;
use Modules\Product\Entities\ProductInventoryDetail;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;
use Illuminate\Support\Facades\Auth;
use Modules\CommissionManage\Entities\CommissionSetting;

class CheckoutToPaymentService
{
    public static function checkoutToGateway($data) // getting all parameter in one array
    {

        $payment_details = ProductOrder::find($data['order_log_id']);
        // dd($payment_details);
        $payment_gateway = $payment_details->payment_gateway;
        $checkout_type = $payment_details->checkout_type;
        $amount_to_charge = $payment_details->total_amount;

        do_action('nazmart:payment_initiated', $payment_details, $payment_details);

        $ordered_products = OrderProducts::where('order_id', $payment_details->id)->get();

        foreach ($ordered_products ?? [] as $product) {
            $dynamic_campaign = get_product_dynamic_price($product);

            try {
                DB::transaction(function () use ($product, $dynamic_campaign) {
                    // --- Campaign limit check + sold_count increment (with row lock) ---
                    if ($dynamic_campaign['is_running']) {
                        $cp = CampaignProduct::where('product_id', $product->product_id)
                            ->where('campaign_id', $product->campaign_product->campaign_id)
                            ->lockForUpdate()->first();

                        if ($cp) {
                            $units_for_sale = $cp->units_for_sale; // null = unlimited

                            if ($units_for_sale !== null) {
                                if ($cp->sold_count >= $units_for_sale) {
                                    throw new \Exception(__('Campaign sell limitation is over, You can not purchase this product right now'));
                                }
                                if ($units_for_sale < ($product->quantity + $cp->sold_count)) {
                                    throw new \Exception(__('Campaign sell limitation is over, You can not purchase current amount'));
                                }
                            }

                            $cp->increment('sold_count', $product->quantity);

                            // Auto-mark campaign as ended when units exhausted
                            if ($units_for_sale !== null && $cp->sold_count >= $units_for_sale) {
                                Campaign::where('id', $cp->campaign_id)->update(['status' => 'ended']);
                            }
                        }
                    }

                    // --- Variant stock: lock row, validate, decrement ---
                    if ($product->variant_id !== null) {
                        $variants = ProductInventoryDetail::where(['product_id' => $product->product_id, 'id' => $product->variant_id])
                            ->lockForUpdate()->get();
                        foreach ($variants as $variant) {
                            if ($variant->stock_count < $product->quantity) {
                                throw new \Exception(__('Insufficient variant stock for') . ': ' . $product->name);
                            }
                            $variant->decrement('stock_count', $product->quantity);
                            $variant->increment('sold_count', $product->quantity);
                        }
                    }

                    // --- Product-level inventory: lock row, validate, decrement ---
                    if ($product->product_type == ProductTypeEnum::PHYSICAL) {
                        $product_inventory = ProductInventory::where('product_id', $product->product_id)->lockForUpdate()->first();
                        if (!$product_inventory || $product_inventory->stock_count < $product->quantity) {
                            throw new \Exception(__('Insufficient stock for') . ': ' . $product->name);
                        }
                        $product_inventory->decrement('stock_count', $product->quantity);
                        $product_inventory->sold_count = ($product_inventory->sold_count ?? 0) + $product->quantity;
                        $product_inventory->save();
                    }
                });
            } catch (\Exception $e) {
                return back()->withErrors($e->getMessage());
            }
        }

        if ($product->product_type == ProductTypeEnum::PHYSICAL) {
            self::checkStock(); // Checking Stock for warning and email notification
        }

        if ($product->product_type == ProductTypeEnum::DIGITAL) {
            DigitalProductDownload::create([
                'product_id' => $product->product_id,
                'download_count' => 1,
                'user_id' => Auth::guard('web')->user()->id
            ]);
        }
//dd($payment_gateway);
        if ($payment_gateway != 'manual_payment' && $checkout_type === 'digital') {
            $credential_function = 'get_' . $payment_gateway . '_credential';

            if (!method_exists((new PaymentGatewayCredential()), $credential_function)) {
                $custom_data['request'] = $data;
                $custom_data['payment_details'] = $payment_details->toArray();
                $custom_data['total'] = $amount_to_charge;

                //add extra param support to the shop checkout payment system
                $custom_data['payment_type'] = "shop_checkout";
                $custom_data['payment_for'] = "tenant";
                $custom_data['cancel_url'] = route(PaymentRouteEnum::STATIC_CANCEL_ROUTE);
                $custom_data['success_url'] = route(PaymentRouteEnum::SUCCESS_ROUTE, random_int(111111, 999999) . $payment_details->id . random_int(111111, 999999));

                $charge_customer_class_namespace = getChargeCustomerMethodNameByPaymentGatewayNameSpace($payment_gateway);
                $charge_customer_method_name = getChargeCustomerMethodNameByPaymentGatewayName($payment_gateway);

                abort_if(empty($charge_customer_method_name), 403); // If custom payment gateway not found

                $custom_charge_customer_class_object = new $charge_customer_class_namespace;
                if (class_exists($charge_customer_class_namespace) && method_exists($custom_charge_customer_class_object, $charge_customer_method_name)) {
                    Cart::instance("default")->destroy();
                    // added in wallet
                    return $custom_charge_customer_class_object->$charge_customer_method_name($custom_data);
                } else {
                    return back()->with(FlashMsg::explain('danger', 'Incorrect Class or Method'));
                }
            } else {
                $params = self::common_charge_customer_data($amount_to_charge, $payment_details, route('tenant.user.frontend.' . $payment_gateway . '.ipn'));
                // Credit wallet after successful payment
                return PaymentGatewayCredential::$credential_function()->charge_customer($params);
            }

        } else {
            if ($payment_gateway != null) {
                $payment_details->update(['transaction_id' => $payment_details->transaction_id]);
            }
            $order_id = Str::random(6) . $payment_details->id . Str::random(6);
// Credit wallet for manual payment (after admin approval if needed)
            if ($payment_gateway == 'manual_payment') {
                // Don't credit immediately, wait for admin approval
            } else {

            }

            (new PaymentGatewayIpn())->send_order_mail($payment_details['id']);
            Cart::instance("default")->destroy();

            return redirect()->route(PaymentRouteEnum::SUCCESS_ROUTE, $order_id);
        }

        return redirect()->route('homepage');
    }

    private static function common_charge_customer_data($amount_to_charge, $payment_details, $ipn_url): array
    {
        $purchase_details = "Payment For Order ID: # {$payment_details->id}\n".
                "Payer Name: {$payment_details->name}\n".
                "Payer Email: {$payment_details->email}";

        $data = [
            'amount' => $amount_to_charge,
            'title' => 'Order ID: ' . $payment_details->id,
            'description' => $purchase_details,
            'order_id' => $payment_details->id,
            'track' => $payment_details->payment_track,
            'cancel_url' => route(PaymentRouteEnum::CANCEL_ROUTE, $payment_details->id),
            'success_url' => route(PaymentRouteEnum::SUCCESS_ROUTE, $payment_details->id),
            'email' => $payment_details->email,
            'name' => $payment_details->name,
            'payment_type' => 'order',
            'ipn_url' => $ipn_url,
        ];

        return $data;
    }

    private static function checkStock()
    {
        // Inventory Warnings
        $threshold_amount = get_static_option('stock_threshold_amount');

        $inventory_product_items = \Modules\Product\Entities\ProductInventoryDetail::where('stock_count', '<=', $threshold_amount)
            ->whereHas('is_inventory_warn_able', function ($query) {
                $query->where('is_inventory_warn_able', 1);
            })
            ->select('id', 'product_id')
            ->get();

        $inventory_product_items_id = !empty($inventory_product_items) ? $inventory_product_items->pluck('product_id')->toArray() : [];

        $products = \Modules\Product\Entities\Product::with('inventory')
            ->where('is_inventory_warn_able', 1)
            ->whereHas('inventory', function ($query) use ($threshold_amount) {
                $query->where('stock_count', '<=', $threshold_amount);
            })
            ->select('id')
            ->get();

        $products_id = !empty($products) ? $products->pluck('id')->toArray() : [];

        $every_filtered_product_id = array_unique(array_merge($inventory_product_items_id, $products_id));
        $all_products = \Modules\Product\Entities\Product::whereIn('id', $every_filtered_product_id)->select('id', 'name', 'is_inventory_warn_able')->get();

        if (count($all_products) > 0) {
            foreach ($all_products as $item) {
                $inventory = $item?->inventory?->stock_count;
                $variant = $item->inventoryDetail->where('stock_count', '<=', $threshold_amount)->first();
                $variant = !empty($variant) ? $variant->stock_count : [];

                $stock = min($inventory, $variant);
                $item->stock = $stock;
            }

            $email = get_static_option('order_receiving_email') ?? get_static_option('tenant_site_global_email');
            try {
                Mail::to($email)->send(new StockOutEmail($all_products));
            } catch (\Exception $e) {

            }
        }
    }
}
