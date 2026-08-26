<?php

namespace Themes\Bakerco\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoSection extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'bakerco_promo_section';
    }

    protected function getWidgetName(): string
    {
        return 'BakerCo: Promo Section';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-star';
    }

    protected function getWidgetDescription(): string
    {
        return __('Promotional split section with heading, text, button and decorative right side');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['promo', 'cta', 'bakerco'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('heading', FieldManager::TEXTAREA()->setLabel('Heading (HTML allowed)')->setDefault('Custom Cakes for<br>Every Occasion'))
            ->registerField('text', FieldManager::TEXTAREA()->setLabel('Paragraph')->setDefault('Birthdays, weddings, anniversaries — let us create the perfect centrepiece.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Order Custom Cake'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->registerField('emoji', FieldManager::TEXT()->setLabel('Decorative Emoji / Icon (right side)')->setDefault('🎂'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('promo_image', FieldManager::IMAGE()->setLabel('Right-side Image (overrides emoji if set)'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">BakerCo: Promo Section — preview on the live page</p>
                    </div>';
        }

        $content = $settings['general']['content'] ?? [];
        $media = $settings['general']['media'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];

        $promo_image = null;
        $imageField = $media['promo_image'] ?? null;
        if (is_array($imageField)) {
            $promo_image = $imageField['img_url'] ?? $imageField['url'] ?? null;
        } elseif (!empty($imageField)) {
            $d = get_attachment_image_by_id($imageField);
            $promo_image = $d['img_url'] ?? null;
        }

        // Fallback to theme dummy image if seeding failed or media is missing
        if (empty($promo_image)) {
            $promo_image = global_asset('core/' . theme_assets('images/promo-banner.png', 'bakerco'));
        }

        $rawUrl = $content['button_url'] ?? '#';

        return view('theme-bakerco::widgets.promo_section', [
            'heading' => $content['heading'] ?? 'Custom Cakes for<br>Every Occasion',
            'text' => $content['text'] ?? 'Birthdays, weddings, anniversaries — let us create the perfect centrepiece.',
            'button_text' => $content['button_text'] ?? 'Order Custom Cake',
            'button_url' => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'emoji' => $content['emoji'] ?? '🎂',
            'promo_image' => $promo_image,
            'padding_top' => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bakerco';
    }
}
