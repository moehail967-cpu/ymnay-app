<?php

namespace Modules\YemeniWallets\Src;

use App\PluginSystem\PluginBase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Modules\YemeniWallets\Http\Controllers\Landlord\WalletCatalogController;
use Modules\YemeniWallets\Http\Controllers\Tenant\TenantWalletController;
use Modules\YemeniWallets\Http\Controllers\YemeniWalletsGatewayController;

class YemeniWalletsPlugin extends PluginBase
{
    public function id(): string
    {
        return 'yemeni-wallets';
    }

    public function boot(): void
    {
        $this->registerViewNamespace();
        $this->registerMenus();
        $this->registerRoutes();

        $this->enqueue_admin_style(
            'yemeni-wallets-admin',
            $this->plugin_url('resources/css/admin.css')
        );

        // Hook into the checkout page render filter so our wallet widget
        // (wallet selector + mandatory proof upload field) gets injected
        // alongside the other payment gateway extra-info sections.
        // 'nazmart:render_checkout_form' is applied in:
        //   App\Http\Controllers\Tenant\Frontend\CheckoutPaymentController::checkout_page()
        $this->add_filter('nazmart:render_checkout_form', function (string $html): string {
            try {
                $extra = view('yemeni_wallets::checkout.checkout_widget')->render();
                // Inject before the closing </form> tag of the checkout form
                // so it sits inside the payment_gateway_extra_field_information_wrap
                return str_replace(
                    '</div><!-- end payment_gateway_extra_field_information_wrap -->',
                    view('yemeni_wallets::checkout.checkout_widget')->render()
                        . '</div><!-- end payment_gateway_extra_field_information_wrap -->',
                    $html
                ) ?: $html . $extra;
            } catch (\Throwable) {
                return $html;
            }
        });
    }

    /**
     * Runs for ALL discovered plugins even when inactive, per PluginBase's
     * contract, so admin settings pages stay reachable from the plugin card.
     * We simply reuse the same route registration.
     */
    public function routes(): void
    {
        $this->registerRoutes();
    }

    public function on_activate(): void
    {
        // Creates wallet_payment_proofs in whatever DB context activation
        // runs in. VERIFY: when a tenant enables this plugin from their own
        // admin panel, confirm this executes against that tenant's database
        // (not the central/landlord one) before relying on it in production.
        $this->run_migrations();
    }

    public function on_deactivate(): void
    {
        // Intentionally not rolling back migrations on deactivate, so a
        // tenant that re-enables the gateway later doesn't lose historical
        // payment-proof records.
    }

    protected function registerViewNamespace(): void
    {
        // Defensive: register explicitly in case the platform doesn't
        // already auto-register `resources/views` for every plugin.
        View::addNamespace('yemeni_wallets', $this->plugin_path('resources/views'));
    }

    protected function registerMenus(): void
    {
        // ── Landlord: wallet catalog management ────────────────────────
        $this->add_menu([
            'id'         => 'yemeni-wallets-landlord-menu',
            'label'      => __('Yemeni Wallets'),
            'icon'       => 'mdi-wallet-outline',
            'route'      => 'landlord.yemeniwallets.catalog.index',
            'order'      => 60,
            'context'    => 'landlord',
            'permission' => 'yemeni-wallets-landlord-manage',
        ]);

        $this->add_submenu('yemeni-wallets-landlord-menu', [
            'id'         => 'yemeni-wallets-landlord-catalog-menu',
            'label'      => __('Wallet Catalog'),
            'route'      => 'landlord.yemeniwallets.catalog.index',
            'permission' => null,
        ]);

        // ── Tenant: activation + verification ───────────────────────────
        $this->add_menu([
            'id'         => 'yemeni-wallets-tenant-menu',
            'label'      => __('Yemeni Wallets'),
            'icon'       => 'mdi-wallet',
            'route'      => 'tenant.admin.yemeniwallets.index',
            'order'      => 60,
            'context'    => 'tenant',
            'permission' => 'yemeni-wallets-tenant-manage',
        ]);

        $this->add_submenu('yemeni-wallets-tenant-menu', [
            'id'         => 'yemeni-wallets-tenant-my-wallets-menu',
            'label'      => __('My Wallets'),
            'route'      => 'tenant.admin.yemeniwallets.index',
            'permission' => null,
        ]);

        $this->add_submenu('yemeni-wallets-tenant-menu', [
            'id'         => 'yemeni-wallets-tenant-proofs-menu',
            'label'      => __('Payment Verifications'),
            'route'      => 'tenant.admin.yemeniwallets.proofs.index',
            'permission' => null,
        ]);
    }

    protected function registerRoutes(): void
    {
        $this->register_web_routes(function () {
            // Landlord: catalog CRUD.
            // Confirmed guard: 'auth:admin' (from routes/admin.php and Landlord controllers).
            Route::middleware(['web', 'auth:admin'])
                ->prefix('landlord/yemeni-wallets')
                ->name('landlord.yemeniwallets.catalog.')
                ->group(function () {
                    Route::get('/', [WalletCatalogController::class, 'index'])->name('index');
                    Route::get('/create', [WalletCatalogController::class, 'create'])->name('create');
                    Route::post('/', [WalletCatalogController::class, 'store'])->name('store');
                    Route::get('/{walletId}/edit', [WalletCatalogController::class, 'edit'])->name('edit');
                    Route::put('/{walletId}', [WalletCatalogController::class, 'update'])->name('update');
                    Route::patch('/{walletId}/toggle', [WalletCatalogController::class, 'toggleStatus'])->name('toggle');
                    Route::delete('/{walletId}', [WalletCatalogController::class, 'destroy'])->name('destroy');
                });

            // Tenant admin: activation + proof verification.
            // Confirmed guard: 'auth:admin' (from routes/tenant_admin.php — same guard works in tenant context).
            Route::middleware(['web', 'auth:admin'])
                ->prefix('admin/yemeni-wallets')
                ->name('tenant.admin.yemeniwallets.')
                ->group(function () {
                    Route::get('/', [TenantWalletController::class, 'index'])->name('index');
                    Route::post('/', [TenantWalletController::class, 'store'])->name('store');

                    Route::get('/proofs', [TenantWalletController::class, 'proofsIndex'])->name('proofs.index');
                    Route::patch('/proofs/{proof}/approve', [TenantWalletController::class, 'approveProof'])->name('proofs.approve');
                    Route::patch('/proofs/{proof}/reject', [TenantWalletController::class, 'rejectProof'])->name('proofs.reject');
                });

            // Storefront: customer-facing checkout + proof submission.
            // No tenancy middleware needed in the route — the platform initializes tenancy
            // at the kernel level before routes are matched (confirmed from CheckoutPaymentController).
            Route::middleware(['web'])
                ->prefix('yemeni-wallets')
                ->name('yemeniwallets.')
                ->group(function () {
                    // JSON endpoint: returns active wallets for the checkout widget (AJAX).
                    Route::get('/checkout-options', [YemeniWalletsGatewayController::class, 'paymentGateway'])->name('checkout-options');

                    // Page shown after chargeCustomer() redirect: customer uploads proof here.
                    Route::get('/submit-proof', [YemeniWalletsGatewayController::class, 'submitProofPage'])->name('submit-proof-page');

                    // POST: handle the proof image upload + finalize pending order.
                    Route::post('/submit-proof', [YemeniWalletsGatewayController::class, 'submitProof'])->name('submit-proof');
                });
        });
    }
}
