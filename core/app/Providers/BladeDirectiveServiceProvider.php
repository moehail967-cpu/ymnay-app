<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeDirectiveServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Blade::directive('isTenant',function (){
            return tenant();
        });

        Blade::directive('tenant', function () {
            return "<?php if(tenant()): ?>";
        });

        Blade::directive('else', function () {
            return "<?php else: ?>";
        });

        Blade::directive('endtenant', function () {
            return "<?php endif ?>";
        });

        // Plugin product card filter
        // Usage: @productcard($product) ... html ... @endproductcard
        Blade::directive('productcard', function ($expression) {
            return "<?php ob_start(); \$__plugin_product_card_product = {$expression}; ?>";
        });

        Blade::directive('endproductcard', function () {
            return "<?php echo apply_filters('nazmart:render_product_card', ob_get_clean(), \$__plugin_product_card_product); ?>";
        });
    }
}
