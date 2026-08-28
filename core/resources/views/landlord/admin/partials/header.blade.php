<!doctype html>
<html lang="{{ \App\Facades\GlobalLanguage::default_slug() }}" dir="{{ \App\Facades\GlobalLanguage::default_dir() }}" {{ !empty(get_static_option('dark_mode_for_admin_panel')) ? 'data-theme="dark"' : '' }}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @if(!request()->routeIs('landlord.admin.home'))
            @yield('title')  -
        @endif
        {{get_static_option('site_title', __('Xgenious'))}}
        @if(!empty(get_static_option('site_tag_line')))
            - {{get_static_option('site_tag_line')}}
        @endif
    </title>
    <!-- Fonts -->
    {{-- Shorooq Arabic Font (Ymnay Self-Hosted Web Fonts) --}}
    <link rel="preload" href="{{ global_asset('assets/common/fonts/ymnay-web-fonts/Shorooq/Shorooq.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ global_asset('assets/common/fonts/ymnay-web-fonts/ymnay-fonts.css') }}">
    {{-- Google Fonts removed: replaced by Shorooq (self-hosted ymnay-web-fonts) --}}
    {!! render_favicon_by_id(get_static_option('site_favicon')) !!}

    <!-- Keep: required vendor CSS (Bootstrap base needed for modals/components in inner pages) -->
    <link href="{{ global_asset('assets/landlord/admin/css/landlord.bundle.base.css') }}" rel="stylesheet">
    <link href="{{ global_asset('assets/landlord/admin/css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="{{ global_asset('assets/common/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ global_asset('assets/common/css/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ global_asset('assets/landlord/frontend/css/line-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ global_asset('assets/common/css/toastr.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/new-landlord/css/tablar-icon.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/custom-style.css')}}">

    <!-- dark mode css  -->
    @if(!empty(get_static_option('dark_mode_for_admin_panel')))
        <link href="{{ global_asset('assets/landlord/admin/css/dark-mode.css') }}" rel="stylesheet">
    @endif

    <!-- rtl mode -->
    @if(\App\Enums\LanguageEnums::getdirection(get_user_lang_direction()) === 'rtl')
        <link href="{{ global_asset('assets/landlord/admin/css/rtl.css') }}" rel="stylesheet">
    @endif

    <!-- Tailwind CSS via browser CDN -->
    <script src="{{asset('assets/new-landlord/js/tailwind_browser.js')}}"></script>
    <script src="{{asset('assets/new-landlord/js/tailwind-config.js')}}"></script>

    <!-- Admin Panel Editorial Styles -->


    <!-- Admin Panel New Asset System -->
    <link rel="stylesheet" href="{{asset('assets/new-landlord/admin/css/app.css')}}">
    <link rel="stylesheet" href="{{asset('assets/new-landlord/admin/css/components/actions.css')}}">
    <link rel="stylesheet" href="{{asset('assets/new-landlord/admin/css/components/widgets.css')}}">

    {{-- Dynamic Admin Panel Color Variables — overrides variables.css defaults --}}
    <style>
        :root {
            --color-bg-base:            {{ get_static_option('admin_bg_base', '#EEF1F3') }};
            @php
                $adminPrimary = get_static_option('admin_primary_color', '#1A4A3E');
                $ap = ltrim($adminPrimary, '#');
                if (strlen($ap) === 6 && ctype_xdigit($ap)) {
                    $adminPrimaryRgb = hexdec(substr($ap,0,2)).', '.hexdec(substr($ap,2,2)).', '.hexdec(substr($ap,4,2));
                } else {
                    $adminPrimaryRgb = '26, 74, 62';
                }
            @endphp
            --color-primary:            {{ $adminPrimary }};
            --color-primary-rgb:        {{ $adminPrimaryRgb }};
            --color-primary-hover:      {{ get_static_option('admin_primary_hover_color', '#E6F7EF') }};
            --color-primary-soft:       {{ get_static_option('admin_primary_soft_color', '#F1FFFB') }};
            --color-primary-soft-hover: {{ get_static_option('admin_primary_soft_hover_color', '#D8F1ED') }};
            --color-bg-sidebar:         {{ get_static_option('admin_sidebar_bg', '#FFFFFF') }};
            --color-bg-secondary:       {{ get_static_option('admin_secondary_bg', '#EEF1F3') }};
            --color-bg-muted:           {{ get_static_option('admin_muted_bg', '#EDF9F9') }};
            --color-text-brand:         {{ get_static_option('admin_brand_text_color', '#21B777') }};
            --color-border-main:        {{ get_static_option('admin_border_color', '#F1F7F7') }};
            --color-border-subtle:      {{ get_static_option('admin_border_subtle_color', '#F8F8F8') }};
            --color-border-hover:       {{ get_static_option('admin_border_hover_color', '#D8F1ED') }};
            --color-sidebar-hover:      {{ get_static_option('admin_sidebar_hover_color', '#F8FAFC') }};
            --color-bg-surface:         {{ get_static_option('admin_surface_bg', '#FFFFFF') }};
            --color-text-main:          {{ get_static_option('admin_text_main_color', '#000000') }};
            --color-text-dark:          {{ get_static_option('admin_text_dark_color', '#000000') }};
            --color-text-muted:         {{ get_static_option('admin_text_muted_color', '#64748B') }};
            --color-text-subtle:        {{ get_static_option('admin_text_subtle_color', '#64748B') }};
            --gradient-featured:        linear-gradient(135deg, #0a2520 0%, #0f3530 50%, {{ $adminPrimary }} 100%);
        }
        /* Fix: ensure admin sidebar uses --color-bg-sidebar, not --color-bg-surface */
        .bg-sidebar { background-color: var(--color-bg-sidebar) !important; }
    </style>

    @yield('style')
    @pluginAdminStyles

    {{-- RTL structural overrides — inline so they win every cascade battle --}}
    @if(\App\Facades\GlobalLanguage::default_dir() === 'rtl')
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

{{-- ===== OLD BOOTSTRAP BODY STRUCTURE — commented out =====
<body>
<div class="container-scroller">
@include('landlord.admin.partials.topbar')
    <div class="container-fluid page-body-wrapper">
@include('landlord.admin.partials.sidebar')
===== END OLD BOOTSTRAP BODY STRUCTURE ===== --}}

<body class="font-shorooq antialiased">
<!-- Mobile sidebar overlay -->
<div class="sidebar-overlay" onclick="closeSidebar()"></div>

<div class="flex h-screen overflow-hidden">
    @include('landlord.admin.partials.sidebar')

    <div class="admin-content flex-1 flex flex-col overflow-hidden lg:ml-60 transition-all duration-300">
@include('landlord.admin.partials.topbar')
