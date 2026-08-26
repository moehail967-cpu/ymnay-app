<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Pricing;

use App\Models\PricePlan;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PricingDetailOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'pricing_detail_one';
    }

    protected function getWidgetName(): string
    {
        return 'Pricing Detail One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-table';
    }

    protected function getWidgetDescription(): string
    {
        return 'Detailed pricing comparison table with features across plans';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['pricing', 'comparison', 'table', 'features'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Detailed Comparison')
            )
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('Compare all features across our plans and enjoy the various service')
            )
            ->endGroup();

        $control->addGroup('plans', 'Plans Settings')
            ->registerField('items_count', FieldManager::NUMBER()
                ->setLabel('Number of Plans to Show')
                ->setDefault(3)
                ->setMin(1)
                ->setMax(10)
            )
            ->registerField('order_by', FieldManager::SELECT()
                ->setLabel('Order By')
                ->setOptions([
                    'id' => 'ID',
                    'created_at' => 'Date',
                    'price' => 'Price',
                ])
                ->setDefault('id')
            )
            ->registerField('order', FieldManager::SELECT()
                ->setLabel('Order')
                ->setOptions([
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                ])
                ->setDefault('asc')
            )
            ->endGroup();

        $control->addGroup('custom_features', 'Custom Feature Rows')
            ->registerField('feature_rows', FieldManager::REPEATER()
                ->setLabel('Custom Features')
                ->setItemLabel('Feature Row')
                ->setAddButtonText('Add Feature Row')
                ->setMin(0)
                ->setMax(20)
                ->setFields([
                    'feature_name' => FieldManager::TEXT()
                        ->setLabel('Feature Name')
                        ->setDefault('Feature'),
                    'values' => FieldManager::TEXT()
                        ->setLabel('Values (comma separated, one per plan)')
                        ->setDescription('e.g: Basic, Full POS + offline sync, Full + white-label POS')
                        ->setDefault(''),
                    'display_type' => FieldManager::SELECT()
                        ->setLabel('Display Type')
                        ->setOptions([
                            'text' => 'Text',
                            'check' => 'Check/Cross Icons',
                        ])
                        ->setDefault('text'),
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('section_style', 'Section')
            ->registerField('header_bg', FieldManager::COLOR()
                ->setLabel('Table Header Background')
                ->setDefault('#115e59')
            )
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(0)
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
        $style = $settings['style'] ?? [];

        // Heading
        $headingGroup = $general['heading'] ?? [];
        $title = $headingGroup['title'] ?? 'Detailed Comparison';
        $subtitle = $headingGroup['subtitle'] ?? 'Compare all features across our plans and enjoy the various service';

        // Style
        $styleGroup = $style['section_style'] ?? [];
        $headerBg = $styleGroup['header_bg'] ?? '#115e59';

        // Plans
        $plansGroup = $general['plans'] ?? [];
        $itemsCount = (int) ($plansGroup['items_count'] ?? 3);
        $orderBy = $plansGroup['order_by'] ?? 'id';
        $order = $plansGroup['order'] ?? 'asc';

        $allowedOrderBy = ['id', 'created_at', 'price'];
        if (!in_array($orderBy, $allowedOrderBy)) {
            $orderBy = 'id';
        }

        try {
            $pricePlans = PricePlan::where('status', 1)
                ->orderBy($orderBy, $order === 'desc' ? 'desc' : 'asc')
                ->limit($itemsCount)
                ->get();
        } catch (\Exception $e) {
            return '';
        }

        if ($pricePlans->isEmpty()) {
            return '';
        }

        $periodLabels = [
            0 => '/month',
            1 => '/year',
            2 => '/lifetime',
        ];

        // Build plan names for table header
        $planNames = $pricePlans->pluck('title')->toArray();

        // Build DB feature rows
        $dbFeatureRows = [];

        // Price row
        $dbFeatureRows[] = [
            'name' => 'Price',
            'values' => $pricePlans->map(fn($p) => site_currency_symbol(). number_format((float) $p->price, 0) . ($periodLabels[$p->type] ?? ''))->toArray(),
            'type' => 'text',
        ];

        // Products
        $dbFeatureRows[] = [
            'name' => 'Products',
            'values' => $pricePlans->map(fn($p) => $p->product_permission_feature == -1 ? 'Unlimited' : ($p->product_permission_feature ?? '—'))->toArray(),
            'type' => 'text',
        ];

        // Pages
        $dbFeatureRows[] = [
            'name' => 'Pages',
            'values' => $pricePlans->map(fn($p) => $p->page_permission_feature == -1 ? 'Unlimited' : ($p->page_permission_feature ?? '—'))->toArray(),
            'type' => 'text',
        ];

        // Blog
        $dbFeatureRows[] = [
            'name' => 'Blog',
            'values' => $pricePlans->map(fn($p) => $p->blog_permission_feature == -1 ? 'Unlimited' : ($p->blog_permission_feature ?? '—'))->toArray(),
            'type' => 'text',
        ];

        // Storage
        $dbFeatureRows[] = [
            'name' => 'Storage',
            'values' => $pricePlans->map(fn($p) => $p->storage_permission_feature == -1 ? 'Unlimited' : (($p->storage_permission_feature ?? '—') . ($p->storage_permission_feature > 0 ? ' MB' : '')))->toArray(),
            'type' => 'text',
        ];

        // Free trial
        $dbFeatureRows[] = [
            'name' => 'Free trial',
            'values' => $pricePlans->map(fn($p) => !empty($p->has_trial) ? '✓' : '✕')->toArray(),
            'type' => 'check',
        ];

        // Custom feature rows from admin
        $customGroup = $general['custom_features'] ?? [];
        $customRows = $customGroup['feature_rows'] ?? [];
        $customFeatureRows = [];

        foreach ($customRows as $row) {
            $featureName = $row['feature_name'] ?? '';
            $valuesRaw = $row['values'] ?? '';
            $displayType = $row['display_type'] ?? 'text';

            $values = array_map('trim', explode(',', $valuesRaw));

            // Pad or trim to match plan count
            $planCount = $pricePlans->count();
            while (count($values) < $planCount) {
                $values[] = '—';
            }
            $values = array_slice($values, 0, $planCount);

            $customFeatureRows[] = [
                'name' => $featureName,
                'values' => $values,
                'type' => $displayType,
            ];
        }

        // Merge: DB rows first, then custom rows
        $allFeatureRows = array_merge($dbFeatureRows, $customFeatureRows);

        return view('widgetbuilder::landlord.pricing.pricing_detail', [
            'title' => $title,
            'subtitle' => $subtitle,
            'headerBg' => $headerBg,
            'planNames' => $planNames,
            'featureRows' => $allFeatureRows,
        ])->render();
    }
}
