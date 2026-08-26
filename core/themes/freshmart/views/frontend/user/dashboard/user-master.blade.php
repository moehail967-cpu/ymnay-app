@extends('tenant.frontend.frontend-page-master')

@section('style')
@parent
<x-media-upload.css/>
<style>
.fm-dash-wrap { display:flex; gap:24px; align-items:flex-start; padding:36px 0 60px; }
.fm-dash-sidebar { width:240px; flex-shrink:0; background:#fff; border:1px solid var(--fm-border); border-radius:12px; overflow:hidden; position:sticky; top:90px; }
.fm-dash-user-info { padding:20px 16px; background:linear-gradient(135deg,var(--fm-green),#2ecc71); display:flex; align-items:center; gap:12px; }
.fm-dash-avatar { width:44px; height:44px; border-radius:50%; background:rgba(255,255,255,.25); border:2px solid rgba(255,255,255,.6); color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:800; flex-shrink:0; }
.fm-dash-username { font-size:14px; font-weight:700; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.fm-dash-useremail { font-size:11px; color:rgba(255,255,255,.75); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
.fm-dash-nav { list-style:none; padding:8px 0; margin:0; }
.fm-dash-nav li a { display:flex; align-items:center; gap:10px; padding:11px 18px; font-size:13px; font-weight:600; color:var(--fm-muted); text-decoration:none; transition:all .15s; border-left:3px solid transparent; }
.fm-dash-nav li a:hover, .fm-dash-nav li.active a { background:rgba(39,174,96,.08); color:var(--fm-green); border-left-color:var(--fm-green); }
.fm-dash-nav li a i { font-size:18px; width:20px; text-align:center; }
.fm-dash-nav .fm-dash-divider { height:1px; background:var(--fm-border); margin:4px 0; }
.fm-dash-nav li.logout a { color:#e74c3c; }
.fm-dash-nav li.logout a:hover { background:rgba(231,76,60,.08); border-left-color:#e74c3c; color:#e74c3c; }
.fm-dash-content { flex:1; min-width:0; }
.fm-dash-card { background:#fff; border:1px solid var(--fm-border); border-radius:10px; overflow:hidden; margin-bottom:20px; }
.fm-dash-card-header { padding:14px 20px; border-bottom:1px solid var(--fm-border); font-weight:700; font-size:14px; color:var(--fm-dark); display:flex; align-items:center; justify-content:space-between; }
.fm-dash-card-body { padding:20px; }
.fm-dash-table { width:100%; border-collapse:collapse; font-size:13px; }
.fm-dash-table thead tr { background:var(--fm-surface); border-bottom:1px solid var(--fm-border); }
.fm-dash-table thead th { padding:11px 14px; text-align:left; color:var(--fm-muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; }
.fm-dash-table tbody tr { border-bottom:1px solid var(--fm-border); transition:background .15s; }
.fm-dash-table tbody tr:last-child { border-bottom:none; }
.fm-dash-table tbody tr:hover { background:var(--fm-surface); }
.fm-dash-table tbody td { padding:13px 14px; color:var(--fm-dark); vertical-align:middle; }
.fm-dash-badge { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; display:inline-block; }
.fm-dash-stat { background:#fff; border:1px solid var(--fm-border); border-radius:10px; padding:20px; display:flex; align-items:center; gap:14px; height:100%; }
.fm-dash-stat-icon { width:48px; height:48px; border-radius:10px; background:rgba(39,174,96,.1); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.fm-dash-stat-icon i { font-size:22px; color:var(--fm-green); }
.fm-dash-stat-value { font-size:26px; font-weight:800; color:var(--fm-dark); line-height:1; }
.fm-dash-stat-label { font-size:12px; color:var(--fm-muted); margin-top:3px; }
.fm-dash-input { width:100%; padding:10px 14px; background:#fff; border:1px solid var(--fm-border); border-radius:8px; color:var(--fm-dark); font-size:14px; outline:none; transition:border-color .15s; }
.fm-dash-input:focus { border-color:var(--fm-green); }
.fm-dash-label { font-size:12px; font-weight:700; color:var(--fm-muted); display:block; margin-bottom:6px; }
.fm-dash-mobile-toggle { display:none; align-items:center; gap:8px; background:var(--fm-green); border:none; border-radius:8px; padding:10px 16px; color:#fff; font-size:13px; font-weight:700; cursor:pointer; margin-bottom:16px; }
@media (max-width:991px) {
    .fm-dash-wrap { flex-direction:column; }
    .fm-dash-sidebar { width:100%; position:relative; top:0; display:none; }
    .fm-dash-sidebar.open { display:block; }
    .fm-dash-mobile-toggle { display:flex; }
}
</style>
@endsection

@section('content')
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{{ __('My Account') }}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{{ __('Account') }}</span>
        </div>
    </div>
</div>

<div class="container">
    <div class="fm-dash-wrap">

        <button class="fm-dash-mobile-toggle" onclick="document.querySelector('.fm-dash-sidebar').classList.toggle('open')">
            <i class="las la-bars"></i> {{ __('Account Menu') }}
        </button>

        <aside class="fm-dash-sidebar">
            <div class="fm-dash-user-info">
                <div class="fm-dash-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div style="min-width:0;">
                    <div class="fm-dash-username">{{ auth()->user()->name ?? '' }}</div>
                    <div class="fm-dash-useremail">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <ul class="fm-dash-nav">
                <li class="{{ request()->routeIs('tenant.user.home') ? 'active' : '' }}">
                    <a href="{{ theme_user_dashboard_url() }}">
                        <i class="las la-home"></i> {{ __('Dashboard') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.order.list') ? 'active' : '' }}">
                    <a href="{{ route('tenant.user.dashboard.package.order') }}">
                        <i class="las la-box"></i> {{ __('Orders') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.download.list') ? 'active' : '' }}">
                    <a href="{{ theme_user_downloads_url() }}">
                        <i class="las la-download"></i> {{ __('Downloads') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.support.ticket.*') ? 'active' : '' }}">
                    <a href="{{ theme_user_tickets_url() }}">
                        <i class="las la-headset"></i> {{ __('Support') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.home.manage.account') ? 'active' : '' }}">
                    <a href="{{ theme_user_manage_account_url() }}">
                        <i class="las la-user-edit"></i> {{ __('Profile') }}
                    </a>
                </li>
                @if(moduleExists('PackageManage'))
                <li class="{{ request()->routeIs('tenant.user.package.order.list') ? 'active' : '' }}">
                    <a href="{{ theme_user_package_orders_url() }}">
                        <i class="las la-cube"></i> {{ __('Packages') }}
                    </a>
                </li>
                @endif
                <li class="fm-dash-divider"></li>
                @php do_action('nazmart:user_dashboard_sidebar') @endphp

                <li class="logout">
                    <a href="{{ theme_logout_url() }}">
                        <i class="las la-sign-out-alt"></i> {{ __('Log Out') }}
                    </a>
                </li>
            </ul>
        </aside>

        <div class="fm-dash-content">
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
