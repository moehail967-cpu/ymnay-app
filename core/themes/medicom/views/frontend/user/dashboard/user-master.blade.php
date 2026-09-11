@extends('tenant.frontend.frontend-page-master')

@section('style')
<x-media-upload.css/>
<style></style>
@endsection

@section('content')
<div class="mc-dash-header">
    <div class="container">
        <h2>@yield('dash-title', __('My Account'))</h2>

    </div>
</div>

<div class="container" style="padding:40px 0 80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="mc-dash-sidebar">
                <div class="mc-dash-user-info">
                    <div class="mc-dash-avatar">{{ strtoupper(substr(auth('web')->user()?->name ?? 'U', 0, 1)) }}</div>
                    <div class="mc-dash-name">{{ auth('web')->user()?->name }}</div>
                    <div class="mc-dash-email">{{ auth('web')->user()?->email }}</div>
                </div>
                <ul class="mc-dash-nav">
                    <li><a href="{{ theme_user_dashboard_url() }}" class="{{ request()->routeIs('tenant.user.home') ? 'active' : '' }}">
                        <i class="las la-home"></i> {{ __('Dashboard') }}
                    </a></li>
                    <li><a href="{{ route('tenant.user.dashboard.package.order') }}" class="{{ request()->routeIs('tenant.user.order.list') ? 'active' : '' }}">
                        <i class="las la-shopping-bag"></i> {{ __('My Orders') }}
                    </a></li>
                    <li><a href="{{ theme_user_downloads_url() }}" class="{{ request()->routeIs('tenant.user.downloads') ? 'active' : '' }}">
                        <i class="las la-download"></i> {{ __('Downloads') }}
                    </a></li>
                    <li><a href="{{ theme_user_tickets_url() }}" class="{{ request()->routeIs('tenant.user.home.support.tickets') ? 'active' : '' }}">
                        <i class="las la-headset"></i> {{ __('Support') }}
                    </a></li>
                    <li><a href="{{ theme_user_package_orders_url() }}" class="{{ request()->routeIs('tenant.user.package.order') ? 'active' : '' }}">
                        <i class="las la-box"></i> {{ __('Package Orders') }}
                    </a></li>
                    <li><a href="{{ theme_user_manage_account_url() }}" class="{{ request()->routeIs('tenant.user.manage.account') ? 'active' : '' }}">
                        <i class="las la-user-edit"></i> {{ __('Manage Account') }}
                    </a></li>
                    <li><a href="{{ route('tenant.user.home.change.password') }}" class="{{ request()->routeIs('tenant.user.home.change.password') ? 'active' : '' }}">
                        <i class="las la-lock"></i> {{ __('Change Password') }}
                    </a></li>
                    @php do_action('nazmart:user_dashboard_sidebar') @endphp
                    <li><a href="{{ theme_logout_url() }}">
                        <i class="las la-sign-out-alt"></i> {{ __('Sign Out') }}
                    </a></li>
                </ul>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-lg-9">
            @yield('dashboard-content')
        </div>

    </div>
</div>
@endsection

@section('scripts')
<x-media-upload.js/>
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
