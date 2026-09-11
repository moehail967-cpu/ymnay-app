@extends('tenant.frontend.frontend-page-master')

@section('style')
@parent
<x-media-upload.css/>
<style>
/* VelvetLux Dashboard — vl-dash-* */
.vl-dash-wrap { display:flex; gap:24px; align-items:flex-start; padding:40px 0 72px; }
.vl-dash-sidebar { width:240px; flex-shrink:0; background:var(--vl-card); border:1px solid var(--vl-border); overflow:hidden; position:sticky; top:90px; }
.vl-dash-user-info { padding:24px 20px; background:linear-gradient(135deg,var(--vl-plum-d),var(--vl-plum)); display:flex; align-items:center; gap:12px; }
.vl-dash-avatar { width:44px; height:44px; background:rgba(212,184,150,.2); border:1px solid var(--vl-champagne); color:var(--vl-champagne); display:flex; align-items:center; justify-content:center; font-size:17px; font-weight:600; flex-shrink:0; }
.vl-dash-username { font-size:14px; font-weight:400; color:var(--vl-ivory); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-family:'Cormorant Garamond',serif; letter-spacing:.5px; }
.vl-dash-useremail { font-size:11px; color:var(--vl-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
.vl-dash-nav { list-style:none; padding:8px 0; margin:0; }
.vl-dash-nav li a { display:flex; align-items:center; gap:10px; padding:11px 20px; font-size:12px; font-weight:400; color:var(--vl-muted); text-decoration:none; transition:all .15s; border-left:3px solid transparent; letter-spacing:1px; text-transform:uppercase; }
.vl-dash-nav li a:hover, .vl-dash-nav li.active a { background:rgba(212,184,150,.06); color:var(--vl-champagne); border-left-color:var(--vl-champagne); }
.vl-dash-nav li a i { font-size:17px; width:20px; text-align:center; flex-shrink:0; }
.vl-dash-nav .vl-dash-divider { height:1px; background:var(--vl-border); margin:4px 0; }
.vl-dash-nav li.logout a { color:rgba(229,62,62,.7); }
.vl-dash-nav li.logout a:hover { background:rgba(229,62,62,.06); border-left-color:#E53E3E; color:#E53E3E; }
.vl-dash-content { flex:1; min-width:0; }
.vl-dash-card { background:var(--vl-card); border:1px solid var(--vl-border); overflow:hidden; margin-bottom:20px; }
.vl-dash-card-header { padding:16px 24px; border-bottom:1px solid var(--vl-border); font-size:10px; font-weight:400; color:var(--vl-champagne); font-family:'Inter',sans-serif; letter-spacing:3px; text-transform:uppercase; display:flex; align-items:center; justify-content:space-between; }
.vl-dash-card-body { padding:24px; }
.vl-dash-table { width:100%; border-collapse:collapse; font-size:13px; }
.vl-dash-table thead tr { background:var(--vl-surface); border-bottom:1px solid var(--vl-border); }
.vl-dash-table thead th { padding:12px 16px; text-align:left; color:var(--vl-champagne); font-size:9px; font-weight:400; text-transform:uppercase; letter-spacing:2px; font-family:'Inter',sans-serif; }
.vl-dash-table tbody tr { border-bottom:1px solid rgba(58,36,68,.4); transition:background .15s; }
.vl-dash-table tbody tr:last-child { border-bottom:none; }
.vl-dash-table tbody tr:hover { background:rgba(212,184,150,.03); }
.vl-dash-table tbody td { padding:14px 16px; color:var(--vl-muted); vertical-align:middle; }
.vl-dash-badge { padding:3px 10px; font-size:10px; font-weight:400; display:inline-block; letter-spacing:.5px; }
.vl-dash-stat { background:var(--vl-card); border:1px solid var(--vl-border); padding:20px; display:flex; align-items:center; gap:16px; height:100%; }
.vl-dash-stat-icon { width:48px; height:48px; background:rgba(212,184,150,.08); border:1px solid var(--vl-border); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.vl-dash-stat-icon i { font-size:20px; color:var(--vl-champagne); }
.vl-dash-stat-value { font-size:26px; font-weight:300; color:var(--vl-ivory); line-height:1; font-family:'Cormorant Garamond',serif; }
.vl-dash-stat-label { font-size:10px; color:var(--vl-muted); margin-top:4px; letter-spacing:1px; text-transform:uppercase; font-family:'Inter',sans-serif; }
.vl-dash-input { width:100%; padding:12px 16px; background:var(--vl-surface); border:1px solid var(--vl-border); color:var(--vl-ivory); font-size:14px; font-family:inherit; outline:none; transition:border-color .15s; }
.vl-dash-input:focus { border-color:var(--vl-champagne); }
.vl-dash-label { font-size:10px; font-weight:400; color:var(--vl-muted); display:block; margin-bottom:8px; letter-spacing:2px; text-transform:uppercase; }
.vl-dash-mobile-toggle { display:none; align-items:center; gap:8px; background:var(--vl-plum); border:none; padding:12px 18px; color:var(--vl-champagne); font-size:11px; font-weight:400; cursor:pointer; margin-bottom:16px; letter-spacing:2px; text-transform:uppercase; }
@media (max-width:991px) {
    .vl-dash-wrap { flex-direction:column; }
    .vl-dash-sidebar { width:100%; position:relative; top:0; display:none; }
    .vl-dash-sidebar.open { display:block; }
    .vl-dash-mobile-toggle { display:flex; }
}
</style>
@endsection

@section('content')
<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Client') }}</div>
        <h2 style="font-size:28px;font-weight:400;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:2px;">{{ __('My Account') }}</h2>
        <div class="vl-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span>{{ __('Account') }}</span>
        </div>
    </div>
</div>

<div class="container">
    <div class="vl-dash-wrap">

        <button class="vl-dash-mobile-toggle" onclick="document.querySelector('.vl-dash-sidebar').classList.toggle('open')">
            <i class="mdi mdi-menu"></i> {{ __('Account Menu') }}
        </button>

        <aside class="vl-dash-sidebar">
            <div class="vl-dash-user-info">
                <div class="vl-dash-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div style="min-width:0;">
                    <div class="vl-dash-username">{{ auth()->user()->name ?? '' }}</div>
                    <div class="vl-dash-useremail">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <ul class="vl-dash-nav">
                <li class="{{ request()->routeIs('tenant.user.home') ? 'active' : '' }}">
                    <a href="{{ theme_user_dashboard_url() }}">
                        <i class="mdi mdi-view-dashboard-outline"></i> {{ __('Dashboard') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.dashboard.package.order') ? 'active' : '' }}">
                    <a href="{{ route('tenant.user.dashboard.package.order') }}">
                        <i class="mdi mdi-package-variant-closed"></i> {{ __('Orders') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.download.list') ? 'active' : '' }}">
                    <a href="{{ theme_user_downloads_url() }}">
                        <i class="mdi mdi-cloud-download-outline"></i> {{ __('Downloads') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.support.ticket.*') ? 'active' : '' }}">
                    <a href="{{ theme_user_tickets_url() }}">
                        <i class="mdi mdi-lifebuoy"></i> {{ __('Support') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.home.manage.account') ? 'active' : '' }}">
                    <a href="{{ theme_user_manage_account_url() }}">
                        <i class="mdi mdi-account-edit-outline"></i> {{ __('Profile') }}
                    </a>
                </li>
                @if(moduleExists('PackageManage'))
                <li class="{{ request()->routeIs('tenant.user.package.order.list') ? 'active' : '' }}">
                    <a href="{{ theme_user_package_orders_url() }}">
                        <i class="mdi mdi-cube-outline"></i> {{ __('Packages') }}
                    </a>
                </li>
                @endif
                @php do_action('nazmart:user_dashboard_sidebar') @endphp
                <li class="vl-dash-divider"></li>
                <li class="logout">
                    <a href="{{ theme_logout_url() }}">
                        <i class="mdi mdi-logout-variant"></i> {{ __('Log Out') }}
                    </a>
                </li>
            </ul>
        </aside>

        <div class="vl-dash-content">
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
