@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('style')
<x-media-upload.css/>
<style>
.dk-dash-wrap { display:flex; gap:0; min-height:60vh; align-items:flex-start; }

/* Sidebar */
.dk-dash-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: var(--dk-surface);
    border: 1px solid var(--dk-border);
    border-radius: var(--dk-radius);
    padding: 0;
    position: sticky;
    top: 90px;
}
.dk-dash-user-info {
    padding: 20px 16px;
    border-bottom: 1px solid var(--dk-border);
    display: flex;
    align-items: center;
    gap: 12px;
}
.dk-dash-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: var(--dk-red);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 800;
    flex-shrink: 0;
}
.dk-dash-username {
    font-size: 13px; font-weight: 700; color: var(--dk-white);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.dk-dash-email {
    font-size: 11px; color: var(--dk-muted);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.dk-dash-nav { list-style: none; padding: 8px 0; margin: 0; }
.dk-dash-nav li a {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px;
    font-size: 13px; font-weight: 600; color: var(--dk-silver);
    text-decoration: none;
    transition: background .15s, color .15s;
    border-left: 3px solid transparent;
}
.dk-dash-nav li a:hover,
.dk-dash-nav li.active a {
    background: var(--dk-panel);
    color: var(--dk-white);
    border-left-color: var(--dk-red);
}
.dk-dash-nav li a i { font-size: 18px; width: 20px; text-align: center; }
.dk-dash-nav li.logout a { color: #e57373; }
.dk-dash-nav li.logout a:hover { background: rgba(229,48,48,.1); border-left-color: var(--dk-red); color: var(--dk-red); }

/* Content area */
.dk-dash-content { flex: 1; min-width: 0; }

/* Mobile sidebar toggle */
.dk-dash-mobile-toggle {
    display: none;
    align-items: center; gap: 8px;
    background: var(--dk-surface); border: 1px solid var(--dk-border);
    border-radius: var(--dk-radius); padding: 8px 14px;
    color: var(--dk-white); font-size: 13px; font-weight: 700;
    cursor: pointer; margin-bottom: 16px;
}
@media (max-width: 991px) {
    .dk-dash-wrap { flex-direction: column; }
    .dk-dash-sidebar { width: 100%; position: relative; top: 0; display: none; }
    .dk-dash-sidebar.open { display: block; }
    .dk-dash-mobile-toggle { display: flex; }
}
</style>
@endsection

@section('content')

{{-- Page Banner --}}
<div class="dk-breadcrumb-bar">
    <div class="container">
        <ul class="dk-bc-list">
            <li><a href="{{ theme_home_url() }}"><i class="mdi mdi-home-outline"></i> {{ __('Home') }}</a></li>
            <li class="sep"><i class="mdi mdi-chevron-right"></i></li>
            <li class="active">{{ __('My Account') }}</li>
        </ul>
    </div>
</div>

<section class="py-5" style="background:var(--dk-carbon);">
    <div class="container">

        <button class="dk-dash-mobile-toggle" id="dk-dash-toggle">
            <i class="mdi mdi-menu"></i> {{ __('Dashboard Menu') }}
        </button>

        <div class="dk-dash-wrap gap-4">

            {{-- Sidebar --}}
            <div class="dk-dash-sidebar" id="dk-dash-sidebar">
                @php $u = auth('web')->user(); @endphp
                <div class="dk-dash-user-info">
                    <div class="dk-dash-avatar">{{ strtoupper(substr($u?->name ?? 'U', 0, 1)) }}</div>
                    <div style="min-width:0;">
                        <div class="dk-dash-username">{{ $u?->name }}</div>
                        <div class="dk-dash-email">{{ $u?->email }}</div>
                    </div>
                </div>
                <ul class="dk-dash-nav">
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

                    {{-- Plugin sidebar items --}}
                    @php do_action('nazmart:user_dashboard_sidebar') @endphp

                    <li class="logout">
                        <a href="{{ theme_logout_url() }}">
                            <i class="mdi mdi-logout-variant"></i> {{ __('Logout') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Content --}}
            <div class="dk-dash-content">
                <x-error-msg/>
                <x-flash-msg/>
                @yield('section')
            </div>

        </div>
    </div>
</section>

@endsection

@section('scripts')
<x-media-upload.js/>
<script>
$('#dk-dash-toggle').on('click', function(){
    $('#dk-dash-sidebar').toggleClass('open');
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
