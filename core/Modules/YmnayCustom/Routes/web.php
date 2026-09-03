<?php

use Illuminate\Support\Facades\Route;
use Modules\YmnayCustom\Http\Controllers\ManualWalletAdminController;
use Modules\YmnayCustom\Http\Controllers\TenantWalletAdminController;
use Modules\YmnayCustom\Http\Controllers\TenantWalletReviewController;
use Modules\YmnayCustom\Http\Controllers\LandlordWalletReviewController;
use App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware(['auth:admin', 'adminglobalVariable', 'set_lang'])
    ->prefix('admin-home/ymnay-manual-wallets')->name('ymnaycustom.landlord.wallets.')
    ->group(function () {
        Route::get('/', [ManualWalletAdminController::class, 'index'])->name('index');
        Route::post('/', [ManualWalletAdminController::class, 'store'])->name('store');
        Route::put('/{wallet}', [ManualWalletAdminController::class, 'update'])->name('update');
        Route::delete('/{wallet}', [ManualWalletAdminController::class, 'destroy'])->name('destroy');
        Route::post('/{wallet}/toggle', [ManualWalletAdminController::class, 'toggle'])->name('toggle');
        Route::post('/orders/{order}/approve', [LandlordWalletReviewController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/reject', [LandlordWalletReviewController::class, 'reject'])->name('orders.reject');
    });

Route::middleware([
    'web', InitializeTenancyByDomainCustomisedMiddleware::class,
    PreventAccessFromCentralDomains::class, 'auth:admin', 'tenant_admin_glvar',
    'package_expire', 'tenantAdminPanelMailVerify', 'tenant_status', 'set_lang'
])->prefix('admin-home/ymnay-manual-wallets/tenant')->name('ymnaycustom.tenant.wallets.')
    ->group(function () {
        Route::get('/', [TenantWalletAdminController::class, 'index'])->name('index');
        Route::post('/', [TenantWalletAdminController::class, 'save'])->name('save');
        Route::post('/orders/{order}/approve', [TenantWalletReviewController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/reject', [TenantWalletReviewController::class, 'reject'])->name('orders.reject');
    });
