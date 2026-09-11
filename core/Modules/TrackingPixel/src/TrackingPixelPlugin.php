<?php

namespace Modules\TrackingPixel\Src;

use App\PluginSystem\PluginBase;
use Modules\TrackingPixel\Http\Controllers\TrackingPixelController;
use Modules\TrackingPixel\Jobs\SendServerConversionJob;

class TrackingPixelPlugin extends PluginBase
{
    public function id(): string
    {
        return 'tracking-pixel';
    }

    public function on_activate(): void
    {
        $this->run_migrations();

        $defaults = [
            'ga4_measurement_id'    => '',
            'ga4_enabled'           => '0',
            'gtm_container_id'      => '',
            'gtm_enabled'           => '0',
            'ga4_server_api_secret' => '',
            'ga4_server_enabled'    => '0',
            'meta_pixel_id'         => '',
            'meta_pixel_enabled'    => '0',
            'meta_access_token'     => '',
            'meta_server_enabled'   => '0',
            'meta_test_event_code'  => '',
            'tiktok_pixel_id'       => '',
            'tiktok_pixel_enabled'  => '0',
            'tiktok_access_token'   => '',
            'tiktok_server_enabled' => '0',
        ];

        foreach ($defaults as $key => $value) {
            if ($this->get_option($key) === null) {
                $this->update_option($key, $value);
            }
        }
    }

    public function on_deactivate(): void {}

    public function on_update(string $from_version): void
    {
        $this->run_migrations();
    }

    public function routes(): void
    {
        $this->registerSettings();
        $this->registerRoutes();
    }

    public function boot(): void
    {
        view()->addNamespace('tracking-pixel', $this->plugin_path('resources/views'));

        $this->registerSettings();

        if (!function_exists('tenant') || !tenant()) return;

        $this->registerMenus();
        $this->registerHooks();
    }

    private function registerSettings(): void
    {
        $this->register_settings([
            ['key' => 'ga4_enabled',           'label' => 'Enable GA4',                                    'type' => 'toggle', 'group' => 'Google Analytics 4'],
            ['key' => 'ga4_measurement_id',    'label' => 'Measurement ID',                               'type' => 'text',   'group' => 'Google Analytics 4', 'description' => 'Format: G-XXXXXXXXXX'],
            ['key' => 'ga4_server_enabled',    'label' => 'Enable GA4 Measurement Protocol (server-side)','type' => 'toggle', 'group' => 'Google Analytics 4'],
            ['key' => 'ga4_server_api_secret', 'label' => 'API Secret',                                   'type' => 'text',   'group' => 'Google Analytics 4'],
            ['key' => 'gtm_enabled',           'label' => 'Enable Google Tag Manager',                    'type' => 'toggle', 'group' => 'Google Tag Manager'],
            ['key' => 'gtm_container_id',      'label' => 'Container ID',                                 'type' => 'text',   'group' => 'Google Tag Manager', 'description' => 'Format: GTM-XXXXXXX'],
            ['key' => 'meta_pixel_enabled',    'label' => 'Enable Meta Pixel',                            'type' => 'toggle', 'group' => 'Meta (Facebook)'],
            ['key' => 'meta_pixel_id',         'label' => 'Pixel ID',                                     'type' => 'text',   'group' => 'Meta (Facebook)'],
            ['key' => 'meta_server_enabled',   'label' => 'Enable Conversions API (server-side)',          'type' => 'toggle', 'group' => 'Meta (Facebook)'],
            ['key' => 'meta_access_token',     'label' => 'Conversions API Access Token',                 'type' => 'text',   'group' => 'Meta (Facebook)'],
            ['key' => 'meta_test_event_code',  'label' => 'Test Event Code (optional)',                   'type' => 'text',   'group' => 'Meta (Facebook)', 'description' => 'Leave blank in production'],
            ['key' => 'tiktok_pixel_enabled',  'label' => 'Enable TikTok Pixel',                          'type' => 'toggle', 'group' => 'TikTok'],
            ['key' => 'tiktok_pixel_id',       'label' => 'Pixel ID',                                     'type' => 'text',   'group' => 'TikTok'],
            ['key' => 'tiktok_server_enabled', 'label' => 'Enable TikTok Events API (server-side)',        'type' => 'toggle', 'group' => 'TikTok'],
            ['key' => 'tiktok_access_token',   'label' => 'Events API Access Token',                      'type' => 'text',   'group' => 'TikTok'],
        ]);
    }

    private function registerMenus(): void
    {
        $this->add_menu([
            'id'      => 'tracking-pixel-menu',
            'label'   => __('Tracking Pixels'),
            'icon'    => 'mdi-chart-timeline-variant',
            'route'   => 'tenant.admin.tracking-pixel.settings',
            'order'   => 85,
            'context' => 'tenant',
        ]);

        $this->add_submenu('tracking-pixel-menu', [
            'id'    => 'tracking-pixel-settings',
            'label' => __('Settings'),
            'route' => 'tenant.admin.tracking-pixel.settings',
        ]);

        $this->add_submenu('tracking-pixel-menu', [
            'id'    => 'tracking-pixel-log',
            'label' => __('Event Log'),
            'route' => 'tenant.admin.tracking-pixel.log',
        ]);
    }

    private function registerHooks(): void
    {
        $this->add_action('nazmart:frontend_head_end', function () {
            $this->injectHeadPixels();
        });

        $this->add_action('nazmart:frontend_body_start', function () {
            $this->injectBodyStart();
        });

        $this->add_action('nazmart:frontend_footer', function () {
            $this->injectFooterBridge();
        });

        $this->add_action('nazmart:after_add_to_cart', function ($item, $cart) {
            $this->injectAddToCartEvent($item);
        });

        $this->add_action('nazmart:order_completed', function ($order) {
            dispatch(new SendServerConversionJob(
                $order->load(['items.product', 'user']),
                $this->currentTenantId()
            ));
        });
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
                ->prefix('admin-home/tracking-pixel')
                ->name('tenant.admin.tracking-pixel.')
                ->group(function () {
                    \Route::get('/settings',  [TrackingPixelController::class, 'settings'])->name('settings');
                    \Route::post('/settings', [TrackingPixelController::class, 'saveSettings'])->name('settings.save');
                    \Route::get('/log',       [TrackingPixelController::class, 'log'])->name('log');
                    \Route::post('/test',     [TrackingPixelController::class, 'test'])->name('test');
                });
        });
    }

    // ── Pixel injection ───────────────────────────────────────────────────────

    private function injectHeadPixels(): void
    {
        if ($this->get_option('ga4_enabled') && ($id = $this->get_option('ga4_measurement_id'))) {
            echo <<<HTML
<!-- GA4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$id}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$id}');
</script>
HTML;
        }

        if ($this->get_option('gtm_enabled') && ($gtm = $this->get_option('gtm_container_id'))) {
            echo <<<HTML
<!-- GTM -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$gtm}');</script>
HTML;
        }

        if ($this->get_option('meta_pixel_enabled') && ($pixelId = $this->get_option('meta_pixel_id'))) {
            echo <<<HTML
<!-- Meta Pixel -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$pixelId}');
fbq('track', 'PageView');
</script>
HTML;
        }

        if ($this->get_option('tiktok_pixel_enabled') && ($ttId = $this->get_option('tiktok_pixel_id'))) {
            echo <<<HTML
<!-- TikTok Pixel -->
<script>
!function (w, d, t) {
  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
  ttq.load('{$ttId}');
  ttq.page();
}(window, document, 'ttq');
</script>
HTML;
        }
    }

    private function injectBodyStart(): void
    {
        if ($this->get_option('gtm_enabled') && ($gtm = $this->get_option('gtm_container_id'))) {
            echo <<<HTML
<!-- GTM noscript -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$gtm}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
HTML;
        }

        if ($this->get_option('meta_pixel_enabled') && ($pixelId = $this->get_option('meta_pixel_id'))) {
            echo <<<HTML
<!-- Meta Pixel noscript -->
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={$pixelId}&ev=PageView&noscript=1"/></noscript>
HTML;
        }
    }

    private function injectFooterBridge(): void
    {
        $ga4    = $this->get_option('ga4_enabled')           ? 'true' : 'false';
        $meta   = $this->get_option('meta_pixel_enabled')    ? 'true' : 'false';
        $tiktok = $this->get_option('tiktok_pixel_enabled')  ? 'true' : 'false';

        // Flush any add-to-cart events queued during AJAX requests this session
        $queued = session()->pull('tp_atc_events', []);
        $queued_js = '';
        foreach ($queued as $ev) {
            $id    = (int)   ($ev['id']    ?? 0);
            $name  = addslashes($ev['name'] ?? '');
            $price = (float) ($ev['price'] ?? 0);
            $qty   = (int)   ($ev['qty']   ?? 1);
            $queued_js .= "if(window.nmTrack)window.nmTrack.addToCart({id:{$id},name:'{$name}',price:{$price},qty:{$qty}});\n";
        }

        echo <<<HTML
<script>
window.nmTrack = {
  ga4: {$ga4}, meta: {$meta}, tiktok: {$tiktok},
  addToCart: function(data) {
    if (this.ga4 && window.gtag)
      gtag('event','add_to_cart',{currency:data.currency||'USD',value:data.price,items:[{item_id:data.id,item_name:data.name,price:data.price,quantity:data.qty||1}]});
    if (this.meta && window.fbq)
      fbq('track','AddToCart',{content_ids:[data.id],content_type:'product',value:data.price,currency:data.currency||'USD'});
    if (this.tiktok && window.ttq)
      ttq.track('AddToCart',{content_id:data.id,content_type:'product',value:data.price,currency:data.currency||'USD'});
  },
  viewContent: function(data) {
    if (this.ga4 && window.gtag)
      gtag('event','view_item',{currency:data.currency||'USD',value:data.price,items:[{item_id:data.id,item_name:data.name,price:data.price}]});
    if (this.meta && window.fbq)
      fbq('track','ViewContent',{content_ids:[data.id],content_type:'product',value:data.price,currency:data.currency||'USD'});
    if (this.tiktok && window.ttq)
      ttq.track('ViewContent',{content_id:data.id,content_type:'product',value:data.price,currency:data.currency||'USD'});
  }
};
{$queued_js}
</script>
HTML;
    }

    private function injectAddToCartEvent($item): void
    {
        $data = [
            'id'    => (int)   ($item['product_id']   ?? 0),
            'name'  => $item['product_name'] ?? '',
            'price' => (float) ($item['price']          ?? 0),
            'qty'   => (int)   ($item['quantity']       ?? 1),
        ];

        // AJAX/JSON requests: queue for next full page load to avoid corrupting the JSON response
        if (request()->expectsJson() || request()->ajax()) {
            session()->push('tp_atc_events', $data);
            return;
        }

        $id    = $data['id'];
        $name  = addslashes($data['name']);
        $price = $data['price'];
        $qty   = $data['qty'];
        echo "<script>if(window.nmTrack)window.nmTrack.addToCart({id:{$id},name:'{$name}',price:{$price},qty:{$qty}});</script>\n";
    }

    private function currentTenantId(): ?string
    {
        return function_exists('tenant') && tenant() ? (string) tenant('id') : null;
    }
}
