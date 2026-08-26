<?php

namespace Modules\ProductQA\Src;

use App\PluginSystem\PluginBase;
use Modules\ProductQA\Src\QAService;

class ProductQAPlugin extends PluginBase
{
    public function id(): string
    {
        return 'product-qa';
    }

    private function tenantId(): ?int
    {
        try { return function_exists('tenant') && tenant() ? (int) tenant()->id : null; } catch (\Throwable) { return null; }
    }

    public function routes(): void
    {
        $this->register_settings();
    }

    public function boot(): void
    {
        $this->register_settings();

        if (! $this->get_option('enabled', true)) {
            return;
        }

        $this->add_action('nazmart:frontend_footer', [$this, 'injectQASection'], 20, 0);
    }

    public function injectQASection(): void
    {
        $path = request()->path();

        if (! preg_match('#product[s]?/#i', $path)) {
            return;
        }

        $segments = explode('/', trim($path, '/'));
        $slug     = end($segments);

        $product = \DB::table('products')->where('slug', $slug)->first();
        if (! $product) {
            return;
        }

        $service      = new QAService($this->tenantId());
        $questions    = $service->getForProduct($product->id);
        $submitUrl    = url(route_prefix('') . '/product-qa/ask');
        $helpfulUrl   = url(route_prefix('') . '/product-qa/helpful');
        $requireLogin = (bool) $this->get_option('require_login', false);
        $isLoggedIn   = auth('web')->check();
        $user         = $isLoggedIn ? auth('web')->user() : null;

        echo view('product-qa::frontend.widget', compact(
            'product', 'questions', 'submitUrl', 'helpfulUrl', 'requireLogin', 'isLoggedIn', 'user'
        ))->render();
    }

    public function on_activate(): void
    {
        $this->run_migrations(__DIR__ . '/../database/migrations');
    }

    public function register_web_routes(): void
    {
        $adminPrefix = route_prefix('admin');
        $adminCtrl   = \Modules\ProductQA\Http\Controllers\Admin\QAController::class;
        $frontCtrl   = \Modules\ProductQA\Http\Controllers\Frontend\QAFrontendController::class;
        $adminMid    = ['web', 'auth:web', 'tenant_admin'];

        \Route::prefix($adminPrefix . '/product-qa')->middleware($adminMid)->name('tenant.admin.product-qa.')->group(function () use ($adminCtrl) {
            \Route::get('/',               [$adminCtrl, 'index'])->name('index');
            \Route::post('/{id}/answer',   [$adminCtrl, 'answer'])->name('answer');
            \Route::post('/{id}/reject',   [$adminCtrl, 'reject'])->name('reject');
            \Route::post('/{id}/delete',   [$adminCtrl, 'destroy'])->name('destroy');
            \Route::get('/settings',       [$adminCtrl, 'settings'])->name('settings');
            \Route::post('/settings/save', [$adminCtrl, 'saveSettings'])->name('settings.save');
        });

        // Frontend routes — no auth required (guest questions allowed)
        \Route::prefix(route_prefix('') . '/product-qa')->middleware(['web'])->name('tenant.frontend.product-qa.')->group(function () use ($frontCtrl) {
            \Route::post('/ask',     [$frontCtrl, 'submit'])->name('submit');
            \Route::post('/helpful', [$frontCtrl, 'helpful'])->name('helpful');
        });
    }

    public function register_settings(): void
    {
        $this->add_menu('product-qa', [
            'label'      => __('Product Q&A'),
            'icon'       => 'mdi mdi-comment-question-outline',
            'route'      => 'tenant.admin.product-qa.index',
            'permission' => 'product_qa_manage',
        ]);

        $this->add_submenu('product-qa', [
            ['label' => __('Questions'),  'route' => 'tenant.admin.product-qa.index'],
            ['label' => __('Settings'),   'route' => 'tenant.admin.product-qa.settings'],
        ]);
    }
}
