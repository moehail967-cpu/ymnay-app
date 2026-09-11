<?php
namespace Modules\CommissionManage\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\CommissionManage\Entities\CommissionHistory;
use Modules\CommissionManage\Entities\CommissionSetting;
use Modules\CommissionManage\Entities\TenantWithdrawRequest;
use Modules\CommissionManage\Entities\CommissionSelectedGateway;
use Modules\CommissionManage\Entities\WithdrawGateway;
use Modules\CommissionManage\Http\Requests\CommissionSettingRequest;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;

class CommissionManageController extends Controller
{
    public function history(Request $request)
    {
        $tab = $request->get('tab', 'commission');

        if ($tab === 'withdraw') {
            return $this->withdrawRequests($request);
        }

        return $this->commissionHistory($request);
    }

    private function commissionHistory(Request $request)
    {
        $duration = $request->get('duration', 30);

        $query = CommissionHistory::with(['tenant', 'user'])
            ->where('created_at', '>=', now()->subDays($duration));

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', 'LIKE', '%' . $request->tenant_id . '%');
        }

        if ($request->filled('order_id')) {
            $query->where('uid', $request->order_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $commissions = $query->latest()->paginate(15);
        $statistics = $this->calculateCommissionStatistics($duration);
        $tab = 'commission';
        $withdraws = collect();

        return view('commissionmanage::history', compact(
            'commissions',
            'statistics',
            'tab',
            'withdraws'
        ));
    }

    public function withdrawRequests(Request $request)
    {
        $duration = $request->get('duration', 30);

        $query = TenantWithdrawRequest::with('tenant')
            ->where('created_at', '>=', now()->subDays($duration));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', 'LIKE', '%' . $request->tenant_id . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $withdraws = $query->latest()->paginate(15);
        $statistics = $this->calculateWithdrawStatistics($duration);
        $tab = 'withdraw';
        $commissions = collect();
        return view('commissionmanage::history', compact(
            'withdraws',
            'statistics',
            'tab',
            'commissions'
        ));
    }

    private function calculateCommissionStatistics($duration)
    {
        $dateFilter = now()->subDays($duration);

        return [
            'total_commission' => CommissionHistory::where('created_at', '>=', $dateFilter)
                ->sum('commission_amount'),
            'paid_commission' => CommissionHistory::where('created_at', '>=', $dateFilter)
                ->where('status', 'complete')
                ->sum('commission_amount'),
            'pending_commission' => CommissionHistory::where('created_at', '>=', $dateFilter)
                ->where('payment_status', 'pending')
                ->sum('commission_amount'),
            'unpaid_commission' => CommissionHistory::where('created_at', '>=', $dateFilter)
                ->where('status', '!=', 'complete')
                ->sum('commission_amount'),
        ];
    }

    private function calculateWithdrawStatistics($duration)
    {
        $dateFilter = now()->subDays($duration);

        return [
            'total_requests' => TenantWithdrawRequest::where('created_at', '>=', $dateFilter)->count(),
            'pending_requests' => TenantWithdrawRequest::where('created_at', '>=', $dateFilter)
                ->where('status', 'pending')->count(),
            'approved_requests' => TenantWithdrawRequest::where('created_at', '>=', $dateFilter)
                ->where('status', 'complete')->count(),
            'rejected_requests' => TenantWithdrawRequest::where('created_at', '>=', $dateFilter)
                ->where('status', 'cancel')->count(),
            'pending_amount' => TenantWithdrawRequest::where('created_at', '>=', $dateFilter)
                ->where('status', 'pending')->sum('amount'),
            'approved_amount' => TenantWithdrawRequest::where('created_at', '>=', $dateFilter)
                ->where('status', 'complete')->sum('amount'),
        ];
    }

    public function showWithdrawRequest($id)
    {
        $withdraw = TenantWithdrawRequest::with('tenant')->findOrFail($id);
        $tenant = User::where('id', $withdraw->tenant_id)->first();
        // dd($tenant);
        $html = view('commissionmanage::withdraw-details', compact(['withdraw','tenant']))->render();
        return response()->json(['html' => $html]);
    }

    public function approveWithdraw(Request $request, $id)
    {
        $request->validate([
            'transaction_id' => 'nullable|string',
            'note' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('withdraw-images', 'public');
        }


        try {
            $withdraw = TenantWithdrawRequest::findOrFail($id);

            if ($withdraw->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => __('Withdraw request already processed')
                ], 400);
            }

            DB::beginTransaction();

            // Get tenant wallet
            $wallet = Wallet::where('user_id', $withdraw->tenant_id)->lockForUpdate()->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet not found'
                ], 404);
            }

            // Check balance
            if ($wallet->balance < $withdraw->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient wallet balance'
                ], 400);
            }

            // Decrease wallet balance
            $wallet->balance -= $withdraw->amount;
            $wallet->save();

            // Update withdraw request
            $withdraw->update([
                'status' => 'complete',
                'approved_by' => Auth::guard('admin')->id(),
                'approved_at' => now(),
                'transaction_id' => $request->transaction_id,
                'image' => $imagePath,
                'note' => $request->note,
            ]);

            // Create wallet history
            WalletHistory::create([
                'user_id' => $withdraw->tenant_id,
                'amount' => -$withdraw->amount,
                'type' => 'withdraw',
                'payment_status' => 'complete',
                'note' => 'Withdraw approved by admin',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdraw request approved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve: ' . $e->getMessage()
            ], 500);
        }
    }


    public function rejectWithdraw(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        try {
            $withdraw = TenantWithdrawRequest::findOrFail($id);

            if ($withdraw->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => __('Withdraw request already processed')
                ], 400);
            }

            DB::beginTransaction();

            $withdraw->update([
                'status' => 'cancel',
                'approved_by' => Auth::guard('admin')->id(),
                'approved_at' => now(),
                'note' => $request->note,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Withdraw request rejected')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportWithdraws(Request $request)
    {
        $duration = $request->get('duration', 30);

        $query = TenantWithdrawRequest::with('tenant')
            ->where('created_at', '>=', now()->subDays($duration));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', 'LIKE', '%' . $request->tenant_id . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $withdraws = $query->latest()->get();

        $filename = 'withdraw_requests_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($withdraws) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Tenant ID',
                'Tenant Name',
                'Amount',
                'Payment Method',
                'Status',
                'Transaction ID',
                'Note',
                'Created At',
                'Approved At'
            ]);

            foreach ($withdraws as $withdraw) {
                fputcsv($file, [
                    $withdraw->id,
                    $withdraw->tenant_id,
                    $withdraw->tenant->name ?? 'N/A',
                    $withdraw->amount,
                    $withdraw->payment_method,
                    $withdraw->status,
                    $withdraw->transaction_id ?? 'N/A',
                    $withdraw->note ?? 'N/A',
                    $withdraw->created_at->format('Y-m-d H:i:s'),
                    $withdraw->approved_at ? $withdraw->approved_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Keep all other existing methods...
    public function index()
    {
        $setting = CommissionSetting::where('user_id', Auth::guard('admin')->id())->first();
        $landlord_payment_gateways = [];
        if ($setting && $setting->payment_gateways) {
            $landlord_payment_gateways = is_array($setting->payment_gateways)
                ? $setting->payment_gateways
                : explode(',', $setting->payment_gateways);
        }
        // Format withdraw gateways for JavaScript
        $withdraw_gateways_json = $setting && $setting->withdrawGateways->count() > 0
            ? $setting->withdrawGateways->map(function ($gateway) {
                return [
                    'id' => $gateway->gateway_id,
                    'name' => $gateway->name,
                    'fields' => $gateway->fields ?? []
                ];
            })->toJson()
            : '[]';

        $commissionTypes = [
            'subscription_only' => 'Subscription Only',
            'subscription_and_commission' => 'Subscription + Commission',
            'commission_only' => 'Commission Only',
        ];

        $gatewaySources = [
            'landlord_gateway' => 'Use Landlord Gateway',
            'tenant_default_gateway' => "Use Tenant's Default Gateway",
        ];

        return view('commissionmanage::index', compact(
            'setting',
            'commissionTypes',
            'gatewaySources',
            'landlord_payment_gateways',
            'withdraw_gateways_json'
        ));
    }

    public function store(CommissionSettingRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::guard('admin')->id();

        if ($data['commission_type'] === 'subscription_only') {
            $data = array_merge($data, [
                'commission_rate' => 0,
                'payment_gateway_source' => 'tenant_default_gateway',
                'payment_gateways' => null,
                'selected_gateway' => null,
            ]);
        } elseif ($data['payment_gateway_source'] === 'landlord_gateway') {
            $data['payment_gateways'] = $request->filled('payment_gateways')
                ? explode(',', $request->input('payment_gateways'))
                : [];
        } else {
            $data['payment_gateways'] = null;
            $data['selected_gateway'] = null;
        }

        $commissionSetting = CommissionSetting::updateOrCreate(
            ['user_id' => $data['user_id']],
            $data
        );

        CommissionSelectedGateway::where('commission_setting_id', $commissionSetting->id)->delete();
        if (!empty($data['payment_gateways'])) {
            $gatewayIds = PaymentGateway::whereIn('name', $data['payment_gateways'])->pluck('id')->toArray();
            $gateways = PaymentGateway::whereIn('id', $gatewayIds)->get();
            foreach ($gateways as $gateway) {
                CommissionSelectedGateway::create([
                    'commission_setting_id' => $commissionSetting->id,
                    'original_gateway_id' => $gateway->id,
                    'name' => $gateway->name,
                    'image' => $gateway->image,
                    'description' => $gateway->description,
                    'status' => $gateway->status,
                    'test_mode' => $gateway->test_mode,
                    'credentials' => $gateway->credentials,
                ]);
            }
        }
        // Handle withdraw gateways
        return back()->with('success', __('Commission setting and gateways saved successfully!'));
    }
    /**
     * Handle withdraw gateways save/update
     */


    public function show($id)
    {
        $commission = CommissionHistory::with('tenant')->findOrFail($id);
        $html = view('commissionmanage::details', compact('commission'))->render();
        return response()->json(['html' => $html]);
    }

    public function markAsPaid($id)
    {
        try {
            $commission = CommissionHistory::findOrFail($id);

            $commission->update([
                'status' => 'complete',
                'payment_status' => 'success',
                'paid_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Commission marked as paid successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update commission status')
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $duration = $request->get('duration', 30);

        $query = CommissionHistory::with('tenant')
            ->where('created_at', '>=', now()->subDays($duration));

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', 'LIKE', '%' . $request->tenant_id . '%');
        }

        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $commissions = $query->latest()->get();

        $filename = 'commission_history_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($commissions) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Tenant ID',
                'Order UID',
                'Order Total',
                'Commission Rate',
                'Commission Amount',
                'Payment Status',
                'Status',
                'Created At'
            ]);

            foreach ($commissions as $commission) {
                fputcsv($file, [
                    $commission->id,
                    $commission->tenant_id,
                    $commission->uid,
                    $commission->order_total,
                    $commission->commission_rate . '%',
                    $commission->commission_amount,
                    $commission->payment_status,
                    $commission->status,
                    $commission->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy($id)
    {
        $setting = CommissionSetting::findOrFail($id);

        if ($setting->user_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }

        $setting->delete();

        return redirect()->route('landlord.dashboard')->with('success', 'Commission settings deleted successfully.');
    }
    public function destroyCommission($id)
    {
        $commission = CommissionHistory::findOrFail($id);
        $commission->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Commission History deleted successfully.'),
        ]);
    }

    public function destroyWithdraw($id)
    {
        $withdraw = TenantWithdrawRequest::findOrFail($id);
        $withdraw->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Withdraw request deleted successfully.')
        ]);
    }



}
