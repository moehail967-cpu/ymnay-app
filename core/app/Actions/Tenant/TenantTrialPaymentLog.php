<?php

namespace App\Actions\Tenant;

use App\Models\PaymentLogs;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TenantTrialPaymentLog
{
    public static function trial_payment_log($user, $plan,$subdomain = null, $theme = 'hexfashion')
    {
        $trial_start_date = '';
        $trial_expire_date =  '';

        $plan_trial_days = (int) $plan->trial_days;

        if(!empty($plan)){
            if($plan->type == 0){
                $trial_start_date = \Illuminate\Support\Carbon::now()->format('d-m-Y h:i:s');
                $trial_expire_date = Carbon::now()->addDays($plan_trial_days)->format('d-m-Y h:i:s');

            }elseif ($plan->type == 1){
                $trial_start_date = Carbon::now()->format('d-m-Y h:i:s');
                $trial_expire_date = Carbon::now()->addDays($plan_trial_days)->format('d-m-Y h:i:s');
            }else{
                $trial_start_date = Carbon::now()->format('d-m-Y h:i:s');
                $trial_expire_date =  Carbon::now()->addDays($plan_trial_days)->format('d-m-Y h:i:s');
            }
        }

        Log::info('TenantTrialPaymentLog: creating payment log', [
            'subdomain' => $subdomain,
            'plan_id' => $plan->id,
            'trial_days' => $plan_trial_days,
            'expire_date' => $trial_expire_date,
        ]);

        $paymentLog = PaymentLogs::create([
            'email' => $user->email,
            'name' => $user->name,
            'package_name' => $plan->title,
            'package_price' => $plan->price,
            'package_id' => $plan->id,
            'user_id' => $user->id ?? null,
            'tenant_id' => $subdomain ?? null,
            'status' => 'trial',
            'payment_status' => 'pending',
            'is_renew' => 0,
            'track' => Str::random(10),
            'created_at' => \Illuminate\Support\Carbon::now(),
            'updated_at' => Carbon::now(),
            'start_date' => $trial_start_date,
            'expire_date' => $trial_expire_date,
            'theme_slug' => $theme,
        ]);

        DB::table('tenants')->where('id', $subdomain)->update([
            'user_id' => $user->id,
            'start_date' => $trial_start_date,
            'expire_date' => $trial_expire_date,
            'theme_slug' => $theme,
            'renewal_payment_log_id' => $paymentLog->id,
        ]);

        Log::info('TenantTrialPaymentLog: tenant updated', [
            'subdomain' => $subdomain,
            'payment_log_id' => $paymentLog->id,
            'renewal_payment_log_id_set' => $paymentLog->id,
        ]);

        return true;
    }
}
