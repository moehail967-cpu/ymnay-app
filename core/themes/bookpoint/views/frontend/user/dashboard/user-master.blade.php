@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('style')
<x-media-upload.css/>
<style></style>
@endsection

@section('content')

<div class="bp-page-banner">
    <div class="container">
        <h1>{{ __('My Account') }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="bp-dash-section">
    <div class="container">

        <button class="bp-dash-mobile-toggle" id="bp-dash-toggle">
            <i class="las la-bars"></i> {{ __('Account Menu') }}
        </button>

        <div class="bp-dash-wrap">

            {{-- Sidebar --}}
            <div class="bp-dash-sidebar" id="bp-dash-sidebar">
                @php $u = auth('web')->user(); @endphp
                <div class="bp-dash-user">
                    <div class="bp-dash-avatar">{{ strtoupper(substr($u?->name ?? 'U', 0, 1)) }}</div>
                    <div class="bp-dash-user-info">
                        <div class="bp-dash-name">{{ $u?->name }}</div>
                        <div class="bp-dash-email">{{ $u?->email }}</div>
                    </div>
                </div>
                <ul class="bp-dash-nav">
                    <li class="{{ request()->routeIs('tenant.user.home') ? 'active' : '' }}">
                        <a href="{{ theme_user_dashboard_url() }}">
                            <i class="las la-tachometer-alt"></i> {{ __('Dashboard') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.dashboard.package.order') ? 'active' : '' }}">
                        <a href="{{ theme_user_package_orders_url() }}">
                            <i class="las la-shopping-bag"></i> {{ __('My Orders') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.dashboard.download.list') ? 'active' : '' }}">
                        <a href="{{ theme_user_downloads_url() }}">
                            <i class="las la-download"></i> {{ __('Downloads') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.home.support.tickets', 'tenant.frontend.support.ticket') ? 'active' : '' }}">
                        <a href="{{ theme_user_tickets_url() }}">
                            <i class="las la-ticket-alt"></i> {{ __('Support Tickets') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.home.manage.account') ? 'active' : '' }}">
                        <a href="{{ theme_user_manage_account_url() }}">
                            <i class="las la-user-circle"></i> {{ __('My Account') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.home.change.password') ? 'active' : '' }}">
                        <a href="{{ route('tenant.user.home.change.password') }}">
                            <i class="las la-lock"></i> {{ __('Change Password') }}
                        </a>
                    </li>

                    @php do_action('nazmart:user_dashboard_sidebar') @endphp

                    <li class="bp-dash-logout">
                        <a href="{{ theme_logout_url() }}">
                            <i class="las la-sign-out-alt"></i> {{ __('Logout') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Content --}}
            <div class="bp-dash-content">
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
$('#bp-dash-toggle').on('click', function(){
    $('#bp-dash-sidebar').toggleClass('open');
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
