<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Actions\Tenant\ReGenerateTenant;
use App\Events\TenantRegisterEvent;
use App\Helpers\FlashMsg;
use App\Helpers\Payment\DatabaseUpdateAndMailSend\LandlordPricePlanAndTenantCreate;
use App\Helpers\ResponseMessage;
use App\Helpers\TenantHelper\TenantHelpers;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Mail\PlaceOrder;
use App\Mail\TenantCredentialMail;
use App\Models\CustomDomain;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\Tenant;
use App\Models\TenantException;
use App\Models\User;
use App\Models\ZeroPricePlanHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;
use function Laravel\Prompts\alert;

class TenantManageController extends Controller
{
    const BASE_PATH = 'landlord.admin.tenant.';

    public function __construct()
    {
        $this->middleware('permission:users-list', ['only' => ['all_tenants']]);
        $this->middleware('permission:users-shop', ['only' => ['all_tenants_list']]);
        $this->middleware('permission:users-create', ['only' => ['new_tenant', 'new_tenant_store']]);
        $this->middleware('permission:users-edit', ['only' => ['edit_profile', 'update_edit_profile', 'update_change_password', 'tenant_account_status']]);
        $this->middleware('permission:users-delete', ['only' => ['delete', 'trash', 'trash_restore', 'trash_delete']]);
        $this->middleware('permission:users-shop-delete', ['only' => ['tenant_domain_delete']]);
        $this->middleware('permission:users-assign-subscription', ['only' => ['assign_subscription']]);
        $this->middleware('permission:users-activity', ['only' => ['tenant_activity_log']]);
        $this->middleware('permission:users-settings', ['only' => ['account_settings', 'account_settings_update']]);
        $this->middleware('permission:users-failed-shop', ['only' => ['failed_tenants', 'failed_tenants_edit', 'failed_tenants_delete', 'failed_regenerate_subscription']]);
    }

//    public function all_tenants()
//    {
//        $all_users = User::latest()->get();
//        $deleted_users = User::onlyTrashed()->count();
//
//        return view(self::BASE_PATH . 'index', compact('all_users', 'deleted_users'));
//    }
    public function all_tenants(Request $request)
    {
        $all_users = User::latest()->get();
        $tenant_info = [];

        foreach ($all_users as $user) {
            $tenant_info[$user->id] = \App\Helpers\TenantHelper\TenantHelpers::getTenantInfoForDisplay($user->id);
        }

        $deleted_users = User::onlyTrashed()->count();

        return view(self::BASE_PATH . 'index', compact('all_users', 'tenant_info', 'deleted_users'));
    }

    public function all_tenants_list()
    {
        $all_tenants = \Cache::remember('all_tenants', 60 * 60, function () {
            $every = [];
            Tenant::whereNotNull('user_id')->chunk(100, function ($tenants) use (&$every) {
                foreach ($tenants as $tenant) {
                    $every[] = $tenant;
                }
            });

            return $every;
        });

        return view(self::BASE_PATH . 'shop-list', compact('all_tenants'));
    }

    public function new_tenant()
    {
        return view(self::BASE_PATH . 'new');
    }

    public function new_tenant_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'country' => 'nullable',
            'city' => 'nullable',
            'mobile' => 'nullable',
            'state' => 'nullable',
            'address' => 'nullable',
            'image' => 'nullable',
            'company' => 'nullable',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'username' => Str::slug($request->username),
            'subdomain' => Str::slug($request->subdomain),
            'country' => $request->country,
            'city' => $request->city,
            'mobile' => $request->mobile,
            'state' => $request->state,
            'address' => $request->address,
            'image' => $request->image,
            'company' => $request->company,
        ]);

        return response()->success(ResponseMessage::success(__('Tenant has been created successfully..!')));

    }

    public function edit_profile($id)
    {
        $user = User::find($id);
        return view(self::BASE_PATH . 'edit', compact('user'));
    }

    public function update_edit_profile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($request->id)],
            'country' => 'nullable',
            'city' => 'nullable',
            'mobile' => 'nullable',
            'state' => 'nullable',
            'address' => 'nullable',
            'image' => 'nullable',
            'company' => 'nullable',
        ]);

        User::where('id', $request->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'username' => Str::slug($request->username),
            'country' => $request->country,
            'city' => $request->city,
            'mobile' => $request->mobile,
            'state' => $request->state,
            'address' => $request->address,
            'image' => $request->image,
            'company' => $request->company,
        ]);

        return response()->success(ResponseMessage::success(__('Tenant updated successfully..!')));

    }

//    public function delete($id)
//    {
//        $user = User::findOrFail($id);
//        $tenants = Tenant::where('user_id', $user->id)->get();
//
//        foreach ($tenants ?? [] as $tenant) {
//            $path = 'assets/tenant/uploads/media-uploader/' . $tenant->id;
//
//            if (\File::exists($path) && is_dir($path)) {
//                File::deleteDirectory($path);
//            }
//        }
//
//        PaymentLogs::where('user_id', $user->id)->delete();
//        CustomDomain::where('user_id', $user->id)->delete();
//        ZeroPricePlanHistory::where('user_id', $user->id)->delete();
//
//        if (!empty($tenants)) {
//            foreach ($tenants as $tenant) {
//                // Delete custom domain (if exists) BEFORE deleting the tenant
//                if ($tenant->custom_domain()->exists()) {
//                    $tenant->custom_domain()->delete();
//                }
//
//                // Delete domains
//                $tenant->domains()->delete();
//
//                // Delete the tenant (this triggers the DeleteDatabaseWithFallback job)
//                $tenant->delete();
//            }
//        }
//
//        $user->delete();
//
//        return response()->danger(ResponseMessage::delete(__('Tenant deleted successfully..!')));
//    }
    public function delete($id)
    {
        $user = User::findOrFail($id);
        $tenants = Tenant::where('user_id', $user->id)->get();

        foreach ($tenants ?? [] as $tenant) {
            $path = 'assets/tenant/uploads/media-uploader/' . $tenant->id;

            if (\File::exists($path) && is_dir($path)) {
                File::deleteDirectory($path);
            }
        }

        PaymentLogs::where('user_id', $user->id)->delete();
        CustomDomain::where('user_id', $user->id)->delete();
        ZeroPricePlanHistory::where('user_id', $user->id)->delete();

        if (!empty($tenants)) {
            foreach ($tenants as $tenant) {
                // Delete custom domain (if exists) BEFORE deleting the tenant
                if ($tenant->custom_domain()->exists()) {
                    $tenant->custom_domain()->delete();
                }

                // Delete domains
                $tenant->domains()->delete();

                // Delete the tenant (this triggers the DeleteDatabaseWithFallback job)
                $tenant->delete();
            }
        }

        $user->delete();

        return response()->danger(ResponseMessage::delete(__('Tenant deleted successfully..!')));
    }
    public function trash()
    {
        $trashed_users = User::onlyTrashed()->get();
        return view(self::BASE_PATH . 'trash', compact('trashed_users'));
    }

    public function trash_restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        try {
            $user->restore();
        } catch (\Exception $exception) {
        }

        return back()->with(FlashMsg::explain('success', __('The user is restored')));
    }

    public function trash_delete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        try {
            $user->forceDelete();
        } catch (\Exception $exception) {
        }

        return back()->with(FlashMsg::explain('danger', __('The user is deleted permanently')));
    }

    public function update_change_password(Request $request)
    {
        $this->validate(
            $request, [
            'password' => 'required|string|min:8|confirmed'
        ],
            [
                'password.required' => __('password is required'),
                'password.confirmed' => __('password does not matched'),
                'password.min' => __('password minimum length is 8'),
            ]
        );
        $user = User::findOrFail($request->ch_user_id);
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->success(ResponseMessage::success(__('Password updated successfully..!')));
    }

    public function send_mail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        $sub = $request->subject;
        $msg = $request->message;

        try {
            Mail::to($request->email)->send(new BasicMail($msg, $sub));
        } catch (\Exception $ex) {
            return response()->danger(ResponseMessage::delete($ex->getMessage()));
        }

        return response()->success(ResponseMessage::success(__('Mail Send Successfully..!')));
    }

    public function resend_verify_mail(Request $request)
    {
        $subscriber_details = User::findOrFail($request->id);
        $token = $subscriber_details->email_verify_token ? $subscriber_details->email_verify_token : Str::random(8);

        if (empty($subscriber_details->email_verify_token)) {
            $subscriber_details->email_verify_token = $token;
            $subscriber_details->save();
        }
        $message = __('Verification Code: ') . '<strong>' . $token . '</strong>' . '<br>' . __('Verify your email to get all news from ') . get_static_option('site_' . get_default_language() . '_title') . '<div class="btn-wrap"> <a class="anchor-btn" href="' . route('landlord.user.login') . '">' . __('Login') . '</a></div>';

        $msg = $message;
        $subject = __('verify your email');


        try {
            Mail::to($subscriber_details->email)->send(new BasicMail($msg, $subject));
        } catch (\Exception $ex) {
            return response()->danger(ResponseMessage::delete($ex->getMessage()));
        }

        return response()->success(ResponseMessage::success(__('Email Verify Mail Send Successfully..!')));
    }

    public function tenant_activity_log()
    {
        $activities = Activity::with(['subject', 'causer'])->latest()->paginate(8);
        return view(self::BASE_PATH . 'activity-log', compact('activities'));
    }

//    public function tenant_details($id)
//    {
//        $tenant_helper = TenantHelpers::init();
//        // Fetch all tenants for the given user_id
//        $tenants = Tenant::where('user_id', $id)->get(); // Use get() to retrieve all tenants
//
//        // Initialize an array to store tenant info with tenant IDs
//        $tenant_info = [];
//        $tenant_ids = []; // Array to store tenant IDs
//
//        foreach ($tenants as $tenant) {
//            $tenant_helper->setTenant($tenant); // Set the tenant in the helper
//            $tenant_info[$tenant->id] = $tenant_helper->getTenantInfo(); // Store tenant info with tenant_id as key
//            $tenant_ids[] = $tenant->id; // Collect tenant_id
//        }
//        $user = User::with('tenant_details', 'tenant_details.payment_log')->findOrFail($id);
//
//        return view(self::BASE_PATH . 'details', compact('tenants', 'tenant_info', 'user', 'tenant_ids'));
//    }
    public function tenant_details($id)
    {
        // Fetch all tenants for the given user_id
        $tenants = Tenant::where('user_id', $id)->get();

        // Initialize an array to store tenant info with tenant IDs
        $tenant_info = [];
        $tenant_ids = [];

        foreach ($tenants as $tenant) {
            // Use the static helper function instead of TenantHelpers
            $tenant_info[$tenant->id] = TenantHelpers::getTenantInfoForDisplay($id);
            $tenant_ids[] = $tenant->id;
        }

        $user = User::with('tenant_details', 'tenant_details.payment_log')->findOrFail($id);

        return view(self::BASE_PATH . 'details', compact('tenants', 'tenant_info', 'user', 'tenant_ids'));
    }


// Helper method to get tenant IDs (if needed elsewhere)
    private function tenant()
    {
        // Assuming this method is meant to return tenant IDs for the current user
        $user = auth()->user(); // Get the authenticated user
        if (!$user) {
            return []; // Return empty array if no user is authenticated
        }

        // Fetch all tenant IDs for the user
        $tenant_ids = Tenant::where('user_id', $user->id)->pluck('id')->toArray();

        return $tenant_ids;
    }


    public function tenant_domain_delete($tenant_id)
    {
        // old domain = same = tenant id //

        $tenant = Tenant::findOrFail($tenant_id);
        $user_id = $tenant->user_id;

        $path = 'assets/tenant/uploads/media-uploader/' . $tenant->id;
        CustomDomain::where([['old_domain', $tenant->id], ['custom_domain_status', '!=', 'connected']])
            ->orWhere([['custom_domain', $tenant->id], ['custom_domain_status', '==', 'connected']])->delete();
        PaymentLogs::where('tenant_id', $tenant->id)->delete();
        TenantException::where('tenant_id', $tenant->id)->delete();

        // delete tenant from zero price plan history
        $zero_plans = ZeroPricePlanHistory::where('user_id', $user_id)->take(1)->get();
        foreach ($zero_plans ?? [] as $zero)
        {
            $zero->delete();
        }

        if (!empty($tenant)) {
            $tenant->domains()->delete();
            try {
                $tenant->delete();
            } catch (\Exception $exception) {
            }
        }
        if (File::exists($path) && is_dir($path)) {
            File::deleteDirectory($path);
        }

        $check_tenant = Tenant::where('user_id', $user_id)->count();
        if ($check_tenant > !0) {
            User::findOrFail($user_id)->update(['has_subdomain' => false]);
        }

        return response()->danger(ResponseMessage::delete(__('Tenant deleted successfully..!')));
    }

    public function tenant_account_status(Request $request)
    {
        $request->validate([
            'payment_log_id' => 'required',
            'account_status' => 'required',
            'payment_status' => 'required',
        ]);

        $payment_log = PaymentLogs::findOrFail($request->payment_log_id)->update([
            'status' => $request->account_status,
            'payment_status' => $request->payment_status
        ]);

        return back()->with(ResponseMessage::success(__('Tenant account status is updated..')));
    }

//    public function assign_subscription(Request $request)
//    {
//        ini_set('max_execution_time', 300);
//        ini_set('memory_limit', '512M');
//
//        $request->validate([
//            'package' => 'required',
//            'payment_status' => 'required',
//            'account_status' => 'required',
//            'custom_theme' => 'required'
//        ]);
//
//        if ($request->custom_subdomain == null) {
//            $request->validate([
//                'subdomain' => 'required'
//            ]);
//        }
//
//        $subdomain = $request->custom_subdomain != null ? $request->custom_subdomain : $request->subdomain;
//
//        $user = User::findOrFail($request->subs_user_id);
//        $package = PricePlan::findOrFail($request->subs_pack_id);
//        $old_tenant_log = PaymentLogs::where(['user_id' => $user->id, 'tenant_id' => $subdomain])->latest()->first();
//
//        $tenantHelper = TenantHelpers::init()->setTenantId($subdomain)
//            ->setPackage($package)
//            ->setPaymentLog($old_tenant_log)
//            ->setTheme($request->custom_theme);
//
////        if ($tenantHelper->getPackageType() === 'custom') {
////            $request->validate([
////                'custom_expire_date' => 'required',
////            ], ['custom_expire_date.required' => __('Please select custom package expire date')]);
////        }
//
////        $custom_expire_date = $request->custom_expire_date;
////        $request_theme_slug_or_default = $request->custom_theme;
////        try {
////            $request_theme_slug_or_default = $tenantHelper->isThemeAvailableForThisPlanFeature();
////        } catch (\Exception $e) {
////            return redirect()->back()->with(FlashMsg::item_delete($e->getMessage()));
////        }
//
//        $theme_code = '';
//        $package_start_date = $tenantHelper->getStartDate();
//        $package_expire_date = $tenantHelper->getExpiredDate();
//
//        $tenant = Tenant::find($subdomain);
////        dd($tenant);
//        if (!empty($tenant)) {
//            // existing tenant
//            $old_tenant_log = PaymentLogs::where(['user_id' => $user->id, 'tenant_id' => $tenant->id])->latest()->first();
////            dd($old_tenant_log);
//
//            if ($package_expire_date != null) {
//                $old_days_left = Carbon::now()->diff($old_tenant_log->expire_date);
//                $left_days = 0;
//
//                if (!$old_days_left->invert) {
//                    $left_days = $old_days_left->days;
//                }
//
//                $renew_left_days = Carbon::parse($package_expire_date)->diffInDays();
//                $sum_days = $left_days + $renew_left_days;
//                $new_package_expire_date = Carbon::today()->addDays($sum_days)->format("d-m-Y h:i:s");
////                dd($new_package_expire_date);
//            }
//            else
//            {
//                $new_package_expire_date = null;
//            }
//
//            PaymentLogs::findOrFail($old_tenant_log->id)->update([
//                'custom_fields' => [],
//                'attachments' => [],
//                'email' => $old_tenant_log->email,
//                'name' => $old_tenant_log->name,
//                'package_name' => $package->title,
//                'package_price' => $package->price,
//                'package_gateway' => null,
//                'package_id' => $package->id,
//                'theme_slug' => $tenantHelper->getTheme(),
//                'user_id' => $old_tenant_log->user_id,
//                'tenant_id' => $tenantHelper->getTenantId(),
//                'status' => $request->account_status,
//                'payment_status' => $request->payment_status,
//                'renew_status' => is_null($old_tenant_log->renew_status) ? 1 : $old_tenant_log->renew_status + 1,
//                'is_renew' => 1,
//                'track' => Str::random(10) . Str::random(10),
//                'updated_at' => Carbon::now(),
//                'start_date' => $package_start_date,
//                'expire_date' => $new_package_expire_date
//            ]);
//
//            DB::table('tenants')->where('id', $tenantHelper->getTenantId())->update([
//                'renew_status' => $renew_status = is_null($tenant->renew_status) ? 0 : $tenant->renew_status + 1,
//                'is_renew' => $renew_status == 0 ? 0 : 1,
//                'theme_slug' => $tenantHelper->getTheme(),
//                'start_date' => $package_start_date,
//                'expire_date' => $new_package_expire_date
//            ]);
//        }
//        else
//        {
//            // new tenant
//            $request->validate([
//                'custom_theme' => 'required',
//                'custom_subdomain' => 'required'
//            ]);
//
//            PaymentLogs::create([
//                'custom_fields' => '',
//                'attachments' => '',
//                'email' => $user->email,
//                'name' => $user->name,
//                'package_name' => $package->title,
//                'package_price' => $package->price,
//                'package_gateway' => null,
//                'package_id' => $package->id,
//                'theme_slug' => $tenantHelper->getTheme(),
//                'user_id' => $user->id,
//                'tenant_id' => $tenantHelper->getTenantId(),
//                'payment_status' => $request->payment_status,
//                'status' => $request->account_status,
//                'renew_status' => 0,
//                'is_renew' => 0,
//                'track' => Str::random(10) . Str::random(10),
//                'start_date' => $package_start_date,
//                'expire_date' => $package_expire_date
//            ]);
//            try {
//                Tenant::create(['id' => $subdomain]);
//            }catch (\Exception $e){
//                return response()->success(ResponseMessage::delete(__($e->getMessage())));
//            }
//        }
//
//        return response()->success(ResponseMessage::success(__('Subscription assigned for this user, you will get notified when website is ready')));
//    }
    public function assign_subscription(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        // Validation
        $request->validate([
            'subs_user_id' => 'required|exists:users,id',
            'subs_pack_id' => 'required|exists:price_plans,id',
            'payment_status' => 'required|in:complete,pending,cancel',
            'account_status' => 'required',
            'custom_theme' => 'required|string',
            'custom_subdomain' => 'nullable|string',
            'subdomain' => 'required_without:custom_subdomain|string'
        ]);

        // Determine subdomain
        $subdomain = $request->custom_subdomain ?? $request->subdomain;

        // Get user and package details
        $user = User::findOrFail($request->subs_user_id);
        $package = PricePlan::findOrFail($request->subs_pack_id);

        // Check if tenant exists
        $is_tenant = Tenant::find($subdomain);

        // Get old tenant log (if exists)
        $old_tenant_log = PaymentLogs::where(['user_id' => $user->id, 'tenant_id' => $subdomain])
            ->latest()
            ->first();

        // Initialize tenant helper
        $tenantHelper = TenantHelpers::init()
            ->setTenantId($subdomain)
            ->setPackage($package)
            ->setPaymentLog($old_tenant_log)
            ->setTheme($request->custom_theme);

        // Calculate start and expire dates
        $package_start_date = $tenantHelper->getStartDate();
        $package_expire_date = $tenantHelper->getExpiredDate();

        // Find the user's latest COMPLETED payment log for this tenant
        $old_completed_log = PaymentLogs::where('user_id', $user->id)
            ->where('tenant_id', $subdomain)
            ->where('payment_status', 'complete')
            ->orderBy('id', 'desc')
            ->first();

        // Determine if this is a renewal based on previous completed payments
        $is_renewal = !is_null($old_completed_log) || !is_null($is_tenant);
        $renew_status_value = $is_renewal && $old_completed_log ? (($old_completed_log->renew_status ?? 0) + 1) : null;

        DB::beginTransaction();

        try {
            // ALWAYS CREATE A NEW PAYMENT LOG
            $payment_log = PaymentLogs::create([
                'email' => $user->email,
                'name' => $user->name,
                'package_name' => $package->title,
                'package_price' => $package->price,
                'package_gateway' => 'manual_payment_admin',
                'package_id' => $package->id,
                'user_id' => $user->id,
                'tenant_id' => $subdomain,
                'theme_slug' => $old_completed_log->theme_slug ?? $request->custom_theme,
                'status' => $request->account_status,
                'payment_status' => $request->payment_status,
                'is_renew' => $is_renewal ? 1 : 0,
                'renew_status' => $renew_status_value,
                'track' => Str::random(10),
                'transaction_id' => 'ADMIN_ASSIGN_' . time(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'start_date' => $package_start_date,
                'expire_date' => $package_expire_date,
            ]);

            // Determine renewal timing
            $renewal_at = null;
            $renewal_after_expire = 0;

            if ($is_renewal && $old_completed_log) {
                $old_expire_date = Carbon::parse($old_completed_log->expire_date);
                $renewal_at = Carbon::now();

                // Check if renewal happened after expiry
                if ($renewal_at->greaterThan($old_expire_date)) {
                    $renewal_after_expire = 1;
                }
            }

            // Calculate new renew status
            $newRenewStatus = $is_renewal ? (($is_tenant->renew_status ?? 0) + 1) : 0;

            // Create tenant if it doesn't exist
            if (is_null($is_tenant)) {
                try {
                    Tenant::create([
                        'id' => $subdomain,
                        'user_id' => $user->id,
                        'theme_slug' => $request->custom_theme,
                        'start_date' => $package_start_date,
                        'expire_date' => $package_expire_date,
                        'renew_status' => 0,
                        'is_renew' => 0,
                        'price_plan_id' => $package->id,
                        'renewal_payment_log_id' => $payment_log->id,
                        'renewal_at' => $renewal_at,
                        'renewal_after_expire' => $renewal_after_expire,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with(FlashMsg::explain('danger', __('Failed to create tenant: ' . $e->getMessage())));

                }
            } else {
                // Update existing tenant using DB::table for consistency
                DB::table('tenants')->where('id', $subdomain)->update([
                    'renew_status' => $newRenewStatus,
                    'is_renew' => $payment_log->is_renew ?? 0,
                    'start_date' => $payment_log->start_date,
                    'expire_date' => $package_expire_date,
                    'updated_at' => Carbon::now(),
                    'renewal_at' => $renewal_at,
                    'renewal_after_expire' => $renewal_after_expire,
                    'price_plan_id' => $payment_log->package_id,
                    'renewal_payment_log_id' => $payment_log->id,
                    'theme_slug' => $request->custom_theme,
                ]);
            }

            DB::commit();

            // Send notification email (if method exists)
            if (method_exists($this, 'send_subscription_notification')) {
                try {
                    $this->send_subscription_notification($payment_log->id);
                } catch (\Exception $e) {
                }
            }

            $message = $is_renewal
                ? __('Subscription renewed successfully for this user')
                : __('Subscription assigned successfully for this user');

            return back()->with(FlashMsg::explain('success',$message));
        } catch (\Exception $exception) {
            DB::rollBack();

            // Create exception record if tenant exists
            if (!is_null($is_tenant)) {
                try {
                    TenantException::create([
                        'tenant_id' => $is_tenant->id,
                        'issue_type' => __('Admin subscription assignment failed'),
                        'description' => __('Error: ') . $exception->getMessage(),
                        'domain_create_status' => 1,
                        'seen_status' => 0
                    ]);
                } catch (\Exception $e) {}
            }
            return back()->with(FlashMsg::explain('danger', __('Something went wrong: ') . $exception->getMessage()));
        }
    }

    public function account_settings()
    {
        return view(self::BASE_PATH . 'settings');
    }

    public function account_settings_update(Request $request)
    {
        $request->validate([
            'tenant_account_auto_remove' => 'nullable',
            'tenant_account_delete_notify_mail_days' => 'required',
            'account_remove_day_within_expiration' => 'required|alpha_num|min:1',
        ]);

        $limit_days = 15;

        if ($request->account_remove_day_within_expiration >= $limit_days) {

            return redirect()->back()->with(['type' => 'danger', 'msg' => sprintf('You can not set remove account day above %d', $limit_days)]);
        }

        update_static_option('tenant_account_auto_remove', $request->tenant_account_auto_remove);
        if ($request->tenant_account_auto_remove) {
            update_static_option('tenant_account_delete_notify_mail_days', json_encode($request->tenant_account_delete_notify_mail_days));
            update_static_option('account_remove_day_within_expiration', $request->account_remove_day_within_expiration);
        } else {
            delete_static_option('tenant_account_delete_notify_mail_days');
            delete_static_option('account_remove_day_within_expiration');
        }

        return response()->success(ResponseMessage::success());
    }

    public function verify_account(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer'
        ]);

        $user = User::find($data['id']);
        $user->update([
            'email_verified' => 1,
            'email_verified_at' => now()
        ]);

        $message = wrap_by_paragraph(__('Hello') . ' ' . $user->name, true);
        $message .= wrap_by_paragraph(__('Your user account is verified by admin'));
        $subject = __('Account verification');

        try {
            Mail::to($user->email)->send(new BasicMail($message, $subject));
        } catch (\Exception $ex) {
            return response()->danger(ResponseMessage::delete($ex->getMessage()));
        }

        return back()->with(FlashMsg::explain('success', $user->name . ' ' . __('user account is verified')));
    }

    public function check_subdomain_theme(Request $request)
    {
        $request->validate([
            'subdomain' => 'required'
        ]);

        $new_tenant = true;
        $theme = '';
        $tenant = Tenant::find($request->subdomain);
        if (!empty($tenant)) {
            $theme = $tenant->theme_slug ?? '';
            $new_tenant = false;
        }

        return response()->json([
            'theme_slug' => $theme,
            'new_tenant' => $new_tenant
        ]);
    }

    public function failed_tenants()
    {
        $tenants = Tenant::whereNull('user_id')
            ->orWhereNull('data')
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::orderBy('created_at', 'desc')->get();

        return view(self::BASE_PATH . 'failed', compact('tenants', 'users'));
    }

    public function failed_tenants_edit(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required',
            'tenant_name' => 'required'
        ]);

        $tenant = Tenant::where('id', $request->tenant_id)->first();

        if (!$tenant) {
            return back()->with(FlashMsg::explain('danger', 'Tenant not found'));
        }

        $tenant->id = $request->tenant_name;
        $tenant->save();

        return back()->with(FlashMsg::explain('success', 'Tenant subdomain name updated'));
    }

    public function failed_tenants_delete(Request $request, $id)
    {
        abort_if(empty($id), 403);

        $tenant = DB::table('tenants')->delete($id);
        PaymentLogs::where('tenant_id', $id)->delete();

        return back()->with(FlashMsg::explain($tenant ? 'success' : 'danger', $tenant ? __('Tenant deleted successfully') : __('Something went wrong')));
    }

    public function failed_regenerate_subscription(Request $request)
    {
        $user_validation_rule = isset($request->user) ? 'required' : 'nullable';
        $validated = $request->validate([
            'account_status' => 'required',
            'subs_tenant_id' => 'required',
            'user' => $user_validation_rule
        ]);

        $reassign_object = new ReGenerateTenant($validated);
        $response = $reassign_object->regenerateTenant();

        if (!empty($response)) {
            $response[] = __('Kindly check user website issues to fix it');
            return back()->withErrors($response);
        }

        return back()->with(FlashMsg::explain('success', 'Tenant Regenerated successfully'));
    }

    public function create_payment_log(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'user' => 'required|exists:users,id',
            'custom_theme' => 'required',
            'package' => 'required|exists:price_plans,id',
            'payment_status' => 'required',
            'status' => 'required',
        ]);

        $user = User::find($data['user']);
        $package = PricePlan::find($data['package']);

        $payment_log = new PaymentLogs();
        $payment_log->user_id = $user->id;
        $payment_log->email = $user->email;
        $payment_log->name = $user->name;
        $payment_log->package_name = $package->title;
        $payment_log->package_price = $package->price;
        $payment_log->package_id = $package->id;
        $payment_log->tenant_id = $data['tenant_id'];
        $payment_log->theme_slug = $data['custom_theme'];
        $payment_log->payment_status = $data['payment_status'];
        $payment_log->status = $data['status'];
        $payment_log->start_date = now();
        $payment_log->save();

        Tenant::where('id', $data['tenant_id'])->update(['user_id' => $data['user']]);

        return back()->with(FlashMsg::explain('success', __('Payment Log created successfully')));
    }

    public function migrateTenant(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
        ]);
        $tenantId = $request->input('tenant_id');
        try {
            // Run tenant migration
            Artisan::call('tenants:migrate', [
                '--tenants' => $tenantId,
            ]);
            // Optional: capture migration output if needed
            // $migrationOutput = Artisan::output();

            // Run tenant seeder
//            Artisan::call('tenants:seed', [
//                '--tenants' => $tenantId,
//            ]);
            // Optional: capture seeding output
            // $seedingOutput = Artisan::output();

            return back()->with('success', "Migration completed for tenant: $tenantId");
        } catch (\Exception $e) {
            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }
    public function seed(Request $request)
    {
        $tenantId = $request->input('tenant_id');

        $tenant = Tenant::findOrFail($tenantId);

        // Run in tenant's database context
        tenancy()->initialize($tenant);

        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\TenantSpecificDatabaseSeeder',
            '--force' => true,
        ]);

        tenancy()->end();

        return back()->with('success', "Tenant {$tenant->id} seeded successfully.");
    }
    /**
     * Show recurring settings page for a specific tenant
     */
    public function recurringSettings($id)
    {
        $tenant = Tenant::findOrFail($id);
//        dd($tenant);
        return view(self::BASE_PATH . 'recurring-settings', compact('tenant'));
    }

    /**
     * Update recurring settings for a specific tenant
     */
    public function updateRecurringSettings(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $tenant->update([
            'recurring_enabled' => $request->has('recurring_enabled') ? 1 : 0,
            'payment_grace_days' => $request->payment_grace_days ?? 3
        ]);

        return redirect()->back()->with([
            'msg' => __('Recurring settings updated successfully'),
            'type' => 'success'
        ]);
    }

    /**
     * Cancel Razorpay subscription for a tenant
     */
    public function cancelRecurringSubscription(Request $request, $id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            if (!$tenant->razorpay_subscription_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('No active subscription found')
                ], 404);
            }

            // Set to false to cancel immediately and update Razorpay dashboard status
            // Set to true to cancel at the end of current billing cycle
            $cancelAtCycleEnd = $request->input('cancel_at_cycle_end', false);

            $result = \App\Services\Payment\RazorpaySubscriptionService::cancelSubscription(
                $tenant->razorpay_subscription_id,
                $cancelAtCycleEnd
            );

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Subscription cancelled successfully')
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? __('Failed to cancel subscription')
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription from admin', [
                'tenant_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('An error occurred while cancelling subscription')
            ], 500);
        }
    }

    /**
     * Pause Razorpay subscription for a tenant
     */
    public function pauseRecurringSubscription(Request $request, $id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            if (!$tenant->razorpay_subscription_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('No active subscription found')
                ], 404);
            }

            $result = \App\Services\Payment\RazorpaySubscriptionService::pauseSubscription(
                $tenant->razorpay_subscription_id
            );

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Subscription paused successfully')
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? __('Failed to pause subscription')
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to pause subscription from admin', [
                'tenant_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('An error occurred while pausing subscription')
            ], 500);
        }
    }

    /**
     * Resume Razorpay subscription for a tenant
     */
    public function resumeRecurringSubscription(Request $request, $id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            if (!$tenant->razorpay_subscription_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('No active subscription found')
                ], 404);
            }

            $result = \App\Services\Payment\RazorpaySubscriptionService::resumeSubscription(
                $tenant->razorpay_subscription_id
            );

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Subscription resumed successfully')
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? __('Failed to resume subscription')
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to resume subscription from admin', [
                'tenant_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('An error occurred while resuming subscription')
            ], 500);
        }
    }

}
