<?php

namespace Modules\SalesReport\Http\Services;

use App\Enums\ProductTypeEnum;
use App\Models\ProductOrder;
use Carbon\Carbon;
use Modules\Campaign\Entities\Campaign;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductInventory;

class SalesReport
{
    public static function reports($orders): array
    {
        $total_sale    = 0;
        $total_revenue = 0;
        $total_profit  = 0;
        $total_cost    = 0;
        $products      = [];

        foreach ($orders ?? [] as $key => $order) {
            $order_details = json_decode($order->order_details);

            $index = 0;
            foreach ($order_details ?? [] as $item) {
                $product_cost  = ($item->options->base_cost ?? 0) * $item->qty;
                $product_price = $item->price * $item->qty;

                $total_sale   += $item->qty;
                $total_cost   += $product_cost;
                $total_profit += ($product_price - $product_cost);

                // T12: use JSON attributes if available, fall back to legacy color/size fields
                $attrs = $item->options->attributes ?? [];
                if (empty($attrs)) {
                    $attrs = array_filter([
                        'color' => $item->options->color_name ?? null,
                        'size'  => $item->options->size_name  ?? null,
                    ]);
                }

                $products[$key][$index++] = [
                    'product_id'   => $item->id,
                    'product_type' => $item->options->type ?? ProductTypeEnum::PHYSICAL,
                    'name'         => $item->name,
                    'qty'          => $item->qty,
                    'cost'         => $product_cost,
                    'price'        => $product_price,
                    'profit'       => ($product_price - $product_cost),
                    'sale_date'    => $order->updated_at,
                    'variant'      => [
                        'color'      => $item->options->color_name ?? '',
                        'size'       => $item->options->size_name  ?? '',
                        'attributes' => $attrs,
                    ],
                    'campaign_name' => $item->options->campaign_name ?? null,
                ];
            }

            $total_revenue += json_decode($order->payment_meta)->subtotal ?? 0;
        }

        return [
            'total_sale'    => $total_sale,
            'total_revenue' => $total_revenue,
            'total_cost'    => $total_cost,
            'total_profit'  => $total_profit,
            'products'      => $products,
        ];
    }

    public static function reportByMonthsOrYears($orders_months): array
    {
        $monthly_reports = [];

        foreach ($orders_months ?? [] as $month => $orders) {
            $reports = self::reports($orders);
            $monthly_reports[$month] = [
                'total_sale'    => $reports['total_sale'],
                'total_profit'  => $reports['total_profit'],
                'total_revenue' => $reports['total_revenue'],
                'total_cost'    => $reports['total_cost'],
                'products'      => $reports['products'],
            ];
        }

        return $monthly_reports;
    }

    // T11: campaign breakdown — per campaign revenue, discount given, and profit vs regular price
    public static function campaignReport(): array
    {
        $campaigns = Campaign::with(['products'])->get();
        $rows = [];

        foreach ($campaigns as $campaign) {
            $total_units  = $campaign->products->sum('sold_count');
            $regular_rev  = 0;
            $campaign_rev = 0;

            foreach ($campaign->products as $cp) {
                $units         = (int) ($cp->sold_count ?? 0);
                $regular_price = $cp->product?->sale_price ?? $cp->product?->price ?? 0;
                $regular_rev  += $regular_price * $units;
                $campaign_rev += ($cp->campaign_price ?? 0) * $units;
            }

            $discount_given = max(0, $regular_rev - $campaign_rev);

            $rows[] = [
                'id'              => $campaign->id,
                'name'            => $campaign->title,
                'status'          => $campaign->status,
                'start_date'      => $campaign->start_date,
                'end_date'        => $campaign->end_date,
                'total_units'     => $total_units,
                'campaign_rev'    => $campaign_rev,
                'regular_rev'     => $regular_rev,
                'discount_given'  => $discount_given,
            ];
        }

        return $rows;
    }

    // T12: variant performance — best/worst selling combinations
    public static function variantReport($orders): array
    {
        $variants = [];

        foreach ($orders ?? [] as $order) {
            $items = json_decode($order->order_details);
            foreach ($items ?? [] as $item) {
                $attrs = $item->options->attributes ?? null;
                $color = $item->options->color_name ?? '';
                $size  = $item->options->size_name  ?? '';
                $label = $color || $size ? trim($color . ' / ' . $size, ' /') : 'No Variant';

                if (!isset($variants[$label])) {
                    $variants[$label] = ['label' => $label, 'qty' => 0, 'revenue' => 0];
                }
                $variants[$label]['qty']     += $item->qty;
                $variants[$label]['revenue'] += $item->price * $item->qty;
            }
        }

        usort($variants, fn($a, $b) => $b['qty'] - $a['qty']);

        return $variants;
    }

    // S21 T1: dead stock — products with no sales in the last N days
    public static function deadStockReport(int $days = 30): array
    {
        $cutoff = Carbon::now()->subDays($days);

        // Product IDs that had a sale in the period
        $active_ids = ProductOrder::completed()
            ->where('updated_at', '>=', $cutoff)
            ->get()
            ->flatMap(function ($order) {
                $items = json_decode($order->order_details, true) ?? [];
                return array_column($items, 'id');
            })
            ->unique()
            ->values()
            ->toArray();

        $dead_stock = Product::with('inventory')
            ->where('status_id', 1)
            ->whereNotIn('id', $active_ids)
            ->whereHas('inventory', fn($q) => $q->where('stock_count', '>', 0))
            ->get()
            ->map(function ($product) use ($days) {
                return [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'stock'       => $product->inventory?->stock_count ?? 0,
                    'sold_total'  => $product->inventory?->sold_count ?? 0,
                    'days_stagnant' => $days,
                ];
            })
            ->sortByDesc('stock')
            ->values()
            ->toArray();

        return $dead_stock;
    }

    // S21 T2: inventory turnover — units sold ÷ avg stock per product
    public static function turnoverReport(): array
    {
        $inventories = ProductInventory::with('product')->get();

        return $inventories->map(function ($inv) {
            $sold  = $inv->sold_count ?? 0;
            $stock = max(1, $inv->stock_count ?? 1);
            $turnover = round($sold / $stock, 2);

            return [
                'product_id'   => $inv->product_id,
                'name'         => $inv->product?->name ?? '—',
                'stock'        => $inv->stock_count ?? 0,
                'sold'         => $sold,
                'turnover'     => $turnover,
                'speed'        => $turnover >= 2 ? 'fast' : ($turnover >= 0.5 ? 'medium' : 'slow'),
            ];
        })
        ->sortByDesc('turnover')
        ->values()
        ->toArray();
    }

    // S21 T10: customer lifetime value
    public static function customerLifetimeValue(): array
    {
        $orders = ProductOrder::completed()
            ->whereNotNull('user_id')
            ->select('user_id', 'id', 'updated_at')
            ->get();

        $customers = [];
        foreach ($orders as $order) {
            $uid = $order->user_id;
            $payment = json_decode(ProductOrder::find($order->id)?->payment_meta ?? '{}');
            $subtotal = $payment->subtotal ?? 0;

            if (!isset($customers[$uid])) {
                $customers[$uid] = ['user_id' => $uid, 'order_count' => 0, 'total_revenue' => 0, 'last_order' => null];
            }
            $customers[$uid]['order_count']++;
            $customers[$uid]['total_revenue'] += $subtotal;
            $customers[$uid]['last_order'] = $order->updated_at;
        }

        foreach ($customers as &$c) {
            $c['avg_order_value'] = $c['order_count'] > 0
                ? round($c['total_revenue'] / $c['order_count'], 2) : 0;
        }

        usort($customers, fn($a, $b) => $b['total_revenue'] <=> $a['total_revenue']);

        return $customers;
    }
}
