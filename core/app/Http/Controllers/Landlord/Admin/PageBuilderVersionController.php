<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\PageBuilderVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Xgenious\PageBuilder\Models\PageBuilderContent;
use Xgenious\PageBuilder\Models\PageBuilderWidget;

class PageBuilderVersionController extends Controller
{
    /**
     * Return version history for a page as JSON.
     */
    public function index(int $pageId)
    {
        $versions = PageBuilderVersion::where('page_id', $pageId)
            ->orderByDesc('id')
            ->get(['id', 'version_label', 'is_pinned', 'created_by', 'created_at']);

        // Attach creator name
        $adminIds = $versions->pluck('created_by')->filter()->unique()->values();
        $admins   = Admin::whereIn('id', $adminIds)->pluck('name', 'id');

        $data = $versions->map(fn($v) => [
            'id'            => $v->id,
            'version_label' => $v->version_label,
            'is_pinned'     => $v->is_pinned,
            'created_by'    => $admins[$v->created_by] ?? 'System',
            'created_at'    => $v->created_at->format('M d, Y — h:i A'),
            'time_ago'      => $v->created_at->diffForHumans(),
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Restore a page to a previous version snapshot.
     */
    public function restore(Request $request, int $versionId)
    {
        $version = PageBuilderVersion::findOrFail($versionId);

        DB::beginTransaction();
        try {
            $adminId = Auth::guard('admin')->id();

            // Restore layout content
            PageBuilderContent::updateOrCreate(
                ['page_id' => $version->page_id],
                [
                    'content'    => $version->content ?? ['containers' => []],
                    'updated_by' => $adminId,
                ]
            );

            // Remove all current widgets for this page
            PageBuilderWidget::where('page_id', $version->page_id)->delete();

            // Re-insert widgets from snapshot
            foreach ($version->widgets_data ?? [] as $w) {
                PageBuilderWidget::create([
                    'page_id'             => $version->page_id,
                    'widget_id'           => $w['widget_id'],
                    'widget_type'         => $w['widget_type'],
                    'container_id'        => $w['container_id'] ?? null,
                    'column_id'           => $w['column_id'] ?? null,
                    'sort_order'          => $w['sort_order'] ?? 0,
                    'is_visible'          => $w['is_visible'] ?? true,
                    'is_enabled'          => $w['is_enabled'] ?? true,
                    'general_settings'    => $w['general_settings'] ?? null,
                    'style_settings'      => $w['style_settings'] ?? null,
                    'advanced_settings'   => $w['advanced_settings'] ?? null,
                    'responsive_settings' => $w['responsive_settings'] ?? null,
                    'version'             => $w['version'] ?? '1.0.0',
                    'created_by'          => $adminId,
                    'updated_by'          => $adminId,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Page restored to selected version successfully.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a single (non-pinned) version.
     */
    public function destroy(int $versionId)
    {
        $version = PageBuilderVersion::findOrFail($versionId);

        if ($version->is_pinned) {
            return response()->json([
                'success' => false,
                'message' => __('The original version cannot be deleted.'),
            ], 422);
        }

        $version->delete();

        return response()->json(['success' => true, 'message' => __('Version deleted.')]);
    }
}
