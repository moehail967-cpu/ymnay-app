@php
    // Same configured central pages as the public footer; do not invent legal URLs/content.
    $termsSlug = get_page_slug(get_static_option('terms_condition'));
    $privacySlug = get_page_slug(get_static_option('privacy_policy'));
@endphp
@if($termsSlug)
    <a class="ym-policy-link" data-policy="terms" href="{{url('/'.$termsSlug)}}" target="_blank" rel="noopener noreferrer">{{__('الشروط والأحكام')}}<span class="sr-only"> — {{__('تفتح في نافذة جديدة')}}</span></a>
@else
    <span class="ym-policy-unavailable">{{__('الشروط والأحكام (الرابط غير متاح حاليًا)')}}</span>
@endif
{{__('و')}}
@if($privacySlug)
    <a class="ym-policy-link" data-policy="privacy" href="{{url('/'.$privacySlug)}}" target="_blank" rel="noopener noreferrer">{{__('سياسة الخصوصية')}}<span class="sr-only"> — {{__('تفتح في نافذة جديدة')}}</span></a>
@else
    <span class="ym-policy-unavailable">{{__('سياسة الخصوصية (الرابط غير متاح حاليًا)')}}</span>
@endif
