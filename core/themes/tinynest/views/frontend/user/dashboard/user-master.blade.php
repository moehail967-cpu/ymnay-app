@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('style')
<x-media-upload.css/>
<style></style>
@endsection

@section('content')

<div class="tn-page-banner">
    <div class="container">
        <h1>{{ __('My Account') }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="tn-dash-section">
    <div class="container">

        <button class="tn-dash-mobile-toggle" id="tn-dash-toggle">
            <i class="mdi mdi-menu"></i> {{ __('Account Menu') }}
        </button>

        <div class="tn-dash-wrap">

            {{-- Sidebar --}}
            <div class="tn-dash-sidebar" id="tn-dash-sidebar">
                @php $u = auth('web')->user(); @endphp
                <div class="tn-dash-user">
                    <div class="tn-dash-avatar">{{ strtoupper(substr($u?->name ?? 'U', 0, 1)) }}</div>
                    <div class="tn-dash-user-info">
                        <div class="tn-dash-name">{{ $u?->name }}</div>
                        <div class="tn-dash-email">{{ $u?->email }}</div>
                    </div>
                </div>
                <ul class="tn-dash-nav">
                    <li class="{{ request()->routeIs('tenant.user.home') ? 'active' : '' }}">
                        <a href="{{ theme_user_dashboard_url() }}">
                            <i class="mdi mdi-view-dashboard-outline"></i> {{ __('Dashboard') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.dashboard.package.order') ? 'active' : '' }}">
                        <a href="{{ theme_user_package_orders_url() }}">
                            <i class="mdi mdi-package-variant-closed"></i> {{ __('My Orders') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.dashboard.download.list') ? 'active' : '' }}">
                        <a href="{{ theme_user_downloads_url() }}">
                            <i class="mdi mdi-download-outline"></i> {{ __('Downloads') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.home.support.tickets', 'tenant.frontend.support.ticket') ? 'active' : '' }}">
                        <a href="{{ theme_user_tickets_url() }}">
                            <i class="mdi mdi-ticket-outline"></i> {{ __('Support Tickets') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.home.manage.account') ? 'active' : '' }}">
                        <a href="{{ theme_user_manage_account_url() }}">
                            <i class="mdi mdi-account-outline"></i> {{ __('My Account') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.home.change.password') ? 'active' : '' }}">
                        <a href="{{ route('tenant.user.home.change.password') }}">
                            <i class="mdi mdi-lock-outline"></i> {{ __('Change Password') }}
                        </a>
                    </li>

                    @php do_action('nazmart:user_dashboard_sidebar') @endphp

                    <li class="tn-dash-logout">
                        <a href="{{ theme_logout_url() }}">
                            <i class="mdi mdi-logout-variant"></i> {{ __('Logout') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Content --}}
            <div class="tn-dash-content">
                <x-error-msg/>
                <x-flash-msg/>
                @yield('section')
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<x-media-upload.js/>
<script>
$('#tn-dash-toggle').on('click', function(){
    $('#tn-dash-sidebar').toggleClass('open');
});
</script>
@yield('dashboard-scripts')

<style>
    /* Reset Plugin Sidebar Item Bullets */
    li.list { list-style: none !important; margin:0; padding:0; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var navLinks = document.querySelectorAll('nav a[class*="-dash-nav-link"], ul a[class*="-dash-nav-link"], nav li a[class*="-dash-nav-link"]');
    var pluginLinks = document.querySelectorAll('li.list > a');
    
    if (navLinks.length > 0 && pluginLinks.length > 0) {
        var baseClasses = navLinks[0].className.split(' ').filter(c => !c.includes('active') && !c.includes('hover'));
        var nativeIcon = navLinks[0].querySelector('i');
        var isLas = nativeIcon && nativeIcon.className.includes('las ');
        
        pluginLinks.forEach(function(link) {
            link.className = baseClasses.join(' ');
            
            var icon = link.querySelector('i');
            if (icon && isLas && icon.className.includes('mdi ')) {
                if (icon.className.includes('shield')) icon.className = 'las la-shield-alt';
                else if (icon.className.includes('account-multiple')) icon.className = 'las la-user-friends';
                else if (icon.className.includes('gift')) icon.className = 'las la-gift';
                else icon.className = 'las la-cog';
            }
        });
    }
});
</script>
@endsection
