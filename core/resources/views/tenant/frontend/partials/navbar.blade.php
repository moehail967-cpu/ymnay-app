@php
    $navbar_area_name = getHeaderNavbarArea();
    $navbar_view      = !empty($navbar_area_name) ? 'theme::' . str_replace('/', '.', $navbar_area_name) : null;
@endphp

@if($navbar_view && View::exists($navbar_view))
    @include($navbar_view)
@else
    @include('tenant.frontend.partials.pages-portion.navbars.navbar-01')
@endif
