@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection

@section('style')
<style>
.tz-dash-wrap { display:flex; gap:24px; align-items:flex-start; padding:40px 0 72px; }
.tz-dash-sidebar { width:260px; flex-shrink:0; background:var(--tz-card); border:1px solid var(--tz-border); border-radius:var(--tz-radius); overflow:hidden; position:sticky; top:90px; }
.tz-dash-nav-link, .tz-dash-sidebar nav ul li.list > a { display:flex; align-items:center; gap:10px; padding:12px 20px; font-family:var(--tz-font); font-size:13px; font-weight:500; color:var(--tz-muted); text-decoration:none; border-left:3px solid transparent; transition:all .2s; }
.tz-dash-nav-link:hover, .tz-dash-nav-link.active, .tz-dash-sidebar nav ul li.list:hover > a, .tz-dash-sidebar nav ul li.list.active > a { color:var(--tz-blue); background:var(--tz-blue-glow); border-left-color:var(--tz-blue); }
.tz-dash-nav-link i, .tz-dash-sidebar nav ul li.list > a i { font-size:18px; width:22px; text-align:center; flex-shrink:0; }
.tz-dash-nav-link.tz-dash-logout { color:rgba(239,68,68,.8); border-top:1px solid var(--tz-border); margin-top:4px; padding-top:16px; }
.tz-dash-nav-link.tz-dash-logout:hover { color:#ef4444; background:rgba(239,68,68,.08); border-left-color:#ef4444; }
.tz-dash-card { background:var(--tz-card); border:1px solid var(--tz-border); border-radius:var(--tz-radius); overflow:hidden; margin-bottom:20px; }
.tz-dash-card-header { padding:16px 20px; border-bottom:1px solid var(--tz-border); font-size:14px; font-weight:700; color:#fff; display:flex; align-items:center; justify-content:space-between; }
.tz-dash-card-body { padding:20px; }
.tz-dash-table { width:100%; border-collapse:collapse; font-size:13px; }
.tz-dash-table thead tr { background:var(--tz-surface); border-bottom:1px solid var(--tz-border); }
.tz-dash-table thead th { padding:11px 14px; text-align:left; color:var(--tz-muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; }
.tz-dash-table tbody tr { border-bottom:1px solid var(--tz-border); transition:background .15s; }
.tz-dash-table tbody tr:last-child { border-bottom:none; }
.tz-dash-table tbody tr:hover { background:var(--tz-surface); }
.tz-dash-table tbody td { padding:12px 14px; color:var(--tz-text); vertical-align:middle; }
.tz-dash-badge { padding:3px 10px; border-radius:var(--tz-radius-sm); font-size:11px; font-weight:700; display:inline-block; }
.tz-dash-badge-success { background:rgba(34,197,94,.12); color:#22c55e; }
.tz-dash-badge-warning { background:rgba(245,158,11,.12); color:#f59e0b; }
.tz-dash-badge-danger  { background:rgba(239,68,68,.12); color:#ef4444; }
.tz-dash-badge-muted   { background:var(--tz-surface); color:var(--tz-muted); }
.tz-dash-badge-info    { background:var(--tz-blue-glow); color:var(--tz-blue); }
.tz-dash-input { width:100%; padding:10px 14px; background:var(--tz-mid); border:1px solid var(--tz-border); border-radius:var(--tz-radius-sm); color:var(--tz-text); font-size:14px; font-family:var(--tz-font); outline:none; transition:border-color .2s; }
.tz-dash-input:focus { border-color:var(--tz-blue); }
.tz-dash-label { font-size:12px; font-weight:600; color:var(--tz-muted); display:block; margin-bottom:6px; }
.tz-dash-stat { background:var(--tz-card); border:1px solid var(--tz-border); border-radius:var(--tz-radius); padding:20px; display:flex; align-items:center; gap:14px; }
.tz-dash-stat-icon { width:48px; height:48px; border-radius:var(--tz-radius-sm); background:var(--tz-blue-glow); border:1px solid var(--tz-blue); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.tz-dash-stat-icon i { font-size:22px; color:var(--tz-blue); }
.tz-dash-stat-value { font-size:26px; font-weight:800; color:#fff; line-height:1; }
.tz-dash-stat-label { font-size:12px; color:var(--tz-muted); margin-top:3px; }
@media (max-width:991px) {
    .tz-dash-wrap { flex-direction:column; }
    .tz-dash-sidebar { width:100%; position:relative; top:0; display:none; }
    .tz-dash-sidebar.open { display:block; }
    .tz-dash-mobile-toggle { display:flex !important; }
}
</style>
@endsection

@section('content')
<div style="background:var(--tz-surface);border-bottom:1px solid var(--tz-border);padding:28px 0 20px;">
    <div class="container">
        <h2 style="font-size:20px;font-weight:700;color:#fff;margin-bottom:8px;">{{ __('My Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--tz-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--tz-muted);text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Dashboard') }}</span>
        </div>
    </div>
</div>

<div style="background:var(--tz-bg);padding:40px 0 72px;">
    <div class="container">

        {{-- Mobile toggle --}}
        <button id="tz_dash_toggle"
                style="display:none;align-items:center;gap:8px;background:var(--tz-blue);border:0;color:#fff;padding:11px 18px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:700;cursor:pointer;font-family:var(--tz-font);margin-bottom:16px;width:100%;">
            <i class="mdi mdi-menu" style="font-size:18px;"></i> {{ __('Dashboard Menu') }}
        </button>

        <div class="tz-dash-wrap">

            {{-- Sidebar --}}
            <div class="tz-dash-sidebar" id="tz_dash_sidebar">
                @php $u = auth('web')->user(); @endphp
                <div style="padding:24px 20px;border-bottom:1px solid var(--tz-border);text-align:center;">
                    <div style="width:60px;height:60px;border-radius:50%;background:var(--tz-blue-glow);border:2px solid var(--tz-blue);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:var(--tz-blue);margin:0 auto 12px;">
                        {{ strtoupper(substr($u?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div style="font-size:14px;font-weight:700;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $u?->name }}</div>
                    <div style="font-size:11px;color:var(--tz-muted);margin-top:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $u?->email }}</div>
                </div>

                <nav style="padding:8px 0;">
                    <ul style="list-style:none; padding:0; margin:0;">
                    @php
                        $tz_nav = [
                            ['icon'=>'mdi-view-dashboard-outline', 'label'=>__('Dashboard'),       'route'=>'tenant.user.home'],
                            ['icon'=>'mdi-package-variant-closed', 'label'=>__('My Orders'),       'route'=>'tenant.user.dashboard.package.order'],
                            ['icon'=>'mdi-download-outline',       'label'=>__('Downloads'),       'route'=>'tenant.user.dashboard.download.list'],
                            ['icon'=>'mdi-ticket-outline',         'label'=>__('Support Tickets'), 'route'=>'tenant.user.home.support.tickets'],
                            ['icon'=>'mdi-account-outline',        'label'=>__('Manage Account'),  'route'=>'tenant.user.home.manage.account'],
                            ['icon'=>'mdi-lock-outline',           'label'=>__('Change Password'), 'route'=>'tenant.user.home.change.password'],
                        ];
                    @endphp
                    @foreach($tz_nav as $nav)
                    <li>
                        <a href="{{ !is_null(tenant()) ? route($nav['route']) : '#' }}"
                           class="tz-dash-nav-link {{ request()->routeIs($nav['route']) ? 'active' : '' }}">
                            <i class="mdi {{ $nav['icon'] }}"></i>
                            <span>{{ $nav['label'] }}</span>
                        </a>
                    </li>
                    @endforeach

                    @php do_action('nazmart:user_dashboard_sidebar') @endphp

                    <li>
                        <a href="{{ theme_logout_url() }}" class="tz-dash-nav-link tz-dash-logout">
                            <i class="mdi mdi-logout-variant"></i>
                            <span>{{ __('Sign Out') }}</span>
                        </a>
                    </li>
                    </ul>
                </nav>
            </div>

            {{-- Main Content --}}
            <div style="flex:1;min-width:0;">
                <x-error-msg/>
                <x-flash-msg/>
                @yield('dashboard_content')
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$('#tz_dash_toggle').on('click', function(){ $('#tz_dash_sidebar').toggleClass('open'); });
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
