@extends('tenant.admin.admin-master')
@section('title')
    {{__('Sales Dashboard')}}
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- S21 T7: Revenue Intelligence Widgets --}}
@php
    $rev_widgets = [
        ['label' => __('Today'), 'value' => $revenue_today, 'prev' => $revenue_prev_day, 'color' => '#FC4F00', 'icon' => 'mdi-calendar-today'],
        ['label' => __('This Week'), 'value' => $revenue_week, 'prev' => $revenue_prev_wk, 'color' => '#0079FF', 'icon' => 'mdi-calendar-week'],
        ['label' => __('This Month'), 'value' => $revenue_month, 'prev' => $revenue_prev_mo, 'color' => '#22A699', 'icon' => 'mdi-calendar-month'],
    ];
@endphp
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
    @foreach($rev_widgets as $w)
    @php
        $change = $w['prev'] > 0 ? round((($w['value'] - $w['prev']) / $w['prev']) * 100, 1) : ($w['value'] > 0 ? 100 : 0);
    @endphp
    <div class="bg-surface rounded-xl shadow-main border border-main p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:{{ $w['color'] }}1a">
                <i class="mdi {{ $w['icon'] }} text-base" style="color:{{ $w['color'] }}"></i>
            </div>
            <span class="text-xs text-muted font-medium">{{ $w['label'] }}</span>
        </div>
        <div class="text-xl font-bold text-dark font-urbanist mb-1">{{amount_with_currency_symbol($w['value'])}}</div>
        <div class="text-xs {{ $change >= 0 ? 'text-green-600' : 'text-red-500' }}">
            <i class="mdi {{ $change >= 0 ? 'mdi-trending-up' : 'mdi-trending-down' }}"></i>
            {{ $change >= 0 ? '+' : '' }}{{ $change }}% {{__('vs last period')}}
        </div>
    </div>
    @endforeach
</div>

{{-- S21 T8: Top Products Widget --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="lg:col-span-2">
        {{-- Placeholder: top products column grows --}}
    </div>
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
        <div class="px-4 py-3 border-b border-main flex items-center gap-2">
            <i class="mdi mdi-trophy-outline text-[#FC4F00] text-base"></i>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Top 5 Products')}}</h3>
        </div>
        <div class="divide-y divide-main">
            @foreach($top_products as $i => $tp)
            <div class="px-4 py-2.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-xs font-bold text-muted w-4">{{ $i+1 }}</span>
                    <span class="text-xs text-dark font-medium truncate">{{ $tp['name'] }}</span>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-xs font-bold text-dark">{{amount_with_currency_symbol($tp['revenue'])}}</div>
                    <div class="text-[10px] text-muted">{{ $tp['qty'] }} {{__('units')}}</div>
                </div>
            </div>
            @endforeach
            @if(empty($top_products))
            <div class="px-4 py-6 text-center text-xs text-muted">{{__('No sales data yet')}}</div>
            @endif
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(252,79,0,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-cart-outline text-xl text-[#FC4F00]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Number of Sales')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{$total_report['total_sale']}}</h3>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(0,121,255,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-cash-multiple text-xl text-[#0079FF]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Total Revenue')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{amount_with_currency_symbol($total_report['total_revenue'])}}</h3>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(34,166,153,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-trending-up text-xl text-[#22A699]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Total Profit')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{amount_with_currency_symbol($total_report['total_profit'])}}</h3>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(143,67,238,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-currency-usd text-xl text-[#8F43EE]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Total Cost')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{amount_with_currency_symbol($total_report['total_cost'])}}</h3>
        </div>
    </div>
</div>

{{-- Charts Row 1: Daily & Weekly --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[rgba(252,79,0,0.1)] flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-chart-line text-[#FC4F00] text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Today\'s Report')}}</h3>
                <p class="text-xs text-muted">{{__('Revenue, cost and profit')}}</p>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            <div id="chart-daily"></div>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[rgba(0,121,255,0.1)] flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-chart-line-variant text-[#0079FF] text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Weekly Report')}}</h3>
                <p class="text-xs text-muted">{{__('Current week breakdown')}}</p>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            <div id="chart-weekly"></div>
        </div>
    </div>
</div>

{{-- Charts Row 2: Monthly & Yearly --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[rgba(143,67,238,0.1)] flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-chart-areaspline text-[#8F43EE] text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Monthly Report')}}</h3>
                <p class="text-xs text-muted">{{__('Month-by-month overview')}}</p>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            <div id="chart-monthly"></div>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-[rgba(34,166,153,0.1)] flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-chart-bar text-[#22A699] text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Yearly Report')}}</h3>
                <p class="text-xs text-muted">{{__('Year-over-year comparison')}}</p>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            <div id="chart-yearly"></div>
        </div>
    </div>
</div>

{{-- Product Sales Table --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-table-large text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Product Sales')}}</h3>
            <p class="text-xs text-muted">{{__('Detailed product sales breakdown')}}</p>
        </div>
    </div>

    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Type')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Product')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Qty')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Cost')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Price')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Profit')}}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products['items'] ?? [] as $product)
                @foreach($product ?? [] as $item)
                    <tr class="border-b border-main hover:bg-muted transition-colors">
                        <td class="px-4 py-3.5"><span class="text-[11px] font-bold text-primary">{{__('#')}} {{$item['product_id']}}</span></td>
                        <td class="px-4 py-3.5"><span class="text-xs text-muted">{{$item['sale_date']->format('d M Y')}}</span></td>
                        <td class="px-4 py-3.5">
                            <span class="tw-pill tw-pill-info">{{\App\Enums\ProductTypeEnum::getText($item['product_type'])}}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-semibold text-dark">{{$item['name']}}</span>
                            @if(!empty($item['variant']))
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @if(!empty($item['variant']['color']))
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-secondary text-[10px] text-muted">{{$item['variant']['color']}}</span>
                                    @endif
                                    @if(!empty($item['variant']['size']))
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-secondary text-[10px] text-muted">{{$item['variant']['size']}}</span>
                                    @endif
                                    @foreach($item['variant']['attributes'] as $attr_name => $attr_val)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-secondary text-[10px] text-muted">{{$attr_name}}: {{$attr_val}}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{$item['qty']}}</span></td>
                        <td class="px-4 py-3.5"><span class="text-sm text-muted">{{amount_with_currency_symbol($item['cost'])}}</span></td>
                        <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{amount_with_currency_symbol($item['price'])}}</span></td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-bold {{ $item['profit'] >= 0 ? 'text-success' : 'text-danger' }}">{{amount_with_currency_symbol($item['profit'])}}</span>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td class="px-4 py-8 text-center text-sm text-muted" colspan="8">{{__('No Data Available')}}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if(!empty($products['links']) && count($products['links']) > 2)
        <div class="px-4 sm:px-6 py-4 border-t border-main flex items-center justify-center gap-1">
            @foreach($products["links"] as $link)
                @php if($loop->iteration == 1) continue; @endphp
                <a href="{{ $link }}"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-semibold transition {{ ($loop->iteration - 1) == $products["current_page"] ? 'bg-primary text-white' : 'bg-secondary border border-main text-dark hover:bg-primary-soft hover:text-primary' }}">
                    {{ $loop->iteration - 1 }}
                </a>
            @endforeach
        </div>
    @endif
</div>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/landlord/admin/js/apexcharts.js')}}"></script>

    @php
        $today = $today_report;
        $weekly = $weekly_report;
        $monthly = $monthly_report;
        $yearly = $yearly_report;
    @endphp

    <script>
        $(document).ready(function () {
            const chartByToday = () => {
                return {
                    series: [
                        {
                            name: '{{__('Total Sale')}}',
                            data: {{json_encode($today['salesData'])}}
                        },
                        {
                            name: '{{__('Total Revenue')}}',
                            data: {{json_encode($today['revenueData'])}}
                        },
                        {
                            name: '{{__('Total Cost')}}',
                            data: {{json_encode($today['costData'])}}
                        },
                        {
                            name: '{{__('Total Profit')}}',
                            data: {{json_encode($today['profitData'])}}
                        },
                    ],
                    chart: {
                        height: 350,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#ff5252', '#0079FF', '#8F43EE', '#22A699'],
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    xaxis: {
                        categories: <?php echo json_encode($today['categories']) ?>,
                        title: {
                            text: '{{__('Time')}}'
                        }
                    },
                    yaxis: {
                        title: {
                            text: '{{__('Amount')}}'
                        },
                        min: 0,
                        max: {{$today['max_value']}}
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center'
                    }
                };
            }
            const chartByWeekly = () => {
                return {
                    series: [
                        {
                            name: '{{__('Total Sale')}}',
                            data: {{json_encode($weekly['salesData'])}}
                        },
                        {
                            name: '{{__('Total Revenue')}}',
                            data: {{json_encode($weekly['revenueData'])}}
                        },
                        {
                            name: '{{__('Total Cost')}}',
                            data: {{json_encode($weekly['costData'])}}
                        },
                        {
                            name: '{{__('Total Profit')}}',
                            data: {{json_encode($weekly['profitData'])}}
                        },
                    ],
                    chart: {
                        height: 350,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#ff5252', '#0079FF', '#8F43EE', '#22A699'],
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    xaxis: {
                        categories: <?php echo json_encode($weekly['categories']) ?>,
                        title: {
                            text: '{{__('Days')}}'
                        }
                    },
                    yaxis: {
                        title: {
                            text: '{{__('Amount')}}'
                        },
                        min: 0,
                        max: {{$weekly['max_value']}}
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center'
                    }
                };
            }
            const chartByMonth = () => {
                return {
                    series: [
                        {
                            name: '{{__('Total Revenue')}}',
                            data: {{json_encode($monthly['revenueData'])}}
                        },
                        {
                            name: '{{__('Total Cost')}}',
                            data: {{json_encode($monthly['costData'])}}
                        },
                        {
                            name: '{{__('Total Profit')}}',
                            data: {{json_encode($monthly['profitData'])}}
                        },
                    ],
                    chart: {
                        height: 500,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#0079FF', '#8F43EE', '#22A699'],
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    xaxis: {
                        categories: <?php echo json_encode($monthly['categories']) ?>,
                        title: {
                            text: '{{__('Month')}}'
                        }
                    },
                    yaxis: {
                        title: {
                            text: '{{__('Amount')}}'
                        },
                        min: 0,
                        max: {{$monthly['max_value']}}
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center'
                    }
                };
            }
            const chartByYear = () => {
                return {
                    series: [
                        {
                            name: '{{__('Total Revenue')}}',
                            data: {{json_encode($yearly['revenueData'])}}
                        },
                        {
                            name: '{{__('Total Cost')}}',
                            data: {{json_encode($yearly['costData'])}}
                        },
                        {
                            name: '{{__('Total Profit')}}',
                            data: {{json_encode($yearly['profitData'])}}
                        },
                    ],
                    chart: {
                        height: 500,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#0079FF', '#8F43EE', '#22A699'],
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    xaxis: {
                        categories: <?php echo json_encode($yearly['categories']) ?>,
                        title: {
                            text: '{{__('Year')}}'
                        }
                    },
                    yaxis: {
                        title: {
                            text: '{{__('Amount')}}'
                        },
                        min: 0,
                        max: {{$yearly['max_value']}}
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center'
                    }
                };
            }

            new ApexCharts(document.querySelector("#chart-daily"), chartByToday()).render();
            new ApexCharts(document.querySelector("#chart-weekly"), chartByWeekly()).render();
            new ApexCharts(document.querySelector("#chart-monthly"), chartByMonth()).render();
            new ApexCharts(document.querySelector("#chart-yearly"), chartByYear()).render();
        });
    </script>
@endsection
