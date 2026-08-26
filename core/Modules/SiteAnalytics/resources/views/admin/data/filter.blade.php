@php
    if (isset($type)) {
        if ($type == 'dashboard') {
            $route = route(route_prefix().'admin.dashboard.analytics');
        } elseif ($type == 'analytics') {
            $route = route(route_prefix().'admin.analytics');
        }
    }
@endphp

<div class="analytics-filter">
    <button type="button" class="filter-btn js-analytics-filter-toggle">
        <i class="mdi mdi-calendar-clock"></i>
        {{ $periods[$period] }}
        <i class="mdi mdi-chevron-down text-sm"></i>
    </button>
    <div class="filter-menu js-analytics-filter-menu">
        @foreach ($periods as $key => $value)
            <a href="{{ $route }}?period={{ $key }}">{{ $value }}</a>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.js-analytics-filter-toggle');
        var menus = document.querySelectorAll('.js-analytics-filter-menu');
        if (btn) {
            btn.nextElementSibling.classList.toggle('show');
        } else {
            menus.forEach(function(m) { m.classList.remove('show'); });
        }
    });
</script>
