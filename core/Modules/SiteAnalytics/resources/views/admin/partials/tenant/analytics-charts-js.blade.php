@php
    $product_array = $pages->toArray();
    $order_array = $orders->toArray();

    $views = json_encode(array_column($product_array, 'total_views'));
    $sale = json_encode(array_column($order_array, 'total_sale'));

    $tempProductArr = $pages->pluck("total_views", "time");
    $tempOrderArr = $orders->pluck("total_sale", "time");
    $finalArray = [];

    $index = 0;
    foreach($tempProductArr as $key => $value){
        $finalArray[$index] = 0;
        if($tempOrderArr[$key] ?? false) {
            $calculated = (1 - $tempOrderArr[$key] / $value) * 100;
            $finalArray[$index++] = (float) number_format($calculated, 2);
        }
    }

    $bounce_rates = json_encode($finalArray);

    $months = count($product_array) > count($order_array) ? array_column($product_array, 'time') : array_column($order_array, 'time');
    $months = $months ? \Modules\SiteAnalytics\Http\Services\SiteAnalyticsService::sortElements($months) : [];
    $months = json_encode($months);

    $country = json_encode(array_column(current($users) ,'country'));
    $country_users = json_encode(array_column(current($users) ,'users'));

    $device = json_encode(array_column(current($devices) ,'type'));
    $device_users = json_encode(array_column(current($devices) ,'users'));
@endphp

<script>
    $(document).ready(function () {
        var c = { primary: '#1a5c4e', hover: '#103d34', light: '#2d8a73', accent: '#4db89c', soft: '#e0f0f0', muted: '#6b7280', dark: '#111827' };
        var palette = [c.primary, c.light, c.accent, '#7ccfb8', '#a8e4d1', '#d1f2e8'];

        new ApexCharts(document.querySelector("#chart-total"), {
            series: [
                { name: `{{__('Views')}}`, data: {{$views}} },
                { name: `{{__('Sales')}}`, data: {{$sale}} },
                { name: `{{__('Bounce Rate (%)')}}`, data: {{$bounce_rates}} }
            ],
            chart: { height: 380, type: 'area', fontFamily: 'Urbanist, sans-serif', toolbar: { show: false }, background: 'transparent' },
            colors: [c.primary, c.light, c.accent],
            stroke: { curve: 'smooth', width: 2.5 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.02, stops: [0, 95, 100] } },
            dataLabels: { enabled: false },
            grid: { borderColor: c.soft, strokeDashArray: 3, padding: { bottom: 0 } },
            xaxis: {
                type: 'string',
                categories: <?php echo $months ?>,
                axisBorder: { show: false }, axisTicks: { show: false },
                labels: { style: { fontSize: '11px', colors: c.muted, fontWeight: 600 } }
            },
            yaxis: { labels: { style: { fontSize: '11px', colors: c.muted } } },
            legend: { position: 'top', horizontalAlign: 'left', fontSize: '12px', fontWeight: 600, markers: { width: 8, height: 8, radius: 3 } },
            tooltip: { theme: 'light', style: { fontSize: '12px' }, x: { show: true } },
            states: { hover: { filter: { type: 'lighten', value: 0.05 } } }
        }).render();

        new ApexCharts(document.querySelector("#chart-country"), {
            series: {{$country_users}},
            chart: { type: 'donut', fontFamily: 'Urbanist, sans-serif', height: 260 },
            colors: palette,
            labels: <?php echo $country ?>,
            plotOptions: {
                pie: {
                    donut: { size: '60%', labels: { show: true, name: { fontSize: '13px', fontWeight: 700 }, value: { fontSize: '18px', fontWeight: 800, color: c.dark }, total: { show: true, label: '{{__("Total")}}', fontSize: '11px', color: c.muted } } }
                }
            },
            legend: { position: 'bottom', fontSize: '11px', fontWeight: 600, markers: { width: 8, height: 8, radius: 3 } },
            stroke: { width: 3, colors: ['#fff'] },
            dataLabels: { enabled: false },
            tooltip: { style: { fontSize: '12px' } },
            responsive: [{ breakpoint: 480, options: { chart: { height: 220 } } }]
        }).render();

        new ApexCharts(document.querySelector("#chart-device"), {
            series: {{$device_users}},
            chart: { type: 'donut', fontFamily: 'Urbanist, sans-serif', height: 260 },
            colors: [c.primary, c.accent, '#a8e4d1', '#d1f2e8'],
            labels: <?php echo $device ?>,
            plotOptions: {
                pie: {
                    donut: { size: '60%', labels: { show: true, name: { fontSize: '13px', fontWeight: 700 }, value: { fontSize: '18px', fontWeight: 800, color: c.dark }, total: { show: true, label: '{{__("Total")}}', fontSize: '11px', color: c.muted } } }
                }
            },
            legend: { position: 'bottom', fontSize: '11px', fontWeight: 600, markers: { width: 8, height: 8, radius: 3 } },
            stroke: { width: 3, colors: ['#fff'] },
            dataLabels: { enabled: false },
            tooltip: { style: { fontSize: '12px' } },
            responsive: [{ breakpoint: 480, options: { chart: { height: 220 } } }]
        }).render();
    });
</script>
