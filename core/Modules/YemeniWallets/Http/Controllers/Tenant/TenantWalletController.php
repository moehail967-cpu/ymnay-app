<?php

namespace Modules\YemeniWallets\Http\Controllers\Tenant;

use App\Mail\ProductOrderEmail;
use App\Mail\ProductOrderManualEmail;
use App\Models\ProductOrder;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Modules\YemeniWallets\Entities\WalletPaymentProof;
use Modules\YemeniWallets\Src\PluginOptions;

/**
 * Tenant-facing admin. Lets the shop owner:
 *  1. Activate wallets from the landlord catalog and fill in their own account details.
 *  2. Review customer payment proofs and approve or reject them.
 *
 * Uses PluginOptions (which wraps PluginBase::get_option / update_option) so
 * all settings are automatically scoped to the current tenant's database.
 */
class TenantWalletController extends Controller
{
    public function index(): View
    {
        $catalog = collect(PluginOptions::getGlobal('catalog', []))
            ->where('status', true)
            ->sortBy('sort_order')
            ->values();

        // Tenant-scoped: PluginOptions resolves the current tenant automatically.
        $activations = PluginOptions::get('activated_wallets', []);

        return view('yemeni_wallets::tenant.wallets-index', [
            'catalog'     => $catalog,
            'activations' => $activations,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'catalog_wallet_id' => ['required', 'string'],
            'is_active'         => ['boolean'],
            'values'            => ['required', 'array'],
        ]);

        $catalog       = collect(PluginOptions::getGlobal('catalog', []));
        $catalogWallet = $catalog->firstWhere('id', $data['catalog_wallet_id']);
        abort_if(! $catalogWallet, 404);

        // Validate submitted values against the wallet's own dynamic field schema.
        $rules = [];
        foreach ($catalogWallet['fields_schema'] as $field) {
            $rules['values.' . $field['key']] = ($field['required'] ?? false)
                ? 'required|string|max:255'
                : 'nullable|string|max:255';
        }
        $validatedValues = $request->validate($rules)['values'] ?? [];

        $activations = PluginOptions::get('activated_wallets', []);
        $activations[$data['catalog_wallet_id']] = [
            'is_active' => (bool) ($data['is_active'] ?? false),
            'values'    => $validatedValues,
        ];

        PluginOptions::set('activated_wallets', $activations);

        return back()->with('success', __('Wallet settings saved.'));
    }

    public function proofsIndex(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $proofs = WalletPaymentProof::query()
            ->when($status !== 'all', fn ($q) => $q->where('verification_status', $status))
            ->latest()
            ->paginate(20);

        return view('yemeni_wallets::tenant.verify-proofs', compact('proofs', 'status'));
    }

    /**
     * Approve a payment proof:
     *  1. Mark the proof as approved.
     *  2. Set ProductOrder->payment_status = 'success' and status = 'complete'.
     *  3. Send a confirmation email to the customer (same mail used by other gateways).
     */
    public function approveProof(WalletPaymentProof $proof, Request $request): RedirectResponse
    {
        $proof->approve($request->input('admin_note'));

        // Find and update the related order.
        $order = ProductOrder::find($proof->order_id);
        if ($order) {
            $order->payment_status = 'success';
            $order->status         = 'complete';
            $order->save();

            // Notify the customer — same mailable pattern used by PaymentGatewayIpn.
            try {
                $customer_email = $order->email;
                if ($customer_email) {
                    Mail::to($customer_email)->send(new ProductOrderEmail($order));
                }
            } catch (\Exception) {
                // Non-fatal: log but do not crash the admin's approval action.
            }
        }

        return back()->with('success', __('Payment approved. Customer has been notified.'));
    }

    /**
     * Reject a payment proof:
     *  1. Mark the proof as rejected (with optional admin note).
     *  2. Keep order payment_status = 'pending' (customer must re-submit or contact support).
     *  3. Send a rejection email to the customer including the admin note as the reason.
     */
    public function rejectProof(WalletPaymentProof $proof, Request $request): RedirectResponse
    {
        $admin_note = $request->input('admin_note');
        $proof->reject($admin_note);

        // Notify the customer of the rejection.
        $order = ProductOrder::find($proof->order_id);
        if ($order) {
            try {
                $customer_email = $order->email;
                if ($customer_email) {
                    // ProductOrderManualEmail is the correct mailable for manual-payment
                    // notifications — confirmed from PaymentGatewayIpn::send_order_mail().
                    // We pass admin_note via the order's message field temporarily so the
                    // template can render the reason.
                    $original_message = $order->message;
                    if ($admin_note) {
                        $order->message = __('Payment Rejected') . ': ' . $admin_note;
                    }
                    Mail::to($customer_email)->send(new ProductOrderManualEmail($order));
                    $order->message = $original_message; // restore
                }
            } catch (\Exception) {
                // Non-fatal.
            }
        }

        return back()->with('success', __('Payment rejected. Customer has been notified.'));
    }
}
