@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('style')
<x-media-upload.css/>
<style>
/* --- Maison Dashboard Layout --- */
.ms-dash-wrap { display:flex; gap:0; min-height:60vh; align-items:flex-start; }
.ms-dash-content { flex:1; min-width:0; }

/* Plugin sidebar items style fix */
.ms-dash-sidebar nav ul li.list > a { display:flex; align-items:center; gap:10px; padding:11px 20px; font-size:13px; color:var(--ms-charcoal); text-decoration:none; transition:all .2s; border-left:3px solid transparent; }
.ms-dash-sidebar nav ul li.list > a:hover { background:var(--ms-warm); color:var(--ms-linen-d); }
.ms-dash-sidebar nav ul li.list.active > a { border-left-color:var(--ms-linen); background:var(--ms-warm); color:var(--ms-linen-d); font-weight:600; }
.ms-dash-nav-link i, .ms-dash-sidebar nav ul li.list > a i { font-size:17px; width:20px; text-align:center; }

/* Mobile sidebar toggle */
.ms-dash-mobile-toggle {
    display:none;
    align-items:center; gap:8px;
    background:#fff; border:1px solid var(--ms-border);
    border-radius:var(--ms-radius); padding:10px 16px;
    color:var(--ms-dark); font-size:13px; font-weight:600;
    cursor:pointer; margin-bottom:16px; width:100%;
}
@media (max-width:991px){
    .ms-dash-wrap { flex-direction:column; }
    .ms-dash-sidebar { width:100% !important; position:relative !important; top:0 !important; display:none; }
    .ms-dash-sidebar.open { display:block; }
    .ms-dash-mobile-toggle { display:flex; }
}
nav li.list { list-style: none; margin: 0; padding: 0; }
</style>
@endsection

@section('content')

{{-- Page Banner --}}
<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:44px 0 32px;text-align:center;">
    <div class="container">
        <div style="font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ __('Account') }}</div>
        <h1 style="font-size:clamp(22px,4vw,36px);font-weight:300;color:var(--ms-dark);margin:0 auto 12px;line-height:1.2;">{{ __('My Account') }}</h1>
        <div style="display:flex;align-items:center;justify-content:center;gap:8px;font-size:12px;color:var(--ms-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;"
               onmouseover="this.style.color='var(--ms-olive)'" onmouseout="this.style.color='var(--ms-linen-d)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<section style="background:var(--ms-cream);padding:48px 0 72px;">
    <div class="container">

        <button class="ms-dash-mobile-toggle" id="ms-dash-toggle">
            <i class="mdi mdi-menu" style="font-size:18px;"></i> {{ __('Dashboard Menu') }}
        </button>

        <div class="ms-dash-wrap gap-4">

            {{-- Sidebar --}}
            <div class="ms-dash-sidebar" id="ms-dash-sidebar"
                 style="width:240px;flex-shrink:0;background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);box-shadow:var(--ms-shadow);position:sticky;top:90px;">
                @php $u = auth('web')->user(); @endphp
                {{-- User Info --}}
                <div style="padding:20px 18px;border-bottom:1px solid var(--ms-border);display:flex;align-items:center;gap:12px;">
                    <div style="width:42px;height:42px;border-radius:50%;background:var(--ms-linen);color:#fff;display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($u?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--ms-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $u?->name }}</div>
                        <div style="font-size:11px;color:var(--ms-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $u?->email }}</div>
                    </div>
                </div>

                {{-- Nav --}}
                <nav style="padding:8px 0;">
                    <ul style="list-style:none; padding:0; margin:0;">
                    @php
                        $nav_items = [
                            ['icon'=>'mdi-view-dashboard-outline', 'label'=>__('Dashboard'),       'route'=>'tenant.user.home'],
                            ['icon'=>'mdi-package-variant-closed', 'label'=>__('My Orders'),       'route'=>'tenant.user.dashboard.package.order'],
                            ['icon'=>'mdi-account-outline',        'label'=>__('My Account'),      'route'=>'tenant.user.home.manage.account'],
                            ['icon'=>'mdi-lock-outline',           'label'=>__('Change Password'), 'route'=>'tenant.user.home.change.password'],
                            ['icon'=>'mdi-download-outline',       'label'=>__('Downloads'),       'route'=>'tenant.user.dashboard.download.list'],
                            ['icon'=>'mdi-ticket-outline',         'label'=>__('Support Tickets'), 'route'=>'tenant.user.home.support.tickets'],
                        ];
                    @endphp

                    @foreach($nav_items as $nav)
                    <li>
                        <a href="{{ !is_null(tenant()) ? route($nav['route']) : '#' }}"
                           class="ms-dash-nav-link {{ request()->routeIs($nav['route']) ? 'active' : '' }}">
                            <i class="mdi {{ $nav['icon'] }}"></i>
                            <span>{{ $nav['label'] }}</span>
                        </a>
                    </li>
                    @endforeach

                    {{-- Plugin sidebar items --}}
                    @php do_action('nazmart:user_dashboard_sidebar') @endphp

                    <li>
                        <a href="{{ theme_logout_url() }}"
                           class="ms-dash-nav-link"
                           style="color:#c0725a !important;"
                           onmouseover="this.style.background='rgba(192,114,90,.06)';this.style.borderLeftColor='#c0725a'"
                           onmouseout="this.style.background='transparent';this.style.borderLeftColor='transparent'">
                            <i class="mdi mdi-logout-variant"></i>
                            <span>{{ __('Sign Out') }}</span>
                        </a>
                    </li>
                    </ul>
                </nav>
            </div>

            {{-- Content --}}
            <div class="ms-dash-content">
                <x-error-msg/>
                <x-flash-msg/>
                @yield('dashboard_content')
            </div>

        </div>
    </div>
</section>

@endsection

@section('scripts')
<x-media-upload.js/>
<script>
$('#ms-dash-toggle').on('click', function(){
    $('#ms-dash-sidebar').toggleClass('open');
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
