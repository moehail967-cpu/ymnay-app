<aside class="ym-card ym-summary">
    <h2>اختياراتك</h2>
    <dl>
        <div><dt>الباقة</dt><dd>{{$plan?->title ?: '—'}}</dd></div>
        <div><dt>القالب</dt><dd>{{$onboarding?->theme_slug ?: '—'}}</dd></div>
        <div><dt>المتجر</dt><dd>{{$onboarding?->store_name ?: '—'}}</dd></div>
        <div><dt>الرابط</dt><dd class="ym-ltr">{{$onboarding?->subdomain ? $onboarding->subdomain.'.'.current(config('tenancy.central_domains')) : '—'}}</dd></div>
        @if($plan)
            <div><dt>التجربة</dt><dd>{{$plan->has_trial ? $plan->trial_days.' يومًا' : 'غير متاحة'}}</dd></div>
            <div><dt>بعد التجربة</dt><dd>{{number_format((float) $plan->price, 2)}} ريال سعودي</dd></div>
        @endif
        @if($user)<div><dt>الحساب</dt><dd class="ym-ltr">{{$user->email}}</dd></div>@endif
    </dl>
</aside>
