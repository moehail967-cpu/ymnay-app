<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Xgenious\PageBuilder\Core\WidgetRegistry;

/**
 * Wraps the package's WidgetController to filter widgets by enable().
 * Registered in CustomPageBuilderServiceProvider before the package api.php
 * so Laravel's first-match routing uses these handlers instead.
 *
 * Vendor patch tracking: see docs/page-builder-vendor-patches.md
 */
class FilteredWidgetController extends Controller
{
    /**
     * Determine whether a widget instance should be visible in the current context.
     *
     * Priority:
     *   1. If the widget defines enable(), use its return value.
     *   2. If the widget's class is in a "landlord" namespace (no enable() defined),
     *      only show it when there is no active tenant.
     *   3. Everything else is always visible.
     */
    private function isEnabled(object $instance): bool
    {
        if (method_exists($instance, 'enable')) {
            return $instance->enable();
        }

        $class = get_class($instance);
        if (str_contains($class, '\\landlord\\') || str_contains($class, '\\Landlord\\')) {
            return is_null(tenant());
        }

        return true;
    }

    private function filterWidgets(array $widgets): array
    {
        return array_values(array_filter($widgets, function ($widgetData) {
            $instance = WidgetRegistry::getWidget($widgetData['type'] ?? '');
            return $instance ? $this->isEnabled($instance) : true;
        }));
    }

    private function filterWidgetMap(array $widgetMap): array
    {
        return array_filter($widgetMap, function ($widgetData) {
            $instance = WidgetRegistry::getWidget($widgetData['config']['type'] ?? '');
            return $instance ? $this->isEnabled($instance) : true;
        });
    }

    public function index()
    {
        $widgets = WidgetRegistry::getWidgetsForApi();

        return response()->json([
            'success' => true,
            'data'    => $this->filterWidgets($widgets),
        ]);
    }

    public function grouped()
    {
        $allWidgets = $this->filterWidgetMap(WidgetRegistry::getAllWidgets());
        $grouped    = [];

        foreach ($allWidgets as $widgetType => $widgetData) {
            $category = $widgetData['config']['category'] ?? 'other';

            if (!isset($grouped[$category])) {
                $grouped[$category] = [
                    'category' => [
                        'key'         => $category,
                        'name'        => ucfirst($category),
                        'description' => '',
                    ],
                    'widgets' => [],
                ];
            }

            $grouped[$category]['widgets'][$widgetType] = $widgetData;
        }

        return response()->json([
            'success' => true,
            'data'    => $grouped,
        ]);
    }

    public function search(Request $request)
    {
        $query      = strtolower($request->input('q', ''));
        $allWidgets = $this->filterWidgetMap(WidgetRegistry::getAllWidgets());
        $results    = [];

        if (empty($query)) {
            return response()->json(['success' => true, 'data' => $allWidgets]);
        }

        foreach ($allWidgets as $widgetType => $widgetData) {
            $config         = $widgetData['config'];
            $searchableText = strtolower(
                ($config['name'] ?? '') . ' ' .
                ($config['description'] ?? '') . ' ' .
                implode(' ', $config['tags'] ?? [])
            );

            if (str_contains($searchableText, $query)) {
                $results[$widgetType] = $widgetData;
            }
        }

        return response()->json(['success' => true, 'data' => $results]);
    }
}
