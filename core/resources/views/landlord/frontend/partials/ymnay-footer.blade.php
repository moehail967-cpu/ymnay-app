@php
    $logo = render_image_markup_by_attachment_id(get_static_option('site_white_logo'), 'ym-brand-image')
        ?: render_image_markup_by_attachment_id(get_static_option('site_logo'), 'ym-brand-image');
    $siteTitle = get_static_option('site_'.get_user_lang().'_title') ?: get_static_option('site_title') ?: 'YMNAY';
    $termsSlug = get_page_slug(get_static_option('terms_condition'));
    $privacySlug = get_page_slug(get_static_option('privacy_policy'));
@endphp
<footer class="ym-site-footer">
    <div class="ym-public-container ym-footer-grid">
        <div class="ym-footer-brand">
            <a class="ym-brand" href="{{route('landlord.homepage')}}">
                @if($logo){!! $logo !!}@else<span>{{$siteTitle}}</span>@endif
            </a>
            <p>منصة عربية تساعدك على إطلاق موقعك أو متجرك وإدارته من مكان واحد.</p>
        </div>
        <nav aria-label="روابط المنتج">
            <strong>المنتج</strong>
            <a href="#features">المزايا</a><a href="#themes">القوالب</a><a href="#pricing">الأسعار</a>
        </nav>
        <nav aria-label="روابط المساعدة">
            <strong>المساعدة</strong>
            <a href="#faq">الأسئلة الشائعة</a>
            <a href="{{route('landlord.frontend.support.ticket')}}">الدعم</a>
        </nav>
        @if($termsSlug || $privacySlug)
            <nav aria-label="روابط قانونية">
                <strong>قانوني</strong>
                @if($termsSlug)<a href="{{url('/'.$termsSlug)}}">الشروط والأحكام</a>@endif
                @if($privacySlug)<a href="{{url('/'.$privacySlug)}}">سياسة الخصوصية</a>@endif
            </nav>
        @endif
    </div>
    <div class="ym-public-container ym-footer-bottom">
        <span>© {{date('Y')}} {{$siteTitle}}. جميع الحقوق محفوظة.</span>
    </div>
</footer>
