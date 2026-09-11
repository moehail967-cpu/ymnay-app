<!doctype html>
<html lang="{{ \App\Facades\GlobalLanguage::default_slug() }}" dir="{{ \App\Facades\GlobalLanguage::custom_default_dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- header for PWA -->
    @yield('pwa-header')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @if(!request()->routeIs('tenant.home'))
            @yield('title')  -
        @endif
        {{get_static_option('site_title',__('Xgenious'))}}
        @if(!empty(get_static_option('site_tag_line')))
            - {{get_static_option('site_tag_line')}}
        @endif
    </title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Urbanist:wght@400;500;600;700&display=swap" rel="stylesheet">
    {!! render_favicon_by_id(get_static_option('site_favicon')) !!}

    <!-- Keep: required vendor CSS (Bootstrap base needed for modals/components in inner pages) -->
    <link href="{{ global_asset('assets/landlord/admin/css/landlord.bundle.base.css') }}" rel="stylesheet">
    <link href="{{ global_asset('assets/landlord/admin/css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="{{ global_asset('assets/common/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ global_asset('assets/common/css/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ global_asset('assets/landlord/frontend/css/line-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ global_asset('assets/common/css/toastr.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{global_asset('assets/tenant/frontend/css/flaticon.css')}}">
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/css/tablar-icon.css')}}">
    <link rel="stylesheet" href="{{global_asset('assets/common/css/custom-style.css')}}">

    <link rel="stylesheet" href="{{global_asset('assets/tenant/backend/css/module-fix-style.css')}}">

    <!-- dark mode css  -->
    @if(!empty(get_static_option('dark_mode_for_admin_panel')))
        <link href="{{ global_asset('assets/landlord/admin/css/dark-mode.css') }}" rel="stylesheet">
    @endif

    <!-- rtl mode -->
    @if(\App\Enums\LanguageEnums::getdirection(get_user_lang_direction()) === 'rtl')
        <link href="{{ global_asset('assets/landlord/admin/css/rtl.css') }}" rel="stylesheet">
    @endif

    <!-- Tailwind CSS via browser CDN -->
    <script src="{{global_asset('assets/new-landlord/js/tailwind_browser.js')}}"></script>
    <script src="{{global_asset('assets/new-landlord/js/tailwind-config.js')}}"></script>

    <!-- Admin Panel New Asset System -->
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/admin/css/app.css')}}">
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/admin/css/components/actions.css')}}">
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/admin/css/components/widgets.css')}}">

    @yield('style')
    @pluginAdminStyles

    {{-- RTL structural overrides — inline so they win every cascade battle --}}
    @if(\App\Facades\GlobalLanguage::custom_default_dir() === 'rtl')
    <style>
        html[dir="rtl"] .admin-sidebar { left: auto !important; right: 0 !important; }
        html[dir="rtl"] .admin-content { margin-left: 0 !important; margin-right: 15rem !important; }
        html[dir="rtl"] .sidebar-collapsed .admin-content { margin-left: 0 !important; margin-right: 4.5rem !important; }
        html[dir="rtl"] .admin-sidebar .nav .nav-item.active > .nav-link { border-left: none !important; border-right: 3px solid var(--color-primary, #1a5c4e) !important; padding-left: 24px !important; padding-right: 21px !important; }
        html[dir="rtl"] .admin-sidebar .nav .nav-item > .nav-link .menu-icon { margin-right: 0 !important; margin-left: 12px !important; }
        html[dir="rtl"] .admin-sidebar .nav .nav-item > .nav-link .menu-arrow { margin-left: 0 !important; margin-right: auto !important; }
        html[dir="rtl"] .admin-sidebar .nav .nav-item .collapse,
        html[dir="rtl"] .admin-sidebar .nav .nav-item .collapse.show { padding-left: 0 !important; padding-right: 12px !important; }
        html[dir="rtl"] .admin-sidebar .relative > i.mdi-magnify { left: auto !important; right: 0.75rem !important; }
        html[dir="rtl"] .admin-sidebar #menuSearch { padding-left: 3.5rem !important; padding-right: 2.5rem !important; }
        html[dir="rtl"] .sidebar-collapsed .admin-sidebar .nav > .nav-item > .nav-flyout { left: auto !important; right: 4.5rem !important; border-radius: 0.5rem 0 0 0.5rem !important; box-shadow: -4px 6px 24px rgba(0,0,0,.12) !important; }
        /* Sidebar scrollbar: keep on the viewport-facing edge (right side of sidebar) */
        html[dir="rtl"] .sidebar-scroll { direction: ltr !important; }
        html[dir="rtl"] .sidebar-scroll > ul { direction: rtl !important; }
        @media (max-width: 1023px) {
            html[dir="rtl"] .admin-sidebar { transform: translateX(100%) !important; }
            html[dir="rtl"] .admin-content { margin-right: 0 !important; }
            html[dir="rtl"] .sidebar-mobile-open .admin-sidebar { transform: translateX(0) !important; }
        }
    </style>
    @endif
</head>

<body class="font-inter antialiased">
<!-- Mobile sidebar overlay -->
<div class="sidebar-overlay" onclick="closeSidebar()"></div>

<div class="flex h-screen overflow-hidden">
    @include('tenant.admin.partials.sidebar')

    <div class="admin-content flex-1 flex flex-col overflow-hidden lg:ml-60 transition-all duration-300">
        @include('tenant.admin.partials.topbar')
