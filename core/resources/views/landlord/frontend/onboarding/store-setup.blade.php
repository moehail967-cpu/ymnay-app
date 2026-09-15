@extends('landlord.frontend.frontend-page-master')

@section('title', __('Create your store'))

@section('style')
<style>
    .ym-onboarding{direction:rtl;background:#f6f8ff;min-height:calc(100vh - 72px);padding:48px 16px;color:#0f172a}
    .ym-shell{max-width:1200px;margin:auto}.ym-progress{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:32px}
    .ym-progress a,.ym-progress span{display:flex;align-items:center;gap:8px;color:#64748b;text-decoration:none;font-size:14px}
    .ym-dot{display:grid;place-items:center;width:34px;height:34px;border:2px solid #d8dfec;border-radius:50%;background:#fff;font-weight:700}
    .ym-active .ym-dot,.ym-done .ym-dot{border-color:#4338ca}.ym-active .ym-dot{background:#4338ca;color:#fff}.ym-done{color:#4338ca!important}
    .ym-card{background:#fff;border:1px solid #d8dfec;border-radius:16px;padding:24px;box-shadow:0 8px 28px rgba(15,23,42,.06)}
    .ym-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:24px}.ym-theme-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
    .ym-option{position:relative;text-align:right;width:100%;height:100%;background:#fff;border:2px solid #d8dfec;border-radius:16px;padding:20px;cursor:pointer;transition:.18s}
    .ym-option:hover,.ym-option:focus-within{border-color:#4338ca;box-shadow:0 8px 28px rgba(67,56,202,.10)}
    .ym-option input{position:absolute;top:16px;left:16px;width:20px;height:20px;accent-color:#4338ca}
    .ym-theme-image{width:100%;aspect-ratio:16/10;object-fit:cover;object-position:top;border-radius:12px;background:#eef2ff;margin-bottom:14px}
    .ym-field{margin-bottom:18px}.ym-field label{display:block;font-weight:600;margin-bottom:8px}.ym-field input{width:100%;height:50px;border:1px solid #64748b;border-radius:12px;padding:0 14px;background:#fff}
    .ym-field input:focus{outline:2px solid #4338ca;outline-offset:3px}.ym-ltr{direction:ltr;text-align:left}.ym-help{font-size:14px;color:#64748b;margin-top:7px}
    .ym-field-error{display:block;margin-top:7px;color:#b91c1c;font-size:14px}.ym-domain-status{min-height:24px;margin-top:7px;font-size:14px}.ym-domain-status.checking{color:#475569}.ym-domain-status.available{color:#166534}.ym-domain-status.unavailable{color:#b91c1c}
    .ym-password-wrap{position:relative}.ym-password-wrap input{padding-left:62px}.ym-password-toggle{position:absolute;left:8px;top:6px;min-height:38px;padding:0 10px;color:#4338ca;background:#eef2ff;border:0;border-radius:8px;font:inherit;font-size:13px;font-weight:700;cursor:pointer}
    .ym-actions{display:flex;justify-content:space-between;gap:12px;margin-top:24px}.ym-btn{min-height:48px;border:0;border-radius:12px;padding:12px 24px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}
    .ym-primary{background:#4338ca;color:#fff}.ym-primary:hover{background:#3730a3}.ym-secondary{background:#eef2ff;color:#3730a3}.ym-btn:disabled{opacity:.55;cursor:not-allowed}
    .ym-alert{border-radius:12px;padding:14px 16px;margin-bottom:18px}.ym-error{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}.ym-warning{background:#fffbeb;color:#92400e;border:1px solid #fde68a}.ym-success{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
    .ym-layout{display:grid;grid-template-columns:minmax(0,720px) minmax(260px,320px);gap:32px;justify-content:center}.ym-summary{align-self:start}.ym-summary dl{margin:0}.ym-summary div{display:flex;justify-content:space-between;gap:12px;padding:11px 0;border-bottom:1px solid #e2e8f0}.ym-summary dt{color:#64748b}.ym-summary dd{margin:0;font-weight:600;text-align:left}
    .ym-summary-mobile,.ym-mobile-step{display:none}.ym-summary-mobile summary{font-weight:800;cursor:pointer}.ym-summary-mobile .ym-summary{margin-top:14px;padding:0;border:0;box-shadow:none}
    .ym-auth-tabs{display:flex;gap:8px;margin-bottom:18px}.ym-auth-tabs button{flex:1}.ym-panel[hidden]{display:none}.ym-title{font-size:32px;line-height:1.4;margin:0 0 8px}.ym-lead{color:#475569;margin:0 0 24px}
    .ym-overlay{display:none;position:fixed;inset:0;z-index:99999;background:rgba(255,255,255,.97);align-items:center;justify-content:center;padding:20px}.ym-overlay.active{display:flex}.ym-spinner{width:54px;height:54px;border:5px solid #eef2ff;border-top-color:#4338ca;border-radius:50%;animation:ym-spin .8s linear infinite;margin:0 auto 20px}@keyframes ym-spin{to{transform:rotate(360deg)}}
    .ym-theme-preview-dialog{width:min(1050px,calc(100% - 32px));max-height:calc(100vh - 32px);padding:0;border:0;border-radius:16px;box-shadow:0 30px 90px rgba(15,23,42,.35);direction:rtl}.ym-theme-preview-dialog::backdrop{background:rgba(15,23,42,.72)}.ym-preview-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #d8dfec}.ym-preview-head h2{margin:0;font-size:20px}.ym-preview-head button{width:42px;height:42px;border:0;border-radius:10px;background:#eef2ff;font-size:25px;cursor:pointer}.ym-preview-tools{display:flex;gap:8px;justify-content:center;padding:12px;background:#f6f8ff}.ym-preview-tools button,.ym-preview-tools a{padding:8px 12px;color:#3730a3;background:#fff;border:1px solid #d8dfec;border-radius:9px;text-decoration:none;font:inherit;font-weight:700;cursor:pointer}.ym-preview-tools button.active{color:#fff;background:#4338ca}.ym-preview-canvas{width:calc(100% - 32px);height:620px;margin:16px auto;overflow:auto;border:1px solid #d8dfec;border-radius:12px;background:#e2e8f0;transition:width .2s}.ym-preview-canvas.mobile{width:min(390px,calc(100% - 32px))}.ym-preview-canvas img{display:block;width:100%;height:auto;min-height:100%;object-fit:cover;object-position:top}
    @media(prefers-reduced-motion:reduce){*{scroll-behavior:auto!important;animation-duration:.01ms!important;transition-duration:.01ms!important}}
    @media(max-width:1199px){.ym-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:767px){.ym-onboarding{min-height:calc(100vh - 64px);padding:28px 16px}.ym-mobile-step{display:block;margin:0 0 10px;color:#4338ca;font-size:14px;font-weight:800}.ym-progress{grid-template-columns:repeat(5,1fr);margin-bottom:24px}.ym-progress .ym-label{display:none}.ym-progress a,.ym-progress span{justify-content:center}.ym-grid,.ym-theme-grid,.ym-layout{grid-template-columns:1fr}.ym-card{padding:20px}.ym-title{font-size:26px}.ym-actions{flex-direction:column}.ym-btn{width:100%}.ym-layout>.ym-summary{display:none}.ym-summary-mobile{display:block;order:-1}.ym-preview-canvas{height:520px}}
</style>
@endsection

@section('content')
<main class="ym-onboarding">
    <div class="ym-shell">
        <nav class="ym-progress" aria-label="{{__('Store creation steps')}}">
            @foreach([1 => 'الباقة', 2 => 'القالب', 3 => 'بيانات المتجر', 4 => 'الحساب والتحقق', 5 => 'المراجعة والإنشاء'] as $number => $label)
                @php $state = $number === $step ? 'ym-active' : ($number < $step ? 'ym-done' : ''); @endphp
                @if($number <= $maxStep)
                    <a href="{{route('landlord.store.onboarding', ['step' => $number])}}" class="{{$state}}" @if($number === $step) aria-current="step" @endif>
                        <span class="ym-dot">{{$number < $step ? '✓' : $number}}</span><span class="ym-label">{{$label}}</span>
                    </a>
                @else
                    <span class="{{$state}}" aria-disabled="true"><span class="ym-dot">{{$number}}</span><span class="ym-label">{{$label}}</span></span>
                @endif
            @endforeach
        </nav>
        <p class="ym-mobile-step">الخطوة {{$step}} من 5 — {{[1=>'الباقة',2=>'القالب',3=>'بيانات المتجر',4=>'الحساب والتحقق',5=>'المراجعة والإنشاء'][$step]}}</p>

        @if($errors->any())
            <div class="ym-alert ym-error" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>
        @endif

        @if($step === 1)
            <h1 class="ym-title">اختر باقتك</h1><p class="ym-lead">اختر الباقة المناسبة، ويمكنك تعديل اختيارك قبل إنشاء المتجر.</p>
            @if($plans->isEmpty())
                <div class="ym-card">لا توجد باقات متاحة حاليًا.</div>
            @else
                <form method="post" action="{{route('landlord.store.onboarding.plan')}}">@csrf
                    <div class="ym-grid">
                        @foreach($plans as $item)
                            <label class="ym-option">
                                <input type="radio" name="plan_id" value="{{$item->id}}" @checked($onboarding?->plan_id === $item->id) required>
                                <h2>{{$item->title}}</h2>
                                <p><strong>{!! amount_with_currency_symbol($item->price) !!}</strong> / {{[0=>'شهريًا',1=>'سنويًا',2=>'مدى الحياة'][$item->type] ?? ''}}</p>
                                @if($item->has_trial && (int)$item->trial_days > 0)<p class="ym-success">{{$item->trial_days}} يوم تجربة مجانية</p>@endif
                            </label>
                        @endforeach
                    </div>
                    <div class="ym-actions"><span></span><button class="ym-btn ym-primary" type="submit">متابعة إلى القوالب</button></div>
                </form>
            @endif
        @elseif($step === 2)
            <h1 class="ym-title">اختر قالب متجرك</h1><p class="ym-lead">المعاينة لا تختار القالب تلقائيًا. حدّد القالب ثم تابع.</p>
            @if(empty($themes))
                <div class="ym-alert ym-warning">لا يوجد قالب متاح لهذه الباقة حاليًا.</div>
            @else
                <form method="post" action="{{route('landlord.store.onboarding.theme')}}">@csrf
                    <div class="ym-grid ym-theme-grid">
                        @foreach($themes as $theme)
                            @php
                                $details = getIndividualThemeDetails($theme->slug);
                                $name = get_static_option_central($theme->slug.'_theme_name') ?: ($details['name'] ?? $theme->name);
                                $image = get_static_option_central($theme->slug.'_theme_image') ?: loadScreenshot($theme->slug);
                                $preview = get_static_option_central($theme->slug.'_theme_url');
                            @endphp
                            <div class="ym-option">
                                <label>
                                    <input type="radio" name="theme_slug" value="{{$theme->slug}}" @checked($onboarding?->theme_slug === $theme->slug) required>
                                    <img class="ym-theme-image" src="{{$image}}" alt="{{__('Preview of')}} {{$name}}">
                                    <strong>{{$name}}</strong>
                                </label>
                                <button type="button" class="ym-preview-button" data-onboarding-theme-preview data-name="{{$name}}" data-image="{{$image}}" @if($preview) data-url="{{$preview}}" @endif>معاينة القالب</button>
                            </div>
                        @endforeach
                    </div>
                    <div class="ym-actions"><a class="ym-btn ym-secondary" href="{{route('landlord.store.onboarding',['step'=>1])}}">رجوع</a><button class="ym-btn ym-primary" type="submit">متابعة</button></div>
                </form>
            @endif
        @elseif($step === 3)
            <div class="ym-layout">
                <section class="ym-card">
                    <h1 class="ym-title">عرّفنا بمتجرك</h1><p class="ym-lead">أدخل اسم المتجر وعنوانه الفرعي.</p>
                    <form method="post" action="{{route('landlord.store.onboarding.details')}}">@csrf
                        <div class="ym-field"><label for="store_name">اسم المتجر</label><input id="store_name" name="store_name" value="{{old('store_name',$onboarding?->store_name)}}" maxlength="191" autocomplete="organization" required>@error('store_name')<span class="ym-field-error">{{$message}}</span>@enderror</div>
                        <div class="ym-field"><label for="subdomain">عنوان المتجر</label><input class="ym-ltr" id="subdomain" name="subdomain" value="{{old('subdomain',$onboarding?->subdomain)}}" minlength="3" maxlength="63" pattern="[a-z0-9](?:[a-z0-9-]*[a-z0-9])?" autocomplete="off" aria-describedby="domain-preview subdomain-status" required><p class="ym-help ym-ltr"><span id="domain-preview">{{$onboarding?->subdomain ?: 'your-store'}}</span>.{{current(config('tenancy.central_domains'))}}</p><p id="subdomain-status" class="ym-domain-status" aria-live="polite"></p>@error('subdomain')<span class="ym-field-error">{{$message}}</span>@enderror</div>
                        <div class="ym-actions"><a class="ym-btn ym-secondary" href="{{route('landlord.store.onboarding',['step'=>2])}}">رجوع</a><button class="ym-btn ym-primary" type="submit">متابعة إلى الحساب</button></div>
                    </form>
                </section>
                @include('landlord.frontend.onboarding.summary')
            </div>
        @elseif($step === 4)
            <div class="ym-layout">
                <section class="ym-card">
                    <h1 class="ym-title">أنشئ حسابك</h1><p class="ym-lead">سنرسل رمز التحقق إلى بريدك، ثم تنتقل إلى مراجعة الاشتراك.</p>
                    @if($user)
                        @if($user->email_verified)
                            <div class="ym-alert ym-success">تم التحقق من الحساب: <span class="ym-ltr">{{$user->email}}</span></div>
                            <a class="ym-btn ym-primary" href="{{route('landlord.store.onboarding',['step'=>5])}}">متابعة إلى المراجعة</a>
                        @else
                            <div class="ym-alert ym-warning">يجب التحقق من بريدك قبل المتابعة.</div>
                            <a class="ym-btn ym-primary" href="{{route('landlord.store.onboarding.email.verify')}}">تحقق من البريد</a>
                        @endif
                    @else
                        <div class="ym-auth-tabs"><button type="button" class="ym-btn ym-primary" data-auth="register">حساب جديد</button><button type="button" class="ym-btn ym-secondary" data-auth="login">تسجيل الدخول</button></div>
                        <div id="auth-message" aria-live="polite"></div>
                        <form id="register-panel" class="ym-panel">
                            <div class="ym-field"><label for="reg_name">الاسم الثلاثي</label><input id="reg_name" autocomplete="name" maxlength="191" required></div>
                            <div class="ym-field"><label for="reg_email">البريد الإلكتروني</label><input class="ym-ltr" id="reg_email" type="email" autocomplete="email" required></div>
                            <div class="ym-field"><label for="reg_phone">رقم الهاتف</label><input class="ym-ltr" id="reg_phone" type="tel" autocomplete="tel" required></div>
                            <div class="ym-field"><label for="reg_password">كلمة المرور</label><div class="ym-password-wrap"><input class="ym-ltr" id="reg_password" type="password" autocomplete="new-password" minlength="8" required><button class="ym-password-toggle" type="button" data-password-toggle="reg_password" aria-label="إظهار كلمة المرور">إظهار</button></div></div>
                            <div class="ym-field"><label for="reg_password_confirmation">تأكيد كلمة المرور</label><div class="ym-password-wrap"><input class="ym-ltr" id="reg_password_confirmation" type="password" autocomplete="new-password" minlength="8" required><button class="ym-password-toggle" type="button" data-password-toggle="reg_password_confirmation" aria-label="إظهار تأكيد كلمة المرور">إظهار</button></div></div>
                            <label><input id="reg_terms" type="checkbox" required> أوافق على الشروط والأحكام</label>
                            <div class="ym-actions"><a class="ym-btn ym-secondary" href="{{route('landlord.store.onboarding',['step'=>3])}}">رجوع</a><button id="register-btn" class="ym-btn ym-primary" type="submit">إرسال رمز التحقق</button></div>
                        </form>
                        <form id="otp-panel" class="ym-panel" hidden>
                            <div class="ym-field"><label for="otp">رمز التحقق</label><input class="ym-ltr" id="otp" inputmode="numeric" autocomplete="one-time-code" minlength="6" maxlength="6" pattern="[0-9]{6}" required><p class="ym-help">أرسلنا الرمز إلى <span id="otp-email" class="ym-ltr"></span></p></div>
                            <div class="ym-actions"><button id="resend-btn" class="ym-btn ym-secondary" type="button" disabled>إعادة الإرسال</button><button class="ym-btn ym-primary" type="submit">تحقق وتابع</button></div>
                        </form>
                        <form id="login-panel" class="ym-panel" hidden>
                            <div class="ym-field"><label for="login_email">البريد الإلكتروني</label><input class="ym-ltr" id="login_email" type="email" autocomplete="email" required></div>
                            <div class="ym-field"><label for="login_password">كلمة المرور</label><div class="ym-password-wrap"><input class="ym-ltr" id="login_password" type="password" autocomplete="current-password" required><button class="ym-password-toggle" type="button" data-password-toggle="login_password" aria-label="إظهار كلمة المرور">إظهار</button></div></div>
                            <div class="ym-actions"><a class="ym-btn ym-secondary" href="{{route('landlord.user.forget.password')}}">استعادة الوصول</a><button class="ym-btn ym-primary" type="submit">تسجيل الدخول والمتابعة</button></div>
                        </form>
                    @endif
                </section>
                @include('landlord.frontend.onboarding.summary')
            </div>
        @else
            <div class="ym-layout">
                <section class="ym-card">
                    <h1 class="ym-title">راجع اشتراكك</h1><p class="ym-lead">راجع اختياراتك قبل إنشاء المتجر.</p>
                    @if($onboarding?->status === 'ready')
                        @if($readyDashboardUrl)
                            <div class="ym-alert ym-success">متجرك جاهز. يمكنك فتح لوحة التحكم مباشرة.</div>
                            <a class="ym-btn ym-primary" href="{{$readyDashboardUrl}}">فتح لوحة تحكم المتجر</a>
                        @else
                            <div class="ym-alert ym-warning">المتجر مسجل، لكن تعذر إنشاء رابط لوحة التحكم. تواصل مع الدعم واذكر المرجع: <span class="ym-ltr">{{$onboarding->id}}</span></div>
                        @endif
                    @elseif($planChanged)
                        <div class="ym-alert ym-warning">
                            <strong>تغيرت تفاصيل الباقة أثناء الرحلة.</strong><br>
                            @if(isset($onboarding->plan_snapshot['price'])) السعر السابق: {!! amount_with_currency_symbol($onboarding->plan_snapshot['price']) !!} — @endif السعر الحالي: {!! amount_with_currency_symbol($plan->price) !!}.<br>
                            @if(isset($onboarding->plan_snapshot['trial_days'])) التجربة السابقة: {{$onboarding->plan_snapshot['trial_days']}} يومًا — @endif التجربة الحالية: {{$plan->trial_days}} يومًا.
                        </div>
                        <form method="post" action="{{route('landlord.store.onboarding.plan-change')}}">@csrf<button class="ym-btn ym-primary" type="submit">راجعت التغيير، متابعة</button></form>
                    @elseif(!$trialEligible)
                        <div class="ym-alert ym-warning">التجربة المجانية غير متاحة لهذا الحساب أو لهذه الباقة. يمكنك العودة إلى خيارات الاشتراك من حسابك.</div>
                        <a class="ym-btn ym-secondary" href="{{route('landlord.user.home')}}">العودة إلى حسابي</a>
                    @else
                        <div class="ym-alert ym-success"><strong>المطلوب الآن: {!! amount_with_currency_symbol(0) !!}</strong><br>لا يلزم دفع رسوم الاشتراك لبدء التجربة المؤهلة.</div>
                        <label><input id="final-terms" type="checkbox"> أوافق على الشروط والأحكام</label>
                        <div id="complete-message" aria-live="polite"></div>
                        <div class="ym-actions"><a class="ym-btn ym-secondary" href="{{route('landlord.store.onboarding',['step'=>3])}}">تعديل بيانات المتجر</a><button id="complete-btn" class="ym-btn ym-primary" type="button">إنشاء المتجر</button></div>
                    @endif
                </section>
                @include('landlord.frontend.onboarding.summary', ['expanded' => true])
            </div>
        @endif
    </div>
</main>

<div id="provisioning-overlay" class="ym-overlay" role="status" aria-live="polite">
    <div class="ym-card" style="max-width:430px;text-align:center"><div class="ym-spinner" aria-hidden="true"></div><h2>جار إنشاء متجرك وتهيئته</h2><p>سيتم تحويلك تلقائيًا إلى لوحة التحكم بعد اكتمال التجهيز.</p><div id="provisioning-error"></div></div>
</div>

<dialog class="ym-theme-preview-dialog" id="onboarding-theme-preview" aria-labelledby="onboarding-preview-title">
    <div class="ym-preview-head"><h2 id="onboarding-preview-title">معاينة القالب</h2><button type="button" aria-label="إغلاق المعاينة" data-onboarding-preview-close>×</button></div>
    <div class="ym-preview-tools"><button type="button" class="active" data-onboarding-preview-size="desktop">سطح المكتب</button><button type="button" data-onboarding-preview-size="mobile">الجوال</button><a target="_blank" rel="noopener" hidden data-onboarding-preview-link>فتح الموقع</a></div>
    <div class="ym-preview-canvas" data-onboarding-preview-canvas><img alt="" data-onboarding-preview-image></div>
</dialog>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{csrf_token()}}';
    const message = document.getElementById('auth-message');
    const showMessage = (text, type = 'error', target = message) => {
        if(!target) return;
        const alert = document.createElement('div');
        alert.className = `ym-alert ym-${type}`;
        alert.style.whiteSpace = 'pre-line';
        alert.textContent = text;
        target.replaceChildren(alert);
    };
    const request = async (url, data) => {
        const response = await fetch(url, {method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf}, body:JSON.stringify(data)});
        const json = await response.json().catch(() => ({}));
        if(!response.ok) throw {response, json};
        return json;
    };

    const subdomain = document.getElementById('subdomain');
    const subdomainStatus = document.getElementById('subdomain-status');
    let subdomainTimer;
    const checkSubdomain = async () => {
        const value = subdomain.value.trim().toLowerCase();
        subdomain.value = value;
        if(!subdomain.checkValidity()) { subdomainStatus.textContent = value ? 'استخدم أحرفًا إنجليزية صغيرة وأرقامًا وشرطات فقط.' : ''; subdomainStatus.className = 'ym-domain-status unavailable'; return; }
        subdomainStatus.textContent = 'جار التحقق من توفر الرابط…'; subdomainStatus.className = 'ym-domain-status checking';
        try {
            await request('{{route('landlord.subdomain.check')}}', {subdomain:value});
            subdomainStatus.textContent = 'الرابط متاح.'; subdomainStatus.className = 'ym-domain-status available';
        } catch(error) {
            subdomainStatus.textContent = Object.values(error.json?.errors || {}).flat().join(' ') || 'الرابط غير متاح.';
            subdomainStatus.className = 'ym-domain-status unavailable';
        }
    };
    if(subdomain) {
        subdomain.addEventListener('input', () => {
            document.getElementById('domain-preview').textContent = subdomain.value || 'your-store';
            clearTimeout(subdomainTimer); subdomainTimer = setTimeout(checkSubdomain, 500);
        });
        subdomain.addEventListener('blur', () => { clearTimeout(subdomainTimer); if(subdomain.value) checkSubdomain(); });
    }

    document.querySelectorAll('[data-password-toggle]').forEach(button => button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        const revealing = input.type === 'password';
        input.type = revealing ? 'text' : 'password';
        button.textContent = revealing ? 'إخفاء' : 'إظهار';
        button.setAttribute('aria-label', revealing ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
    }));

    const themeDialog = document.getElementById('onboarding-theme-preview');
    const themeImage = themeDialog?.querySelector('[data-onboarding-preview-image]');
    const themeLink = themeDialog?.querySelector('[data-onboarding-preview-link]');
    const themeCanvas = themeDialog?.querySelector('[data-onboarding-preview-canvas]');
    let themeOpener;
    document.querySelectorAll('[data-onboarding-theme-preview]').forEach(button => button.addEventListener('click', event => {
        event.preventDefault(); event.stopPropagation(); themeOpener = button;
        themeImage.src = button.dataset.image; themeImage.alt = `معاينة قالب ${button.dataset.name}`;
        document.getElementById('onboarding-preview-title').textContent = `معاينة قالب ${button.dataset.name}`;
        themeLink.hidden = !button.dataset.url;
        if(button.dataset.url) themeLink.href = button.dataset.url; else themeLink.removeAttribute('href');
        themeDialog.showModal();
    }));
    themeDialog?.querySelector('[data-onboarding-preview-close]')?.addEventListener('click', () => themeDialog.close());
    themeDialog?.addEventListener('close', () => themeOpener?.focus());
    themeDialog?.addEventListener('click', event => { if(event.target === themeDialog) themeDialog.close(); });
    themeDialog?.querySelectorAll('[data-onboarding-preview-size]').forEach(button => button.addEventListener('click', () => {
        themeDialog.querySelectorAll('[data-onboarding-preview-size]').forEach(item => item.classList.toggle('active', item === button));
        themeCanvas.classList.toggle('mobile', button.dataset.onboardingPreviewSize === 'mobile');
    }));

    document.querySelectorAll('[data-auth]').forEach(button => button.addEventListener('click', () => {
        const selected = button.dataset.auth;
        document.getElementById('register-panel').hidden = selected !== 'register';
        document.getElementById('login-panel').hidden = selected !== 'login';
        document.getElementById('otp-panel').hidden = true;
    }));

    const register = document.getElementById('register-panel');
    let resendTimer;
    const beginCooldown = seconds => {
        const button = document.getElementById('resend-btn');
        clearInterval(resendTimer); button.disabled = true;
        let left = seconds;
        const tick = () => { button.textContent = left > 0 ? `إعادة الإرسال (${left})` : 'إعادة الإرسال'; button.disabled = left > 0; left--; };
        tick(); resendTimer = setInterval(() => { tick(); if(left < 0) clearInterval(resendTimer); }, 1000);
    };
    if(register) register.addEventListener('submit', async event => {
        event.preventDefault();
        try {
            const data = await request('{{route('landlord.user.register.otp.store')}}', {
                name:document.getElementById('reg_name').value,
                email:document.getElementById('reg_email').value,
                phone:document.getElementById('reg_phone').value,
                password:document.getElementById('reg_password').value,
                password_confirmation:document.getElementById('reg_password_confirmation').value,
                terms_condition:document.getElementById('reg_terms').checked ? 'on' : ''
            });
            if(data.status !== 'otp_sent') throw {json:data};
            document.getElementById('otp-email').textContent = data.email;
            register.hidden = true; document.getElementById('otp-panel').hidden = false;
            beginCooldown(data.retry_after || 60); document.getElementById('otp').focus();
        } catch(error) { showMessage(Object.values(error.json?.errors || {}).flat().join('\n') || error.json?.msg || 'تعذر إرسال رمز التحقق.'); }
    });

    const otp = document.getElementById('otp-panel');
    if(otp) otp.addEventListener('submit', async event => {
        event.preventDefault();
        try {
            const data = await request('{{route('landlord.user.register.otp.verify')}}', {otp:document.getElementById('otp').value});
            if(data.status !== 'valid' || !data.redirect_url) throw {json:data};
            window.location.assign(data.redirect_url);
        }
        catch(error) { showMessage(error.json?.msg || 'رمز التحقق غير صحيح.'); }
    });
    const resend = document.getElementById('resend-btn');
    if(resend) resend.addEventListener('click', async () => {
        try {
            const data = await request('{{route('landlord.user.register.otp.resend')}}', {});
            if(data.status !== 'resent') throw {json:data};
            beginCooldown(data.retry_after || 60); showMessage(data.msg, 'success');
        }
        catch(error) { showMessage(error.json?.msg || 'تعذر إعادة إرسال الرمز.'); }
    });

    const login = document.getElementById('login-panel');
    if(login) login.addEventListener('submit', async event => {
        event.preventDefault();
        try {
            const data = await request('{{route('landlord.user.ajax.login')}}', {username:document.getElementById('login_email').value,password:document.getElementById('login_password').value});
            if(data.status !== 'valid' || !data.redirect_url) throw {json:data};
            window.location.assign(data.redirect_url);
        } catch(error) { showMessage(error.json?.msg || 'تعذر تسجيل الدخول.'); }
    });

    const complete = document.getElementById('complete-btn');
    const completeMessage = document.getElementById('complete-message');
    if(complete) complete.addEventListener('click', async () => {
        if(!document.getElementById('final-terms').checked) { showMessage('وافق على الشروط والأحكام للمتابعة.', 'error', completeMessage); return; }
        complete.disabled = true; document.getElementById('provisioning-overlay').classList.add('active');
        try {
            const data = await request('{{route('landlord.store.onboarding.complete')}}', {terms_condition:true});
            if(data.dashboard_url) window.location.assign(data.dashboard_url);
            else if(data.status === 'ready') throw {json:{message:'المتجر جاهز، لكن تعذر إنشاء رابط لوحة التحكم. تواصل مع الدعم باستخدام مرجع الطلب.'}};
            else pollStatus();
        } catch(error) {
            document.getElementById('provisioning-overlay').classList.remove('active'); complete.disabled = false;
            showMessage(error.json?.message || 'لم يكتمل تجهيز متجرك بعد.', 'error', completeMessage);
        }
    });
    const pollStatus = () => setTimeout(async () => {
        try {
            const response = await fetch('{{route('landlord.store.onboarding.status')}}', {headers:{'Accept':'application/json'}});
            const data = await response.json();
            if(data.dashboard_url) window.location.assign(data.dashboard_url);
            else if(data.status === 'failed' || data.status === 'ready') {
                document.getElementById('provisioning-overlay').classList.remove('active');
                complete.disabled = false;
                showMessage(data.status === 'ready' ? 'المتجر جاهز، لكن تعذر إنشاء رابط لوحة التحكم. تواصل مع الدعم باستخدام مرجع الطلب.' : 'لم يكتمل تجهيز متجرك بعد.', 'error', completeMessage);
            } else pollStatus();
        } catch(error) {
            document.getElementById('provisioning-overlay').classList.remove('active');
            complete.disabled = false;
            showMessage('تعذر التحقق من حالة المتجر. أعد المحاولة من نفس الصفحة.', 'error', completeMessage);
        }
    }, 2000);
});
</script>
@endsection
