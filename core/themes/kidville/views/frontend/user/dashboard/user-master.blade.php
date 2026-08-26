@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection

@section('style')
<x-media-upload.css/>
<style>
.kv-dash-wrap { display:flex; gap:24px; min-height:60vh; align-items:flex-start; }

.kv-dash-sidebar {
    width: 250px;
    flex-shrink: 0;
    background: var(--kv-white);
    border: 2px solid var(--kv-border);
    border-radius: var(--kv-radius);
    position: sticky;
    top: 90px;
    overflow: hidden;
    box-shadow: var(--kv-shadow);
}

.kv-dash-user-info {
    padding: 20px 16px;
    border-bottom: 2px solid var(--kv-border);
    background: var(--kv-light);
    display: flex;
    align-items: center;
    gap: 12px;
}

.kv-dash-avatar {
    width: 46px; height: 46px;
    border-radius: 50%;
    background: var(--kv-red);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 800;
    flex-shrink: 0;
}

.kv-dash-username { font-size: 14px; font-weight: 700; color: var(--kv-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.kv-dash-email { font-size: 11px; color: var(--kv-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }

.kv-dash-nav { list-style: none; padding: 8px 0; margin: 0; }
.kv-dash-nav li a {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 18px;
    font-size: 13px; font-weight: 700; color: var(--kv-muted);
    text-decoration: none;
    transition: all .15s;
    border-left: 4px solid transparent;
}
.kv-dash-nav li a:hover,
.kv-dash-nav li.active a {
    background: var(--kv-bg);
    color: var(--kv-dark);
    border-left-color: var(--kv-red);
}
.kv-dash-nav li a i { font-size: 18px; width: 22px; text-align: center; flex-shrink: 0; }
.kv-dash-nav li.logout a { color: var(--kv-red); }
.kv-dash-nav li.logout a:hover { background: rgba(244,67,54,.06); border-left-color: var(--kv-red); color: #c62828; }

.kv-dash-content { flex: 1; min-width: 0; }

.kv-dash-mobile-toggle {
    display: none;
    align-items: center; gap: 8px;
    background: var(--kv-white); border: 2px solid var(--kv-border);
    border-radius: var(--kv-radius); padding: 10px 16px;
    color: var(--kv-dark); font-size: 14px; font-weight: 700;
    cursor: pointer; margin-bottom: 16px;
    box-shadow: var(--kv-shadow);
}

@media (max-width: 991px) {
    .kv-dash-wrap { flex-direction: column; }
    .kv-dash-sidebar { width: 100%; position: relative; top: 0; display: none; }
    .kv-dash-sidebar.open { display: block; }
    .kv-dash-mobile-toggle { display: flex; }
}
</style>
@yield('dash-style')
@endsection

@section('content')

<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ __('My Account') }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div style="background:var(--kv-bg);padding:40px 0 72px;">
    <div class="container">

        <button class="kv-dash-mobile-toggle" id="kv-dash-toggle">
            <i class="las la-bars"></i> {{ __('Dashboard Menu') }}
        </button>

        <div class="kv-dash-wrap">

            {{-- Sidebar --}}
            <div class="kv-dash-sidebar" id="kv-dash-sidebar">
                @php $u = auth('web')->user(); @endphp
                <div class="kv-dash-user-info">
                    <div class="kv-dash-avatar">{{ strtoupper(substr($u?->name ?? 'U', 0, 1)) }}</div>
                    <div style="min-width:0;">
                        <div class="kv-dash-username">{{ $u?->name }}</div>
                        <div class="kv-dash-email">{{ $u?->email }}</div>
                    </div>
                </div>
                <ul class="kv-dash-nav">
                    <li class="{{ request()->routeIs('tenant.user.home') ? 'active' : '' }}">
                        <a href="{{ theme_user_dashboard_url() }}">
                            <i class="las la-tachometer-alt"></i> {{ __('Dashboard') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.dashboard.package.order') ? 'active' : '' }}">
                        <a href="{{ theme_user_package_orders_url() }}">
                            <i class="las la-box"></i> {{ __('My Orders') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.dashboard.download.list') ? 'active' : '' }}">
                        <a href="{{ theme_user_downloads_url() }}">
                            <i class="las la-download"></i> {{ __('Downloads') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.home.support.tickets', 'tenant.frontend.support.ticket') ? 'active' : '' }}">
                        <a href="{{ theme_user_tickets_url() }}">
                            <i class="las la-ticket-alt"></i> {{ __('Support Tickets') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.home.manage.account') ? 'active' : '' }}">
                        <a href="{{ theme_user_manage_account_url() }}">
                            <i class="las la-user"></i> {{ __('My Account') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('tenant.user.home.change.password') ? 'active' : '' }}">
                        <a href="{{ route('tenant.user.home.change.password') }}">
                            <i class="las la-lock"></i> {{ __('Change Password') }}
                        </a>
                    </li>

                    @php do_action('nazmart:user_dashboard_sidebar') @endphp

                    <li class="logout">
                        <a href="{{ theme_logout_url() }}">
                            <i class="las la-sign-out-alt"></i> {{ __('Logout') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Content --}}
            <div class="kv-dash-content">
                <x-error-msg/>
                <x-flash-msg/>
                @yield('section')
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<x-media-upload.js/>
<script>
$('#kv-dash-toggle').on('click', function(){
    $('#kv-dash-sidebar').toggleClass('open');
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
