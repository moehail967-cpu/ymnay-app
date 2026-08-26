<?php

namespace Modules\Integrations\Src;

use App\PluginSystem\PluginBase;

class IntegrationsPlugin extends PluginBase
{
    public function id(): string
    {
        return 'integrations';
    }

    public function boot(): void
    {
        // Re-register legacy tracking pixel hooks through HookEngine
        $this->add_action('nazmart:frontend_head', function () {
            if (view()->exists('integrations::tracking-just-after-head-open')) {
                echo view('integrations::tracking-just-after-head-open')->render();
            }
        });

        $this->add_action('nazmart:frontend_head_end', function () {
            if (view()->exists('integrations::tracking-before-head')) {
                echo view('integrations::tracking-before-head')->render();
            }
        });

        $this->add_action('nazmart:frontend_body_start', function () {
            if (view()->exists('integrations::tracking-after-body-open')) {
                echo view('integrations::tracking-after-body-open')->render();
            }
        });

        $this->add_action('nazmart:frontend_footer', function () {
            if (view()->exists('integrations::tracking-before-body-close')) {
                echo view('integrations::tracking-before-body-close')->render();
            }
        });

        $this->add_menu([
            'id'         => 'integration',
            'label'      => __('Integrations'),
            'icon'       => 'mdi-connection',
            'route'      => route_prefix().'integration',
            'order'      => 60,
            'context'    => 'both',
            'permission' => 'integration',
        ]);
    }
}
