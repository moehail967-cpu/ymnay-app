<?php

use App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware;
use Modules\SocialAuth\Http\Controllers\Admin\SocialAuthSettingsController;
use Modules\SocialAuth\Http\Controllers\Frontend\SocialAuthController;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Landlord Admin — Settings
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:admin', 'adminglobalVariable', 'set_lang'])
    ->prefix('admin-home/social-auth')
    ->name('landlord.')
    ->controller(SocialAuthSettingsController::class)
    ->group(function () {
        Route::get('/', 'settings')->name('admin.social.auth.settings');
        Route::post('/', 'update')->name('admin.social.auth.update');
    });

/*
|--------------------------------------------------------------------------
| Tenant Admin — Settings
|--------------------------------------------------------------------------
*/
Route::group([
    'middleware' => [
        'auth:admin', 'adminglobalVariable', 'set_lang',
        InitializeTenancyByDomainCustomisedMiddleware::class,
        PreventAccessFromCentralDomains::class,
    ],
    'prefix' => 'admin-home/tenant/social-auth',
    'as'     => 'tenant.',
], function () {
    Route::get('/', [SocialAuthSettingsController::class, 'settings'])->name('admin.social.auth.settings');
    Route::post('/', [SocialAuthSettingsController::class, 'update'])->name('admin.social.auth.update');
});

/*
|--------------------------------------------------------------------------
| Landlord Frontend — Google OAuth (central domain only)
| Registered before tenant routes so landlord routes win URL matching.
| Landlord users are logged in directly inside handleGoogleCallback — no
| separate "complete" route is needed on the central domain.
|--------------------------------------------------------------------------
*/
Route::middleware(['landlord_glvar', 'set_lang'])
    ->controller(SocialAuthController::class)
    ->name('landlord.')
    ->group(function () {
        // Accepts ?from_tenant=shop.example.com when a tenant user clicks the button
        Route::get('/auth/google', 'redirectToGoogle')->name('user.google.redirect');
        // Google always redirects back here (one registered callback URL for all flows)
        Route::get('/auth/google/callback', 'handleGoogleCallback')->name('user.google.callback');
    });

/*
|--------------------------------------------------------------------------
| Tenant Frontend — complete login on subdomain after central-domain callback
| Registered after landlord routes — tenant name wins for auth/google/complete.
| The Google button on tenant pages links directly to the central domain URL;
| this route only receives the signed-token redirect after a successful callback.
|--------------------------------------------------------------------------
*/
Route::middleware([
    'web',
    InitializeTenancyByDomainCustomisedMiddleware::class,
    PreventAccessFromCentralDomains::class,
    'tenant_glvar',
    'set_lang',
])
    ->controller(SocialAuthController::class)
    ->name('tenant.')
    ->group(function () {
        Route::get('/auth/google/complete', 'completeTenantLogin')->name('user.google.complete');
    });
