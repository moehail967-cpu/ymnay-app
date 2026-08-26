@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection

@section('style')
<x-media-upload.css/>
<style>
.gc-dash-wrap { display:flex; gap:24px; min-height:60vh; align-items:flex-start; }

.gc-dash-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: var(--gc-ivory);
    border: 1px solid var(--gc-border);
    border-radius: var(--gc-radius);
    position: sticky;
    top: 90px;
    overflow: hidden;
    box-shadow: var(--gc-shadow);
}
.gc-dash-user-info {
    padding: 20px 16px;
    border-bottom: 1px solid var(--gc-border);
    background: var(--gc-warm);
    display: flex;
    align-items: center;
    gap: 12px;
}
.gc-dash-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: var(--gc-rose);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 400; font-family: Georgia, serif;
    flex-shrink: 0;
}
.gc-dash-username { font-size: 13px; font-weight: 400; color: var(--gc-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: Georgia, serif; font-style: italic; }
.gc-dash-email { font-size: 11px; color: var(--gc-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.gc-dash-nav { list-style: none; padding: 8px 0; margin: 0; }
.gc-dash-nav li a {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px;
    font-size: 13px; font-weight: 400; color: var(--gc-muted);
    text-decoration: none;
    transition: all .15s;
    border-left: 3px solid transparent;
    font-style: italic;
}
.gc-dash-nav li a:hover,
.gc-dash-nav li.active a {
    background: var(--gc-warm);
    color: var(--gc-dark);
    border-left-color: var(--gc-rose);
}
.gc-dash-nav li a i { font-size: 16px; width: 20px; text-align: center; flex-shrink: 0; }
.gc-dash-nav li.logout a { color: #e53e3e; }
.gc-dash-nav li.logout a:hover { background: rgba(229,62,62,.06); border-left-color: #e53e3e; color: #c53030; }

.gc-dash-content { flex: 1; min-width: 0; }

.gc-dash-mobile-toggle {
    display: none;
    align-items: center; gap: 8px;
    background: var(--gc-ivory); border: 1.5px solid var(--gc-border);
    border-radius: var(--gc-radius); padding: 8px 14px;
    color: var(--gc-dark); font-size: 13px; font-weight: 400;
    cursor: pointer; margin-bottom: 16px;
    box-shadow: var(--gc-shadow); font-family: Georgia, serif; font-style: italic;
}

@media (max-width: 991px) {
    .gc-dash-wrap { flex-direction: column; }
    .gc-dash-sidebar { width: 100%; position: relative; top: 0; display: none; }
    .gc-dash-sidebar.open { display: block; }
    .gc-dash-mobile-toggle { display: flex; }
}
</style>
@yield('dash-style')
@endsection

@section('content')

<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:28px 0 20px;">
    <div class="container">
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <span>{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div style="background:#f5f3f0;padding:40px 0 72px;">
    <div class="container">

        <button class="gc-dash-mobile-toggle" id="gc-dash-toggle">
            <i class="las la-bars"></i> {{ __('Dashboard Menu') }}
        </button>

        <div class="gc-dash-wrap">

            {{-- Sidebar --}}
            <div class="gc-dash-sidebar" id="gc-dash-sidebar">
                @php $u = auth('web')->user(); @endphp
                <div class="gc-dash-user-info">
                    <div class="gc-dash-avatar">{{ strtoupper(substr($u?->name ?? 'U', 0, 1)) }}</div>
                    <div style="min-width:0;">
                        <div class="gc-dash-username">{{ $u?->name }}</div>
                        <div class="gc-dash-email">{{ $u?->email }}</div>
                    </div>
                </div>
                <ul class="gc-dash-nav">
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
            <div class="gc-dash-content">
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
$('#gc-dash-toggle').on('click', function(){
    $('#gc-dash-sidebar').toggleClass('open');
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
