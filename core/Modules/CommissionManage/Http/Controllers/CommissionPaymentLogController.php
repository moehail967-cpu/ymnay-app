<?php
namespace Modules\CommissionManage\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Modules\CommissionManage\Entities\CommissionHistory;
use Modules\CommissionManage\Entities\CommissionPaymentLog;


class CommissionPaymentLogController extends Controller{
   /**
     * Display payment logs list with filters
     */
    public function index(Request $request)
    {
        $query = CommissionPaymentLog::query();

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment gateway
        if ($request->filled('payment_gateway')) {
            $query->where('payment_gateway', 'like', '%' . $request->payment_gateway . '%');
        }

        // Filter by user ID
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by transaction ID
        if ($request->filled('transaction_id')) {
            $query->where('transaction_id', 'like', '%' . $request->transaction_id . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // Filter by amount range
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Filter by duration (default 30 days)
        $duration = $request->get('duration', 30);
        $dateFrom = Carbon::now()->subDays($duration);
        $query->where('created_at', '>=', $dateFrom);

        // Order and paginate
        $paymentLogs = $query->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate statistics
        $allPaymentLogs = CommissionPaymentLog::query();

        $totalPayments = $allPaymentLogs->count();

        $paidAmount = $allPaymentLogs
            ->where('payment_status', 'complete')
            ->sum('amount');

        $pendingAmount = $allPaymentLogs
            ->where('payment_status', 'pending')
            ->sum('amount');

        $failedAmount = $allPaymentLogs
            ->where('payment_status', 'failed')
            ->sum('amount');

        return view('commissionmanage::commission-pay-logs', [
            'paymentLogs' => $paymentLogs,
            'totalPayments' => $totalPayments,
            'paidAmount' => $paidAmount,
            'pendingAmount' => $pendingAmount,
            'failedAmount' => $failedAmount,
        ]);
    }

    /**
     * Get payment details via AJAX
     */

    public function getDetails(Request $request, $id)
{
    $paymentLog = CommissionPaymentLog::with('user')->findOrFail($id);

    // Decode commission_ids if it's a JSON string
    $commissionIds = $paymentLog->commission_ids;
    if (is_string($commissionIds)) {
        $commissionIds = json_decode($commissionIds, true) ?? [];
    } else {
        $commissionIds = is_array($commissionIds) ? $commissionIds : [];
    }

    // Get associated commission histories
    $commissions = [];
    if (!empty($commissionIds)) {
        $commissions = CommissionHistory::whereIn('id', $commissionIds)->get();
    }

    // Generate status badges
    $paymentLog->statusBadge = $this->getStatusBadge($paymentLog->payment_status);

    // Add badge to each commission
    foreach ($commissions as $commission) {
        $commission->statusBadge = $this->getCommissionStatusBadge($commission->payment_status);
    }

    $html = view('commissionmanage::payment-log-details', [
        'paymentLog' => $paymentLog,
        'commissions' => $commissions,
    ])->render();

    return response()->json(['html' => $html]);
}

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $paymentLog = CommissionPaymentLog::with('user')->findOrFail($id);

        return view('admin.commission.payment-log-edit', [
            'paymentLog' => $paymentLog,
        ]);
    }

    /**
     * Update payment log
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:success,pending,processing,failed',
            'payment_date' => 'nullable|date',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $paymentLog = CommissionPaymentLog::findOrFail($id);

        // Only allow editing if not already completed
        if ($paymentLog->payment_status === 'success') {
            return redirect()->back()->with('error', 'Cannot edit completed payments');
        }

        $paymentLog->update($validated);

        // If status changed to success, update associated commissions
        if ($validated['payment_status'] === 'success' && $paymentLog->getOriginal('payment_status') !== 'success') {
            CommissionHistory::whereIn('id', $paymentLog->commission_ids ?? [])
                ->update([
                    'payment_status' => 'success',
                    'status' => 'complete'
                ]);
        }

        return redirect()
            ->route('landlord.admin.commission.payment-logs')
            ->with('success', 'Payment log updated successfully');
    }

    /**
     * Delete payment log
     */
    public function delete($id)
    {
        $paymentLog = CommissionPaymentLog::findOrFail($id);

        // Only allow deleting if not completed
        if ($paymentLog->payment_status === 'success') {
            return redirect()->back()->with('error', 'Cannot delete completed payments');
        }

        $paymentLog->delete();

        return redirect()
            ->route('admin.commission.payment-logs')
            ->with('success', 'Payment log deleted successfully');
    }

    /**
     * Get status badge HTML
     */
    private function getStatusBadge($status)
    {
        $badges = [
            'complete' => '<span class="badge-success"><i class="mdi mdi-check-circle"></i> Complete</span>',
            'pending' => '<span class="badge-pending"><i class="mdi mdi-clock"></i> Pending</span>',
            'processing' => '<span class="badge-processing"><i class="mdi mdi-sync"></i> Processing</span>',
            'failed' => '<span class="badge-failed"><i class="mdi mdi-close-circle"></i> Failed</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Get commission status badge
     */
    private function getCommissionStatusBadge($status)
    {
        $badges = [
            'success' => '<span class="badge-success"><i class="mdi mdi-check-circle"></i> Success</span>',
            'pending' => '<span class="badge-pending"><i class="mdi mdi-clock"></i> Pending</span>',
            'cancel' => '<span class="badge-failed"><i class="mdi mdi-close-circle"></i> Cancel</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}
