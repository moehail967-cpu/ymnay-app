<?php

namespace App\Http\Middleware;

use App\Models\PageBuilderVersion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Xgenious\PageBuilder\Models\PageBuilderContent;
use Xgenious\PageBuilder\Models\PageBuilderWidget;

class PageBuilderSnapshotMiddleware
{
    /**
     * Snapshot throttle window in seconds (1 snapshot per page per window).
     * Prevents flooding the versions table when many widget saves fire in quick succession.
     */
    private const THROTTLE_SECONDS = 300; // 5 minutes

    /**
     * Intercept any page-builder save call and snapshot the current state.
     *
     * Watched endpoints (all POST):
     *   POST api/page-builder/save                                     (full layout save)
     *   POST api/page-builder/pages/{n}/widgets/{id}/save-all-settings (individual widget save)
     *   POST api/page-builder/pages/{n}/widgets/{id}/save-general-settings
     *   POST api/page-builder/pages/{n}/widgets/{id}/save-style-settings
     *   POST api/page-builder/pages/{n}/widgets/{id}/save-advanced-settings
     *   POST api/page-builder/pages/{n}/sections/{id}/save-all-settings
     *   POST api/page-builder/pages/{n}/columns/{id}/save-all-settings
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->isMethod('POST')) {
            return $next($request);
        }

        $pageId = $this->resolvePageId($request);

        if ($pageId && $this->isSavePath($request)) {
            $existingContent = PageBuilderContent::where('page_id', $pageId)->first();

            if ($existingContent) {
                if ($this->shouldSnapshot($pageId)) {
                    $this->doSnapshot($pageId, $existingContent);
                    $this->markThrottled($pageId);
                }
                return $next($request);
            }

            // First-ever save: snapshot the new state after the vendor creates it
            $response = $next($request);
            $this->snapshotAfterFirstSave($pageId);
            return $response;
        }

        return $next($request);
    }

    private function isSavePath(Request $request): bool
    {
        return $request->is('api/page-builder/save')
            || $request->is('api/page-builder/pages/*/widgets/*/save-all-settings')
            || $request->is('api/page-builder/pages/*/widgets/*/save-general-settings')
            || $request->is('api/page-builder/pages/*/widgets/*/save-style-settings')
            || $request->is('api/page-builder/pages/*/widgets/*/save-advanced-settings')
            || $request->is('api/page-builder/pages/*/sections/*/save-all-settings')
            || $request->is('api/page-builder/pages/*/columns/*/save-all-settings');
    }

    private function resolvePageId(Request $request): ?int
    {
        // Full layout save sends page_id in body
        if ($id = $request->input('page_id')) {
            return (int) $id;
        }

        // Widget/section/column saves encode page_id in the URL: /pages/{pageId}/widgets/...
        if (preg_match('#/pages/(\d+)/#', $request->path(), $m)) {
            return (int) $m[1];
        }

        return null;
    }

    private function shouldSnapshot(int $pageId): bool
    {
        return ! Cache::has($this->throttleKey($pageId));
    }

    private function markThrottled(int $pageId): void
    {
        Cache::put($this->throttleKey($pageId), true, self::THROTTLE_SECONDS);
    }

    private function throttleKey(int $pageId): string
    {
        return 'pb_snapshot_throttle_' . $pageId;
    }

    private function doSnapshot(int $pageId, PageBuilderContent $content): void
    {
        $widgets = $this->collectWidgets($pageId);
        $this->pruneIfNeeded($pageId);

        $isFirst = PageBuilderVersion::where('page_id', $pageId)->count() === 0;

        PageBuilderVersion::create([
            'page_id'       => $pageId,
            'content'       => $content->content,
            'widgets_data'  => $widgets,
            'version_label' => $isFirst ? 'Original' : 'Auto-save',
            'is_pinned'     => $isFirst,
            'created_by'    => Auth::guard('admin')->id(),
        ]);
    }

    private function snapshotAfterFirstSave(int $pageId): void
    {
        $content = PageBuilderContent::where('page_id', $pageId)->first();
        if (! $content) return;

        if (PageBuilderVersion::where('page_id', $pageId)->exists()) return;

        PageBuilderVersion::create([
            'page_id'       => $pageId,
            'content'       => $content->content,
            'widgets_data'  => $this->collectWidgets($pageId),
            'version_label' => 'Original',
            'is_pinned'     => true,
            'created_by'    => Auth::guard('admin')->id(),
        ]);

        $this->markThrottled($pageId);
    }

    private function collectWidgets(int $pageId): array
    {
        return PageBuilderWidget::where('page_id', $pageId)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($w) => [
                'widget_id'           => $w->widget_id,
                'widget_type'         => $w->widget_type,
                'container_id'        => $w->container_id,
                'column_id'           => $w->column_id,
                'sort_order'          => $w->sort_order,
                'is_visible'          => $w->is_visible,
                'is_enabled'          => $w->is_enabled,
                'general_settings'    => $w->general_settings,
                'style_settings'      => $w->style_settings,
                'advanced_settings'   => $w->advanced_settings,
                'responsive_settings' => $w->responsive_settings,
                'version'             => $w->version,
            ])
            ->values()
            ->toArray();
    }

    private function pruneIfNeeded(int $pageId): void
    {
        $count = PageBuilderVersion::where('page_id', $pageId)
            ->where('is_pinned', false)
            ->count();

        if ($count >= 30) {
            PageBuilderVersion::where('page_id', $pageId)
                ->where('is_pinned', false)
                ->oldest()
                ->limit(1)
                ->delete();
        }
    }
}
