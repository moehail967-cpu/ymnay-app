@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('style')
<x-media-upload.css/>
<style>
.gl-dash-wrap { display:flex; gap:24px; min-height:60vh; align-items:flex-start; }

.gl-dash-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: #fff;
    border: 1px solid var(--gl-border);
    border-radius: var(--gl-radius);
    position: sticky;
    top: 90px;
    overflow: hidden;
    box-shadow: var(--gl-shadow);
}
.gl-dash-user-info {
    padding: 20px 16px;
    border-bottom: 1px solid var(--gl-border);
    background: var(--gl-gold-pale);
    display: flex;
    align-items: center;
    gap: 12px;
}
.gl-dash-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: var(--gl-gold);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 700;
    flex-shrink: 0;
}
.gl-dash-username { font-size: 13px; font-weight: 700; color: var(--gl-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.gl-dash-email { font-size: 11px; color: var(--gl-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.gl-dash-nav { list-style: none; padding: 8px 0; margin: 0; }
.gl-dash-nav li a {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px;
    font-size: 13px; font-weight: 600; color: var(--gl-muted);
    text-decoration: none;
    transition: all .15s;
    border-left: 3px solid transparent;
}
.gl-dash-nav li a:hover,
.gl-dash-nav li.active a {
    background: var(--gl-gold-pale);
    color: var(--gl-dark);
    border-left-color: var(--gl-gold);
}
.gl-dash-nav li a i { font-size: 17px; width: 20px; text-align: center; flex-shrink: 0; }
.gl-dash-nav li.logout a { color: #e53e3e; }
.gl-dash-nav li.logout a:hover { background: rgba(229,62,62,.06); border-left-color: #e53e3e; color: #c53030; }

.gl-dash-content { flex: 1; min-width: 0; }

.gl-dash-mobile-toggle {
    display: none;
    align-items: center; gap: 8px;
    background: #fff; border: 1.5px solid var(--gl-border);
    border-radius: var(--gl-radius); padding: 8px 14px;
    color: var(--gl-dark); font-size: 13px; font-weight: 700;
    cursor: pointer; margin-bottom: 16px;
    box-shadow: var(--gl-shadow);
}

@media (max-width: 991px) {
    .gl-dash-wrap { flex-direction: column; }
    .gl-dash-sidebar { width: 100%; position: relative; top: 0; display: none; }
    .gl-dash-sidebar.open { display: block; }
    .gl-dash-mobile-toggle { display: flex; }
}
</style>
@yield('dash-style')
@endsection

@section('content')

<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:28px 0 20px;">
    <div class="container">
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div style="background:#f8f7f5;padding:40px 0 72px;">
    <div class="container">

        <button class="gl-dash-mobile-toggle" id="gl-dash-toggle">
            <i class="mdi mdi-menu"></i> {{ __('Dashboard Menu') }}
        </button>

        <div class="gl-dash-wrap">

            {{-- Sidebar --}}
            <div class="gl-dash-sidebar" id="gl-dash-sidebar">
                @php $u = auth('web')->user(); @endphp
                <div class="gl-dash-user-info">
                    <div class="gl-dash-avatar">{{ strtoupper(substr($u?->name ?? 'U', 0, 1)) }}</div>
                    <div style="min-width:0;">
                        <div class="gl-dash-username">{{ $u?->name }}</div>
                        <div class="gl-dash-email">{{ $u?->email }}</div>
                    </div>
                </div>
                <ul class="gl-dash-nav">
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

                    @php do_action('nazmart:user_dashboard_sidebar') @endphp

                    <li class="logout">
                        <a href="{{ theme_logout_url() }}">
                            <i class="mdi mdi-logout-variant"></i> {{ __('Logout') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Content --}}
            <div class="gl-dash-content">
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
$('#gl-dash-toggle').on('click', function(){
    $('#gl-dash-sidebar').toggleClass('open');
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
