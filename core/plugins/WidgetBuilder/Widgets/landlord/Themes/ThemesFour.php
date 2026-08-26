<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Themes;

use Illuminate\Pagination\LengthAwarePaginator;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ThemesFour extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'themes_four';
    }

    protected function getWidgetName(): string
    {
        return 'Themes Four';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-palette';
    }

    protected function getWidgetDescription(): string
    {
        return 'Theme showcase grid section with hover overlay preview and pagination';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['themes', 'showcase', 'grid', 'pagination'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('themes', 'Themes')
            ->registerField('items_count', FieldManager::NUMBER()
                ->setLabel('Number of Themes Per Page')
                ->setDefault(9)
                ->setMin(1)
                ->setMax(24)
            )
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Preview')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('section_style', 'Section')
            ->registerField('section_bg', FieldManager::COLOR()
                ->setLabel('Section Background')
                ->setDefault('rgba(12,77,84,0.1)')
            )
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(80)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(80)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];

        // Themes
        $themesGroup = $general['themes'] ?? [];
        $itemsCount = (int) ($themesGroup['items_count'] ?? 9);
        $buttonText = $themesGroup['button_text'] ?? 'Preview';

        try {
            $allThemeData = getAllThemeData();
        } catch (\Exception $e) {
            return '';
        }

        if (empty($allThemeData)) {
            return '';
        }

        $themes = [];
        foreach ($allThemeData as $themeData) {
            $slug = $themeData->slug ?? '';
            if (empty($slug)) {
                continue;
            }

            $customImage = get_static_option_central($slug . '_theme_image');
            $image = !empty($customImage) ? $customImage : loadScreenshot($slug);

            $customName = get_static_option_central($slug . '_theme_name');
            $name = !empty($customName) ? $customName : ($themeData->name ?? '');

            $themeUrl = get_static_option_central($slug . '_theme_url');

            $themes[] = [
                'name' => $name,
                'image' => $image,
                'url' => !empty($themeUrl) ? $themeUrl : '#',
            ];
        }

        if (empty($themes)) {
            return '';
        }

        // Manual pagination for collection
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $collection = collect($themes);
        $currentItems = $collection->slice(($currentPage - 1) * $itemsCount, $itemsCount)->values();
        $themeRecords = new LengthAwarePaginator(
            $currentItems,
            $collection->count(),
            $itemsCount,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('widgetbuilder::landlord.themes.amazing_themes', [
            'themes' => $currentItems->all(),
            'themeRecords' => $themeRecords,
            'buttonText' => $buttonText,
        ])->render();
    }
}
