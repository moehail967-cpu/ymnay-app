<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Pricing;

use App\Models\PricePlan;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PricingOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'pricing_one';
    }

    protected function getWidgetName(): string
    {
        return 'Pricing One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-tags';
    }

    protected function getWidgetDescription(): string
    {
        return 'Pricing plans section with monthly/yearly toggle, feature lists and CTA buttons';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['pricing', 'plans', 'subscription', 'payment'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Affordable and competitive pricing')
            )
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Choose the plan that\'s right for your business.')
            )
            ->registerField('decorative_image', FieldManager::IMAGE()
                ->setLabel('Card Decorative Image')
                ->setDescription('Decorative dots/pattern image on pricing cards')
            )
            ->endGroup();

        $control->addGroup('plans', 'Pricing Plans')
            ->registerField('items_count', FieldManager::NUMBER()
                ->setLabel('Number of Plans to Show')
                ->setDefault(3)
                ->setMin(1)
            )
            ->registerField('order_by', FieldManager::SELECT()
                ->setLabel('Order By')
                ->setOptions([
                    'id'         => 'ID',
                    'created_at' => 'Date',
                    'price'      => 'Price',
                ])
                ->setDefault('id')
            )
            ->registerField('order', FieldManager::SELECT()
                ->setLabel('Order')
                ->setOptions([
                    'asc'  => 'Ascending',
                    'desc' => 'Descending',
                ])
                ->setDefault('asc')
            )
            ->registerField('btn_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Buy Now')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        // ── Section ────────────────────────────────────────────────────────────
        $control->addGroup('section_style', 'Section')
            ->registerField('section_bg', FieldManager::COLOR()
                ->setLabel('Section Background')
                ->setDescription('Defaults to theme Background One (white)')
                ->setDefault('#ffffff')
            )
            ->registerField('section_title_color', FieldManager::COLOR()
                ->setLabel('Section Title Color')
                ->setDescription('Defaults to theme Heading Color')
                ->setDefault('')
            )
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(144)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(144)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->endGroup();

        // ── Card — Normal State ────────────────────────────────────────────────
        // Note: Card bg uses var(--section-bg-1), hover/featured bg uses
        // var(--main-color-one) — both follow global Color Settings automatically.
        $control->addGroup('card_normal_style', 'Card — Normal State')
            ->registerField('card_border_color', FieldManager::COLOR()
                ->setLabel('Border Color')
                ->setDescription('Defaults to theme Extra Light Color')
                ->setDefault('#e5e7eb')
            )
            ->registerField('card_heading_color', FieldManager::COLOR()
                ->setLabel('Title / Price Color')
                ->setDescription('Defaults to theme Heading Color')
                ->setDefault('#1f2937')
            )
            ->registerField('card_body_color', FieldManager::COLOR()
                ->setLabel('Subtitle / Period / Feature Text')
                ->setDescription('Defaults to theme Body Color')
                ->setDefault('#666666')
            )
            ->registerField('card_divider_color', FieldManager::COLOR()
                ->setLabel('Divider Color')
                ->setDescription('Defaults to theme Extra Light Color')
                ->setDefault('#e5e7eb')
            )
            ->registerField('feature_icon_color', FieldManager::COLOR()
                ->setLabel('Feature Check Icon Color')
                ->setDescription('Defaults to theme Heading Color')
                ->setDefault('#1f2937')
            )
            ->endGroup();

        // ── Card — Hover / Featured State ─────────────────────────────────────
        // Background automatically uses var(--main-color-one) from Color Settings.
        $control->addGroup('card_hover_style', 'Card — Hover / Featured State')
            ->registerField('hover_title_color', FieldManager::COLOR()
                ->setLabel('Title Color')
                ->setDefault('#ffffff')
            )
            ->registerField('hover_price_color', FieldManager::COLOR()
                ->setLabel('Price Color')
                ->setDefault('#ffffff')
            )
            ->registerField('hover_body_color', FieldManager::COLOR()
                ->setLabel('Subtitle / Period Color')
                ->setDefault('#ffffff')
            )
            ->registerField('hover_divider_color', FieldManager::COLOR()
                ->setLabel('Divider Color')
                ->setDefault('#ffffff40')
            )
            ->registerField('hover_feature_text_color', FieldManager::COLOR()
                ->setLabel('Feature Text Color')
                ->setDefault('#ffffff')
            )
            ->registerField('hover_feature_icon_color', FieldManager::COLOR()
                ->setLabel('Feature Icon Color')
                ->setDefault('#ffffff')
            )
            ->endGroup();

        // ── Popular Badge ──────────────────────────────────────────────────────
        // Normal bg uses var(--main-color-two) from Color Settings.
        $control->addGroup('badge_style', 'Popular Badge')
            ->registerField('badge_text_color', FieldManager::COLOR()
                ->setLabel('Text Color (Normal)')
                ->setDescription('Defaults to theme Heading Color')
                ->setDefault('#1f2937')
            )
            ->registerField('badge_hover_bg', FieldManager::COLOR()
                ->setLabel('Background (on Hover / Featured)')
                ->setDescription('Defaults to theme Main Color Two')
                ->setDefault('#84cc16')
            )
            ->registerField('badge_hover_text_color', FieldManager::COLOR()
                ->setLabel('Text Color (on Hover / Featured)')
                ->setDefault('#1f2937')
            )
            ->endGroup();

        // ── Button — Normal ────────────────────────────────────────────────────
        $control->addGroup('btn_normal_style', 'Button — Normal')
            ->registerField('btn_bg', FieldManager::COLOR()
                ->setLabel('Background')
                ->setDefault('transparent')
            )
            ->registerField('btn_text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#1f2937')
            )
            ->registerField('btn_border_color', FieldManager::COLOR()
                ->setLabel('Border Color')
                ->setDefault('#D1D5D9')
            )
            ->endGroup();

        // ── Button — Hover ─────────────────────────────────────────────────────
        $control->addGroup('btn_hover_style', 'Button — Hover')
            ->registerField('btn_hover_bg', FieldManager::COLOR()
                ->setLabel('Background')
                ->setDefault('#1f2937')
            )
            ->registerField('btn_hover_text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#ffffff')
            )
            ->registerField('btn_hover_border_color', FieldManager::COLOR()
                ->setLabel('Border Color')
                ->setDefault('transparent')
            )
            ->endGroup();

        // ── Button on Card Hover / Featured ────────────────────────────────────
        $control->addGroup('card_hover_btn_style', 'Button — On Card Hover / Featured')
            ->registerField('card_hover_btn_bg', FieldManager::COLOR()
                ->setLabel('Background')
                ->setDefault('#ffffff')
            )
            ->registerField('card_hover_btn_text', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#1f2937')
            )
            ->registerField('card_hover_btn_border', FieldManager::COLOR()
                ->setLabel('Border Color')
                ->setDefault('transparent')
            )
            ->endGroup();

        return $control->getFields();
    }

    private function resolveImageUrl($value): string
    {
        if (is_array($value)) {
            return $value['url'] ?? '';
        }
        return is_string($value) ? $value : '';
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];

        // Heading
        $headingGroup   = $general['heading'] ?? [];
        $badgeText      = $headingGroup['badge_text'] ?? 'Affordable and competitive pricing';
        $title          = $headingGroup['title'] ?? 'Choose the plan that\'s right for your business.';
        $decorativeImage = $this->resolveImageUrl($headingGroup['decorative_image'] ?? '');

        // Plans
        $plansGroup = $general['plans'] ?? [];
        $itemsCount = (int) ($plansGroup['items_count'] ?? 3);
        $orderBy    = $plansGroup['order_by'] ?? 'id';
        $order      = $plansGroup['order'] ?? 'asc';
        $btnText    = $plansGroup['btn_text'] ?? 'Buy Now';

        $allowedOrderBy = ['id', 'created_at', 'price'];
        if (!in_array($orderBy, $allowedOrderBy)) {
            $orderBy = 'id';
        }

        try {
            $pricePlans = PricePlan::where('status', 1)
                ->orderBy($orderBy, $order === 'desc' ? 'desc' : 'asc')
                ->get()
                ->groupBy('type')
                ->map(fn($group) => $group->take($itemsCount));
        } catch (\Exception $e) {
            return '';
        }

        if ($pricePlans->flatten()->isEmpty()) {
            return '';
        }

        $periodLabels = [0 => '/month', 1 => '/year', 2 => '/lifetime'];
        $typeLabels   = [0 => 'Monthly', 1 => 'Annual', 2 => 'Lifetime'];

        $plansByType = [];
        foreach ($pricePlans as $type => $groupPlans) {
            $type = (int) $type;
            foreach ($groupPlans as $plan) {
                $featureMap = [
                    'product_permission_feature' => 'Products',
                    'page_permission_feature'    => 'Pages',
                    'blog_permission_feature'    => 'Blog',
                    'storage_permission_feature' => 'Storage (MB)',
                ];

                $features = [];
                foreach ($featureMap as $column => $label) {
                    $value = $plan->{$column};
                    if (is_null($value)) continue;
                    $features[] = ($value == -1 ? 'Unlimited' : $value) . ' ' . $label;
                }

                try { $btnUrl   = route('landlord.frontend.plan.order', $plan->id); } catch (\Exception $e) { $btnUrl = '#'; }
                try { $trialUrl = route('landlord.frontend.plan.view', [$plan->id, 'trial']); } catch (\Exception $e) { $trialUrl = '#'; }

                $plansByType[$type][] = [
                    'plan_name'     => $plan->title ?? '',
                    'subtitle'      => $plan->package_description ?? '',
                    'price'         => site_currency_symbol() . number_format((float) $plan->price, 0),
                    'period'        => $periodLabels[$type] ?? '/month',
                    'is_popular'    => !empty($plan->package_badge),
                    'popular_text'  => $plan->package_badge ?? 'Most Popular',
                    'features'      => $features,
                    'btn_text'      => $btnText,
                    'btn_url'       => $btnUrl,
                    'has_free_trial'=> $plan->has_trial === 1,
                    'trial_url'     => $trialUrl,
                ];
            }
        }

        ksort($plansByType);
        $availableTypes = [];
        foreach ($plansByType as $type => $typePlans) {
            $availableTypes[] = ['key' => $type, 'label' => $typeLabels[$type] ?? 'Type ' . $type];
        }

        // ── Style ──────────────────────────────────────────────────────────────
        $style = $settings['style'] ?? [];

        $sg                = $style['section_style'] ?? [];
        $sectionBg         = $sg['section_bg'] ?? '';
        $sectionTitleColor = $sg['section_title_color'] ?? '';
        $paddingTop        = (int) ($sg['padding_top'] ?? 144);
        $paddingBottom     = (int) ($sg['padding_bottom'] ?? 144);

        $cn                = $style['card_normal_style'] ?? [];
        $cardBorderColor   = $cn['card_border_color'] ?? '';
        $cardHeadingColor  = $cn['card_heading_color'] ?? '';
        $cardBodyColor     = $cn['card_body_color'] ?? '';
        $cardDividerColor  = $cn['card_divider_color'] ?? '';
        $featureIconColor  = $cn['feature_icon_color'] ?? '';

        $ch                     = $style['card_hover_style'] ?? [];
        $hoverTitleColor        = $ch['hover_title_color'] ?? '#ffffff';
        $hoverPriceColor        = $ch['hover_price_color'] ?? '#ffffff';
        $hoverBodyColor         = $ch['hover_body_color'] ?? '#ffffff';
        $hoverDividerColor      = $ch['hover_divider_color'] ?? '#ffffff40';
        $hoverFeatureTextColor  = $ch['hover_feature_text_color'] ?? '#ffffff';
        $hoverFeatureIconColor  = $ch['hover_feature_icon_color'] ?? '#ffffff';

        $bg               = $style['badge_style'] ?? [];
        $badgeTextColor   = $bg['badge_text_color'] ?? '';
        $badgeHoverBg     = $bg['badge_hover_bg'] ?? '';
        $badgeHoverText   = $bg['badge_hover_text_color'] ?? '';

        $bn               = $style['btn_normal_style'] ?? [];
        $btnBg            = $bn['btn_bg'] ?? 'transparent';
        $btnTextColor     = $bn['btn_text_color'] ?? '';
        $btnBorderColor   = $bn['btn_border_color'] ?? '#D1D5D9';

        $bh                  = $style['btn_hover_style'] ?? [];
        $btnHoverBg          = $bh['btn_hover_bg'] ?? '';
        $btnHoverTextColor   = $bh['btn_hover_text_color'] ?? '#ffffff';
        $btnHoverBorderColor = $bh['btn_hover_border_color'] ?? 'transparent';

        $cbh                = $style['card_hover_btn_style'] ?? [];
        $cardHoverBtnBg     = $cbh['card_hover_btn_bg'] ?? '#ffffff';
        $cardHoverBtnText   = $cbh['card_hover_btn_text'] ?? '';
        $cardHoverBtnBorder = $cbh['card_hover_btn_border'] ?? 'transparent';

        $widgetId = 'pricing-one-' . substr(md5(uniqid('', true)), 0, 8);

        return view('widgetbuilder::landlord.pricing.pricing', compact(
            'badgeText', 'title', 'decorativeImage', 'availableTypes', 'plansByType',
            'widgetId',
            'sectionBg', 'sectionTitleColor',
            'paddingTop', 'paddingBottom',
            'cardBorderColor', 'cardHeadingColor', 'cardBodyColor', 'cardDividerColor', 'featureIconColor',
            'hoverTitleColor', 'hoverPriceColor', 'hoverBodyColor', 'hoverDividerColor',
            'hoverFeatureTextColor', 'hoverFeatureIconColor',
            'badgeTextColor', 'badgeHoverBg', 'badgeHoverText',
            'btnBg', 'btnTextColor', 'btnBorderColor',
            'btnHoverBg', 'btnHoverTextColor', 'btnHoverBorderColor',
            'cardHoverBtnBg', 'cardHoverBtnText', 'cardHoverBtnBorder'
        ))->render();
    }
}
