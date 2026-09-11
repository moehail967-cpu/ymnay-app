@extends('tenant.frontend.frontend-page-master')
@section('title') @yield('dash-title', __('Dashboard')) @endsection

@section('style')
<style>
/* ── LuxeGems Dashboard ──────────────────────────────── */
.lg-dash-wrap { display:flex; gap:0; min-height:60vh; padding:40px 0 80px; }
.lg-dash-sidebar { width:240px; min-width:240px; margin-right:32px; }
.lg-dash-nav { background:var(--lx-card); border:1px solid var(--lx-border); }
.lg-dash-nav-user { padding:24px 20px; border-bottom:1px solid var(--lx-border); }
.lg-dash-nav-avatar { width:48px;height:48px;border-radius:50%;background:var(--lx-surface);border:1px solid var(--lx-gold);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:600;color:var(--lx-gold);margin-bottom:10px; }
.lg-dash-nav-name { font-size:14px;font-weight:400;color:var(--lx-white);letter-spacing:.5px; }
.lg-dash-nav-email { font-size:11px;color:var(--lx-muted);margin-top:2px; }
.lg-dash-nav-link, nav li.list > a { display:flex;align-items:center;gap:10px;padding:12px 20px;font-size:12px;font-weight:400;letter-spacing:1.5px;text-transform:uppercase;color:var(--lx-muted);text-decoration:none;transition:all .2s;border-left:2px solid transparent; }
nav li.list { list-style: none; }
nav li.list > a { display: flex; align-items: center; gap: 10px; padding: 12px 20px; font-size: 13px; color: inherit; text-decoration: none; border-left: 2px solid transparent; }
nav li.list > a:hover { color: inherit; opacity: 0.8; }
.lg-dash-nav-link:hover, nav li.list > a:hover { color:var(--lx-gold);background:rgba(201,168,76,.05); }
.lg-dash-nav-link.active, nav li.list > a.active { color:var(--lx-gold);border-left-color:var(--lx-gold);background:rgba(201,168,76,.08); }
.lg-dash-nav-link i { font-size:16px;width:20px;text-align:center; }
.lg-dash-main { flex:1;min-width:0; }
.lg-dash-card { background:var(--lx-card);border:1px solid var(--lx-border);padding:28px; }
.lg-dash-card-title { font-size:10px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--lx-gold);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--lx-border); }
.lg-dash-table { width:100%;border-collapse:collapse;font-size:13px; }
.lg-dash-table th { font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--lx-muted);padding:8px 12px;border-bottom:1px solid var(--lx-border);text-align:left; }
.lg-dash-table td { padding:12px;border-bottom:1px solid var(--lx-border);color:var(--lx-muted); }
.lg-dash-table tr:last-child td { border-bottom:none; }
.lg-dash-table a { color:var(--lx-gold);text-decoration:none; }
.lg-dash-badge { display:inline-block;padding:3px 10px;font-size:10px;font-weight:600;letter-spacing:1px;border:1px solid; }
.lg-dash-badge-pending { color:#f59e0b;border-color:#f59e0b; }
.lg-dash-badge-processing { color:#3b82f6;border-color:#3b82f6; }
.lg-dash-badge-delivered { color:#10b981;border-color:#10b981; }
.lg-dash-badge-cancelled { color:#ef4444;border-color:#ef4444; }
.lg-dash-btn { display:inline-flex;align-items:center;gap:6px;font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;padding:8px 18px;border:1px solid;cursor:pointer;text-decoration:none;transition:all .2s; }
.lg-dash-btn-gold { background:var(--lx-gold);color:var(--lx-black);border-color:var(--lx-gold); }
.lg-dash-btn-gold:hover { background:var(--lx-gold-l);color:var(--lx-black); }
.lg-dash-btn-outline { background:transparent;color:var(--lx-muted);border-color:var(--lx-border); }
.lg-dash-btn-outline:hover { border-color:var(--lx-gold);color:var(--lx-gold); }
.lg-dash-input { width:100%;background:var(--lx-surface);border:1px solid var(--lx-border);color:var(--lx-white);padding:10px 14px;font-size:13px;font-family:inherit;outline:none;transition:border-color .2s; }
.lg-dash-input:focus { border-color:var(--lx-gold); }
select.lg-dash-input option { background:var(--lx-dark); }
.lg-dash-label { font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--lx-muted);display:block;margin-bottom:6px; }
@media(max-width:768px) { .lg-dash-wrap{flex-direction:column;} .lg-dash-sidebar{width:100%;min-width:0;margin-right:0;margin-bottom:24px;} }
</style>
@endsection

@section('content')
<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ url('/') }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="active">@yield('dash-title', __('Dashboard'))</span>
        </div>
    </div>
</div>

<div class="container">
    <div class="lg-dash-wrap">
        {{-- Sidebar --}}
        <aside class="lg-dash-sidebar">
            <nav class="lg-dash-nav">
                <div class="lg-dash-nav-user">
                    <div class="lg-dash-nav-avatar">{{ strtoupper(substr(auth('web')->user()?->name ?? 'U', 0, 1)) }}</div>
                    <div class="lg-dash-nav-name">{{ auth('web')->user()?->name }}</div>
                    <div class="lg-dash-nav-email">{{ auth('web')->user()?->email }}</div>
                </div>
                <a href="{{ route('tenant.user.home') }}" class="lg-dash-nav-link {{ request()->routeIs('tenant.user.home') ? 'active' : '' }}">
                    <i class="las la-tachometer-alt"></i> {{ __('Dashboard') }}
                </a>
                <a href="{{ route('tenant.user.dashboard.package.order') }}" class="lg-dash-nav-link {{ request()->routeIs('tenant.user.dashboard.package.order') ? 'active' : '' }}">
                    <i class="las la-box"></i> {{ __('My Orders') }}
                </a>
                <a href="{{ route('tenant.user.dashboard.download.list') }}" class="lg-dash-nav-link {{ request()->routeIs('tenant.user.dashboard.download.list') ? 'active' : '' }}">
                    <i class="las la-download"></i> {{ __('Downloads') }}
                </a>
                <a href="{{ route('tenant.user.home.manage.account') }}" class="lg-dash-nav-link {{ request()->routeIs('tenant.user.home.manage.account') ? 'active' : '' }}">
                    <i class="las la-user-cog"></i> {{ __('My Account') }}
                </a>
                <a href="{{ route('tenant.user.home.change.password') }}" class="lg-dash-nav-link {{ request()->routeIs('tenant.user.home.change.password') ? 'active' : '' }}">
                    <i class="las la-lock"></i> {{ __('Change Password') }}
                </a>
                <a href="{{ route('tenant.user.home.support.tickets') }}" class="lg-dash-nav-link {{ request()->routeIs('tenant.user.home.support.tickets') ? 'active' : '' }}">
                    <i class="las la-headset"></i> {{ __('Support') }}
                </a>
                @php do_action('nazmart:user_dashboard_sidebar') @endphp
                <a href="{{ route('tenant.user.logout') }}" class="lg-dash-nav-link" onclick="event.preventDefault();document.getElementById('lg-logout-form').submit();">
                    <i class="las la-sign-out-alt"></i> {{ __('Sign Out') }}
                </a>
                <form id="lg-logout-form" action="{{ route('tenant.user.logout') }}" method="POST" style="display:none;">@csrf</form>
            </nav>
        </aside>

        {{-- Main content --}}
        <main class="lg-dash-main">
            @yield('dash-content')
        </main>
    </div>
</div>
@endsection

@section('scripts')
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
@yield('dash-scripts')
@endsection
