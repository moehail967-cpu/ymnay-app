@extends('tenant.frontend.frontend-page-master')

@section('style')
<x-media-upload.css/>
<style>
.fp-dash-wrap { display:flex; gap:24px; align-items:flex-start; padding:40px 0 60px; }
.fp-dash-sidebar { width:240px; flex-shrink:0; background:var(--fp-surface); border:1px solid var(--fp-border); border-radius:var(--fp-radius); overflow:hidden; position:sticky; top:90px; }
.fp-dash-user-info { padding:20px 16px; background:linear-gradient(135deg,#0A1A0A,#0F2A0F); border-bottom:1px solid var(--fp-border); display:flex; align-items:center; gap:12px; }
.fp-dash-avatar { width:44px; height:44px; border-radius:var(--fp-radius); background:var(--fp-green-glow); border:2px solid var(--fp-green); color:var(--fp-green); display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:800; flex-shrink:0; font-family:var(--fp-font-head); }
.fp-dash-username { font-family:var(--fp-font-head); font-size:14px; font-weight:700; color:var(--fp-text); text-transform:uppercase; letter-spacing:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.fp-dash-email { font-size:11px; color:var(--fp-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
.fp-dash-nav { list-style:none; padding:8px 0; margin:0; }
.fp-dash-nav li a { display:flex; align-items:center; gap:10px; padding:11px 18px; font-family:var(--fp-font-head); font-size:13px; font-weight:600; color:var(--fp-muted); text-decoration:none; text-transform:uppercase; letter-spacing:1px; transition:all .15s; border-left:3px solid transparent; }
.fp-dash-nav li a:hover, .fp-dash-nav li.active a { background:var(--fp-green-glow); color:var(--fp-green); border-left-color:var(--fp-green); }
.fp-dash-nav li a i { font-size:18px; width:20px; text-align:center; }
.fp-dash-nav li.logout a { color:#ff4d4d; }
.fp-dash-nav li.logout a:hover { background:rgba(255,77,77,.08); border-left-color:#ff4d4d; color:#ff4d4d; }
.fp-dash-divider { height:1px; background:var(--fp-border); margin:4px 0; }
.fp-dash-content { flex:1; min-width:0; }
.fp-dash-card { background:var(--fp-surface); border:1px solid var(--fp-border); border-radius:var(--fp-radius); overflow:hidden; margin-bottom:20px; }
.fp-dash-card-header { padding:14px 20px; border-bottom:1px solid var(--fp-border); font-family:var(--fp-font-head); font-weight:700; font-size:13px; color:var(--fp-green); text-transform:uppercase; letter-spacing:2px; background:var(--fp-card); display:flex; align-items:center; justify-content:space-between; }
.fp-dash-card-body { padding:20px; }
.fp-dash-table { width:100%; border-collapse:collapse; font-size:13px; }
.fp-dash-table thead tr { background:var(--fp-card); border-bottom:1px solid var(--fp-border); }
.fp-dash-table thead th { padding:11px 14px; text-align:left; color:var(--fp-muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; font-family:var(--fp-font-head); }
.fp-dash-table tbody tr { border-bottom:1px solid var(--fp-border); transition:background .15s; }
.fp-dash-table tbody tr:last-child { border-bottom:none; }
.fp-dash-table tbody tr:hover { background:var(--fp-card); }
.fp-dash-table tbody td { padding:13px 14px; color:var(--fp-text); vertical-align:middle; }
.fp-dash-badge { padding:3px 10px; border-radius:2px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; display:inline-block; font-family:var(--fp-font-head); }
.fp-stat-card { background:var(--fp-card); border:1px solid var(--fp-border); border-radius:var(--fp-radius); padding:20px; display:flex; align-items:center; gap:14px; height:100%; }
.fp-stat-icon { width:50px; height:50px; border:1px solid var(--fp-green); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.fp-stat-icon i { font-size:24px; color:var(--fp-green); }
.fp-stat-value { font-family:var(--fp-font-head); font-size:28px; font-weight:800; color:var(--fp-text); line-height:1; }
.fp-stat-label { font-family:var(--fp-font-head); font-size:11px; color:var(--fp-muted); margin-top:4px; text-transform:uppercase; letter-spacing:1.5px; }
.fp-dash-input { width:100%; padding:10px 14px; background:var(--fp-mid); border:1px solid var(--fp-border); border-radius:var(--fp-radius); color:var(--fp-text); font-size:14px; outline:none; transition:border-color .15s; font-family:var(--fp-font-body); }
.fp-dash-input:focus { border-color:var(--fp-green); }
.fp-dash-label { font-family:var(--fp-font-head); font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--fp-muted); display:block; margin-bottom:6px; }
.fp-dash-mobile-toggle { display:none; align-items:center; gap:8px; background:var(--fp-green); border:none; border-radius:var(--fp-radius); padding:10px 16px; color:#000; font-family:var(--fp-font-head); font-size:13px; font-weight:700; cursor:pointer; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px; }
@media (max-width:991px) {
    .fp-dash-wrap { flex-direction:column; }
    .fp-dash-sidebar { width:100%; position:relative; top:0; display:none; }
    .fp-dash-sidebar.open { display:block; }
    .fp-dash-mobile-toggle { display:flex; }
}
</style>
@endsection

@section('content')
{{-- Page Hero --}}
<div class="fp-page-hero">
    <div class="container">
        <h1 class="fp-page-title">{{ __('My') }} <span>{{ __('Account') }}</span></h1>
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li>{{ __('Account') }}</li>
        </ul>
    </div>
</div>

<div class="container">
    <div class="fp-dash-wrap">

        {{-- Mobile sidebar toggle --}}
        <button class="fp-dash-mobile-toggle" onclick="document.querySelector('.fp-dash-sidebar').classList.toggle('open')">
            <i class="mdi mdi-menu"></i> {{ __('Account Menu') }}
        </button>

        {{-- Sidebar --}}
        <aside class="fp-dash-sidebar">
            <div class="fp-dash-user-info">
                <div class="fp-dash-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div style="min-width:0;">
                    <div class="fp-dash-username">{{ auth()->user()->name ?? '' }}</div>
                    <div class="fp-dash-email">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <ul class="fp-dash-nav">
                <li class="{{ request()->routeIs('tenant.user.home') ? 'active' : '' }}">
                    <a href="{{ theme_user_dashboard_url() }}">
                        <i class="mdi mdi-view-dashboard-outline"></i> {{ __('Dashboard') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.dashboard.package.order') ? 'active' : '' }}">
                    <a href="{{ theme_user_package_orders_url() }}">
                        <i class="mdi mdi-package-variant-closed"></i> {{ __('Orders') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.dashboard.download.list') ? 'active' : '' }}">
                    <a href="{{ theme_user_downloads_url() }}">
                        <i class="mdi mdi-download-outline"></i> {{ __('Downloads') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.home.support.tickets', 'tenant.frontend.support.ticket') ? 'active' : '' }}">
                    <a href="{{ theme_user_tickets_url() }}">
                        <i class="mdi mdi-headset"></i> {{ __('Support') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.home.manage.account') ? 'active' : '' }}">
                    <a href="{{ theme_user_manage_account_url() }}">
                        <i class="mdi mdi-account-edit-outline"></i> {{ __('Profile') }}
                    </a>
                </li>
                @if(moduleExists('PackageManage'))
                <li class="{{ request()->routeIs('tenant.user.dashboard.package.order') ? 'active' : '' }}">
                    <a href="{{ theme_user_package_orders_url() }}">
                        <i class="mdi mdi-cube-outline"></i> {{ __('Packages') }}
                    </a>
                </li>
                @endif
                <div class="fp-dash-divider"></div>
                @php do_action('nazmart:user_dashboard_sidebar') @endphp

                <li class="logout">
                    <a href="{{ theme_logout_url() }}">
                        <i class="mdi mdi-logout"></i> {{ __('Log Out') }}
                    </a>
                </li>
            </ul>
        </aside>

        {{-- Main content --}}
        <div class="fp-dash-content">
            @yield('dashboard-content')
        </div>

    </div>
</div>
@endsection

@section('scripts')
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
