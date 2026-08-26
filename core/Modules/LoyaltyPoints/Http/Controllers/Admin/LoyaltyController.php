<?php

namespace Modules\LoyaltyPoints\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\LoyaltyPoints\src\LoyaltyService;

class LoyaltyController extends Controller
{
    private function plugin(): mixed
    {
        return app(\App\PluginSystem\PluginManager::class)->get('loyalty-points');
    }

    private function service(): LoyaltyService
    {
        return new LoyaltyService;
    }

    public function index()
    {
        $customers = $this->service()->getAllCustomers();

        $userIds = $customers->pluck('user_id')->all();
        $users = DB::table('users')->whereIn('id', $userIds)->pluck('email', 'id');

        $customers = $customers->map(function ($row) use ($users) {
            $row->email = $users[$row->user_id] ?? 'Unknown';
            return $row;
        });

        return view('loyalty-points::admin.index', compact('customers'));
    }

    public function show($userId)
    {
        $service = $this->service();
        $balance = $service->getBalance((int) $userId);
        $transactions = $service->getTransactions((int) $userId, 100);
        $email = DB::table('users')->where('id', $userId)->value('email');

        return view('loyalty-points::admin.show', compact('userId', 'balance', 'transactions', 'email'));
    }

    public function adjust(Request $request, $userId)
    {
        $request->validate([
            'type' => 'required|in:add,deduct',
            'points' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $service = $this->service();
        $note = $request->input('note', 'Manual adjustment');

        if ($request->type === 'add') {
            $service->addPoints((int) $userId, (int) $request->points, 'manual', $note);
            return response()->json(['type' => 'success', 'msg' => __('Points added successfully')]);
        }

        $ok = $service->deductPoints((int) $userId, (int) $request->points, 'manual', $note);
        if (!$ok) {
            return response()->json(['type' => 'danger', 'msg' => __('Insufficient balance')]);
        }
        return response()->json(['type' => 'success', 'msg' => __('Points deducted successfully')]);
    }

    public function settings()
    {
        $plugin = $this->plugin();
        $settings = [
            'enabled' => $plugin->get_option('enabled', '0'),
            'earn_rate' => $plugin->get_option('earn_rate', 1),
            'redeem_rate' => $plugin->get_option('redeem_rate', 100),
            'min_points_redeem' => $plugin->get_option('min_points_redeem', 100),
            'max_redeem_percent' => $plugin->get_option('max_redeem_percent', 50),
            'point_expiry_days' => $plugin->get_option('point_expiry_days', 0),
            'earn_on_signup' => $plugin->get_option('earn_on_signup', 0),
        ];

        return view('loyalty-points::admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $plugin = $this->plugin();
        $plugin->update_option('enabled', $request->has('enabled') ? '1' : '0');
        $plugin->update_option('earn_rate', (int) $request->input('earn_rate', 1));
        $plugin->update_option('redeem_rate', (int) $request->input('redeem_rate', 100));
        $plugin->update_option('min_points_redeem', (int) $request->input('min_points_redeem', 100));
        $plugin->update_option('max_redeem_percent', (int) $request->input('max_redeem_percent', 50));
        $plugin->update_option('point_expiry_days', (int) $request->input('point_expiry_days', 0));
        $plugin->update_option('earn_on_signup', (int) $request->input('earn_on_signup', 0));

        return redirect()->back()->with('success', __('Settings saved'));
    }
}
