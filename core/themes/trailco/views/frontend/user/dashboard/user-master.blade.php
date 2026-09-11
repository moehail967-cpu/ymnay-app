@extends('tenant.frontend.frontend-page-master')

@section('style')
@parent
<x-media-upload.css/>
<style>
/* ── TrailCo Dashboard Layout ── */
.tr-dash-wrap { display:flex; gap:24px; align-items:flex-start; padding:36px 0 60px; }

/* Sidebar */
.tr-dash-sidebar {
    width:240px; flex-shrink:0;
    background:#fff;
    border:1px solid var(--tr-border);
    border-radius:var(--tr-radius);
    overflow:hidden;
    position:sticky; top:90px;
    box-shadow:var(--tr-shadow);
}
.tr-dash-user-info {
    padding:20px 16px;
    background:var(--tr-bark);
    display:flex; align-items:center; gap:12px;
}
.tr-dash-avatar {
    width:44px; height:44px; border-radius:50%;
    background:var(--tr-olive);
    border:2px solid rgba(255,255,255,.35);
    color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-size:18px; font-weight:800; flex-shrink:0;
}
.tr-dash-username { font-size:14px; font-weight:700; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.tr-dash-useremail { font-size:11px; color:rgba(255,255,255,.65); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }

/* Nav */
.tr-dash-nav { list-style:none; padding:8px 0; margin:0; }
.tr-dash-nav li a {
    display:flex; align-items:center; gap:10px;
    padding:11px 18px;
    font-size:13px; font-weight:600;
    color:var(--tr-stone);
    text-decoration:none;
    transition:all .15s;
    border-left:3px solid transparent;
}
.tr-dash-nav li a:hover,
.tr-dash-nav li.active a {
    background:rgba(92,122,62,.09);
    color:var(--tr-olive);
    border-left-color:var(--tr-olive);
}
.tr-dash-nav li a .mdi { font-size:18px; width:20px; text-align:center; }
.tr-dash-nav .tr-dash-divider { height:1px; background:var(--tr-border); margin:4px 0; }
.tr-dash-nav li.logout a { color:#c0392b; }
.tr-dash-nav li.logout a:hover { background:rgba(192,57,43,.08); border-left-color:#c0392b; color:#c0392b; }

/* Content area */
.tr-dash-content { flex:1; min-width:0; }

/* Cards */
.tr-dash-card {
    background:#fff;
    border:1px solid var(--tr-border);
    border-radius:var(--tr-radius);
    overflow:hidden;
    margin-bottom:20px;
    box-shadow:var(--tr-shadow);
}
.tr-dash-card-header {
    padding:14px 20px;
    border-bottom:1px solid var(--tr-border);
    font-weight:700; font-size:14px;
    color:var(--tr-bark);
    display:flex; align-items:center; justify-content:space-between;
    background:var(--tr-cream);
}
.tr-dash-card-body { padding:20px; }

/* Table */
.tr-dash-table { width:100%; border-collapse:collapse; font-size:13px; }
.tr-dash-table thead tr { background:var(--tr-cream); border-bottom:1px solid var(--tr-border); }
.tr-dash-table thead th { padding:11px 14px; text-align:left; color:var(--tr-stone); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; }
.tr-dash-table tbody tr { border-bottom:1px solid var(--tr-border); transition:background .15s; }
.tr-dash-table tbody tr:last-child { border-bottom:none; }
.tr-dash-table tbody tr:hover { background:var(--tr-cream); }
.tr-dash-table tbody td { padding:13px 14px; color:var(--tr-bark); vertical-align:middle; }

/* Badge */
.tr-dash-badge { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; display:inline-block; }

/* Stat cards */
.tr-dash-stat {
    background:#fff;
    border:1px solid var(--tr-border);
    border-radius:var(--tr-radius);
    padding:20px;
    display:flex; align-items:center; gap:14px;
    height:100%;
    box-shadow:var(--tr-shadow);
}
.tr-dash-stat-icon {
    width:48px; height:48px;
    border-radius:10px;
    background:rgba(92,122,62,.12);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.tr-dash-stat-icon .mdi { font-size:24px; color:var(--tr-olive); }
.tr-dash-stat-value { font-size:26px; font-weight:800; color:var(--tr-bark); line-height:1; }
.tr-dash-stat-label { font-size:12px; color:var(--tr-stone); margin-top:3px; }

/* Buttons */
.tr-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 16px; border-radius:var(--tr-radius);
    font-size:13px; font-weight:700; cursor:pointer;
    border:none; text-decoration:none; transition:all .15s;
}
.tr-btn-primary { background:var(--tr-olive); color:#fff; }
.tr-btn-primary:hover { background:#4a6632; color:#fff; }
.tr-btn-terra { background:var(--tr-terra); color:#fff; }
.tr-btn-terra:hover { background:#a34a22; color:#fff; }
.tr-btn-outline {
    background:transparent; color:var(--tr-olive);
    border:1.5px solid var(--tr-olive);
}
.tr-btn-outline:hover { background:var(--tr-olive); color:#fff; }
.tr-btn-sm { padding:5px 12px; font-size:12px; }

/* Mobile toggle */
.tr-dash-mobile-toggle {
    display:none; align-items:center; gap:8px;
    background:var(--tr-olive); border:none;
    border-radius:var(--tr-radius); padding:10px 16px;
    color:#fff; font-size:13px; font-weight:700; cursor:pointer;
    margin-bottom:16px;
}
@media (max-width:991px) {
    .tr-dash-wrap { flex-direction:column; }
    .tr-dash-sidebar { width:100%; position:relative; top:0; display:none; }
    .tr-dash-sidebar.open { display:block; }
    .tr-dash-mobile-toggle { display:flex; }
}
</style>
@endsection

@section('content')
{{-- Page Banner --}}
<div style="background:var(--tr-bark);padding:32px 0;">
    <div class="container">
        <h1 style="font-size:26px;font-weight:800;color:#fff;margin-bottom:8px;">{{ __('My Account') }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
            <a href="{{ theme_home_url() }}" style="color:rgba(255,255,255,.7);text-decoration:none;">{{ __('Home') }}</a>
            <span style="color:rgba(255,255,255,.45);"><i class="mdi mdi-chevron-right"></i></span>
            <span style="color:var(--tr-sand);">{{ __('Account') }}</span>
        </div>
    </div>
</div>

<div class="container">
    <div class="tr-dash-wrap">

        <button class="tr-dash-mobile-toggle" onclick="document.querySelector('.tr-dash-sidebar').classList.toggle('open')">
            <i class="mdi mdi-menu"></i> {{ __('Account Menu') }}
        </button>

        <aside class="tr-dash-sidebar">
            <div class="tr-dash-user-info">
                <div class="tr-dash-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div style="min-width:0;">
                    <div class="tr-dash-username">{{ auth()->user()->name ?? '' }}</div>
                    <div class="tr-dash-useremail">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <ul class="tr-dash-nav">
                <li class="{{ request()->routeIs('tenant.user.home') ? 'active' : '' }}">
                    <a href="{{ theme_user_dashboard_url() }}">
                        <i class="mdi mdi-view-dashboard-outline"></i> {{ __('Dashboard') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.order.list') ? 'active' : '' }}">
                    <a href="{{ route('tenant.user.dashboard.package.order') }}">
                        <i class="mdi mdi-package-variant-closed"></i> {{ __('Orders') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.download.list') ? 'active' : '' }}">
                    <a href="{{ theme_user_downloads_url() }}">
                        <i class="mdi mdi-download-outline"></i> {{ __('Downloads') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('tenant.user.support.ticket.*') ? 'active' : '' }}">
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
                <li class="{{ request()->routeIs('tenant.user.package.order.list') ? 'active' : '' }}">
                    <a href="{{ theme_user_package_orders_url() }}">
                        <i class="mdi mdi-cube-outline"></i> {{ __('Packages') }}
                    </a>
                </li>
                @endif
                <li class="tr-dash-divider"></li>
                @php do_action('nazmart:user_dashboard_sidebar') @endphp

                <li class="logout">
                    <a href="{{ theme_logout_url() }}">
                        <i class="mdi mdi-logout-variant"></i> {{ __('Log Out') }}
                    </a>
                </li>
            </ul>
        </aside>

        <div class="tr-dash-content">
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
