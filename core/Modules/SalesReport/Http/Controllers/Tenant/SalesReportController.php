<?php

namespace Modules\SalesReport\Http\Controllers\Tenant;

use App\Helpers\FlashMsg;
use App\Http\Services\CustomPaginationService;
use App\Models\OrderProducts;
use App\Models\ProductOrder;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Modules\SalesReport\Http\Services\SalesReport;
use phpDocumentor\Reflection\Types\This;

class SalesReportController extends Controller
{
    public array $daysOfWeek = [
        "sunday" => "Sunday",
        "monday" => "Monday",
        "tuesday" => "Tuesday",
        "wednesday" => "Wednesday",
        "thursday" => "Thursday",
        "friday" => "Friday",
        "saturday" => "Saturday"
    ];

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        // S21 T7/T8: revenue intelligence widgets
        $revenue_today    = $this->periodRevenue('today');
        $revenue_week     = $this->periodRevenue('week');
        $revenue_month    = $this->periodRevenue('month');
        $revenue_prev_day = $this->periodRevenue('yesterday');
        $revenue_prev_wk  = $this->periodRevenue('prev_week');
        $revenue_prev_mo  = $this->periodRevenue('prev_month');

        $top_products = $this->topProductsByRevenue(5);

        $orders = ProductOrder::completed()->orderBy('id','desc')->get();

        $orders_today = ProductOrder::completed()->whereDate('updated_at', today())->orderBy('updated_at','asc')->get()
            ->groupBy(function ($query){
                // 'D = day name'
                // 'h = hour number'
                // 'A = AM/PM'
                return Carbon::parse($query->updated_at)->format('D h A');
            });

        $first_workday = get_static_option('first_workday') ?? 'sunday';
        $orders_weekly = $this->getWeeklyReport($first_workday);

        $orders_months = ProductOrder::completed()->orderBy('updated_at','asc')->get()
            ->groupBy(function ($query){
            // 'm' if month number is need, eg 05
            // 'M' if month name is needed, eg may
            return Carbon::parse($query->updated_at)->format('M Y');
        });

        $orders_years = ProductOrder::completed()->orderBy('id','desc')->get()
            ->groupBy(function ($query){
            // 'y' if year last two number is need, eg 23
            // 'Y' if full year number is needed, eg 2023
            return Carbon::parse($query->updated_at)->format('Y');
        });

        $reports = SalesReport::reports($orders);
        $total_report = [
            'total_sale' => $reports['total_sale'],
            'total_profit' => $reports['total_profit'],
            'total_revenue' => $reports['total_revenue'],
            'total_cost' => $reports['total_cost'],
            'products' => $reports['products']
        ];

        $today_report = $this->prepareDataForChart($orders_today);
        $weekly_report = $this->prepareDataForChart($orders_weekly);
        $monthly_report = $this->prepareDataForChart($orders_months);
        $yearly_report = $this->prepareDataForChart($orders_years);

        $display_item_count = request()->count ?? 10;
        $current_query = request()->all();
        $create_query = http_build_query($current_query);
        $route = 'tenant.admin';

        $products = $this->pagination_type($total_report['products'], $display_item_count, 'custom', route($route . ".sales.dashboard") . '?' . $create_query);
        return view('salesreport::tenant.admin.index', compact(
            'total_report', 'today_report', 'weekly_report', 'monthly_report', 'yearly_report', 'products',
            'revenue_today', 'revenue_week', 'revenue_month',
            'revenue_prev_day', 'revenue_prev_wk', 'revenue_prev_mo',
            'top_products'
        ));
    }

    private function prepareDataForChart($orders_months)
    {
        $data = SalesReport::reportByMonthsOrYears($orders_months);

        $categories = [];
        $salesData = [];
        $profitData = [];
        $revenueData = [];
        $costData = [];

        foreach ($data ?? [] as $month => $values) {
            $categories[] = $month;
            $salesData[] = $values['total_sale'];
            $profitData[] = $values['total_profit'];
            $revenueData[] = $values['total_revenue'];
            $costData[] = $values['total_cost'];
        }

        if (!empty($profitData) && !empty($revenueData) && !empty($costData))
        {
            $max_value = max(array_merge($profitData, $revenueData, $costData));
        } else {
            $max_value = 0;
        }

        return [
            'categories' => $categories,
            'salesData' => $salesData,
            'profitData' => $profitData,
            'revenueData' => $revenueData,
            'costData' => $costData,
            'max_value' => $max_value
        ];
    }

    public function paginate($items, $perPage = 50, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function pagination_type($all_products, $count, $type = "custom", $route=null){
        $display_item_count = $count ?? 10;
        $all_products = $this->paginate($all_products, $display_item_count);

        if(!empty($route)){
            $all_products->withPath($route);
        }


        if($type == "custom"){
            $current_items = (($all_products->currentPage() - 1) * $display_item_count);
            return [
                "items" => $all_products->items(),
                "current_page" => $all_products->currentPage(),
                "total_items" => $all_products->total(),
                "total_page" => $all_products->lastPage(),
                "next_page" => $all_products->nextPageUrl(),
                "previous_page" => $all_products->previousPageUrl(),
                "last_page" => $all_products->lastPage(),
                "per_page" => $all_products->perPage(),
                "path" => $all_products->path(),
                "current_list" => $all_products->count(),
                "from" => $all_products->count() ? $current_items + 1 : 0,
                "to" => $current_items + $all_products->count(),
                "on_first_page" => $all_products->onFirstPage(),
                "hasMorePages" => $all_products->hasMorePages(),
                "links" => $all_products->getUrlRange(0,$all_products->lastPage())
            ];
        }else{
            return $all_products;
        }
    }

    public function getWeeklyReport($dayOfWeek)
    {
        $dayOfWeek = ucfirst(strtolower($dayOfWeek));

        // Get the current date and time
        $now = Carbon::now();

        // Find the last occurrence of the day
        $startDate = $now->copy();
        while($startDate->format('l') !== $dayOfWeek) {
            $startDate->subDay();
        }

        // Set the end date as 7 days from the start date
        $endDate = $startDate->copy()->addDays(7);

        return ProductOrder::whereBetween('updated_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('updated_at', 'asc')->get()
            ->groupBy(function ($query){
                // 'D' if day name if needed, eg Sun
                return Carbon::parse($query->updated_at)->format('D');
            });
    }

    public function weekly_report()
    {

    }

    public function dynamic_report()
    {
        $current_url = \request()->url();
        $url_array = explode('/', $current_url);

        $orders = ProductOrder::completed();

        $era = last($url_array);
        if ($era == 'weekly')
        {
            $workDay = get_static_option('first_workday') ?? 'sunday';
            $workDay = $this->getCarbonDays($workDay);

            $weekStartDate = Carbon::now()->startOfWeek($workDay);
            $weekEndDate = Carbon::now()->endOfWeek();

            $orders->whereBetween('updated_at', [$weekStartDate, $weekEndDate]);
        }
        elseif ($era == 'monthly')
        {
            $orders->whereMonth('updated_at', now()->month);
        }
        elseif ($era == 'yearly')
        {
            $orders->whereYear('updated_at', now()->year);
        } else {
            return to_route('tenant.admin.sales.dashboard');
        }

        $orders = $orders->orderBy('updated_at','desc')->get();
        $page_title = $era ?? 'all';

        $reports = SalesReport::reports($orders);
        $total_report = [
            'total_sale' => $reports['total_sale'],
            'total_profit' => $reports['total_profit'],
            'total_revenue' => $reports['total_revenue'],
            'total_cost' => $reports['total_cost'],
            'products' => $reports['products']
        ];

        $display_item_count = request()->count ?? 20;
        $current_query = request()->all();
        $create_query = http_build_query($current_query);
        $route = 'tenant.admin';

        $products = $this->pagination_type($total_report['products'], $display_item_count, 'custom', route($route . ".sales.report.".$page_title) . '?' . $create_query);

        return view('salesreport::tenant.admin.monthly_report', compact('total_report','products', 'page_title'));
    }

    private function getCarbonDays($day_name)
    {
        switch (strtoupper($day_name)) {
            case 'SUNDAY':
                return Carbon::SUNDAY;
            case 'MONDAY':
                return Carbon::MONDAY;
            case 'TUESDAY':
                return Carbon::TUESDAY;
            case 'WEDNESDAY':
                return Carbon::WEDNESDAY;
            case 'THURSDAY':
                return Carbon::THURSDAY;
            case 'FRIDAY':
                return Carbon::FRIDAY;
            case 'SATURDAY':
                return Carbon::SATURDAY;
            default:
                throw new Exception('Invalid day name');
        }
    }

    public function campaignPerformance()
    {
        $rows = SalesReport::campaignReport();
        return view('salesreport::tenant.admin.campaign_performance', compact('rows'));
    }

    // S21 T1
    public function deadStock()
    {
        $days = (int) (request()->days ?? 30);
        $rows = SalesReport::deadStockReport($days);
        return view('salesreport::tenant.admin.dead_stock', compact('rows', 'days'));
    }

    // S21 T2
    public function inventoryTurnover()
    {
        $rows = SalesReport::turnoverReport();
        return view('salesreport::tenant.admin.inventory_turnover', compact('rows'));
    }

    // S21 T4
    public function variantPerformance()
    {
        $orders = \App\Models\ProductOrder::completed()->get();
        $rows   = SalesReport::variantReport($orders);
        return view('salesreport::tenant.admin.variant_performance', compact('rows'));
    }

    // S21 T10
    public function customerLtv()
    {
        $rows = SalesReport::customerLifetimeValue();
        return view('salesreport::tenant.admin.customer_ltv', compact('rows'));
    }

    // S21 T9: CSV export
    public function export(string $type)
    {
        $allowed = ['sales', 'campaign', 'inventory', 'dead-stock', 'variant'];
        abort_if(!in_array($type, $allowed), 404);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $type . '-report-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($type) {
            $out = fopen('php://output', 'w');

            if ($type === 'sales') {
                fputcsv($out, ['Order #', 'Product', 'Qty', 'Price', 'Cost', 'Profit', 'Date']);
                $orders = \App\Models\ProductOrder::completed()->get();
                $data   = SalesReport::reports($orders);
                foreach ($data['products'] as $order_products) {
                    foreach ($order_products as $p) {
                        fputcsv($out, [$p['product_id'], $p['name'], $p['qty'], $p['price'], $p['cost'], $p['profit'], $p['sale_date']]);
                    }
                }
            } elseif ($type === 'campaign') {
                fputcsv($out, ['ID', 'Campaign', 'Status', 'Start', 'End', 'Units', 'Campaign Revenue', 'Regular Revenue', 'Discount Given']);
                foreach (SalesReport::campaignReport() as $r) {
                    fputcsv($out, [$r['id'], $r['name'], $r['status'], $r['start_date'], $r['end_date'], $r['total_units'], $r['campaign_rev'], $r['regular_rev'], $r['discount_given']]);
                }
            } elseif ($type === 'inventory') {
                fputcsv($out, ['Product', 'Stock', 'Sold', 'Turnover', 'Speed']);
                foreach (SalesReport::turnoverReport() as $r) {
                    fputcsv($out, [$r['name'], $r['stock'], $r['sold'], $r['turnover'], $r['speed']]);
                }
            } elseif ($type === 'dead-stock') {
                fputcsv($out, ['Product', 'Stock', 'Total Sold', 'Days Stagnant']);
                foreach (SalesReport::deadStockReport() as $r) {
                    fputcsv($out, [$r['name'], $r['stock'], $r['sold_total'], $r['days_stagnant']]);
                }
            } elseif ($type === 'variant') {
                fputcsv($out, ['Variant', 'Units Sold', 'Revenue']);
                $orders = \App\Models\ProductOrder::completed()->get();
                foreach (SalesReport::variantReport($orders) as $r) {
                    fputcsv($out, [$r['label'], $r['qty'], $r['revenue']]);
                }
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function periodRevenue(string $period): float
    {
        $q = ProductOrder::completed();
        switch ($period) {
            case 'today':     $q->whereDate('updated_at', today()); break;
            case 'yesterday': $q->whereDate('updated_at', today()->subDay()); break;
            case 'week':      $q->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]); break;
            case 'prev_week': $q->whereBetween('updated_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]); break;
            case 'month':     $q->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year); break;
            case 'prev_month':$q->whereMonth('updated_at', now()->subMonth()->month)->whereYear('updated_at', now()->subMonth()->year); break;
        }
        return (float) $q->get()->sum(fn($o) => json_decode($o->payment_meta)->subtotal ?? 0);
    }

    private function topProductsByRevenue(int $limit = 5): array
    {
        $orders = ProductOrder::completed()->get();
        $products = [];
        foreach ($orders as $order) {
            $items = json_decode($order->order_details, true) ?? [];
            foreach ($items as $item) {
                $id = $item['id'] ?? 0;
                if (!$id) continue;
                if (!isset($products[$id])) {
                    $products[$id] = ['name' => $item['name'] ?? '—', 'qty' => 0, 'revenue' => 0];
                }
                $products[$id]['qty']     += $item['qty'] ?? 0;
                $products[$id]['revenue'] += ($item['price'] ?? 0) * ($item['qty'] ?? 0);
            }
        }
        usort($products, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
        return array_slice($products, 0, $limit);
    }

    public function settings()
    {
        return view('salesreport::tenant.admin.settings', ['daysOfWeek' => $this->daysOfWeek]);
    }

    public function settings_update(Request $request)
    {
        $request->validate([
            'first_workday' => 'required|min:6|max:9'
        ]);
        abort_if(!array_key_exists($request->first_workday, $this->daysOfWeek), 404);

        update_static_option('first_workday', trim($request->first_workday));

        return back()->with(FlashMsg::settings_update(__('First Day of The Week is Updated')));
    }
}
