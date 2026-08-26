<?php

namespace Modules\AiIntegration\Src;

use App\PluginSystem\PluginBase;
use Modules\AiIntegration\Http\Controllers\AiIntegrationController;

class AiIntegrationPlugin extends PluginBase
{
    public function id(): string
    {
        return 'ai-integration';
    }

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function on_activate(): void {}

    public function on_deactivate(): void {}

    public function on_update(string $from_version): void {}

    // ── Routes ────────────────────────────────────────────────────────────────

    public function routes(): void
    {
        $this->registerSettings();
        $this->registerRoutes();
    }

    // ── Boot ──────────────────────────────────────────────────────────────────

    public function boot(): void
    {
        $this->registerSettings();

        if (!function_exists('tenant') || !tenant()) {
            return;
        }

        $this->add_menu([
            'id'      => 'ai-integration-menu',
            'label'   => __('AI Integration'),
            'icon'    => 'mdi-robot-outline',
            'route'   => 'tenant.admin.ai-integration.settings',
            'order'   => 90,
            'context' => 'tenant',
        ]);

        $this->enqueueInlineAssets();
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function registerSettings(): void
    {
        $providers = ['openai' => 'OpenAI', 'deepseek' => 'DeepSeek', 'gemini' => 'Gemini'];

        $this->register_settings([
            ['key' => 'provider',          'label' => 'Default Provider',  'type' => 'select', 'default' => 'openai',           'options' => $providers],
            ['key' => 'fallback_provider', 'label' => 'Fallback Provider', 'type' => 'select', 'default' => '',                 'options' => array_merge(['' => 'None'], $providers)],
            ['key' => 'openai_key',        'label' => 'OpenAI API Key',    'type' => 'text',   'default' => ''],
            ['key' => 'openai_model',      'label' => 'OpenAI Model',      'type' => 'select', 'default' => 'gpt-4o-mini',      'options' => ['gpt-5' => 'GPT-5', 'gpt-4o' => 'GPT-4o', 'gpt-4o-mini' => 'GPT-4o Mini', 'gpt-4-turbo' => 'GPT-4 Turbo', 'gpt-3.5-turbo' => 'GPT-3.5 Turbo']],
            ['key' => 'deepseek_key',      'label' => 'DeepSeek API Key',  'type' => 'text',   'default' => ''],
            ['key' => 'deepseek_model',    'label' => 'DeepSeek Model',    'type' => 'select', 'default' => 'deepseek-chat',    'options' => ['deepseek-v4' => 'DeepSeek V4', 'deepseek-chat' => 'DeepSeek Chat (V3)', 'deepseek-reasoner' => 'DeepSeek Reasoner']],
            ['key' => 'gemini_key',        'label' => 'Gemini API Key',    'type' => 'text',   'default' => ''],
            ['key' => 'gemini_model',      'label' => 'Gemini Model',      'type' => 'select', 'default' => 'gemini-1.5-flash', 'options' => ['gemini-3.0-pro' => 'Gemini 3 Pro', 'gemini-2.0-flash' => 'Gemini 2.0 Flash', 'gemini-1.5-pro' => 'Gemini 1.5 Pro', 'gemini-1.5-flash' => 'Gemini 1.5 Flash']],
            ['key' => 'temperature',       'label' => 'Temperature (0–2)', 'type' => 'text',   'default' => '0.7'],
        ]);
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
                ->prefix('admin-home/ai-integration')
                ->name('tenant.admin.ai-integration.')
                ->group(function () {
                    \Route::get('/settings',  [AiIntegrationController::class, 'tenantSettings'])->name('settings');
                    \Route::post('/settings', [AiIntegrationController::class, 'tenantSaveSettings'])->name('settings.save');
                    \Route::post('/generate', [AiIntegrationController::class, 'tenantGenerate'])->name('generate');
                });
        });
    }

    private function enqueueInlineAssets(): void
    {
        $cssFile = $this->plugin_path('Resources/css/ai-integration.css');
        $jsFile  = $this->plugin_path('Resources/js/ai-integration.js');

        if (file_exists($cssFile)) {
            app(\App\PluginSystem\AssetManager::class)
                ->enqueueInlineStyle('admin', 'ai-integration-css', file_get_contents($cssFile));
        }

        if (file_exists($jsFile)) {
            try {
                $provider = $this->get_option('provider', 'openai');
            } catch (\Throwable) {
                $provider = 'openai';
            }

            $config = 'window.aiIntegration=' . json_encode([
                'generateUrl' => url('/admin-home/ai-integration/generate'),
                'provider'    => $provider,
            ]) . ';';

            app(\App\PluginSystem\AssetManager::class)
                ->enqueueInlineScript('admin', 'ai-integration-js', $config . file_get_contents($jsFile), true);
        }
    }
}
