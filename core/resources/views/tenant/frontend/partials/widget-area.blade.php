@php
    $widget_area_name = getFooterWidgetArea();
    $footer_view      = !empty($widget_area_name) ? 'theme::' . str_replace('/', '.', $widget_area_name) : null;
@endphp

@if($footer_view && View::exists($footer_view))
    @include($footer_view)
@else
    @include('tenant.frontend.partials.pages-portion.footers.footer-medicom')
@endif
