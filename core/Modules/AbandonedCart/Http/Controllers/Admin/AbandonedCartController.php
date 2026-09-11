<?php

namespace Modules\AbandonedCart\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbandonedCartController extends Controller
{
    private function tenantId(): ?int
    {
        try { return function_exists('tenant') && tenant() ? (int) tenant()->id : null; } catch (\Throwable) { return null; }
    }

    public function index()
    {
        $tenantId = $this->tenantId();
        $carts = DB::table('abandoned_carts')
            ->where('tenant_id', $tenantId)
            ->orderByDesc('updated_at')
            ->paginate(25);

        // Stats
        $total     = DB::table('abandoned_carts')->where('tenant_id', $tenantId)->count();
        $converted = DB::table('abandoned_carts')->where('tenant_id', $tenantId)->where('status', 'converted')->count();
        $rate      = $total > 0 ? round($converted / $total * 100, 1) : 0;
        $revenue   = DB::table('abandoned_carts')->where('tenant_id', $tenantId)->where('status', 'converted')->sum('cart_total');

        return view('abandoned-cart::admin.index', compact('carts', 'total', 'converted', 'rate', 'revenue'));
    }

    public function show($id)
    {
        $tenantId = $this->tenantId();
        $cart = DB::table('abandoned_carts')->where('id', $id)->where('tenant_id', $tenantId)->firstOrFail();
        $cart->cart_items = json_decode($cart->cart_items, true);
        return view('abandoned-cart::admin.show', compact('cart'));
    }

    public function resend(Request $request, $id)
    {
        $tenantId = $this->tenantId();
        $cart = DB::table('abandoned_carts')->where('id', $id)->where('tenant_id', $tenantId)->first();
        if (!$cart) return response()->json(['type' => 'danger', 'msg' => __('Cart not found')]);

        try {
            $plugin = app(\App\PluginSystem\PluginManager::class)->get('abandoned-cart');
            $plugin->sendRemindersForCart($cart);
            return response()->json(['type' => 'success', 'msg' => __('Reminder sent successfully')]);
        } catch (\Throwable $e) {
            return response()->json(['type' => 'danger', 'msg' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $tenantId = $this->tenantId();
        DB::table('abandoned_carts')->where('id', $id)->where('tenant_id', $tenantId)->delete();
        return response()->json(['type' => 'success', 'msg' => __('Deleted')]);
    }

    public function settings()
    {
        $plugin = app(\App\PluginSystem\PluginManager::class)->get('abandoned-cart');
        $settings = [
            'delay_hours'   => $plugin->get_option('delay_hours', 2),
            'max_reminders' => $plugin->get_option('max_reminders', 2),
            'sender_name'   => $plugin->get_option('sender_name', ''),
            'email_subject' => $plugin->get_option('email_subject', 'You left something behind!'),
            'email_body'    => $plugin->get_option('email_body', ''),
        ];
        return view('abandoned-cart::admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $plugin = app(\App\PluginSystem\PluginManager::class)->get('abandoned-cart');
        $plugin->update_option('delay_hours',   $request->input('delay_hours', 2));
        $plugin->update_option('max_reminders', $request->input('max_reminders', 2));
        $plugin->update_option('sender_name',   $request->input('sender_name', ''));
        $plugin->update_option('email_subject', $request->input('email_subject', ''));
        $plugin->update_option('email_body',    $request->input('email_body', ''));

        return redirect()->back()->with('success', __('Settings saved'));
    }
}
