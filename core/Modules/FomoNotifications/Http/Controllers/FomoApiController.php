<?php

namespace Modules\FomoNotifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\FomoNotifications\Models\FomoSyntheticEntry;

class FomoApiController extends Controller
{
    private function plugin()
    {
        return app(\App\PluginSystem\PluginManager::class)->get('fomo-notifications');
    }

    public function entries(): JsonResponse
    {
        $plugin   = $this->plugin();
        $tenantId = function_exists('tenant') && tenant() ? (string) tenant('id') : null;

        if (!$plugin->get_option('enabled')) {
            return response()->json(['entries' => []]);
        }

        $source  = $plugin->get_option('data_source', 'mixed');
        $count   = max(1, (int) ($plugin->get_option('real_order_count', 10) ?: 10));
        $minReal = max(1, (int) ($plugin->get_option('min_real_orders', 3) ?: 3));

        $entries = match ($source) {
            'real'      => $this->realEntries($count, $plugin),
            'synthetic' => $this->syntheticEntries($tenantId),
            default     => $this->mixedEntries($count, $minReal, $tenantId, $plugin),
        };

        return response()->json(['entries' => $entries]);
    }

    private function realEntries(int $count, $plugin): array
    {
        $anonymise = (bool) $plugin->get_option('anonymise_names', '1');

        $orders = \DB::table('orders')
            ->where('status', 'complete')
            ->latest('created_at')
            ->limit($count)
            ->get();

        $entries = [];
        foreach ($orders as $order) {
            $item = \DB::table('order_products')
                ->where('order_id', $order->id)
                ->first();
            if (!$item) continue;

            $product = \DB::table('products')->where('id', $item->product_id)->first();
            $user    = $order->user_id ? \DB::table('users')->where('id', $order->user_id)->first() : null;

            $fullName = $user?->name ?? 'Customer';
            $parts    = explode(' ', trim($fullName));
            $first    = $parts[0] ?? 'Customer';
            $last     = $parts[1] ?? '';

            $name = $anonymise
                ? ($first . ($last ? ' ' . strtoupper(substr($last, 0, 1)) . '.' : ''))
                : $fullName;

            $location = trim(($user?->city ?? '') . ($user?->city && $user?->country ? ', ' : '') . ($user?->country ?? ''));
            $imgUrl   = $this->resolveProductImage($product);

            $minutesAgo = (int) max(1, now()->diffInMinutes(\Carbon\Carbon::parse($order->created_at)));

            $entries[] = [
                'name'          => $name,
                'location'      => $location,
                'product_name'  => $product?->name ?? '',
                'variant'       => null,
                'product_image' => $imgUrl,
                'minutes_ago'   => $minutesAgo,
            ];
        }

        return $entries;
    }

    private function syntheticEntries(?string $tenantId): array
    {
        $rows = FomoSyntheticEntry::where('active', true)
            ->orderBy('sort_order')
            ->get();

        $entries = [];
        foreach ($rows as $row) {
            $product = \DB::table('products')->where('id', $row->product_id)->first();
            $imgUrl  = $this->resolveProductImage($product);

            $entries[] = [
                'name'          => $row->display_name,
                'location'      => $row->location,
                'product_name'  => $product?->name ?? '',
                'variant'       => $row->variant_label,
                'product_image' => $imgUrl,
                'minutes_ago'   => rand(3, 120),
            ];
        }

        return $entries;
    }

    private function mixedEntries(int $count, int $minReal, ?string $tenantId, $plugin): array
    {
        $real = $this->realEntries($count, $plugin);
        if (count($real) >= $minReal) {
            return $real;
        }
        $synthetic = $this->syntheticEntries($tenantId);
        return array_merge($real, array_slice($synthetic, 0, $count - count($real)));
    }

    private function resolveProductImage(?object $product): ?string
    {
        if (!$product) return null;

        // media_uploaders table — used by the Nazmart attachment system
        if (!empty($product->image_id)) {
            try {
                $media = \DB::table('media_uploaders')->where('id', $product->image_id)->first();
                if ($media?->path) {
                    if (str_starts_with($media->path, 'http')) {
                        return $media->path;
                    }
                    $tenantId = (function_exists('tenant') && tenant()) ? tenant()->id . '/' : '';
                    return global_asset('assets/tenant/uploads/media-uploader/' . $tenantId . $media->path);
                }
            } catch (\Throwable) {}
        }

        // direct URL fallback columns
        foreach (['thumbnail', 'featured_image', 'image'] as $col) {
            $val = $product->$col ?? null;
            if ($val) {
                return str_starts_with($val, 'http') ? $val : asset($val);
            }
        }

        return null;
    }
}
