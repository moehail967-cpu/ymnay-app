<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Themes;

use App\Facades\ThemeDataFacade;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ThemesFive extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'themes_five';
    }

    protected function getWidgetName(): string
    {
        return 'Themes Five';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-th-large';
    }

    protected function getWidgetDescription(): string
    {
        return 'Theme showcase grid — auto-loaded from installed themes, with card hover effect';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['themes', 'showcase', 'grid', 'cards', 'hover'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Label')
                ->setDefault('DESIGN COLLECTION')
            )
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title')
                ->setDescription('Use {count} as a placeholder for the total number of themes.')
                ->setDefault('Launch Your Online Store with {count}+ Premium Themes')
            )
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('Choose from premium, conversion-optimized themes designed by industry experts.')
            )
            ->endGroup();

        $control->addGroup('display', 'Display Options')
            ->registerField('theme_limit', FieldManager::NUMBER()
                ->setLabel('Number of Themes to Show')
                ->setDescription('Set how many themes to display. Leave 0 to show all.')
                ->setDefault(6)
                ->setMin(0)
                ->setMax(100)
                ->setStep(1)
            )
            ->endGroup();

        $control->addGroup('cta', 'CTA Button')
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text (after count)')
                ->setDefault('Premium Themes')
                ->setDescription('Rendered as: Browse All {count} [your text]. E.g. "Premium Themes" → "Browse All 24 Premium Themes"')
            )
            ->registerField('button_url', FieldManager::TEXT()
                ->setLabel('Button URL')
                ->setDefault('#')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('section_style', 'Section')
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(100)
                ->setMin(0)
                ->setMax(300)
                ->setStep(4)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(100)
                ->setMin(0)
                ->setMax(300)
                ->setStep(4)
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style   = $settings['style']   ?? [];

        // Heading
        $headingGroup = $general['heading'] ?? [];
        $badgeText = $headingGroup['badge_text'] ?? 'DESIGN COLLECTION';
        $title     = $headingGroup['title']      ?? 'Launch Your Online Store with {count}+ Premium Themes';
        $subtitle  = $headingGroup['subtitle']   ?? '';

        // Display options
        $displayGroup = $general['display'] ?? [];
        $themeLimit   = (int) ($displayGroup['theme_limit'] ?? 6);

        // CTA
        $ctaGroup   = $general['cta'] ?? [];
        $buttonText = $ctaGroup['button_text'] ?? 'Premium Themes';
        $buttonUrl  = $ctaGroup['button_url']  ?? '#';

        // Style
        $sectionStyle  = $style['section_style'] ?? [];
        $paddingTop    = $sectionStyle['padding_top']    ?? 100;
        $paddingBottom = $sectionStyle['padding_bottom'] ?? 100;

        // Build theme cards from installed themes (all fields from theme.json)
        $cards = [];
        foreach (ThemeDataFacade::getAllThemeData() as $theme) {
            $slug = $theme->slug ?? '';
            if (empty($slug)) continue;

            $customName  = get_static_option_central($slug . '_theme_name');
            $customUrl   = get_static_option_central($slug . '_theme_url');
            $customImage = get_static_option_central($slug . '_theme_image');

            $cards[] = [
                'title'    => !empty($customName)  ? $customName  : ($theme->name ?? $slug),
                'category' => $theme->niche ?? '',
                'url'      => !empty($customUrl)   ? $customUrl   : '#',
                'image'    => !empty($customImage) ? $customImage : loadScreenshot($slug),
                'slug'     => $slug,
            ];
        }

        // Total count before limiting (for CTA label)
        $totalThemes = count($cards);

        // Limit number of displayed themes (0 = show all)
        if ($themeLimit > 0) {
            $cards = array_slice($cards, 0, $themeLimit);
        }

        return view('widgetbuilder::landlord.themes.themes_five', [
            'badgeText'     => $badgeText,
            'title'         => $title,
            'subtitle'      => $subtitle,
            'cards'         => $cards,
            'totalThemes'   => $totalThemes,
            'buttonText'    => $buttonText,
            'buttonUrl'     => $buttonUrl,
            'paddingTop'    => $paddingTop,
            'paddingBottom' => $paddingBottom,
        ])->render();
    }
}
