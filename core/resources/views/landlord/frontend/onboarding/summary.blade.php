@php
    $summaryRows = [
        ['الباقة', $plan?->title ?: '—', false],
        ['القالب', $onboarding?->theme_slug ?: '—', false],
        ['المتجر', $onboarding?->store_name ?: '—', false],
        ['الرابط', $onboarding?->subdomain ? $onboarding->subdomain.'.'.current(config('tenancy.central_domains')) : '—', true],
    ];
    if ($plan) {
        $summaryRows[] = ['التجربة', $plan->has_trial ? $plan->trial_days.' يومًا' : 'غير متاحة', false];
        $summaryRows[] = ['بعد التجربة', strip_tags(amount_with_currency_symbol($plan->price)), false];
    }
    if ($user) $summaryRows[] = ['الحساب', $user->email, true];
@endphp

<aside class="ym-card ym-summary" aria-label="ملخص اختياراتك">
    <h2>اختياراتك</h2>
    <dl>
        @foreach($summaryRows as [$label, $value, $ltr])
            <div><dt>{{$label}}</dt><dd @class(['ym-ltr' => $ltr])>{{$value}}</dd></div>
        @endforeach
    </dl>
</aside>

<details class="ym-card ym-summary-mobile" @if($expanded ?? false) open @endif>
    <summary>ملخص اختياراتك</summary>
    <div class="ym-summary" aria-label="ملخص اختياراتك">
        <dl>
            @foreach($summaryRows as [$label, $value, $ltr])
                <div><dt>{{$label}}</dt><dd @class(['ym-ltr' => $ltr])>{{$value}}</dd></div>
            @endforeach
        </dl>
    </div>
</details>
