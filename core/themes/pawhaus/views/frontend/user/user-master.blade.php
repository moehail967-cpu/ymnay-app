@extends('tenant.frontend.frontend-page-master')

@section('style')
<x-media-upload.css/>
<style>
.ph-dash-sidebar { background:var(--ph-white);border:2px solid var(--ph-border);border-radius:var(--ph-radius);padding:20px;position:sticky;top:90px; }
.ph-dash-avatar { width:72px;height:72px;border-radius:50%;background:var(--ph-terra);color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;margin:0 auto 12px;border:3px solid var(--ph-terra-light); }
.ph-dash-name { font-size:15px;font-weight:800;color:var(--ph-dark);text-align:center;margin-bottom:4px; }
.ph-dash-email { font-size:12px;color:var(--ph-muted);text-align:center;margin-bottom:20px;word-break:break-all; }
.ph-dash-nav { list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:4px; }
.ph-dash-nav a, .ph-dash-nav li.list > a { display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:var(--ph-radius-sm);font-size:13px;font-weight:700;color:var(--ph-muted);text-decoration:none;transition:all .2s; }
.ph-dash-nav a:hover, .ph-dash-nav a.active, .ph-dash-nav li.list > a:hover, .ph-dash-nav li.list.active > a { background:var(--ph-terra-light);color:var(--ph-terra); }
.ph-dash-nav a i, .ph-dash-nav li.list > a i { font-size:17px;width:20px;text-align:center; }
.ph-dash-card { background:var(--ph-white);border:2px solid var(--ph-border);border-radius:var(--ph-radius);padding:24px;margin-bottom:20px; }
.ph-dash-card-title { font-size:16px;font-weight:800;color:var(--ph-dark);margin-bottom:18px;padding-bottom:12px;border-bottom:2px solid var(--ph-border);display:flex;align-items:center;gap:8px; }
.ph-dash-card-title i { color:var(--ph-terra); }
.ph-dash-table { width:100%;border-collapse:collapse; }
.ph-dash-table th { font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:var(--ph-muted);padding:10px 14px;background:var(--ph-cream);border-bottom:2px solid var(--ph-border); }
.ph-dash-table td { font-size:13px;color:var(--ph-dark);padding:12px 14px;border-bottom:1px solid var(--ph-border); }
.ph-dash-table tr:last-child td { border-bottom:none; }
.ph-badge-pill { display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800; }
.ph-badge-success { background:var(--ph-sage-light);color:var(--ph-sage); }
.ph-badge-warning { background:#FEF3C7;color:#D97706; }
.ph-badge-danger { background:#FEE2E2;color:#DC2626; }
.ph-badge-muted { background:var(--ph-cream);color:var(--ph-muted); }
</style>
@endsection

@section('content')
<div class="ph-page-banner">
    <div class="container">
        <h1>@yield('dash-title', __('My Account'))</h1>
        <div class="ph-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">@yield('dash-title', __('My Account'))</span>
        </div>
    </div>
</div>

<div class="container" style="padding:40px 0 80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="ph-dash-sidebar">
                <div class="ph-dash-avatar">{{ strtoupper(substr(auth('web')->user()?->name ?? 'U', 0, 1)) }}</div>
                <div class="ph-dash-name">{{ auth('web')->user()?->name }}</div>
                <div class="ph-dash-email">{{ auth('web')->user()?->email }}</div>
                <ul class="ph-dash-nav">
                    <li><a href="{{ route('tenant.user.home') }}" class="{{ request()->routeIs('tenant.user.home') ? 'active' : '' }}">
                        <i class="las la-home"></i> {{ __('Dashboard') }}
                    </a></li>
                    <li><a href="{{ route('tenant.user.dashboard.package.order') }}" class="{{ request()->routeIs('tenant.user.dashboard.package.order') ? 'active' : '' }}">
                        <i class="las la-shopping-bag"></i> {{ __('My Orders') }}
                    </a></li>
                    <li><a href="{{ route('tenant.user.dashboard.download.list') }}" class="{{ request()->routeIs('tenant.user.dashboard.download.list') ? 'active' : '' }}">
                        <i class="las la-download"></i> {{ __('Downloads') }}
                    </a></li>
                    <li><a href="{{ route('tenant.user.home.support.tickets') }}" class="{{ request()->routeIs('tenant.user.home.support.tickets') ? 'active' : '' }}">
                        <i class="las la-headset"></i> {{ __('Support') }}
                    </a></li>
                    <li><a href="{{ route('tenant.user.home.manage.account') }}" class="{{ request()->routeIs('tenant.user.home.manage.account') ? 'active' : '' }}">
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

        {{-- Content --}}
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
