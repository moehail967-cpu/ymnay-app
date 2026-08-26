@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection

@section('style')
<style>
/* --- SportZone Dashboard Layout --- */
.sz-dash-sidebar {
    width: 260px;
    flex-shrink: 0;
    background: var(--sz-navy);
    position: sticky;
    top: 90px;
    align-self: flex-start;
}
.sz-dash-nav-link, .sz-dash-sidebar nav ul li.list > a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    font-family: var(--sz-font-head);
    font-size: 13px;
    font-weight: 400;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: rgba(255,255,255,.7);
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all .2s;
}
.sz-dash-nav-link:hover,
.sz-dash-nav-link.active,
.sz-dash-sidebar nav ul li.list:hover > a,
.sz-dash-sidebar nav ul li.list.active > a {
    color: #fff;
    background: rgba(255,255,255,.07);
    border-left-color: var(--sz-red);
}
.sz-dash-nav-link i, .sz-dash-sidebar nav ul li.list > a i {
    font-size: 18px;
    width: 22px;
    text-align: center;
    flex-shrink: 0;
}
.sz-dash-nav-link.sz-dash-logout {
    color: rgba(198,40,40,.8);
    margin-top: 8px;
    border-top: 1px solid rgba(255,255,255,.08);
}
.sz-dash-nav-link.sz-dash-logout:hover {
    color: var(--sz-red);
    background: rgba(198,40,40,.08);
    border-left-color: var(--sz-red);
}
.sz-dash-card {
    background: var(--sz-white);
    border: 2px solid var(--sz-border);
    border-radius: 0;
    padding: 28px;
}
.sz-dash-section-title {
    font-family: var(--sz-font-head);
    font-size: 16px;
    font-weight: 700;
    color: var(--sz-dark);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--sz-red);
    display: inline-block;
}
.sz-dash-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
.sz-dash-table th {
    padding: 12px 16px;
    font-family: var(--sz-font-head);
    font-size: 11px;
    font-weight: 400;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--sz-white);
    background: var(--sz-navy);
    text-align: left;
}
.sz-dash-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--sz-border);
    color: var(--sz-dark);
    vertical-align: middle;
}
.sz-dash-table tbody tr:hover td {
    background: var(--sz-bg);
}
.sz-dash-badge {
    display: inline-block;
    padding: 3px 10px;
    font-family: var(--sz-font-head);
    font-size: 10px;
    font-weight: 400;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 0;
}
.sz-dash-badge-success { background: #E8F5E9; color: #2E7D32; }
.sz-dash-badge-warning { background: #FFF8E1; color: #F57F17; }
.sz-dash-badge-danger  { background: var(--sz-red-light); color: var(--sz-red); }
.sz-dash-badge-muted   { background: var(--sz-bg); color: var(--sz-muted); }
.sz-dash-badge-info    { background: #E3F2FD; color: #1565C0; }
.sz-dash-form-group { margin-bottom: 18px; }
.sz-dash-form-label {
    display: block;
    font-family: var(--sz-font-head);
    font-size: 11px;
    font-weight: 400;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--sz-muted);
    margin-bottom: 6px;
}
.sz-dash-form-input {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid var(--sz-border);
    background: var(--sz-bg);
    font-family: var(--sz-font-body);
    font-size: 14px;
    color: var(--sz-dark);
    outline: none;
    transition: border-color .2s;
    border-radius: 0;
}
.sz-dash-form-input:focus { border-color: var(--sz-red); background: var(--sz-white); }
.sz-dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    font-family: var(--sz-font-head);
    font-size: 13px;
    font-weight: 400;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all .2s;
}
.sz-dash-btn-red { background: var(--sz-red); color: #fff; }
.sz-dash-btn-red:hover { background: #a81f1f; color: #fff; }
.sz-dash-btn-navy { background: var(--sz-navy); color: #fff; }
.sz-dash-btn-navy:hover { background: var(--sz-navy-mid); color: #fff; }
.sz-dash-btn-outline { background: transparent; border: 2px solid var(--sz-border); color: var(--sz-dark); }
.sz-dash-btn-outline:hover { border-color: var(--sz-red); color: var(--sz-red); }

/* Mobile responsive */
@media (max-width: 991px) {
    .sz-dash-wrap { flex-direction: column !important; }
    .sz-dash-sidebar { width: 100% !important; position: relative !important; top: 0 !important; display: none; }
    .sz-dash-sidebar.open { display: block; }
    .sz-dash-mobile-toggle { display: flex !important; }
}
</style>
@endsection

@section('content')

<div class="sz-page-banner">
    <div class="container">
        <h1>{{ __('My Account') }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div style="background:var(--sz-bg);padding:48px 0 72px;">
    <div class="container">

        {{-- Mobile toggle --}}
        <button class="sz-dash-mobile-toggle" id="sz_dash_toggle"
                style="display:none;align-items:center;gap:8px;background:var(--sz-navy);border:none;color:#fff;padding:12px 20px;font-family:var(--sz-font-head);font-size:13px;text-transform:uppercase;letter-spacing:1.5px;cursor:pointer;margin-bottom:16px;width:100%;">
            <i class="mdi mdi-menu" style="font-size:18px;"></i> {{ __('Dashboard Menu') }}
        </button>

        <div class="sz-dash-wrap" style="display:flex;align-items:flex-start;gap:24px;">

            {{-- Sidebar --}}
            <div class="sz-dash-sidebar" id="sz_dash_sidebar">
                @php $u = auth('web')->user(); @endphp

                {{-- User Avatar & Info --}}
                <div style="padding:28px 20px 20px;border-bottom:1px solid rgba(255,255,255,.1);text-align:center;">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--sz-red);display:flex;align-items:center;justify-content:center;font-family:var(--sz-font-head);font-size:26px;font-weight:700;color:#fff;margin:0 auto 14px;">
                        {{ strtoupper(substr($u?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div style="font-family:var(--sz-font-head);font-size:15px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $u?->name }}
                    </div>
                    <div style="font-family:var(--sz-font-body);font-size:12px;color:rgba(255,255,255,.5);margin-top:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $u?->email }}
                    </div>
                </div>

                {{-- Nav --}}
                <nav style="padding:8px 0;">
                    <ul style="list-style:none; padding:0; margin:0;">
                    @php
                        $sz_nav = [
                            ['icon'=>'mdi-view-dashboard-outline', 'label'=>__('Dashboard'),       'route'=>'tenant.user.home'],
                            ['icon'=>'mdi-package-variant-closed', 'label'=>__('My Orders'),       'route'=>'tenant.user.dashboard.package.order'],
                            ['icon'=>'mdi-download-outline',       'label'=>__('Downloads'),       'route'=>'tenant.user.dashboard.download.list'],
                            ['icon'=>'mdi-ticket-outline',         'label'=>__('Support Tickets'), 'route'=>'tenant.user.home.support.tickets'],
                            ['icon'=>'mdi-account-outline',        'label'=>__('Manage Account'),  'route'=>'tenant.user.home.manage.account'],
                            ['icon'=>'mdi-lock-outline',           'label'=>__('Change Password'), 'route'=>'tenant.user.home.change.password'],
                        ];
                    @endphp

                    @foreach($sz_nav as $nav)
                    <li>
                        <a href="{{ !is_null(tenant()) ? route($nav['route']) : '#' }}"
                           class="sz-dash-nav-link {{ request()->routeIs($nav['route']) ? 'active' : '' }}">
                            <i class="mdi {{ $nav['icon'] }}"></i>
                            <span>{{ $nav['label'] }}</span>
                        </a>
                    </li>
                    @endforeach

                    @php do_action('nazmart:user_dashboard_sidebar') @endphp

                    <li>
                        <a href="{{ theme_logout_url() }}" class="sz-dash-nav-link sz-dash-logout">
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
$('#sz_dash_toggle').on('click', function(){
    $('#sz_dash_sidebar').toggleClass('open');
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
