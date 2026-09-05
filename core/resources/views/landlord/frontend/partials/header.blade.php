<!DOCTYPE html>
<html dir="{{ \App\Facades\GlobalLanguage::user_lang_dir() }}"
      lang="{{ \App\Facades\GlobalLanguage::user_lang_slug() }}">
<head>
    {!! renderHeadStartHooks() !!}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(isset($page_post) && $page_post->id == get_static_option('home_page'))
        <title>
            {{get_static_option('site_title')}}
            @if(!empty(get_static_option('site_tag_line')))
                - {{get_static_option('site_tag_line')}}
            @endif
        </title>
        {!! render_site_seo() !!}
    @else
        @if(!empty(SEOMeta::generate()))
            {!! SEOMeta::generate() !!}
        @else
            <title>@yield('page-title')</title>
            <link rel="canonical" href="{{canonical_url()}}"/>
        @endif

        {!! OpenGraph::generate() !!}
        {!! Twitter::generate() !!}
        {!! JsonLd::generate() !!}
    @endif

    {!! load_google_fonts() !!}
    {!! render_favicon_by_id(get_static_option('site_favicon')) !!}

{{--    <link rel="stylesheet" href="{{global_asset('assets/landlord/frontend/css/bootstrap.min.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('assets/landlord/frontend/css/animate.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('assets/landlord/frontend/css/slick.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('assets/landlord/frontend/css/nice-select.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('assets/landlord/frontend/css/line-awesome.min.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('assets/common/css/jquery.ihavecookies.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('assets/landlord/frontend/css/odometer.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('assets/landlord/frontend/css/common.css')}}">--}}
    <link rel="stylesheet" href="{{global_asset('assets/common/css/toastr.css')}}">
    <link rel="stylesheet" href="{{global_asset('assets/common/css/loader.css')}}">
{{--    <link rel="stylesheet" href="{{asset('assets/landlord/frontend/css/style.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('assets/landlord/common/css/helpers.css')}}">--}}

{{--    <link rel="stylesheet" href="{{global_asset('assets/landlord/frontend/css/custom-style.css')}}">--}}


{{--    new css for new landing page : --}}
{!! render_favicon_by_id(get_static_option('site_favicon')) !!}
    <!-- Icon Library (use local stylesheet that defines .ti and .tabler-* classes) -->
    <link rel="stylesheet" href="{{versioned_asset('assets/new-landlord/css/glightbox.min.css')}}">
    <link rel="stylesheet" href="{{versioned_asset('assets/new-landlord/css/tablar-icon.css')}}">
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="{{versioned_asset('assets/new-landlord/css/owl.css')}}">
    <link rel="stylesheet" href="{{versioned_asset('assets/new-landlord/css/slider.css')}}">
    <link rel="stylesheet" href="{{versioned_asset('assets/new-landlord/css/custom_slider.css')}}">

    <link rel="stylesheet" href="{{versioned_asset('assets/new-landlord/css/style.css')}}">

    @if(\App\Facades\GlobalLanguage::user_lang_dir() == 'rtl')
        <link rel="stylesheet" href="{{asset('assets/landlord/frontend/css/rtl.css')}}">
    @endif

    @include('landlord.frontend.partials.color-font-variable')
    @yield('style')

{{--    @yield('seo_data')--}}

    <x-landlord-others.dynamic-style/>
    <x-landlord-others.highlighted-text/>

    {!! renderHeadEndHooks() !!}

    @if(get_static_option('site_third_party_tracking_code'))
        <script>
            {!! get_static_option('site_third_party_tracking_code') !!}
        </script>
    @endif

    <script src="{{versioned_asset('assets/new-landlord/js/tailwind_browser.js')}}"></script>
    <script src="{{versioned_asset('assets/new-landlord/js/tailwind-config.js')}}"></script>
</head>
<body>
{!! renderBodyStartHooks() !!}
@include('tenant.frontend.partials.loader')


