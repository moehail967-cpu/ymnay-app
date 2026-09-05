<?php

namespace Modules\Wallet\Http\Services;

use App\Mail\BasicMail;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletSettings;
use Modules\Wallet\Entities\WalletTenantList;
use Throwable;

class WalletService
{
    function __construct()
    {
        if (empty(get_static_option('user_wallet')))
        {
            return back();
        }
    }
    public static function check_wallet_balance($user_id)
    {
        $user_id = $user_id ?? Auth::guard('web')->user()?->id;
        $user = User::find($user_id);

        if ($user?->wallet?->walletSettings?->wallet_alert)
        {
            $wallet_balance = $user?->wallet?->balance;
            $wallet_minimum_amount = $user?->wallet?->walletSettings?->minimum_amount ?? 0;

            if ($wallet_balance <= $wallet_minimum_amount)
            {
                $email = $user->email;
                $subject = 'Wallet balance alert';
                $message = sprintf('Your wallet balance is low. Your current balance is %g'.site_currency_symbol().'', $wallet_balance);

                Mail::to($email)->send(new BasicMail($message, $subject));
            }
        }
    }

    public static function renew_package_from_wallet()
    {
        $summary = ['failed' => 0, 'completed' => 0];

        $settings = WalletSettings::query()
            ->where('renew_package', true)
            ->get();

        foreach ($settings as $walletSettings) {
            $user = User::find($walletSettings->user_id);

            if (!$user) {
                continue;
            }

            $failedPackages = [];
            $completedPackages = [];

            $walletTenantList = WalletTenantList::query()
                ->where('user_id', $user->id)
                ->with('tenant')
                ->get();

            foreach ($walletTenantList as $tenantEntry) {
                $tenant = $tenantEntry->tenant;

                if (!$tenant?->expire_date || Carbon::parse($tenant->expire_date)->greaterThan(now())) {
                    continue;
                }

                $paymentLog = PaymentLogs::query()
                    ->where('tenant_id', $tenantEntry->tenant_id)
                    ->where('user_id', $user->id)
                    ->where('payment_status', 'complete')
                    ->latest()
                    ->first();

                $plan = $paymentLog
                    ? PricePlan::select('id', 'title', 'type', 'price')->find($paymentLog->package_id)
                    : null;

                if (!$paymentLog || !$plan) {
                    continue;
                }

                try {
                    $renewed = DB::transaction(function () use ($tenantEntry, $paymentLog, $plan, $user) {
                        $lockedTenant = Tenant::query()
                            ->whereKey($tenantEntry->tenant_id)
                            ->lockForUpdate()
                            ->first();

                        if (!$lockedTenant?->expire_date || Carbon::parse($lockedTenant->expire_date)->greaterThan(now())) {
                            return null;
                        }

                        $wallet = Wallet::query()
                            ->where('user_id', $user->id)
                            ->lockForUpdate()
                            ->first();

                        if (!$wallet || (float) $wallet->balance < (float) $plan->price) {
                            return false;
                        }

                        self::renew_package($tenantEntry, $paymentLog, $plan, $user, $wallet);

                        return true;
                    }, 3);
                } catch (Throwable $exception) {
                    throw $exception;
                }

                if ($renewed === null) {
                    continue;
                }

                $package = [
                    'tenant_id' => $tenantEntry->tenant_id,
                    'price_plan_title' => $plan->title,
                    'price_plan_price' => $plan->price,
                ];

                if ($renewed) {
                    $completedPackages[] = $package;
                    $summary['completed']++;
                } else {
                    $failedPackages[] = $package;
                    $summary['failed']++;
                }
            }

            self::failed_renewal(count($failedPackages), $failedPackages, $user->email);
            self::renew_package_email(count($completedPackages), $completedPackages, $user->email);
            self::check_wallet_balance($user->id);
        }

        return $summary;
    }

    private static function failed_renewal($failed_packages_count, $failed_packages, $email)
    {
        if ($failed_packages_count > 0)
        {
            $unit = $failed_packages_count > 1 ? __('Some') : __('One');

            $subject = 'Package renewal failed';
            $message = '<h4>'.$unit.' '.__('of your package is failed to renew due to low balance in wallet').'</h4></br>';

            $i=1;
            foreach ($failed_packages as $key => $package)
            {
                $message .= '<span>'.$i++.'</span>. <span>'.$package['tenant_id'].'</span> - <span>'.$package['price_plan_title'].'</span> - <span>'.$package['price_plan_price'].site_currency_symbol().'</span></br>';
            }
            $message .= '<br><p>'.__('Please deposit balance to continue using the renewal feature').'</p>';

            Mail::to($email)->send(new BasicMail($message, $subject));
        }
    }

    private static function renew_package($tenant, $last_payment_log, $used_price_plan, $user, Wallet $wallet)
    {
        $package_start_date = '';
        $package_expire_date = '';

        if (!empty($used_price_plan)) {
            if ($used_price_plan->type == 0) { //monthly
                $package_start_date = Carbon::now()->format('d-m-Y h:i:s');
                $package_expire_date = Carbon::now()->addMonth(1)->format('d-m-Y h:i:s');

            } elseif ($used_price_plan->type == 1) { //yearly
                $package_start_date = Carbon::now()->format('d-m-Y h:i:s');
                $package_expire_date = Carbon::now()->addYear(1)->format('d-m-Y h:i:s');
            } else { //lifetime
                $package_start_date = Carbon::now()->format('d-m-Y h:i:s');
                $package_expire_date = null;
            }
        }

        if ($package_expire_date != null) {
            $old_days_left = Carbon::now()->diff($last_payment_log->expire_date);
            $left_days = 0;

            if ($old_days_left->invert == 0) {
                $left_days = $old_days_left->days;
            }

            $renew_left_days = 0;
            $renew_left_days = Carbon::parse($package_expire_date)->diffInDays();

            $sum_days = $left_days + $renew_left_days;
            $new_package_expire_date = Carbon::today()->addDays($sum_days)->format("d-m-Y h:i:s");
        } else {
            $new_package_expire_date = null;
        }

        PaymentLogs::findOrFail($last_payment_log->id)->update([
            'email' => $last_payment_log->email,
            'name' => $last_payment_log->name,
            'package_name' => $used_price_plan->title,
            'package_price' => $used_price_plan->price,
            'package_gateway' => 'wallet',
            'package_id' => $used_price_plan->id,
            'user_id' => $tenant->user_id ?? null,
            'tenant_id' => $tenant->tenant_id ?? null,
            'status' => 'complete',
            'payment_status' => 'complete',
            'renew_status' => is_null($last_payment_log->renew_status) ? 1 : $last_payment_log->renew_status + 1,
            'is_renew' => 1,
            'track' => Str::random(10) . Str::random(10),
            'updated_at' => Carbon::now(),
            'start_date' => $package_start_date,
            'expire_date' => $new_package_expire_date
        ]);

        self::update_tenant($last_payment_log->id);

        $wallet->update([
            'balance' => (float) $wallet->balance - (float) $used_price_plan->price
        ]);
    }

    private static function update_tenant($payment_id)
    {
        $payment_log = PaymentLogs::where('id', $payment_id)->first();
        $tenant = Tenant::find($payment_log->tenant_id);

        \DB::table('tenants')->where('id', $tenant->id)->update([
            'renew_status' => $renew_status = is_null($tenant->renew_status) ? 0 : $tenant->renew_status+1,
            'is_renew' => $renew_status == 0 ? 0 : 1,
            'start_date' => $payment_log->start_date,
            'expire_date' => get_plan_left_days($payment_log->package_id, $tenant->expire_date)
        ]);
    }

    private static function renew_package_email($completed_packages_count, $completed_packages, $email)
    {
        if ($completed_packages_count > 0)
        {
            $unit = $completed_packages_count > 1 ? ['are', 's'] : ['is', ''];

            $subject = 'Package auto renewed';
            $message = '<h4>'.__('Your package'.$unit[1].' '.$unit[0].' renewed successfully using wallet balance').'</h4></br>';

            $i=1;
            foreach ($completed_packages as $key => $package)
            {
                $message .= '<span>'.$i++.'</span>. <span>'.$package['tenant_id'].'</span> - <span>'.$package['price_plan_title'].'</span> - <span>'.amount_with_currency_symbol($package['price_plan_price'] ?? 0).'</span></br>';
            }
            $message .= '<br><p>'.__('To check it out please visit the website').'</p>';

            Mail::to($email)->send(new BasicMail($message, $subject));
        }
    }
}
