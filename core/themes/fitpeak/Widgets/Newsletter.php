<?php

namespace Themes\Fitpeak\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Newsletter extends BaseWidget
{
    protected function getWidgetType(): string { return 'fitpeak_newsletter'; }
    protected function getWidgetName(): string { return 'FitPeak: Newsletter'; }
    protected function getWidgetIcon(): string|array { return 'mdi mdi-email-outline'; }
    protected function getWidgetDescription(): string { return __('Email subscription section with FitPeak dark background and neon accent'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['newsletter', 'email', 'subscribe', 'fitpeak']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('tag_text', FieldManager::TEXT()
                ->setLabel('Eyebrow Tag')
                ->setDefault('Join The Community'))
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title')
                ->setDefault('TRAIN SMARTER, RECOVER FASTER'))
            ->registerField('title_accent', FieldManager::TEXT()
                ->setLabel('Accent Part (highlighted in neon green)')
                ->setDefault('RECOVER FASTER'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('Weekly training tips, nutrition guides, and exclusive member discounts — built for serious athletes.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Subscribe'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 72, 'right' => 0, 'bottom' => 72, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .fp-newsletter-section' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ])
            )
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">FitPeak: Newsletter — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];

        return view('theme-fitpeak::widgets.newsletter', [
            'tag_text'     => $content['tag_text']      ?? 'Join The Community',
            'title'        => $content['title']         ?? 'TRAIN SMARTER, RECOVER FASTER',
            'title_accent' => $content['title_accent']  ?? 'RECOVER FASTER',
            'subtitle'     => $content['subtitle']      ?? 'Weekly training tips, nutrition guides, and exclusive member discounts — built for serious athletes.',
            'button_text'  => $content['button_text']   ?? 'Subscribe',
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'fitpeak';
    }
}
