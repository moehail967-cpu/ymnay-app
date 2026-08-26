<?php

use App\Helpers\Payment\PaymentGatewayCredential;
use App\Http\Controllers\Landlord\Admin\LandlordAdminController;
use App\Http\Controllers\Landlord\Frontend\LandlordFrontendController;
use App\Http\Controllers\Tenant\Frontend\UserDashboardController;
use App\Models\PaymentLogs;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\Frontend\PaymentLogController;
use App\Http\Controllers\Landlord\Frontend\TenantUpgradeCheckoutController;
use Modules\CommissionManage\Http\Controllers\BuyerCommissionPaymentController;
use Modules\CommissionManage\Http\Controllers\TenantCommissionController;
use Modules\SiteAnalytics\Http\Middleware\Analytics;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

Route::middleware(['landlord_glvar','maintenance_mode'])->group(function (){
    Auth::routes(['register' => false]);
});

/* ---------------------------------
    landlord frontend login routes
----------------------------------- */
Route::middleware(['landlord_glvar','set_lang','maintenance_mode'])->controller(LandlordFrontendController::class)->group(function (){
    Route::get('/', 'homepage')->name('landlord.homepage');
    Route::post('/subdomain-check',  'subdomain_check')->name('landlord.subdomain.check');
    Route::post('/custom-domain-check',  'subdomain_custom_domain_check')->name('landlord.subdomain.custom-domain.check');
    Route::get('/verify-email','verify_user_email')->name('tenant.email.verify');
    Route::post('/verify-email','check_verify_user_email');
    Route::get('/resend-verify-email','resend_verify_user_email')->name('tenant.email.verify.resend');
    Route::post('store-login','ajax_login')->name('landlord.ajax.login');
    Route::get('/logout-from-landlord','logout_tenant_from_landlord')->name('tenant.admin.logout.from.landlord.home');

});

/* ---------------------------------------
    LANDLORD TO TENANT ADMIN TOKEN LOGIN
----------------------------------------- */
Route::get('/token-login/{token}', [LandlordFrontendController::class,'loginUsingToken'])->name('landlord.user.login.with.token');


/* -----------------------------
    landlord admin login routes
------------------------------ */
Route::middleware('set_lang')->controller(\App\Http\Controllers\Landlord\Admin\Auth\AdminLoginController::class)->prefix('admin')->group(function (){
    Route::get('/','login_form')->name('landlord.admin.login');
    Route::post('/','login_admin');
    Route::post('/logout','logout_admin')->name('landlord.admin.logout');

    Route::get('/login/forget-password','showUserForgetPasswordForm')->name('landlord.admin.forget.password');
    Route::get('/login/reset-password/{user}/{token}','showUserResetPasswordForm')->name('landlord.admin.reset.password');
    Route::post('/login/reset-password','UserResetPassword')->name('landlord.admin.reset.password.change');
    Route::post('/login/forget-password','sendUserForgetPasswordMail');
});


Route::controller(\App\Http\Controllers\Landlord\Frontend\FrontendFormController::class)->prefix('landlord')->group(function () {
    Route::post('submit-custom-form', 'custom_form_builder_message')->name('landlord.frontend.form.builder.custom.submit');
    Route::post('contact-direct', 'direct_contact_message')->name('landlord.frontend.contact.direct');
});


Route::prefix('user-home')->middleware(['auth:web','maintenance_mode','userMailVerify'])->controller(\App\Http\Controllers\Tenant\Admin\TenantDashboardController::class)->group(function (){
    Route::get('/','redirect_to_tenant_admin_panel')->name('tenant.home');
});



require_once __DIR__ .'/admin.php';

Route::middleware(['maintenance_mode','landlord_glvar'])->controller(PaymentLogController::class)->name('landlord.')->group(function () {
    Route::post('/paytm-ipn', 'paytm_ipn')->name('frontend.paytm.ipn');
    Route::post('/toyyibpay-ipn', 'toyyibpay_ipn')->name('frontend.toyyibpay.ipn');
    Route::get('/mollie-ipn', 'mollie_ipn')->name('frontend.mollie.ipn');
    Route::get('/stripe-ipn', 'stripe_ipn')->name('frontend.stripe.ipn');
    Route::post('/razorpay-ipn', 'razorpay_ipn')->name('frontend.razorpay.ipn');
    Route::post('/payfast-ipn', 'payfast_ipn')->name('frontend.payfast.ipn');
    Route::get('/flutterwave/ipn', 'flutterwave_ipn')->name('frontend.flutterwave.ipn');
    Route::get('/paystack-ipn', 'paystack_ipn')->name('frontend.paystack.ipn');
    Route::get('/midtrans-ipn', 'midtrans_ipn')->name('frontend.midtrans.ipn');
    Route::get('/cashfree-ipn', 'cashfree_ipn')->name('frontend.cashfree.ipn');
    Route::get('/instamojo-ipn', 'instamojo_ipn')->name('frontend.instamojo.ipn');
    Route::get('/paypal-ipn', 'paypal_ipn')->name('frontend.paypal.ipn');
    Route::get('/marcadopago-ipn', 'marcadopago_ipn')->name('frontend.marcadopago.ipn');
    Route::get('/squareup-ipn', 'squareup_ipn')->name('frontend.squareup.ipn');
    Route::post('/cinetpay-ipn', 'cinetpay_ipn')->name('frontend.cinetpay.ipn');
    Route::post('/kineticpay-ipn', 'kineticpay_ipn')->name('frontend.kineticpay.ipn');
    Route::post('/paytabs-ipn', 'paytabs_ipn')->name('frontend.paytabs.ipn');
    Route::post('/billplz-ipn', 'billplz_ipn')->name('frontend.billplz.ipn');
    Route::post('/zitopay-ipn', 'zitopay_ipn')->name('frontend.zitopay.ipn');
    Route::post('/iyzipay-ipn', 'iyzipay_ipn')->name('frontend.iyzipay.ipn');
    Route::post('/awdpay-ipn', 'awdpay_ipn')->name('frontend.awdpay.ipn');
    Route::post('/sslcommerz-ipn', 'sslcommerz_ipn')->name('frontend.sslcommerz.ipn');
    Route::post('/order-confirm','order_payment_form')->name('frontend.order.payment.form')->middleware('set_lang');
});

// Tenant in-dashboard plan upgrade — HMAC-signed, no auth required
Route::middleware(['maintenance_mode','landlord_glvar'])->get('/landlord/tenant-upgrade', [TenantUpgradeCheckoutController::class, 'initiate'])->name('landlord.frontend.tenant.upgrade.checkout');
//Route::post('/razorpay-ipn', [PaymentLogController::class,'razorpay_ipn'])->name('landlord.frontend.razorpay.ipn');



//LANDLORD HOME PAGE FRONTEND TENANT LOGIN - REGISTRATION
Route::middleware(['landlord_glvar','set_lang','maintenance_mode'])->controller(\App\Http\Controllers\Landlord\Frontend\LandlordFrontendController::class)->name('landlord.')->group(function () {
    Route::get('/login', 'showTenantLoginForm')->name('user.login');
    Route::post('store-login','ajax_login')->name('user.ajax.login');
    Route::get('/register','showTenantRegistrationForm')->name('user.register');
    Route::post('/register-store','tenant_user_create')->name('user.register.store');
    // OTP-gated registration used only by the order-page modal
    Route::post('/register-store-otp','tenant_user_create_otp')->name('user.register.otp.store');
    Route::post('/register-otp-verify','verify_registration_otp')->name('user.register.otp.verify');
    Route::post('/register-otp-resend','resend_registration_otp')->name('user.register.otp.resend');
    Route::get('/logout','tenant_logout')->name('user.logout');

    Route::get('/login/forget-password','showUserForgetPasswordForm')->name('user.forget.password');
    Route::get('/login/reset-password/{user}/{token}','showUserResetPasswordForm')->name('user.reset.password');
    Route::post('/login/reset-password','UserResetPassword')->name('user.reset.password.change');
    Route::post('/login/forget-password','sendUserForgetPasswordMail');

    Route::get('/user-logout','user_logout')->name('frontend.user.logout');

    Route::get('/verify-email','verify_user_email')->name('user.email.verify');
    Route::post('/verify-email','check_verify_user_email');
    Route::get('/resend-verify-email','resend_verify_user_email')->name('user.email.verify.resend');

    //Order
    Route::get('/plan-order/{id}','plan_order')->name('frontend.plan.order');
    //payment status route
    Route::get('/order-success/{id}','order_payment_success')->name('frontend.order.payment.success');
    Route::get('/order-cancel/{id}','order_payment_cancel')->name('frontend.order.payment.cancel');
    Route::get('/order-cancel-static','order_payment_cancel_static')->name('frontend.order.payment.cancel.static');
    Route::get('/order-confirm/{id}','order_confirm')->name('frontend.order.confirm');

    // Trial Account
    Route::post('/user/trial/account', 'user_trial_account')->name('frontend.trial.account');

    // Coupon Apply
    Route::get('/apply-coupon', 'applyCoupon')->name('frontend.coupon.apply');
});

// LANDLORD HOME PAGE Tenant Dashboard Routes
Route::controller(\App\Http\Controllers\Landlord\Frontend\UserDashboardController::class)->middleware(['landlord_glvar','set_lang','maintenance_mode','tenantMailVerify'])
    ->name('landlord.')->group(function(){
    Route::get('/user-home', 'user_index')->name('user.home');
    Route::get('/user/download/file/{id}', 'download_file')->name('user.dashboard.download.file');
    Route::get('/user/change-password', 'change_password')->name('user.home.change.password');
    Route::get('/user/edit-profile', 'edit_profile')->name('user.home.edit.profile');
    Route::post('/user/profile-update', 'user_profile_update')->name('user.profile.update');
    Route::post('/user/password-change', 'user_password_change')->name('user.password.change');
    Route::get('/user/support-tickets', 'support_tickets')->name('user.home.support.tickets');
    Route::get('support-ticket/view/{id}', 'support_ticket_view')->name('user.dashboard.support.ticket.view');

    // Razorpay recurring subscription routes
    Route::post('/user/enable-recurring', 'enableRecurring')->name('user.enable.recurring');
    Route::post('/user/disable-recurring', 'disableRecurring')->name('user.disable.recurring');
    Route::post('support-ticket/priority-change', 'support_ticket_priority_change')->name('user.dashboard.support.ticket.priority.change');
    Route::post('support-ticket/status-change', 'support_ticket_status_change')->name('user.dashboard.support.ticket.status.change');
    Route::post('support-ticket/message', 'support_ticket_message')->name('user.dashboard.support.ticket.message');
    Route::get('/package-orders', 'package_orders')->name('user.dashboard.package.order');
    Route::get('/custom-domain', 'custom_domain')->name('user.dashboard.custom.domain');
    Route::get('/user/site-issues', 'site_issues')->name('user.dashboard.site.issues');
    Route::post('/custom-domain', 'submit_custom_domain');
    Route::post('/package-order/cancel', 'package_order_cancel')->name('user.dashboard.package.order.cancel');
    Route::post('/package-order/quick-pay', [\App\Http\Controllers\Landlord\Frontend\PaymentLogController::class, 'quick_pay'])
        ->name('user.dashboard.package.order.quick.pay')
        ->middleware('throttle:5,1');
    Route::post('/package-user/generate-invoice', 'generate_package_invoice')->name('frontend.package.invoice.generate');

    // Razorpay Recurring Subscription Routes
    Route::post('/recurring/enable', 'enableRecurring')->name('user.enable.recurring');
    Route::post('/recurring/disable', 'disableRecurring')->name('user.disable.recurring');
    Route::post('/recurring/pause', 'pauseRecurring')->name('user.pause.recurring');
    Route::post('/recurring/resume', 'resumeRecurring')->name('user.resume.recurring');

    Route::get('/user/shops', 'shop_list')->name('user.shops');
    Route::post('/user/shop/delete/{tenant_id}', 'deleteShop')->name('user.shop.delete');

    Route::post('/package/check', 'package_check')->name('frontend.package.check');
    Route::post('/tenant-helper', 'getPackageDates');
    Route::post('/payment-log',  'getLatest');


    // Commission Payment Routes
    Route::get('/commission/payment-modal/{id}', [TenantCommissionController::class, 'showPaymentModal'])
        ->name('tenant.commission.payment.modal');

    Route::post('/commission/pay/{id}', [TenantCommissionController::class, 'payCommission'])
        ->name('tenant.commission.pay');

//    Route::get('/commission/commission-payment-cancel-static','commission_payment_cancel_static')->name('commission.payment.cancel.static');
//    Route::get('/commission/commission-payment-success','commission_payment_success')->name('commission.payment.success');

// Commission payment success/cancel routes
    Route::get('landlord/commission/payment/success', [TenantCommissionController::class, 'commission_payment_success'])
        ->name('commission.payment.success');

    Route::get('landlord/commission/payment/cancel', [TenantCommissionController::class, 'commission_payment_cancel_static'])
        ->name('commission.payment.cancel.static');

    // Withdraw Routes
    Route::get('/withdraw/form', [TenantCommissionController::class, 'showWithdrawForm'])
        ->name('tenant.withdraw.form');



    Route::get('/withdraw/my-requests', [TenantCommissionController::class, 'myWithdrawRequests'])
        ->name('tenant.withdraw.my-requests');

  Route::group(['prefix' => 'wallet'],function(){

      // Commission payment routes
      Route::get('/paypal-ipn', [BuyerCommissionPaymentController::class, 'paypal_ipn_for_commission'])->name('user.paypal.ipn.commission');
      Route::post('/paytm-ipn', [BuyerCommissionPaymentController::class, 'paytm_ipn_for_commission'])->name('user.paytm.ipn.commission');
      Route::get('/paystack-ipn', [BuyerCommissionPaymentController::class, 'paystack_ipn_for_commission'])->name('user.paystack.ipn.commission');
      Route::get('/mollie/ipn', [BuyerCommissionPaymentController::class, 'mollie_ipn_for_commission'])->name('user.mollie.ipn.commission');
      Route::get('/stripe/ipn', [BuyerCommissionPaymentController::class, 'stripe_ipn_for_commission'])->name('user.stripe.ipn.commission');
      Route::post('/razorpay-ipn', [BuyerCommissionPaymentController::class, 'razorpay_ipn_for_commission'])->name('user.razorpay.ipn.commission');
      Route::get('/flutterwave/ipn', [BuyerCommissionPaymentController::class, 'flutterwave_ipn_for_commission'])->name('user.flutterwave.ipn.commission');
      Route::get('/midtrans-ipn', [BuyerCommissionPaymentController::class, 'midtrans_ipn_for_commission'])->name('user.midtrans.ipn.commission');
      Route::post('/payfast-ipn', [BuyerCommissionPaymentController::class, 'payfast_ipn_for_commission'])->name('user.payfast.ipn.commission');
      Route::get('/cashfree-ipn', [BuyerCommissionPaymentController::class, 'cashfree_ipn_for_commission'])->name('user.cashfree.ipn.commission');
      Route::get('/instamojo-ipn', [BuyerCommissionPaymentController::class, 'instamojo_ipn_for_commission'])->name('user.instamojo.ipn.commission');
      Route::get('/marcadopago-ipn', [BuyerCommissionPaymentController::class, 'marcadopago_ipn_for_commission'])->name('user.marcadopago.ipn.commission');
      Route::get('/squareup-ipn', [BuyerCommissionPaymentController::class, 'squareup_ipn_for_commission'])->name('user.squareup.ipn.commission');
      Route::post('/cinetpay-ipn', [BuyerCommissionPaymentController::class, 'cinetpay_ipn_for_commission'])->name('user.cinetpay.ipn.commission');
      Route::post('/paytabs-ipn', [BuyerCommissionPaymentController::class, 'paytabs_ipn_for_commission'])->name('user.paytabs.ipn.commission');
      Route::post('/billplz-ipn', [BuyerCommissionPaymentController::class, 'billplz_ipn_for_commission'])->name('user.billplz.ipn.commission');
      Route::post('/zitopay-ipn', [BuyerCommissionPaymentController::class, 'zitopay_ipn_for_commission'])->name('user.zitopay.ipn.commission');
      Route::post('/toyyibpay-ipn', [BuyerCommissionPaymentController::class, 'toyyibpay_ipn_for_commission'])->name('user.toyyibpay.ipn.commission');
  });

});



Route::post('/withdraw/submit', [UserDashboardController::class, 'submitWithdrawRequest'])
    ->name('tenant.withdraw.submit');
//User Support Ticket Routes
Route::controller(\App\Http\Controllers\Landlord\Frontend\SupportTicketController::class)->middleware(['landlord_glvar', 'set_lang'])->name('landlord.')->group(function(){
    Route::get('support-tickets', 'page')->name('frontend.support.ticket');
    Route::post('support-tickets/new', 'store')->name('frontend.support.ticket.store');
});


//Visitor Newsletter Routes
Route::controller(\App\Http\Controllers\Landlord\Frontend\LandlordFrontendController::class)->middleware('landlord_glvar')->name('landlord.')->group(function(){
    Route::post('newsletter/new', 'newsletter_store')->name('frontend.newsletter.store.ajax');
});


//single page route
Route::middleware(['landlord_glvar','set_lang','maintenance_mode'])->controller(\App\Http\Controllers\Landlord\Frontend\LandlordFrontendController::class)->name('landlord.')->group(function () {
    //payment page route
    Route::get('/plan-order/{id}','plan_order')->name('frontend.plan.order');
    Route::get('/order-success/{id}','order_payment_success')->name('frontend.order.payment.success');
    Route::get('/order-cancel/{id}','order_payment_cancel')->name('frontend.order.payment.cancel');
    Route::get('/order-cancel-static','order_payment_cancel_static')->name('frontend.order.payment.cancel.static');
    Route::get('/view-plan/{id}/{trial?}','view_plan')->name('frontend.plan.view');
    Route::get('/order-confirm/{id}','order_confirm')->name('frontend.order.confirm');
    Route::get('/lang-change','lang_change')->name('langchange');
    Route::get('/{page:slug}', 'dynamic_single_page')->name('dynamic.page');
});

Route::get("assets/theme/screenshot/{theme}", function ($theme){
    $base = config('theme.base_path') . '/' . $theme . '/screenshot';

    if (is_dir($base)) {
        foreach (['primary.jpg', 'primary.png', 'primary.jpeg', 'primary.webp'] as $candidate) {
            $path = $base . '/' . $candidate;
            if (file_exists($path)) {
                return response()->file($path);
            }
        }
        $files = glob($base . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
        if (!empty($files)) {
            return response()->file($files[0]);
        }
    }

    return abort(404);
})->name("theme.primary.screenshot");

Route::get("assets/payment-gateway/screenshot/{moduleName}/{gatewayName}", function ($moduleName, $gatewayName){
    $image_name = getPaymentGatewayImagePath($gatewayName);
    $module_path = module_path($moduleName).'/assets/payment-gateway-image/'.$image_name;

    if(file_exists($module_path)){
        return response()->file($module_path);
    }

    return abort(404);
})->name("payment.gateway.logo");


Route::get('/admin-home/logs', [LogViewerController::class, 'index']);

// Plugin asset serving — serves Modules/{plugin}/resources/{path} via a safe controller.
// No auth required (CSS/JS must be publicly accessible). Path traversal is blocked in the controller.
Route::get('/plugins/{plugin_id}/assets/{path}', [\App\Http\Controllers\PluginAssetController::class, 'serve'])
    ->name('plugin.asset')
    ->where('path', '.+');

// Razorpay subscription webhook
//Route::match(['get','post'],'/razorpay-subscription-webhook',
//    [\App\Http\Controllers\Landlord\Frontend\RazorpayWebhookController::class, 'handle'])
//    ->name('landlord.frontend.razorpay.subscription.webhook')
//    ->withoutMiddleware(['csrf', 'auth', 'maintenance_mode']);

// Razorpay subscription webhook
Route::match(['get','post'],'/razorpay-subscription-webhook', function(\Illuminate\Http\Request $request) {
    return app(\App\Http\Controllers\Landlord\Frontend\RazorpayWebhookController::class)->handle($request);
})
    ->name('landlord.frontend.razorpay.subscription.webhook')
    ->withoutMiddleware(['csrf', 'auth', 'maintenance_mode']);


Route::fallback(function () {
    if (request()->expectsJson()) {
        return response()->json([
            'message' => 'Not Found'
        ], 404);
    }

    return response()->view('errors.404', [], 404);
});
