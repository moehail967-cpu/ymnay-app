<?php

namespace App\Http\Controllers\Landlord\Frontend;

use App\Actions\Sms\SmsSendAction;
use App\Actions\Tenant\ReGenerateTenant;
use App\Actions\Tenant\TenantCreateEventWithMail;
use App\Actions\Tenant\TenantRegisterSeeding;
use App\Actions\Tenant\TenantTrialPaymentLog;
use App\Enums\LandlordCouponType;
use App\Events\TenantRegisterEvent;
use App\Facades\GlobalLanguage;
use App\Helpers\FlashMsg;
use App\Helpers\GenerateTenantToken;
use App\Helpers\ImageDataSeedingHelper;
use App\Helpers\EmailHelpers\MarkupGenerator;
use App\Helpers\EmailHelpers\VerifyUserMailSend;
use Illuminate\Support\Facades\Log;
use App\Helpers\LanguageHelper;
use App\Helpers\Payment\DatabaseUpdateAndMailSend\LandlordPricePlanAndTenantCreate;
use App\Http\Controllers\Controller;
use App\Http\Services\DynamicRouteManager;
use App\Mail\AdminResetEmail;
use App\Mail\BasicMail;
use App\Mail\TenantCredentialMail;
use App\Models\Admin;
use App\Models\Coupon;
use App\Models\Newsletter;
use App\Models\Page;
use App\Models\PaymentGateway;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\Tenant;
use App\Models\TenantUniqueKey;
use App\Models\Themes;
use App\Models\User;
use App\Traits\SeoDataConfig;
use Artesaos\SEOTools\SEOMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Xgenious\PageBuilder\Services\PageBuilderRenderService;
use function Laravel\Prompts\alert;
use function view;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class LandlordFrontendController extends Controller
{
    use SEOToolsTrait, SeoDataConfig;

    private const BASE_VIEW_PATH = 'landlord.frontend.';

    public function homepage()
    {
        $id = get_static_option('home_page');
        $page_post = Page::where('id', $id)->first();
        if ($page_post->use_page_builder) {
            $pageBuilderService = app(PageBuilderRenderService::class);
            $renderable_object = $pageBuilderService->renderPage($page_post,true);
            $page_post->rendered_content  = $renderable_object['html'];
            $page_post->pagebuilder_generated_styles = $renderable_object['css'] ?? '';
        }


        $this->setMetaDataInfo($page_post);
        return view(self::BASE_VIEW_PATH . 'frontend-home', compact('page_post'));
    }

    /* -------------------------
        SUBDOMAIN AVAILABILITY
    -------------------------- */
    public function subdomain_check(Request $request)
    {
        $this->validate($request, [
            'subdomain' => 'required|unique:tenants,id'
        ]);
        // get admin defined forbidden subdomains
        $restricted = explode(',', get_static_option('forbidden_subdomains') ?? '');

        // sanitize
        $restricted = array_map('strtolower', array_map('trim', $restricted));

        if (in_array(strtolower($request->subdomain), $restricted)) {
            return response()->json([
                'errors' => ['subdomain' => __('This subdomain is not available')]
            ], 422);
        }
        return response()->json('ok');
    }

    public function subdomain_custom_domain_check(Request $request)
    {
        $this->validate($request, [
            'subdomain' => 'required|unique:tenants,id|unique:domains,domain',
        ]);

        return response()->json('ok');
    }

    /* -------------------------
        TENANT EMAIL VERIFY
    -------------------------- */
    public function verify_user_email()
    {
        if (empty(get_static_option('user_email_verify_status')) || Auth::guard('web')->user()) {
            if (Auth::guard('web')->user()->email_verified == 1) {
                return redirect()->route('landlord.user.home');
            }
        }

        //return view('landlord.frontend.auth.email-verify');
        return view('landlord.frontend.dashboard.email-verify');
    }

    public function check_verify_user_email(Request $request)
    {
        $this->validate($request, [
            'verify_code' => 'required|string'
        ]);
        $user_info = User::where(['id' => Auth::guard('web')->id(), 'email_verify_token' => $request->verify_code])->first();
        if (is_null($user_info)) {
            return back()->with(['msg' => __('enter a valid verify code'), 'type' => 'danger']);
        }

        $user_info->email_verified = 1;
        $user_info->save();

        return redirect()->route('landlord.user.home');
    }

    public function resend_verify_user_email(Request $request)
    {

        VerifyUserMailSend::sendMail(Auth::guard('web')->user());
        return redirect()->route('landlord.user.email.verify')->with(['msg' => __('Verify mail send'), 'type' => 'success']);
    }

    public function dynamic_single_page($slug)
    {
        return DynamicRouteManager::handle($slug);
//        $page_post = Page::where('slug', $slug)->firstOrFail();
//
//        abort_if(empty($page_post), 404);
//
//        $this->setMetaDataInfo($page_post);
//
//        $price_page_slug = get_page_slug(get_static_option('pricing-plan'), 'price-plan');
//        if ($slug === $price_page_slug) {
//            $all_blogs = PricePlan::where(['status' => 'publish'])->paginate(10);
//            return view(self::BASE_VIEW_PATH . 'pages.dynamic-single')->with([
//                'all_blogs' => $all_blogs,
//                'page_post' => $page_post
//            ]);
//        }
//
//        return view(self::BASE_VIEW_PATH . 'pages.dynamic-single')->with([
//            'page_post' => $page_post
//        ]);
    }

    public function ajax_login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|min:6'
        ], [
            'username.required' => __('Username required'),
            'password.required' => __('Password required'),
            'password.min' => __('Password length must be 6 characters')
        ]);

        $type = 'username';
        //check is email or username
        if (filter_var($request->username,FILTER_VALIDATE_EMAIL)){
            $type = 'email';
        }
        if (Auth::guard('web')->attempt([ $type => $request->username, 'password' => $request->password], $request->get('remember'))) {
            $loggedInUser = Auth::guard('web')->user();
            return response()->json([
                'msg'        => __('Login Success Redirecting'),
                'type'       => 'success',
                'status'     => 'valid',
                'user_id'    => $loggedInUser->id,
                'name'       => $loggedInUser->name,
                'email'      => $loggedInUser->email,
                'csrf_token' => csrf_token(), // Session regenerates on login — return fresh token
            ]);
        }
        return response()->json([
            'msg' => __('User name and password do not match'),
            'type' => 'danger',
            'status' => 'invalid'
        ]);
    }


    public function lang_change(Request $request)
    {
        session()->put('lang', $request->lang);
        return redirect()->route('landlord.homepage');
    }


    public function order_payment_cancel($id)
    {
        $order_details = PaymentLogs::find($id);
        return view('landlord.frontend.payment.payment-cancel')->with(['order_details' => $order_details]);
    }

    public function order_payment_cancel_static()
    {
        return view('landlord.frontend.payment.payment-cancel-static');
    }
    public function view_plan($id, $trial = null)
    {
        $order_details = PricePlan::findOrFail($id);
        if ($order_details->has_trial != 1) {
            return redirect()->route('landlord.frontend.plan.order', $id);
        }

        return view('landlord.frontend.pages.package.view-plan-new')->with([
            'order_details' => $order_details,
            'trial' => $trial != null ? true : false,
        ]);

    }

    public function plan_order($id)
    {
        abort_if(empty($id), 404);

        $order_details    = PricePlan::findOrFail($id);
        $payment_gateways = PaymentGateway::where('name', 'manual_payment')->first();

        $user             = Auth::guard('web')->user();
        $payment_old_data = [];
        $user_has_trial   = false;

        if ($user) {
            $payment_old_data = PaymentLogs::where(['user_id' => $user->id, 'payment_status' => 'complete'])->get()->toArray();
            $user_has_trial   = PaymentLogs::where('user_id', $user->id)->where('status', 'trial')->exists();
        }

        // Show trial toggle only if plan supports trial AND user hasn't used one yet
        $show_trial_option = $order_details->has_trial == 1 && !$user_has_trial;

        return view('landlord.frontend.pages.package.order-page-new')->with([
            'order_details'     => $order_details,
            'payment_old_data'  => $payment_old_data,
            'payment_gateways'  => $payment_gateways ?? [],
            'user_has_trial'    => $user_has_trial,
            'show_trial_option' => $show_trial_option,
        ]);
    }

    public function order_confirm($id)
    {
        $order_details = PricePlan::where('id', $id)->first();

        $user = Auth::guard('web')->user();
        $payment_old_data = PaymentLogs::where(['user_id' => $user->id, 'payment_status' => 'complete'])->get()->toArray();
        $user_has_trial   = PaymentLogs::where('user_id', $user->id)->where('status', 'trial')->exists();
        $show_trial_option = $order_details->has_trial == 1 && !$user_has_trial;

        return view('landlord.frontend.pages.package.order-page-new')->with([
            'order_details'     => $order_details,
            'payment_old_data'  => $payment_old_data ?? [],
            'user_has_trial'    => $user_has_trial,
            'show_trial_option' => $show_trial_option,
        ]);
    }

    public function order_payment_success($id)
    {
        $extract_id = substr($id, 6);
        $extract_id = substr($extract_id, 0, -6);

        $payment_details = PaymentLogs::findOrFail($extract_id);

        $domain = DB::table('domains')->where('tenant_id', $payment_details->tenant_id)->first();

        if (empty($extract_id)) {
            abort(404);
        }

        return view('landlord.frontend.payment.payment-success', compact('payment_details', 'domain'));
    }

    public function logout_tenant_from_landlord()
    {
        Auth::guard('web')->logout();
        return redirect()->back();
    }


// ========================================== LANDLORD HOME PAGE TENANT ROUTES ====================================


    public function showTenantLoginForm()
    {
        if (auth('web')->check()) {
            return redirect()->route('landlord.user.home');
        }
        return view('landlord.frontend.dashboard.login');
    }

    public function showTenantRegistrationForm()
    {
//        dd(request());
//        dd(Auth::guard('web')->user());
        $plan_id = \request()->p ?? '';
//        dd($plan_id);
        abort_if(!empty($plan_id) && empty(PricePlan::find($plan_id)), 404);

        if (auth('web')->check()) {
            return redirect()->route('landlord.user.home');
        }

        //return view('landlord.frontend.auth.register', compact('plan_id'));
        return view('landlord.frontend.dashboard.register', compact('plan_id'));
    }

    protected function tenant_user_create(Request $request)
    {
        // Both /register and /register-store-otp use the same OTP flow
        return $this->tenant_user_create_otp($request);
    }

    /**
     * OTP-gated registration for the order-page modal.
     * Step 1: Validate form data, generate OTP, store in session, send email.
     * Does NOT create the user — creation happens in verify_registration_otp().
     */
    public function tenant_user_create_otp(Request $request)
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:191'],
            'email'           => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'phone'           => ['required', 'string', 'regex:/^[0-9+]+$/', 'unique:users,mobile'],
            'username'        => ['required', 'string', 'max:191', 'unique:users'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'terms_condition' => ['required'],
        ], [
            'terms_condition.required' => __('Please mark on our terms and condition to agree and proceed'),
        ]);

        // Generate a 6-digit numeric OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Store pending registration in session (password is hashed — never stored plain-text)
        session()->put('pending_registration', [
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'country'    => $request->country ?? '',
            'city'       => $request->city ?? '',
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(5)->timestamp,
            'attempts'   => 0,
            'last_sent'  => now()->timestamp,
        ]);
        session()->save();

        // Send OTP email via existing BasicMail + MarkupGenerator
        try {
            $msg  = MarkupGenerator::paragraph(__('Hello') . ' ' . $request->name . ',');
            $msg .= MarkupGenerator::paragraph(__('Use the code below to verify your email address. It expires in 5 minutes.'));
            $msg .= MarkupGenerator::code($otp);
            $msg .= MarkupGenerator::paragraph(__('If you did not request this, please ignore this email.'));
            $subject = __('Your Registration OTP') . ' — ' . get_static_option('site_title');
            Mail::to($request->email)->send(new BasicMail($msg, $subject));
        } catch (\Exception $e) {
            Log::error('[tenant_user_create_otp] OTP email failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
            session()->forget('pending_registration');
            return response()->json([
                'type' => 'danger',
                'msg'  => __('Could not send OTP email. Please check your mail configuration.'),
            ]);
        }

        return response()->json([
            'status' => 'otp_sent',
            'type'   => 'success',
            'msg'    => __('OTP sent to your email. Please check your inbox.'),
            'email'  => $request->email,
        ]);
    }

    public function verify_registration_otp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $pending = session('pending_registration');

        if (empty($pending)) {
            return response()->json([
                'type'   => 'danger',
                'msg'    => __('Session expired. Please register again.'),
                'status' => 'expired',
            ]);
        }

        // Expiry check
        if (now()->timestamp > $pending['expires_at']) {
            session()->forget('pending_registration');
            return response()->json([
                'type'   => 'danger',
                'msg'    => __('OTP has expired. Please register again.'),
                'status' => 'expired',
            ]);
        }

        // Max attempts guard (5 tries)
        if ($pending['attempts'] >= 5) {
            session()->forget('pending_registration');
            return response()->json([
                'type'   => 'danger',
                'msg'    => __('Too many incorrect attempts. Please register again.'),
                'status' => 'expired',
            ]);
        }

        // Wrong OTP — increment counter and return
        if ($request->otp !== $pending['otp']) {
            $pending['attempts']++;
            session()->put('pending_registration', $pending);
            $remaining = 5 - $pending['attempts'];
            return response()->json([
                'type'   => 'danger',
                'msg'    => __('Incorrect OTP.') . ' ' . $remaining . ' ' . __('attempts remaining.'),
                'status' => 'invalid',
            ]);
        }

        // OTP correct — create the user now (email already verified by OTP)
        $user = User::create([
            'name'           => $pending['name'],
            'email'          => $pending['email'],
            'mobile'         => $pending['phone'],
            'country'        => $pending['country'],
            'city'           => $pending['city'],
            'username'       => $pending['username'],
            'password'       => $pending['password'], // already hashed in session
            'email_verified' => 1,
            'created_at'     => Carbon::now(),
            'updated_at'     => Carbon::now(),
        ]);

        session()->forget('pending_registration');

        Auth::guard('web')->login($user);
        session()->save(); // Force write before returning JSON so next request reads auth state

        return response()->json([
            'status'     => 'valid',
            'type'       => 'success',
            'msg'        => __('Registration successful! Redirecting...'),
            'user_id'    => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'csrf_token' => csrf_token(),
        ]);
    }

    public function resend_registration_otp(Request $request)
    {
        $pending = session('pending_registration');

        if (empty($pending)) {
            return response()->json([
                'type'   => 'danger',
                'msg'    => __('Session expired. Please register again.'),
                'status' => 'expired',
            ]);
        }

        // 60-second resend cooldown (enforced server-side)
        if (now()->timestamp - $pending['last_sent'] < 60) {
            $wait = 60 - (now()->timestamp - $pending['last_sent']);
            return response()->json([
                'type' => 'warning',
                'msg'  => sprintf(__('Please wait %d seconds before resending.'), $wait),
            ]);
        }

        // Generate fresh OTP and reset expiry + attempts
        $otp                   = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $pending['otp']        = $otp;
        $pending['expires_at'] = now()->addMinutes(5)->timestamp;
        $pending['attempts']   = 0;
        $pending['last_sent']  = now()->timestamp;
        session()->put('pending_registration', $pending);
        session()->save();

        try {
            $msg  = MarkupGenerator::paragraph(__('Hello') . ' ' . $pending['name'] . ',');
            $msg .= MarkupGenerator::paragraph(__('Your new OTP code (expires in 5 minutes):'));
            $msg .= MarkupGenerator::code($otp);
            $subject = __('Resend: Your Registration OTP') . ' — ' . get_static_option('site_title');
            Mail::to($pending['email'])->send(new BasicMail($msg, $subject));
        } catch (\Exception $e) {
            Log::error('[resend_registration_otp] Email failed', [
                'email' => $pending['email'],
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'type' => 'danger',
                'msg'  => __('Could not resend OTP. Please try again.'),
            ]);
        }

        return response()->json([
            'type'   => 'success',
            'msg'    => __('A new OTP has been sent to your email.'),
            'status' => 'resent',
        ]);
    }

    public function tenant_logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('landlord.user.login');
    }

    public function showUserForgetPasswordForm()
    {
        //return view('landlord.frontend.user.forget-password');
        return view('landlord.frontend.dashboard.forget-password');
    }

    public function sendUserForgetPasswordMail(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string:max:191'
        ]);

        $user_info = User::where('username', $request->username)->orWhere('email', $request->username)->first();

        if (!empty($user_info)) {
            $token_id = Str::random(30);
            $existing_token = DB::table('password_resets')->where('email', $user_info->email)->delete();
            if (empty($existing_token)) {
                DB::table('password_resets')->insert(['email' => $user_info->email, 'token' => $token_id]);
            }
            $message = __('Here is you password reset link, If you did not request to reset your password just ignore this mail.') . '<br> <a class="btn" href="' . route('landlord.user.reset.password', ['user' => $user_info->username, 'token' => $token_id]) . '" style="color:white;background:gray">' . __('Click Reset Password') . '</a>';
            $data = [
                'username' => $user_info->username,
                'message' => $message
            ];
            try {
                Mail::to($user_info->email)->send(new AdminResetEmail($data));
            } catch (\Exception $e) {
                return redirect()->back()->with([
                    'msg' => $e->getMessage(),
                    'type' => 'danger'
                ]);
            }

            return redirect()->back()->with([
                'msg' => __('Check Your Mail For Reset Password Link'),
                'type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'msg' => __('Your Username or Email Is Wrong!!!'),
            'type' => 'danger'
        ]);
    }

    public function showUserResetPasswordForm($username, $token)
    {
        //return view('landlord.frontend.user.reset-password')->with([
        //    'username' => $username,
        //    'token' => $token
        //]);
        return view('landlord.frontend.dashboard.reset-password')->with([
            'username' => $username,
            'token' => $token
        ]);
    }

    public function UserResetPassword(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'username' => 'required',
            'password' => 'required|string|min:8|confirmed'
        ]);
        $user_info = User::where('username', $request->username)->first();
        $token_iinfo = DB::table('password_resets')->where(['email' => $user_info->email, 'token' => $request->token])->first();
        if (!empty($token_iinfo)) {
            $user_info->password = Hash::make($request->password);
            $user_info->save();
            return redirect()->route('landlord.user.login')->with(['msg' => __('Password Changed Successfully'), 'type' => 'success']);
        }
        return redirect()->back()->with(['msg' => __('Somethings Going Wrong! Please Try Again or Check Your Old Password'), 'type' => 'danger']);
    }

    public function newsletter_store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email|max:191|unique:newsletters'
        ]);

        $verify_token = Str::random(32);
        Newsletter::create([
            'email' => $request->email,
            'verified' => 0,
            'token' => $verify_token
        ]);

        return response()->json([
            'msg' => __('Thanks for Subscribe Our Newsletter'),
            'type' => 'success'
        ]);
    }

    public function user_trial_account(Request $request)
    {
        // Safety: must be authenticated
        if (!Auth::guard('web')->check()) {
            return response()->json(['type' => 'danger', 'msg' => __('You must be logged in to start a trial.')]);
        }

        $request->validate([
            'order_id' => 'required',
            'subdomain' => 'required|unique:tenants,id',
            'theme' => 'required',
        ], [
            'theme.required' => __('No theme is selected.')
        ]);

        if ($request->subdomain != null) {
            $has_subdomain = Tenant::find(trim($request->subdomain));
            if (!empty($has_subdomain)) {
                return back()->with(['type' => 'danger', 'msg' => __('This subdomain is already in use, Try something different')]);
            }

            $site_domain = url('/');
            $site_domain = str_replace(['http://', 'https://'], '', $site_domain);
            $site_domain = substr($site_domain, 0, strpos($site_domain, '.'));
            $restricted_words = ['https', 'http', 'http://', 'https://', 'www', 'subdomain', 'domain', 'primary-domain', 'central-domain',
                'landlord', 'landlords', 'tenant', 'tenants', 'admin',
                'user', 'user', $site_domain];

            if (in_array(trim($request->subdomain), $restricted_words)) {
                return response()->json([
                    'msg' => __('Sorry, You can not use this subdomain'),
                    'type' => 'danger'
                ]);
            }

            $sub = $request->subdomain;
            $check_type = false;
            for ($i = 0; $i < strlen($sub); $i++) {
                if (ctype_alnum($sub[$i])) {
                    $check_type = true;
                }
            }

            if ($check_type == false) {
                return response()->json([
                    'msg' => __('Sorry, You can not use this subdomain'),
                    'type' => 'danger'
                ]);
            }
        }

        $user_id = Auth::guard('web')->user()->id;
        $user = User::find($user_id);
        if (is_null($user)) {
            return response()->json([
                'msg' => __('user not found'),
                'type' => 'danger'
            ]);
        }
        if (!empty(get_static_option('user_email_verify_status')) && !$user->email_verified) {
            return response()->json([
                'msg' => __('Please verify your account, Visit user dashboard for verification'),
                'type' => 'danger'
            ]);
        }

        $plan = PricePlan::find($request->order_id);
        if (is_null($plan)) {
            return response()->json([
                'msg' => __('plan not found'),
                'type' => 'danger'
            ]);
        }

        $subdomain = $request->subdomain;
        $theme = $request->theme ?? get_static_option('default_theme');

        session()->put('theme', $theme);

        $tenant_data = $user->tenant_details ?? [];
        $has_trial = false;
//        @dd($tenant_data);
        if (!is_null($tenant_data)) {
            foreach ($tenant_data as $tenant) {
//                @dd($tenant->payment_log);
                if (optional($tenant->payment_log)->status == 'trial') {
                    $has_trial = true;
                }
            }
            if ($has_trial) {
                return response()->json([
                    'msg' => __('Your trial limit is over! Please purchase a plan to continue') . '<br>' . '<small>' . __('You can make trial once only..!') . '</small>',
                    'type' => 'danger'
                ]);
            }
        }

        try {
            event(new TenantRegisterEvent($user, $subdomain, $theme));

            try {
                $raw_pass = get_static_option_central('tenant_admin_default_password') ?? '12345678';
                $credential_username = get_static_option_central('tenant_admin_default_username') ?? 'super_admin';
                Mail::to($user->email)->send(new TenantCredentialMail($credential_username, $raw_pass));
            } catch (\Exception $e) {}

            TenantTrialPaymentLog::trial_payment_log($user, $plan, $subdomain, $theme);
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
            Log::error('Trial tenant creation failed', [
                'subdomain' => $subdomain,
                'user_id' => $user_id,
                'error' => $message,
            ]);
            

            $log = PaymentLogs::where('tenant_id', $subdomain)->first();

            try {
                $admin_mail_message = sprintf(__('Database Crating failed for user id %1$s , please checkout admin panel and generate database for this user trial from admin panel manually'), $log->user_id ?? '');
                $admin_mail_subject = sprintf(__('Database Crating failed on trial request for user id %1$s'), $log->user_id ?? '');
                Mail::to(get_static_option_central('site_global_email'))->send(new BasicMail($admin_mail_message, $admin_mail_subject));
            } catch (\Exception $exception) {
            }

            LandlordPricePlanAndTenantCreate::store_exception($subdomain, 'domain failed on trial', $message, 0);

            return response()->json(['msg' => __('Your trial website is not ready yet, we have notified to admin regarding your request, it is in admin approval stage..! please try later..!'), 'type' => 'danger']);
        }

        $domain_details = DB::table('domains')->where('tenant_id', $subdomain)->first(); //domain; //issue in this line

        if (!is_null($domain_details)) {
            $url = tenant_url_with_protocol($domain_details->domain);
            $orderId = PaymentLogs::where(['user_id' => $user_id, 'package_id' => $request->order_id, 'tenant_id' => $subdomain])->select('id')->first();
            $successUrl = route('landlord.user.home');
            $user->update(['has_subdomain' => 1]);
            return response()->json([
                'url' => __($url),
                'success_url' => $successUrl,
                'type' => 'success'
            ]);
        }

        return response()->json([
            'url' => __('something went wrong, we have notified to admin regarding this issue, please try after sometime'),
            'type' => 'danger'
        ]);
    }

    public function applyCoupon()
    {
        $code = e(strip_tags(trim(\request()->coupon)));
        if (empty($code))
        {
            return response()->json([
                'type' => 'danger', 'msg' => __('Invalid coupon')
            ]);
        }

        $coupon_info = Coupon::published()->active()->where('code', $code)
            ->select('code', 'discount_type', 'discount_amount')
            ->first();
        $coupon_info ? $coupon_info['discount_type'] = LandlordCouponType::getText($coupon_info->discount_type) : [];

        return response()->json(
            $coupon_info ? ['type' => 'success', 'data' => $coupon_info] : ['type' => 'danger', 'msg' => __('Invalid coupon')]
        );
    }

    public function loginUsingToken($token, Request $request)
    {
        if (empty($token)) {
            return to_route('landlord.user.login');
        }

        abort_if(empty(Auth::guard('admin')->user()), 404);

        $user = null;
        if (!empty($request->user_id)) {
            $user = User::find($request->user_id);
        }

        $hash_token = hash_hmac(
            'sha512',
            $user->username,
            $user->id
        );
        if (!hash_equals($hash_token, $token)) {
            return to_route('landlord.user.login');
        }

        //login using super admin id
        if (Auth::guard('web')->loginUsingId($user->id)) {
            return to_route('landlord.user.home');
        }
        //pic a random super admin account...

        return to_route('landlord.user.login');
        //redirect to admin panel home page
    }

    public function unique_checker(Request $request)
    {
        $request->dd();
    }

}
