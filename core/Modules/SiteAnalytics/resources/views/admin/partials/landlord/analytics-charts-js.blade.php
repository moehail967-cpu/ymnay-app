@php
    $plans = \App\Models\PricePlan::where('status', 1)->select(['id', 'title'])->pluck('title', 'id');

    $pages_array = $pages->toArray();
    $plan_pages = array_map(function ($item) {
        $item['page'] = str_replace(['/plan-order/', '/view-plan/','/trial'],'',$item['page']);
        return $item;
    }, $pages_array);

    $plan_with_names = [];
    foreach ($plan_pages ?? [] as $key => $item)
    {
        $plan_with_names[$key]['users'] = $item['users'];
        $plan_with_names[$key]['name'] = current($plans)[$item['page']] ?? '';
    }

    $views = json_encode(array_column($plan_with_names ,'users'));
    $name = json_encode(array_column($plan_with_names ,'name'));

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
            series: [{ name: `{{__('Plan Views')}}`, data: {{$views}} }],
            chart: { height: 380, type: 'bar', fontFamily: 'Urbanist, sans-serif', toolbar: { show: false }, background: 'transparent' },
            colors: [c.primary],
            plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
            dataLabels: { enabled: true, offsetY: -20, style: { fontSize: '12px', fontWeight: 700, colors: [c.dark] } },
            grid: { borderColor: c.soft, strokeDashArray: 3, padding: { bottom: 0 } },
            xaxis: {
                categories: <?php echo $name ?>,
                axisBorder: { show: false }, axisTicks: { show: false },
                labels: { style: { fontSize: '11px', colors: c.muted, fontWeight: 600 } }
            },
            yaxis: { labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false } },
            tooltip: {
                theme: 'light',
                y: { formatter: function(v) { return v + ' {{__("views")}}'; } },
                style: { fontSize: '12px' }
            },
            states: { hover: { filter: { type: 'darken', value: 0.15 } } }
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
