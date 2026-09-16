@php
    $selectedTheme = $onboarding?->theme_slug ? collect($themes)->firstWhere('slug', $onboarding->theme_slug) : null;
    $selectedThemeDetails = $selectedTheme ? getIndividualThemeDetails($selectedTheme->slug) : [];
    $selectedThemeName = $selectedTheme
        ? (get_static_option_central($selectedTheme->slug.'_theme_name') ?: ($selectedThemeDetails['name'] ?? $selectedTheme->name))
        : ($onboarding?->theme_slug ? __('القالب المختار غير متاح') : '—');
    $canEditSummary = $onboarding && in_array($onboarding->status, ['draft', 'account_verified'], true);
    $summaryRows = [
        ['الباقة', $plan?->title ?: '—', false, 1],
        ['القالب', $selectedThemeName, false, 2],
        ['المتجر', $onboarding?->store_name ?: '—', false, 3],
        ['الرابط', $onboarding?->subdomain ? $onboarding->subdomain.'.'.current(config('tenancy.central_domains')) : '—', true, 3],
    ];
    if ($plan) {
        $period = [0 => __('شهريًا'), 1 => __('سنويًا'), 2 => __('مدى الحياة')][(int)$plan->type] ?? '';
        $summaryRows[] = ['التجربة', $plan->has_trial && (int)$plan->trial_days > 0 ? $plan->trial_days.' '.__('يومًا') : __('غير متاحة'), false, null];
        $summaryRows[] = ['بعد التجربة', strip_tags(amount_with_currency_symbol($plan->price)).($period ? ' / '.$period : ''), false, null];
    }
    if ($user) $summaryRows[] = ['الحساب', $user->email, true, null];
@endphp

<aside class="ym-card ym-summary" aria-label="ملخص اختياراتك">
    <h2>{{__('اختياراتك')}}</h2>
    <dl>
        @foreach($summaryRows as [$label, $value, $ltr, $editStep])
            <div><dt>{{__($label)}}</dt><dd><span @class(['ym-summary-value', 'ym-ltr' => $ltr])>{{$value}}</span>
                @if($canEditSummary && $editStep && $editStep <= $maxStep)
                    <a class="ym-summary-edit" data-edit-step="{{$editStep}}" href="{{route('landlord.store.onboarding',['step'=>$editStep])}}" aria-label="{{__('تعديل')}} {{__($label)}}">{{__('تعديل')}}</a>
                @endif
            </dd></div>
        @endforeach
    </dl>
</aside>

<details class="ym-card ym-summary-mobile" @if($expanded ?? false) open @endif>
    <summary>{{__('ملخص اختياراتك')}}</summary>
    <div class="ym-summary" aria-label="ملخص اختياراتك">
        <dl>
            @foreach($summaryRows as [$label, $value, $ltr, $editStep])
                <div><dt>{{__($label)}}</dt><dd><span @class(['ym-summary-value', 'ym-ltr' => $ltr])>{{$value}}</span>
                    @if($canEditSummary && $editStep && $editStep <= $maxStep)
                        <a class="ym-summary-edit" data-edit-step="{{$editStep}}" href="{{route('landlord.store.onboarding',['step'=>$editStep])}}" aria-label="{{__('تعديل')}} {{__($label)}}">{{__('تعديل')}}</a>
                    @endif
                </dd></div>
            @endforeach
        </dl>
    </div>
</details>
