@extends(route_prefix().'admin.admin-master')

@section('title')
    {{ __('Site Analytics Dashboard') }}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/admin/css/components/analytics.css')}}">
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="analytics-page-head">
        <div class="page-info">
            <div class="page-icon">
                <i class="mdi mdi-chart-areaspline"></i>
            </div>
            <div>
                <h2 class="page-title">{{__('Analytics Dashboard')}}</h2>
                <p class="page-subtitle">{{__('Track visitor activity and traffic insights')}}</p>
            </div>
        </div>
        @include('siteanalytics::admin.data.filter', ['type' => 'dashboard'])
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @each('siteanalytics::admin.stats.card', $stats, 'stat')
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        {{-- Bar / Area Chart --}}
        <div class="analytics-chart">
            <div class="chart-head">
                <div class="icon"><i class="mdi mdi-chart-bar"></i></div>
                <div>
                    <h3>{{__("Page Views")}}</h3>
                    <p>{{ucwords(str_replace('_',' ', $period))}} {{__('overview')}}</p>
                </div>
            </div>
            <div class="chart-body">
                <div id="chart-total"></div>
            </div>
        </div>

        {{-- Donut Charts --}}
        <div class="analytics-chart">
            <div class="chart-head">
                <div class="icon"><i class="mdi mdi-map-marker-radius-outline"></i></div>
                <div>
                    <h3>{{__("Locations & Devices")}}</h3>
                    <p>{{__('User distribution breakdown')}}</p>
                </div>
            </div>
            <div class="chart-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-center mb-2" style="color: var(--color-text-muted);">{{__('By Country')}}</p>
                        <div id="chart-country"></div>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-center mb-2" style="color: var(--color-text-muted);">{{__('By Device')}}</p>
                        <div id="chart-device"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        @includeWhen(!empty(get_static_option('site_analytics_page_view')) ,'siteanalytics::admin.data.pages-card')
        @includeWhen(!empty(get_static_option('site_analytics_view_source')) ,'siteanalytics::admin.data.sources-card')
        @includeWhen(!empty(get_static_option('site_analytics_users_country')) ,'siteanalytics::admin.data.users-card')
        @includeWhen(!empty(get_static_option('site_analytics_users_device')) ,'siteanalytics::admin.data.devices-card')
    </div>

    {{-- UTM --}}
    @if(!empty($utm))
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
            @each('siteanalytics::admin.data.utm-card', $utm, 'data')
        </div>
    @endif

@endsection

@section('scripts')
    <script src="{{global_asset('assets/landlord/admin/js/apexcharts.js')}}"></script>

    @php
        $views = json_encode(array_column(current($pages_charts) ,'total_views'));
        $date = json_encode(array_column(current($pages_charts) ,'time'));

        $country = json_encode(array_column(current($users) ,'country'));
        $country_users = json_encode(array_column(current($users) ,'users'));

        $device = json_encode(array_column(current($devices) ,'type'));
        $device_users = json_encode(array_column(current($devices) ,'users'));
    @endphp
    <script>
        $(document).ready(function () {
            var c = { primary: '#1a5c4e', light: '#2d8a73', accent: '#4db89c', soft: '#e0f0f0', muted: '#6b7280', dark: '#111827' };
            var palette = [c.primary, c.light, c.accent, '#7ccfb8', '#a8e4d1', '#d1f2e8'];

            new ApexCharts(document.querySelector("#chart-total"), {
                series: [{ name: `{{__('Page Views')}}`, data: {{$views}} }],
                chart: { height: 380, type: 'bar', fontFamily: 'Urbanist, sans-serif', toolbar: { show: false }, background: 'transparent' },
                colors: [c.primary],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
                dataLabels: { enabled: true, offsetY: -20, style: { fontSize: '12px', fontWeight: 700, colors: [c.dark] } },
                grid: { borderColor: c.soft, strokeDashArray: 3, padding: { bottom: 0 } },
                xaxis: {
                    categories: <?php echo $date ?>,
                    axisBorder: { show: false }, axisTicks: { show: false },
                    labels: { style: { fontSize: '11px', colors: c.muted, fontWeight: 600 } }
                },
                yaxis: { labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false } },
                tooltip: { theme: 'light', y: { formatter: function(v) { return v + ' {{__("views")}}'; } }, style: { fontSize: '12px' } },
                states: { hover: { filter: { type: 'darken', value: 0.15 } } }
            }).render();

            new ApexCharts(document.querySelector("#chart-country"), {
                series: {{$country_users}},
                chart: { type: 'donut', fontFamily: 'Urbanist, sans-serif', height: 240 },
                colors: palette,
                labels: <?php echo $country ?>,
                plotOptions: { pie: { donut: { size: '60%', labels: { show: true, name: { fontSize: '12px', fontWeight: 700 }, value: { fontSize: '16px', fontWeight: 800, color: c.dark }, total: { show: true, label: '{{__("Total")}}', fontSize: '10px', color: c.muted } } } } },
                legend: { position: 'bottom', fontSize: '10px', fontWeight: 600, markers: { width: 7, height: 7, radius: 2 } },
                stroke: { width: 3, colors: ['#fff'] },
                dataLabels: { enabled: false },
                tooltip: { style: { fontSize: '11px' } },
                responsive: [{ breakpoint: 480, options: { chart: { height: 200 } } }]
            }).render();

            new ApexCharts(document.querySelector("#chart-device"), {
                series: {{$device_users}},
                chart: { type: 'donut', fontFamily: 'Urbanist, sans-serif', height: 240 },
                colors: [c.primary, c.accent, '#a8e4d1', '#d1f2e8'],
                labels: <?php echo $device ?>,
                plotOptions: { pie: { donut: { size: '60%', labels: { show: true, name: { fontSize: '12px', fontWeight: 700 }, value: { fontSize: '16px', fontWeight: 800, color: c.dark }, total: { show: true, label: '{{__("Total")}}', fontSize: '10px', color: c.muted } } } } },
                legend: { position: 'bottom', fontSize: '10px', fontWeight: 600, markers: { width: 7, height: 7, radius: 2 } },
                stroke: { width: 3, colors: ['#fff'] },
                dataLabels: { enabled: false },
                tooltip: { style: { fontSize: '11px' } },
                responsive: [{ breakpoint: 480, options: { chart: { height: 200 } } }]
            }).render();
        });
    </script>
@endsection
