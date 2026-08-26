{{-- FreshMart: Breadcrumb / Page Banner — hidden on homepage --}}
@if(request()->route()?->getName() === 'tenant.frontend.homepage')
@else
<div class="fm-page-banner">
    <div class="container">
        @if(!empty($title))<h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{{ $title }}</h1>@endif
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            @if(!empty($crumbs))
                @foreach($crumbs as $crumb)
                    <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
                    @if(!empty($crumb['url']))<a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                    @else<span class="current">{{ $crumb['label'] }}</span>@endif
                @endforeach
            @elseif(!empty($title))
                <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
                <span class="current">{{ $title }}</span>
            @endif
        </div>
    </div>
</div>
@endif
