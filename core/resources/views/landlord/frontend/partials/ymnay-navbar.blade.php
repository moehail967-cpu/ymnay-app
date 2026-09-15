@php
    $logo = render_image_markup_by_attachment_id(get_static_option('site_logo'), 'ym-brand-image')
        ?: render_image_markup_by_attachment_id(get_static_option('site_white_logo'), 'ym-brand-image');
    $siteTitle = get_static_option('site_'.get_user_lang().'_title') ?: get_static_option('site_title') ?: 'YMNAY';
    $homeUrl = route('landlord.homepage');
    $dashboardUrl = Auth::guard('web')->check() ? route('landlord.user.home') : null;
@endphp
<header class="ym-site-header" data-ym-navbar>
    <div class="ym-public-container ym-nav-row">
        <a class="ym-brand" href="{{$homeUrl}}" aria-label="{{$siteTitle}} — الرئيسية">
            @if($logo){!! $logo !!}@else<span>{{$siteTitle}}</span>@endif
        </a>

        <nav class="ym-desktop-nav" aria-label="التنقل الرئيسي">
            <a href="{{$homeUrl}}#features">المزايا</a>
            <a href="{{$homeUrl}}#themes">القوالب</a>
            <a href="{{$homeUrl}}#how-it-works">كيف يعمل؟</a>
            <a href="{{$homeUrl}}#pricing">الأسعار</a>
        </nav>

        <div class="ym-nav-actions">
            @if($dashboardUrl)
                <a class="ym-button ym-button-ghost" href="{{$dashboardUrl}}">لوحة التحكم</a>
            @else
                <a class="ym-button ym-button-ghost" href="{{route('landlord.user.login')}}">تسجيل الدخول</a>
            @endif
            <a class="ym-button ym-button-primary" href="{{route('landlord.store.onboarding')}}">ابدأ الآن</a>
        </div>

        <button class="ym-menu-toggle" type="button" aria-expanded="false" aria-controls="ym-mobile-menu" data-ym-menu-toggle>
            <span class="sr-only">فتح القائمة</span><span aria-hidden="true">☰</span>
        </button>
    </div>

    <div class="ym-mobile-menu" id="ym-mobile-menu" hidden data-ym-mobile-menu>
        <nav aria-label="التنقل على الجوال">
            <a href="{{$homeUrl}}#features">المزايا</a>
            <a href="{{$homeUrl}}#themes">القوالب</a>
            <a href="{{$homeUrl}}#how-it-works">كيف يعمل؟</a>
            <a href="{{$homeUrl}}#pricing">الأسعار</a>
            @if($dashboardUrl)
                <a href="{{$dashboardUrl}}">لوحة التحكم</a>
            @else
                <a href="{{route('landlord.user.login')}}">تسجيل الدخول</a>
            @endif
            <a class="ym-button ym-button-primary" href="{{route('landlord.store.onboarding')}}">ابدأ الآن</a>
        </nav>
    </div>
</header>
