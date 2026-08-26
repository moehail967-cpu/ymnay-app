<?php

namespace Modules\RefundModule\Http\Controllers\admin;

use App\Events\SupportMessage;
use App\Helpers\FlashMsg;
use App\Helpers\ResponseMessage;
use App\Mail\BasicMail;
use App\Models\Language;
use App\Models\OrderProducts;
use App\Models\ProductOrder;
use App\Models\SupportDepartment;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Campaign\Entities\CampaignProduct;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductInventory;
use Modules\Product\Entities\ProductInventoryDetail;
use Modules\RefundModule\Entities\RefundChat;
use Modules\RefundModule\Entities\RefundChatMessage;
use Modules\RefundModule\Entities\RefundProduct;
use Modules\Wallet\Entities\Wallet;

class AdminRefundController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:refund-list|refund-create|refund-edit|refund-delete',['only' => ['all_tickets','priority_change','status_change']]);
        $this->middleware('permission:refund-create',['only' => ['new_ticket','store_ticket']]);
        $this->middleware('permission:refund-edit',['only' => ['priority_change','status_change']]);
        $this->middleware('permission:refund-delete',['only' => ['delete','bulk_action']]);
    }

    private const BASE_PATH = 'refundmodule::tenant.admin.refund.';

    public function index()
    {
        $refunds = RefundProduct::all();
        return view(self::BASE_PATH.'index')->with(['refunds' => $refunds]);
    }
    public function status_change(Request $request){
        $request->validate([
            'status' => 'required|string|max:191'
        ]);

        $refund = RefundProduct::findOrFail($request->id);
        $prev_status = $refund->status;
        $refund->update(['status' => $request->status]);

        // S20 T1: restock inventory when refund is approved
        if ($request->status === 'approved' && $prev_status !== 'approved') {
            $this->restockRefundedProduct($refund);
        }

        return response()->json('ok');
    }

    private function restockRefundedProduct(RefundProduct $refund): void
    {
        $order_product = OrderProducts::where('order_id', $refund->order_id)
            ->where('product_id', $refund->product_id)
            ->first();

        if (!$order_product) return;

        $qty = $order_product->quantity ?? 1;

        DB::transaction(function () use ($refund, $order_product, $qty) {
            // Restore variant-level stock
            if ($order_product->variant_id) {
                $variant = ProductInventoryDetail::where('product_id', $refund->product_id)
                    ->where('id', $order_product->variant_id)
                    ->lockForUpdate()->first();
                if ($variant) {
                    $variant->increment('stock_count', $qty);
                }
            }

            // Restore product-level inventory
            $inventory = ProductInventory::where('product_id', $refund->product_id)->lockForUpdate()->first();
            if ($inventory) {
                $inventory->increment('stock_count', $qty);
                $inventory->sold_count = max(0, ($inventory->sold_count ?? 0) - $qty);
                $inventory->save();
            }

            // Reverse campaign sold_count if applicable
            $cp = CampaignProduct::where('product_id', $refund->product_id)->lockForUpdate()->first();
            if ($cp && $cp->sold_count >= $qty) {
                $cp->decrement('sold_count', $qty);
            }
        });
    }

    public function product_view($id)
    {
        if (!empty($id)) {
            $order_product = OrderProducts::where('product_id', $id)->first();
            $order_details = ProductOrder::find($order_product->order_id);
            $product = Product::find($id);
        }

        return view(self::BASE_PATH. 'view', compact('order_product','order_details', 'product'));
    }
}
