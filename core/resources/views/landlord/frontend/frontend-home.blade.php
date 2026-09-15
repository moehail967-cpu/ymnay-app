@extends('landlord.frontend.frontend-page-master')

@section('seo_data')
    {!! SEOMeta::generate() !!}
@endsection

@section('content')
@php
    $periods = [
        \App\Enums\PricePlanTypEnums::MONTHLY => 'شهريًا',
        \App\Enums\PricePlanTypEnums::YEARLY => 'سنويًا',
        \App\Enums\PricePlanTypEnums::LIFETIME => 'مدى الحياة',
    ];
    $featureLabels = price_plan_feature_list();
    $heroTheme = $featuredThemes->first();
    $heroThemeName = $heroTheme ? theme_custom_name($heroTheme) : 'متجر إلكتروني';
    $heroThemeImage = $heroTheme ? (get_static_option_central($heroTheme->slug.'_theme_image') ?: loadScreenshot($heroTheme->slug)) : null;
    $supportEmail = get_static_option_central('site_global_email') ?: get_static_option('site_global_email');
    $serviceUrl = filter_var($supportEmail, FILTER_VALIDATE_EMAIL)
        ? 'mailto:'.$supportEmail.'?subject='.rawurlencode('طلب تجهيز موقع عبر YMNAY')
        : route('landlord.frontend.support.ticket');
@endphp

<main class="ym-landing" dir="rtl">
    <section class="ym-hero" aria-labelledby="ym-hero-title">
        <div class="ym-public-container ym-hero-grid">
            <div class="ym-hero-copy">
                <span class="ym-eyebrow">منصة واحدة لمشروعك الرقمي</span>
                <h1 id="ym-hero-title">موقعك أو متجرك،<br><span>جاهز لعملك</span></h1>
                <p>أنشئ حضورك الرقمي، اختر القالب المناسب، وأدر المحتوى والطلبات بسهولة من لوحة عربية واضحة.</p>
                <div class="ym-hero-actions">
                    <a class="ym-button ym-button-primary" href="{{route('landlord.store.onboarding')}}">أنشئ موقعك بنفسك</a>
                    <a class="ym-button ym-button-secondary" href="{{$serviceUrl}}">اطلب تجهيز موقعك</a>
                </div>
                <p class="ym-proof"><span aria-hidden="true">✓</span> بدون خبرة برمجية <span>•</span> دعم بالعربية</p>
            </div>
            <div class="ym-product-stage" aria-label="معاينة حقيقية لقالب {{$heroThemeName}}">
                <div class="ym-browser-frame">
                    <div class="ym-browser-bar"><span></span><span></span><span></span><b>معاينة {{$heroThemeName}}</b></div>
                    @if($heroThemeImage)
                        <img src="{{$heroThemeImage}}" alt="معاينة قالب {{$heroThemeName}}" loading="eager">
                    @else
                        <div class="ym-empty-preview">ستظهر معاينة القالب هنا</div>
                    @endif
                </div>
                <div class="ym-phone-frame" aria-hidden="true">
                    @if($heroThemeImage)<img src="{{$heroThemeImage}}" alt="">@endif
                </div>
            </div>
        </div>
    </section>

    <section class="ym-section" id="features" aria-labelledby="ym-types-title">
        <div class="ym-public-container">
            <div class="ym-section-heading">
                <span class="ym-eyebrow">حل يناسب نشاطك</span>
                <h2 id="ym-types-title">ابدأ بالطريقة التي تخدم مشروعك</h2>
                <p>ابنِ حضورًا تعريفيًا احترافيًا أو متجرًا متكاملًا، ثم طوّره كلما نما عملك.</p>
            </div>
            <div class="ym-type-grid">
                <article class="ym-type-card">
                    <span class="ym-card-icon" aria-hidden="true">▤</span>
                    <div><h3>موقع تعريفي</h3><p>عرّف بخدماتك وأعمالك وصفحات مشروعك بهوية واضحة وسهلة التحديث.</p><a href="#how-it-works">اعرف كيف تبدأ <span aria-hidden="true">←</span></a></div>
                </article>
                <article class="ym-type-card ym-type-card-accent">
                    <span class="ym-card-icon" aria-hidden="true">◇</span>
                    <div><h3>متجر إلكتروني</h3><p>اعرض منتجاتك، استقبل الطلبات، وأدر وسائل الدفع والمحتوى من لوحة واحدة.</p><a href="{{route('landlord.store.onboarding')}}">أنشئ متجرك <span aria-hidden="true">←</span></a></div>
                </article>
            </div>
        </div>
    </section>

    <section class="ym-section ym-section-soft" id="themes" aria-labelledby="ym-themes-title">
        <div class="ym-public-container">
            <div class="ym-section-heading ym-heading-row">
                <div><span class="ym-eyebrow">قوالب فعلية</span><h2 id="ym-themes-title">شاهد كيف يمكن أن يظهر مشروعك</h2><p>معاينة القالب مستقلة عن اختياره؛ ستحدد قالبك داخل خطوات الإنشاء.</p></div>
                @if($themes->count() > 3)<button class="ym-text-button" type="button" data-ym-open-themes>عرض كل القوالب</button>@endif
            </div>
            @if($featuredThemes->isEmpty())
                <div class="ym-state-card">لا توجد قوالب منشورة للمعاينة حاليًا.</div>
            @else
                <div class="ym-theme-grid">
                    @foreach($featuredThemes as $theme)
                        @php
                            $name = theme_custom_name($theme);
                            $image = get_static_option_central($theme->slug.'_theme_image') ?: loadScreenshot($theme->slug);
                            $preview = get_static_option_central($theme->slug.'_theme_url');
                        @endphp
                        <article class="ym-theme-card">
                            <div class="ym-theme-shot"><img src="{{$image}}" alt="معاينة قالب {{$name}}" loading="lazy"></div>
                            <div class="ym-theme-card-body">
                                <div><h3>{{$name}}</h3><p>قالب منشور وجاهز للمعاينة والتخصيص حسب هوية مشروعك.</p></div>
                                <button type="button" class="ym-preview-button" data-ym-theme-preview data-name="{{$name}}" data-image="{{$image}}" @if($preview) data-url="{{$preview}}" @endif>معاينة</button>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="ym-section" aria-labelledby="ym-dashboard-title">
        <div class="ym-public-container ym-dashboard-grid">
            <div class="ym-dashboard-copy">
                <span class="ym-eyebrow">إدارة واضحة</span>
                <h2 id="ym-dashboard-title">إدارة موقعك بكل سهولة</h2>
                <p>لوحة عربية تجمع المهام اليومية الأساسية بدون تشتيت أو أرقام تجريبية توحي بنتائج غير حقيقية.</p>
                <ul class="ym-check-list">
                    <li><span>✓</span><div><strong>المنتجات والطلبات</strong><small>تنظيم الكتالوج ومتابعة الطلبات.</small></div></li>
                    <li><span>✓</span><div><strong>المحافظ ووسائل الدفع</strong><small>إدارة إعدادات الدفع المتاحة لمتجرك.</small></div></li>
                    <li><span>✓</span><div><strong>المحتوى والصفحات</strong><small>تحديث النصوص والصفحات من مكان واحد.</small></div></li>
                </ul>
            </div>
            <div class="ym-dashboard-preview" aria-label="معاينة توضيحية لبنية لوحة الإدارة">
                <div class="ym-dashboard-top"><b>لوحة الإدارة</b><span>معاينة توضيحية</span></div>
                <div class="ym-dashboard-body">
                    <nav aria-label="أقسام لوحة الإدارة"><span class="active">نظرة عامة</span><span>المنتجات</span><span>الطلبات</span><span>الصفحات</span><span>الإعدادات</span></nav>
                    <div class="ym-dashboard-content">
                        <div class="ym-dash-toolbar"><b>إدارة المتجر</b><button type="button" disabled>إضافة عنصر</button></div>
                        @foreach(['المنتجات والطلبات','المحافظ ووسائل الدفع','المحتوى والصفحات'] as $row)
                            <div class="ym-dash-row"><span class="ym-dash-thumb"></span><strong>{{$row}}</strong><span>فتح الإدارة</span></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ym-section ym-section-soft" id="how-it-works" aria-labelledby="ym-steps-title">
        <div class="ym-public-container">
            <div class="ym-section-heading"><span class="ym-eyebrow">رحلة واضحة</span><h2 id="ym-steps-title">خمس خطوات ويبدأ مشروعك</h2><p>تحفظ اختياراتك خلال الرحلة ويمكنك مراجعتها قبل إنشاء المتجر.</p></div>
            <ol class="ym-steps">
                @foreach([
                    ['الباقة','اختر الباقة النشطة المناسبة لاحتياجك.'],
                    ['القالب','عاين القوالب المتاحة ضمن الباقة وحدد أحدها.'],
                    ['بيانات المتجر','اكتب اسم المتجر واختر رابطه الفرعي.'],
                    ['الحساب والتحقق','أنشئ حسابك أو سجّل الدخول وتحقق من البريد.'],
                    ['المراجعة والإنشاء','راجع التفاصيل ثم ابدأ التجربة إذا كنت مؤهلًا.'],
                ] as $index => [$title, $description])
                    <li><span>{{$index + 1}}</span><h3>{{$title}}</h3><p>{{$description}}</p></li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="ym-section" id="pricing" aria-labelledby="ym-pricing-title">
        <div class="ym-public-container">
            <div class="ym-section-heading"><span class="ym-eyebrow">الأسعار الحالية</span><h2 id="ym-pricing-title">اختر الباقة المناسبة لك</h2><p>الأسعار والمزايا وفترة التجربة أدناه مقروءة مباشرة من الباقات النشطة.</p></div>
            @if($plans->isEmpty())
                <div class="ym-state-card">لا توجد باقات نشطة حاليًا. تواصل مع الدعم لمعرفة الخيارات المتاحة.</div>
            @else
                <div class="ym-pricing-grid">
                    @foreach($plans as $plan)
                        @php
                            $planTitle = $plan->getTranslation('title', get_user_lang());
                            $limits = collect([
                                $plan->product_permission_feature !== null ? ['المنتجات', (int)$plan->product_permission_feature > 0 ? $plan->product_permission_feature : 'غير محدود'] : null,
                                $plan->page_permission_feature !== null ? ['الصفحات', (int)$plan->page_permission_feature > 0 ? $plan->page_permission_feature : 'غير محدود'] : null,
                                $plan->storage_permission_feature !== null ? ['التخزين', (int)$plan->storage_permission_feature > 0 ? $plan->storage_permission_feature.' MB' : 'غير محدود'] : null,
                            ])->filter();
                        @endphp
                        <article class="ym-price-card">
                            @if($plan->package_badge)<span class="ym-plan-badge">{{$plan->package_badge}}</span>@endif
                            <h3>{{$planTitle}}</h3>
                            @if($plan->package_description)<p class="ym-plan-description">{{$plan->package_description}}</p>@endif
                            <p class="ym-price"><strong>{!! amount_with_currency_symbol($plan->price) !!}</strong><span>/ {{$periods[$plan->type] ?? ''}}</span></p>
                            @if($plan->has_trial && (int)$plan->trial_days > 0)<p class="ym-trial">تجربة مجانية لمدة {{$plan->trial_days}} يومًا</p>@endif
                            <ul>
                                @foreach($limits as [$label, $value])<li><span>✓</span>{{$label}}: {{$value}}</li>@endforeach
                                @foreach($plan->plan_features->where('status', 1)->take(4) as $feature)
                                    <li><span>✓</span>{{$featureLabels[$feature->feature_name] ?? str_replace('_', ' ', $feature->feature_name)}}</li>
                                @endforeach
                            </ul>
                            <form method="post" action="{{route('landlord.store.onboarding.plan')}}">
                                @csrf<input type="hidden" name="plan_id" value="{{$plan->id}}">
                                <button class="ym-button ym-button-primary" type="submit">اختيار الباقة والبدء</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="ym-section ym-section-soft" id="faq" aria-labelledby="ym-faq-title">
        <div class="ym-public-container ym-faq-wrap">
            <div class="ym-section-heading"><span class="ym-eyebrow">أسئلة شائعة</span><h2 id="ym-faq-title">قبل أن تبدأ</h2></div>
            <div class="ym-faq-list">
                @foreach([
                    ['هل أحتاج إلى خبرة برمجية؟','لا. خطوات الإنشاء مصممة لتختار الباقة والقالب وبيانات متجرك ثم تراجعها من واجهة واحدة.'],
                    ['هل تبدأ المعاينة عملية الدفع؟','لا. معاينة القالب لا تختاره ولا تبدأ الدفع. اختيار الباقة أو القالب يتم داخل خطوات إنشاء المتجر.'],
                    ['هل التجربة المجانية متاحة لكل الباقات؟','تظهر مدة التجربة فقط إذا كانت مفعلة في الباقة الحالية، ويُعاد التحقق من أهلية الحساب قبل الإنشاء.'],
                    ['هل يمكنني تعديل اختياراتي؟','نعم، يمكنك الرجوع إلى الخطوات السابقة قبل الإنشاء، كما ستظهر لك أي تغييرات طرأت على تفاصيل الباقة للمراجعة.'],
                ] as $index => [$question, $answer])
                    <article class="ym-faq-item">
                        <h3><button type="button" aria-expanded="false" aria-controls="ym-faq-answer-{{$index}}" data-ym-faq>{{$question}}<span aria-hidden="true">＋</span></button></h3>
                        <div id="ym-faq-answer-{{$index}}" hidden><p>{{$answer}}</p></div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ym-final-cta" aria-labelledby="ym-cta-title">
        <div class="ym-public-container"><div><h2 id="ym-cta-title">جاهز لتحويل فكرتك إلى مشروع رقمي؟</h2><p>ابدأ بخطوات واضحة، وراجع كل شيء قبل إنشاء متجرك.</p></div><div><a class="ym-button ym-button-light" href="{{route('landlord.store.onboarding')}}">ابدأ إنشاء متجرك</a><a class="ym-button ym-button-outline-light" href="{{$serviceUrl}}">تواصل مع الدعم</a></div></div>
    </section>
</main>

<dialog class="ym-dialog" data-ym-preview-dialog aria-labelledby="ym-preview-title">
    <div class="ym-dialog-header"><h2 id="ym-preview-title">معاينة القالب</h2><button type="button" aria-label="إغلاق المعاينة" data-ym-close-dialog>×</button></div>
    <div class="ym-preview-controls"><button type="button" class="active" data-ym-preview-size="desktop">سطح المكتب</button><button type="button" data-ym-preview-size="mobile">الجوال</button><a target="_blank" rel="noopener" hidden data-ym-preview-link>فتح الموقع</a></div>
    <div class="ym-dialog-preview desktop" data-ym-dialog-preview><img alt="" data-ym-preview-image></div>
</dialog>

<dialog class="ym-dialog ym-all-themes-dialog" data-ym-themes-dialog aria-labelledby="ym-all-themes-title">
    <div class="ym-dialog-header"><h2 id="ym-all-themes-title">كل القوالب المنشورة</h2><button type="button" aria-label="إغلاق" data-ym-close-themes>×</button></div>
    <div class="ym-all-themes-grid">
        @foreach($themes as $theme)
            @php $name = theme_custom_name($theme); $image = get_static_option_central($theme->slug.'_theme_image') ?: loadScreenshot($theme->slug); @endphp
            <button type="button" data-ym-theme-preview data-name="{{$name}}" data-image="{{$image}}" @if(get_static_option_central($theme->slug.'_theme_url')) data-url="{{get_static_option_central($theme->slug.'_theme_url')}}" @endif><img src="{{$image}}" alt="" loading="lazy"><span>{{$name}}</span></button>
        @endforeach
    </div>
</dialog>
@endsection
