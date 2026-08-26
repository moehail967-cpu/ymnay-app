<?php

namespace Modules\AbandonedCart\Src;

use App\PluginSystem\PluginBase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\AbandonedCart\Http\Controllers\Admin\AbandonedCartController;

class AbandonedCartPlugin extends PluginBase
{
    public function id(): string
    {
        return 'abandoned-cart';
    }

    public function routes(): void
    {
        $this->register_settings([
            ['key' => 'delay_hours',   'label' => 'Send reminder after (hours)', 'type' => 'number', 'default' => '2'],
            ['key' => 'max_reminders', 'label' => 'Max reminders per cart',       'type' => 'number', 'default' => '2'],
            ['key' => 'sender_name',   'label' => 'Sender name',                  'type' => 'text',   'default' => ''],
            ['key' => 'email_subject', 'label' => 'Email subject',                'type' => 'text',   'default' => 'You left something behind!'],
        ]);
    }

    public function boot(): void
    {
        $this->register_settings([
            ['key' => 'delay_hours',   'label' => 'Send reminder after (hours)', 'type' => 'number', 'default' => '2'],
            ['key' => 'max_reminders', 'label' => 'Max reminders per cart',       'type' => 'number', 'default' => '2'],
            ['key' => 'sender_name',   'label' => 'Sender name',                  'type' => 'text',   'default' => ''],
            ['key' => 'email_subject', 'label' => 'Email subject',                'type' => 'text',   'default' => 'You left something behind!'],
        ]);

        if (!function_exists('tenant') || !tenant()) return;

        $this->add_action('nazmart:after_add_to_cart', [$this, 'upsertAbandonedCart'], 10);
        $this->add_action('nazmart:cart_quantity_changed', [$this, 'upsertAbandonedCart'], 10);
        $this->add_action('nazmart:order_completed', [$this, 'markConverted'], 10);
        $this->add_action('nazmart:cart_cleared', [$this, 'expireCart'], 10);
        $this->schedule('daily', [$this, 'sendReminders']);

        $this->add_menu([
            'id'      => 'abandoned-cart-menu',
            'label'   => __('Abandoned Carts'),
            'icon'    => 'mdi-cart-remove',
            'route'   => 'tenant.admin.abandoned-cart.index',
            'order'   => 72,
            'context' => 'tenant',
        ]);

        $this->add_submenu('abandoned-cart-menu', [
            'id'    => 'abandoned-cart-settings',
            'label' => __('Recovery Settings'),
            'route' => 'tenant.admin.abandoned-cart.settings',
        ]);

        $this->registerRoutes();
    }

    public function on_activate(): void
    {
        $this->run_migrations();

        // Default options
        $this->update_option('delay_hours',   '2');
        $this->update_option('max_reminders', '2');
        $this->update_option('email_subject', 'You left something behind!');
        $this->update_option('email_body', "Hi {customer_name},\n\nYou left some items in your cart. Come back and complete your purchase!\n\nCart total: {cart_total}\n\n{cart_link}");
    }

    public function on_deactivate(): void
    {
        \Cache::forget('abandoned_cart.stats.' . $this->currentTenantId());
    }

    public function on_update(string $from_version): void
    {
        $this->run_migrations();
    }

    // ── Hook callbacks ────────────────────────────────────────────────────────

    public function upsertAbandonedCart(mixed $cartItem, mixed $cartInstance = null): void
    {
        try {
            $tenantId = $this->currentTenantId();
            $user     = auth()->user();
            $email    = $user?->email;

            if (!$email) {
                return; // Only track logged-in users
            }

            $cart = $cartInstance ?? \Cart::instance('default');

            DB::table('abandoned_carts')->updateOrInsert(
                ['tenant_id' => $tenantId, 'email' => $email, 'status' => 'abandoned'],
                [
                    'user_id'         => $user->id,
                    'cart_items'      => json_encode($cart->content()->toArray()),
                    'cart_total'      => $cart->total(),
                    'reminder_count'  => 0,
                    'reminded_at'     => null,
                    'updated_at'      => now(),
                    'created_at'      => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::channel('plugin')->warning("[abandoned-cart] upsertAbandonedCart: {$e->getMessage()}");
        }
    }

    public function markConverted(mixed $order): void
    {
        try {
            $tenantId = $this->currentTenantId();
            $email    = $order->customer_email ?? auth()->user()?->email;

            if (!$email) return;

            DB::table('abandoned_carts')
                ->where('tenant_id', $tenantId)
                ->where('email', $email)
                ->whereIn('status', ['abandoned', 'reminded'])
                ->update(['status' => 'converted', 'converted_at' => now()]);
        } catch (\Throwable $e) {
            Log::channel('plugin')->warning("[abandoned-cart] markConverted: {$e->getMessage()}");
        }
    }

    public function expireCart(mixed $cartInstance): void
    {
        try {
            $user = auth()->user();
            if (!$user) return;

            DB::table('abandoned_carts')
                ->where('tenant_id', $this->currentTenantId())
                ->where('email', $user->email)
                ->where('status', 'abandoned')
                ->update(['status' => 'expired']);
        } catch (\Throwable $e) {
            Log::channel('plugin')->warning("[abandoned-cart] expireCart: {$e->getMessage()}");
        }
    }

    public function sendReminders(): void
    {
        $delayHours   = (int) ($this->get_option('delay_hours', 2));
        $maxReminders = (int) ($this->get_option('max_reminders', 2));
        $tenantId     = $this->currentTenantId();

        $carts = DB::table('abandoned_carts')
            ->where('tenant_id', $tenantId)
            ->where('status', 'abandoned')
            ->where('reminder_count', '<', $maxReminders)
            ->where('updated_at', '<=', now()->subHours($delayHours))
            ->get();

        foreach ($carts as $cart) {
            $this->sendRemindersForCart($cart);
        }
    }

    public function sendRemindersForCart(mixed $cart): void
    {
        $subject = $this->get_option('email_subject', 'You left something behind!');

        try {
            Mail::send('abandoned-cart::emails.reminder', [
                'cart'        => $cart,
                'subject'     => $subject,
                'body'        => $this->get_option('email_body', ''),
                'sender_name' => $this->get_option('sender_name', config('mail.from.name')),
            ], function ($m) use ($cart, $subject) {
                $m->to($cart->email)->subject($subject);
            });

            DB::table('abandoned_carts')->where('id', $cart->id)->update([
                'reminded_at'    => now(),
                'reminder_count' => $cart->reminder_count + 1,
                'status'         => $cart->reminder_count + 1 >= (int) $this->get_option('max_reminders', 2)
                    ? 'reminded'
                    : 'abandoned',
            ]);
        } catch (\Throwable $e) {
            Log::channel('plugin')->error("[abandoned-cart] sendReminders to [{$cart->email}]: {$e->getMessage()}");
        }
    }

    private function currentTenantId(): ?int
    {
        try {
            return function_exists('tenant') && tenant() ? (int) tenant()->id : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function registerRoutes(): void
    {
        $this->register_web_routes(function () {
            \Route::middleware([
                    'web',
                    \App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware::class,
                    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                    'auth:admin',
                    'tenant_admin_glvar',
                    'package_expire',
                    'tenantAdminPanelMailVerify',
                    'tenant_status',
                    'set_lang',
                ])
                ->prefix('admin-home/abandoned-cart')
                ->name('tenant.admin.abandoned-cart.')
                ->group(function () {
                    \Route::get('/',          [AbandonedCartController::class, 'index'])->name('index');
                    \Route::get('/show/{id}', [AbandonedCartController::class, 'show'])->name('show');
                    \Route::post('/resend/{id}', [AbandonedCartController::class, 'resend'])->name('resend');
                    \Route::delete('/{id}',   [AbandonedCartController::class, 'destroy'])->name('destroy');
                    \Route::get('/settings',  [AbandonedCartController::class, 'settings'])->name('settings');
                    \Route::post('/settings', [AbandonedCartController::class, 'saveSettings'])->name('settings.save');
                });
        });
    }
}
