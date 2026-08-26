@extends('landlord.frontend.frontend-page-master')
@section('title')
    {{$order_details->title}}
@endsection

@section('page-title')
    {{__('Order For')}} {{' : '.$order_details->title}}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/toastr.css')}}">
    <style>
        /* ── Store creation progress overlay ── */
        #storeProgressOverlay { display: none; }
        #storeProgressOverlay.active {
            display: flex !important;
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(var(--section-bg-1-rgb, 255,255,255),0.97);
            align-items: center;
            justify-content: center;
        }

        .op-pstep.pstep-active .op-pstep-ico    { border-color: var(--main-color-one, #0C4D54); background-color: #f0fafa; }
        .op-pstep.pstep-active .op-pstep-pending { display: none !important; }
        .op-pstep.pstep-active .op-pstep-spin   { display: block !important; }
        .op-pstep.pstep-active .op-pstep-label  { color: var(--main-color-one, #0C4D54); font-weight: 600; }

        .op-pstep.pstep-done .op-pstep-ico      { border-color: var(--main-color-one, #0C4D54); background-color: var(--main-color-one, #0C4D54); }
        .op-pstep.pstep-done .op-pstep-pending  { display: none !important; }
        .op-pstep.pstep-done .op-pstep-spin     { display: none !important; }
        .op-pstep.pstep-done .op-pstep-check    { display: block !important; }
        .op-pstep.pstep-done .op-pstep-label    { color: var(--body-color, #374151); }

        @keyframes opStorePulse {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.12); }
        }
        #opProgressIcon { animation: opStorePulse 1.6s ease-in-out infinite; }
        #opProgressIcon.op-icon-done { animation: none; color: #166534; }
        #opProgressTitle.op-success  { color: #166534; }
        .theme-card.selected_theme {
            border-color: var(--sectionC, var(--main-color-one, #0C4D54));
        }
        .theme-card.selected_theme .theme-checkmark {
            display: flex !important;
        }
        /* ---- Payment gateway grid (helper renders ul > li) ---- */
        .payment-gateway-wrapper ul {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        @media (min-width: 640px) {
            .payment-gateway-wrapper ul {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }
        }
        @media (min-width: 768px) {
            .payment-gateway-wrapper ul {
                grid-template-columns: repeat(7, minmax(0, 1fr));
            }
        }
        .payment-gateway-wrapper ul li {
            position: relative;
            cursor: pointer;
            background: var(--section-bg-1, #fff);
            border: 2px solid var(--extra-light-color, #d1d5db);
            border-radius: 8px;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.2s;
            min-height: 46px;
        }
        .payment-gateway-wrapper ul li:hover {
            border-color: var(--sectionC, var(--main-color-one, #0C4D54));
        }
        .payment-gateway-wrapper ul li .img-select{
            width: 100%;
        }
        .payment-gateway-wrapper ul li img {
            width: 100%;
            height: 26px;
            object-fit: cover;
        }
        .payment-gateway-wrapper ul li.selected {
            border-color: var(--sectionC, var(--main-color-one, #0C4D54));
        }
        .payment-gateway-wrapper ul li.selected::after {
            content: '';
            position: absolute;
            top: 4px;
            right: 4px;
            width: 18px;
            height: 18px;
            background-color: var(--sectionC, var(--main-color-one, #0C4D54));
            border-radius: 50%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
            background-size: 11px;
            background-repeat: no-repeat;
            background-position: center;
        }
        .payment_gateway_extra_field_information_wrap {
            margin-top: 16px;
        }
        .order-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .del-amount {
            font-size: 1rem;
            margin-left: 6px;
        }
        /* ── Theme card image area ── */
        .theme-card-img {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: var(--section-bg-3, #F0FCFB);
        }
        .theme-card-img img {
            width: 100%;
            height: auto;
            min-height: 100%;
            object-fit: cover;
            object-position: top center;
            pointer-events: none;
            transition: transform 6s cubic-bezier(.25,.46,.45,.94);
        }
        .theme-card:hover .theme-card-img img {
            transform: translateY(calc(-100% + 200px));
        }
        /* ── Gradient overlay ── */
        .theme-card-gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 30%, rgba(15,23,42,.65) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 1;
        }
        .theme-card:hover .theme-card-gradient { opacity: 1; }
        /* ── Centered preview icon ── */
        .theme-preview-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.6);
            width: 46px;
            height: 46px;
            background: rgba(255,255,255,0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #111827;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
            z-index: 2;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }
        .theme-card:hover .theme-preview-icon {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        /* ── Card footer ── */
        .theme-card-footer {
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        /* ---- Trial/Purchase mode toggle ---- */
        .mode-toggle-btn {
            flex: 1;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--body-color, #6b7280);
        }
        .mode-toggle-btn.active {
            background: var(--sectionC, var(--main-color-one, #0C4D54));
            color: #fff;
            box-shadow: 0 2px 8px rgba(12,77,84,0.18);
        }
        /* ---- Login modal overlay ---- */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open {
            display: flex;
        }
    </style>
@endsection

@section('content')
    @php
        $user          = Auth::guard('web')->user();
        $is_auth       = !is_null($user);
        $default_theme = get_static_option('default_theme');
    @endphp

    {{-- Flash messages (shown when controller redirects back with an error) --}}
    <x-flash-msg-tw/>

    {{-- Store creation progress overlay --}}
    <div id="storeProgressOverlay">
        <div class="flex flex-col items-center text-center px-6 w-full max-w-sm">

            <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mb-5">
                <i class="ti tabler-building-store text-primary text-4xl" id="opProgressIcon"></i>
            </div>

            <h3 class="text-xl font-bold text-secondary font-urbanist mb-1" id="opProgressTitle">{{__('Creating Your Store')}}</h3>
            <p class="text-sm mb-6 min-h-[20px]">
                <span id="opProgressStoreName" class="font-medium text-primary"></span>
            </p>

            <div class="w-full mb-6">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span id="opProgressPct" class="font-semibold text-primary">0%</span>
                    <span id="opProgressEta" class="italic" style="color: var(--body-color, #9ca3af)">{{__('Estimated: 10–20 seconds')}}</span>
                </div>
                <div class="w-full rounded-full h-2 overflow-hidden" style="background-color: var(--section-bg-3, #f3f4f6)">
                    <div id="opProgressBar" class="h-2 rounded-full bg-primary transition-all duration-700 ease-out" style="width: 0%"></div>
                </div>
            </div>

            <div class="w-full space-y-2.5 mb-6 text-left">
                @foreach([
                    [1, __('Free Trial Activated')],
                    [2, __('Store Created')],
                    [3, __('Database Initialized')],
                    [4, __('Installing Theme')],
                    [5, __('Configuring Store Settings')],
                    [6, __('Launching Store')],
                ] as [$n, $label])
                <div class="op-pstep flex items-center gap-3" data-pstep="{{ $n }}">
                    <span class="op-pstep-ico w-7 h-7 rounded-full border-2 border-borderCS flex items-center justify-center shrink-0 transition-all duration-300">
                        <i class="ti tabler-minus text-[11px] op-pstep-pending" style="color: var(--extra-light-color, #d1d5db)"></i>
                        <svg class="hidden w-3.5 h-3.5 text-primary animate-spin op-pstep-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        <i class="ti tabler-check hidden text-white text-[11px] op-pstep-check"></i>
                    </span>
                    <span class="text-sm op-pstep-label transition-colors duration-300" style="color: var(--body-color, #9ca3af)">{{ $label }}</span>
                </div>
                @endforeach
            </div>

            <div class="flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700 w-full">
                <i class="ti tabler-alert-triangle text-amber-500 shrink-0 text-sm mt-0.5"></i>
                <span>{{__("Please don't close this window while your store is being created.")}}</span>
            </div>

        </div>
    </div>

    <section class="py-16" style="background-color: var(--section-bg-3, #f4f8fb)">
        <div class="container mx-auto px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                {{-- ===== LEFT COLUMN: Form ===== --}}
                <div class="lg:col-span-2">

                    {{-- Package Description --}}
                    <div class="mb-6">
                        <h2 class="text-2xl font-semibold font-urbanist text-secondary mb-1">{{$order_details->title}}</h2>
                        @if($order_details->description)
                            <p style="color: var(--body-color, #666666)">{{$order_details->description}}</p>
                        @endif
                    </div>

                    {{-- Plan warnings --}}
                    @if(count($payment_old_data) > 0)
                        <div class="mb-4 bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 text-blue-800 text-sm">
                            @if(count($payment_old_data) == 1)
                                {{__('You have already subscribed a plan. If you purchase any package than your old package will be replaced with extended validity!!')}}
                            @else
                                {{__('You have already subscribed multiple plans. If you purchase any package than your old package will be replaced with extended validity!!')}}
                            @endif
                        </div>
                    @endif

                    <x-flash-msg/>
                    <x-error-msg/>

                    {{-- ── TRIAL / PURCHASE TOGGLE ── --}}
                    @if($show_trial_option)
                        <div class="mb-6 border border-borderCS rounded-2xl p-2 flex gap-1" style="background-color: var(--section-bg-3, #F8FAFB)">
                            <button type="button" class="mode-toggle-btn active" data-mode="purchase">
                                <i class="icon-base ti tabler-shopping-cart mr-1"></i>
                                {{__('Purchase')}}
                            </button>
                            <button type="button" class="mode-toggle-btn" data-mode="trial">
                                <i class="icon-base ti tabler-rocket mr-1"></i>
                                {{__('Start Free Trial')}}
                                <span class="text-xs opacity-75">({{$order_details->trial_days}} {{__('days')}})</span>
                            </button>
                        </div>
                    @elseif($order_details->has_trial == 1 && $user_has_trial)
                        {{-- Trial already used — Purchase is active, Free Trial is locked --}}
                        <div class="mb-4 border border-borderCS rounded-2xl p-2 flex gap-1" style="background-color: var(--section-bg-3, #F8FAFB)">
                            <button type="button" class="mode-toggle-btn active" data-mode="purchase">
                                <i class="icon-base ti tabler-shopping-cart mr-1"></i>
                                {{__('Purchase')}}
                            </button>
                            <button type="button" class="mode-toggle-btn opacity-50 cursor-not-allowed" disabled title="{{__('You have already used your free trial')}}">
                                <i class="icon-base ti tabler-lock mr-1"></i>
                                {{__('Free Trial')}}
                                <span class="text-xs opacity-75">({{$order_details->trial_days}} {{__('days')}})</span>
                            </button>
                        </div>
                        <div class="mb-6 flex items-start gap-3 bg-amber-50 border border-amber-300 rounded-xl px-5 py-4">
                            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center mt-0.5">
                                <i class="icon-base ti tabler-clock-off text-amber-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-amber-800 text-sm mb-0.5">{{__('Your free trial has already been used')}}</p>
                                <p class="text-amber-700 text-sm">{{__('Each account is eligible for one free trial only. Purchase a plan below to keep your store running.')}}</p>
                            </div>
                        </div>
                    @endif

                    {{-- ── UNIFIED ORDER FORM ── --}}
                    <form id="order-form" action="{{ route('landlord.frontend.order.payment.form') }}"
                          method="post" enctype="multipart/form-data" class="order-form">
                        @csrf
                        @php
                            $custom_fields    = unserialize($order_details->custom_fields);
                        @endphp

                        {{-- Purchase hidden inputs --}}
                        <input type="hidden" name="theme_slug"   id="theme-slug"  value="{{$default_theme}}">
                        <input type="hidden" name="payment_gateway" value="" class="payment_gateway_passing_clicking_name">
                        <input type="hidden" name="package_id"   value="{{$order_details->id}}">

                        {{-- Trial hidden inputs (user_id is patched after AJAX login if guest) --}}
                        <input type="hidden" id="trial-user-id"  value="{{$is_auth ? $user->id : ''}}">
                        <input type="hidden" id="trial-order-id" value="{{$order_details->id}}">
                        <input type="hidden" id="trial-theme"    value="{{$default_theme}}">

                        {{-- ── INFORMATION SECTION ── --}}
                        <h2 class="text-2xl font-semibold font-urbanist text-secondary mb-2">
                            {{get_static_option('order_page_form_title') ?: __('Information')}}
                        </h2>
                        <div class="rounded-2xl shadow-sm border border-borderCS p-8 md:p-10 mb-8" style="background-color: var(--section-bg-1, #F8FAFB)">

                            {{-- Name --}}
                            <div class="mb-4">
                                <label class="block text-secondary font-medium mb-2">
                                    {{__('Full Name')}}<span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="field-name" name="name"
                                    value="{{$is_auth ? $user->name : ''}}"
                                    class="w-full px-4 py-4 rounded-lg focus:outline-none"
                                    style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)"
                                    placeholder="{{__('Name')}}"
                                    {{$is_auth ? 'readonly' : ''}}>
                            </div>

                            {{-- Email --}}
                            <div class="mb-4">
                                <label class="block text-secondary font-medium mb-2">
                                    {{__('Email Address')}}<span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="field-email" name="email"
                                    value="{{$is_auth ? $user->email : ''}}"
                                    class="w-full px-4 py-4 rounded-lg focus:outline-none"
                                    style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)"
                                    placeholder="{{__('Your Email')}}"
                                    {{$is_auth ? 'readonly' : ''}}>
                            </div>

                            {{-- Existing tenant dropdown (only shown when logged in) --}}
                            <div id="subdomain-select-wrap" class="{{$is_auth ? '' : 'hidden'}} mb-4">
                                <label class="block text-secondary font-medium mb-2">
                                    {{__('Add new subdomain')}}
                                </label>
                                <select class="w-full px-4 py-4 rounded-lg focus:outline-none subdomain" style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)"
                                        id="subdomain" name="subdomain">
                                    <option value="custom_domain__dd" selected>{{__('Add new subdomain')}}</option>
                                    @if($is_auth)
                                        @foreach($user->tenant_details ?? [] as $tenant)
                                            <option value="{{$tenant->id}}">{{optional($tenant->domain)->domain}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="custom_subdomain_wrapper mb-2">
                                <label class="block text-secondary font-medium mb-2">
                                    {{__('Add new subdomain')}}
                                </label>
                                @php
                                    $base_url = str_replace(['http://','https://'], '', url('/'));
                                @endphp
                                <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                                    <div class="col-span-1 md:col-span-5">
                                        <input type="text"
                                               class="w-full py-4 px-4 rounded-lg focus:outline-none custom_subdomain"
                                               style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)"
                                               id="custom-subdomain" name="custom_subdomain"
                                               placeholder="{{__('Subdomain')}}" autocomplete="off" value="">
                                    </div>
                                    <div class="col-span-1 md:col-span-2">
                                        <div class="w-full py-4 px-4 rounded-lg flex items-center justify-center text-sm text-center" style="border: 1px solid var(--main-color-one, #4D8700); background-color: rgba(var(--main-color-one-rgb, 92,231,33), 0.1); color: var(--body-color, #374151)">
                                            .{{$base_url}}
                                        </div>
                                    </div>
                                </div>
                                <div id="subdomain-wrap" class="mt-1"></div>
                                <span class="text-sm mt-1 block" style="color: var(--body-color, #6b7280)">{{__('Your website will be available at subdomain.')}}{{$base_url}}</span>
                            </div>
                        </div>
                        {{-- END INFORMATION SECTION --}}

                        {{-- ── THEME SECTION ── --}}
                        <div class="theme-section mb-8">
                            <h2 class="text-2xl text-secondary font-medium mb-2">{{__('Choose Your Theme')}}</h2>
                            <div class="p-8 rounded-xl border border-borderCS" style="background-color: var(--section-bg-1, #f8fafb)">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                                    @php
//                                        $theme_list = $order_details?->plan_themes?->pluck('theme_slug')->toArray() ?? [];
                                        $theme_list = getAllThemeSlug();
                                    @endphp
                                    @foreach(getPricePlanBasedAllThemeData($theme_list) as $theme)
                                        @php
                                            $theme_slug         = $theme->slug;
                                            $theme_data         = getIndividualThemeDetails($theme_slug);
                                            $theme_image        = loadScreenshot($theme_slug);
                                            $theme_custom_name  = get_static_option_central($theme_data['slug'].'_theme_name');
                                            $theme_custom_url   = get_static_option_central($theme_data['slug'].'_theme_url');
                                            $theme_custom_image = get_static_option_central($theme_data['slug'].'_theme_image');
                                            $display_name  = !empty($theme_custom_name)  ? $theme_custom_name  : $theme_data['name'];
                                            $display_image = !empty($theme_custom_image) ? $theme_custom_image : $theme_image;
                                            $is_selected   = $default_theme == $theme_slug;
                                        @endphp
                                        <div class="theme-card rounded-xl overflow-hidden border-2 border-transparent duration-300 hover:border-sectionC {{$is_selected ? 'selected_theme' : ''}}" style="background-color: var(--section-bg-1, #ffffff)"
                                             data-theme="{{$theme_data['slug']}}"
                                             data-name="{{$display_name}}"
                                             style="cursor:pointer;">

                                            <div class="theme-card-img">
                                                <img src="{{$display_image}}" alt="{{$display_name}}" loading="lazy">
                                                <div class="theme-card-gradient"></div>
                                                <div class="theme-checkmark {{$is_selected ? 'flex' : 'hidden'}} absolute top-3 right-3 bg-primary w-7 h-7 rounded-full items-center justify-center shadow-lg z-20">
                                                    <i class="icon-base ti tabler-check text-white text-xs"></i>
                                                </div>
                                                @if(!empty($theme_custom_url))
                                                    <a href="{{$theme_custom_url}}" target="_blank"
                                                       class="theme-preview-icon"
                                                       onclick="event.stopPropagation()">
                                                        <i class="ti tabler-eye"></i>
                                                    </a>
                                                @endif
                                            </div>

                                            <div class="theme-card-footer">
                                                <h4 class="text-sm font-semibold text-secondary truncate">{{$display_name}}</h4>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        {{-- END THEME SECTION --}}

                        {{-- ── PAYMENT GATEWAY SECTION (hidden when trial mode active) ── --}}
                        @if($order_details->price != 0)
                            <div class="mb-8" id="payment-section">
                                <h2 class="text-2xl font-bold text-secondary mb-4">{{__('Payment Method')}}</h2>
                                <div class="p-8 rounded-xl border border-borderCS" style="background-color: var(--section-bg-1, #F8FAFB)">
                                    {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
                                </div>

                                {{-- Manual transaction fields --}}
                                <div class="mt-4 d-none manual_transaction_id">
                                    @if(!empty($payment_gateways))
                                        <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 text-blue-800 text-sm mb-3">
                                            {{json_decode($payment_gateways->credentials)->description ?? ''}}
                                        </div>
                                    @endif
                                    <input type="text" name="trasaction_id"
                                        class="w-full px-4 py-4 rounded-lg focus:outline-none mb-3"
                                        style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)"
                                        placeholder="{{__('Transaction ID')}}">
                                    <input type="file" name="trasaction_attachment"
                                        class="w-full px-4 py-4 rounded-lg focus:outline-none"
                                        style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff)"
                                        placeholder="{{__('Transaction Attachment')}}" accept="image/*">
                                </div>
                            </div>
                        @endif

                        {{-- ── COUPON + CTA BANNER ── --}}
                        <div class="rounded-2xl border border-borderCS p-8 mb-8" style="background-color: var(--section-bg-1, #F8FAFB)">

                            {{-- Coupon (hidden in trial mode) --}}
{{--                            <div id="coupon-section" class="mb-6">--}}
{{--                                <label class="block text-secondary font-medium mb-2">{{__('Have a coupon?')}}</label>--}}
{{--                                <div class="flex gap-3" id="coupon-form">--}}
{{--                                    <input name="coupon" id="coupon" type="text"--}}
{{--                                        class="flex-1 px-4 py-4 border border-gray-300 rounded-lg focus:outline-none"--}}
{{--                                        placeholder="{{__('Enter coupon code')}}" value="{{old('coupon')}}">--}}
{{--                                    <button type="button"--}}
{{--                                        class="px-6 py-4 bg-primary text-white rounded-lg hover:opacity-90 transition-all duration-300 whitespace-nowrap">--}}
{{--                                        {{__('Apply')}}--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <div id="coupon-section" class="mb-6">
                                <label class="block text-secondary font-medium mb-2">{{__('Have a coupon?')}}</label>
                                <div class="flex flex-col sm:flex-row gap-3" id="coupon-form">
                                    <input name="coupon" id="coupon" type="text"
                                           class="flex-1 px-4 py-4 rounded-lg focus:outline-none"
                                           style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)"
                                           placeholder="{{__('Enter coupon code')}}" value="{{old('coupon')}}">
                                    <button type="button"
                                            class="w-full sm:w-auto px-6 py-4 bg-primary text-white rounded-lg hover:opacity-90 transition-all duration-300 whitespace-nowrap">
                                        {{__('Apply')}}
                                    </button>
                                </div>
                            </div>

                            {{-- CTA Banner --}}
                            <div class="bg-primary rounded-lg px-6 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg">
                                <div class="flex flex-col">
                                    <h2 class="text-white font-urbanist text-xl font-medium mb-1" id="cta-title">{{__('Ready to launch your website?')}}</h2>
                                    <p class="text-white/70 font-normal text-sm" id="cta-subtitle">{{__('Complete your purchase and go live in minutes')}}</p>
                                </div>
{{--                                <button type="button" id="main-submit-btn"--}}
{{--                                    class="order-btn bg-white text-secondary font-medium px-6 py-3.5 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2 shadow-sm whitespace-nowrap">--}}
{{--                                    <span id="submit-btn-text">{{__('Order Package')}}</span>--}}
{{--                                    <i class="icon-base ti tabler-arrow-narrow-right"></i>--}}
{{--                                </button>--}}
                                <button type="button" id="main-submit-btn"
                                        class="order-btn bg-white text-secondary font-medium px-6 py-3.5 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2 shadow-sm whitespace-nowrap w-full sm:w-auto justify-center">
                                    <span id="submit-btn-text">{{__('Order Package')}}</span>
                                    <i class="icon-base ti tabler-arrow-narrow-right"></i>
                                </button>
                            </div>

                            {{-- Terms --}}
                            <div class="mt-4 flex items-center gap-2 justify-start">
                                <input name="terms_condition" id="terms_condition" class="terms_condition" type="checkbox">
                                <label for="terms_condition" class="text-sm text-secondary">
                                    {{__('I agree to the terms & conditions.')}}
                                    <x-fields.mandatory-indicator/>
                                </label>
                            </div>
                        </div>
                        {{-- END COUPON + CTA --}}

                    </form>
                    {{-- END ORDER FORM --}}

                </div>
                {{-- END LEFT COLUMN --}}

                {{-- ===== RIGHT COLUMN: Order Summary ===== --}}
                <div class="col-span-1">
                    <div class="sticky top-6">
                    <h2 class="text-2xl font-semibold font-urbanist text-secondary ml-1 mb-2">{{__('Order Summary')}}</h2>
                    <div class="border border-borderCS rounded-2xl p-8" style="background-color: var(--section-bg-1, #F8FAFB)">

                        {{-- Header --}}
                        <div class="mb-8">
                            <h2 class="text-2xl font-urbanist font-semibold text-secondary mb-1">{{$order_details->title}}</h2>
                            <p class="text-sm" style="color: var(--body-color, #666666)">
                                @php
                                    $validity = match ($order_details->type)
                                    {
                                        0 => __('Monthly subscription'),
                                        1 => __('Yearly subscription'),
                                        2 => __('Lifetime')
                                    }
                                @endphp
                                {{$validity}}
                            </p>
                        </div>

                        {{-- Price --}}
                        <div class="mb-8">
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-urbanist font-semibold text-secondary total-price">
                                    {{amount_with_currency_symbol($order_details->price)}}
                                </span>
                                <span class="text-sm" style="color: var(--body-color, #666666)">{{$order_details->period}}</span>
                            </div>
                        </div>

                        <div class="border-t border-borderCS mb-6"></div>

                        {{-- Features --}}
                        <div class="mb-8">
                            <h3 class="text-base font-medium text-secondary mb-4">{{__("What's Included:")}}</h3>
                            <div class="space-y-3">
                                @if(!empty($order_details->page_permission_feature))
                                    @php
                                        $page_permission_feature = $order_details->page_permission_feature > 0 ? $order_details->page_permission_feature : __('Unlimited');
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--body-color, #374151)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span style="color: var(--body-color, #374151)">{{__(sprintf('Page %s', $page_permission_feature))}}</span>
                                    </div>
                                @endif

                                @if(!empty($order_details->blog_permission_feature))
                                    @php
                                        $blog_permission_feature = $order_details->blog_permission_feature > 0 ? $order_details->blog_permission_feature : __('Unlimited');
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--body-color, #374151)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                        <span style="color: var(--body-color, #374151)">{{__(sprintf('Blog %s', $blog_permission_feature))}}</span>
                                    </div>
                                @endif

                                @if(!empty($order_details->product_permission_feature))
                                    @php
                                        $product_permission_feature = $order_details->product_permission_feature > 0 ? $order_details->product_permission_feature : __('Unlimited');
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--body-color, #374151)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span style="color: var(--body-color, #374151)">{{ __(sprintf('Product %s', $product_permission_feature))}}</span>
                                    </div>
                                @endif

                                @if(!empty($order_details->storage_permission_feature))
                                    @php
                                        $storage_permission_feature = $order_details->storage_permission_feature > 0 ? [$order_details->storage_permission_feature, 'MB']: [__('Unlimited'), ''];
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--body-color, #374151)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span style="color: var(--body-color, #374151)">{{__(sprintf('Storage %s %s', current($storage_permission_feature), last($storage_permission_feature)))}}</span>
                                    </div>
                                @endif

                                @foreach($order_details->plan_features as $key => $item)
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--body-color, #374151)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span style="color: var(--body-color, #374151)">{{__(str_replace('_', ' ', ucfirst($item->feature_name))) ?? ''}}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t border-borderCS mb-6"></div>

                        {{-- Pricing breakdown --}}
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center">
                                <span style="color: var(--body-color, #374151)">{{__('Subtotal')}}</span>
                                <span class="font-medium text-secondary">{{amount_with_currency_symbol($order_details->price)}}</span>
                            </div>
                            <div class="flex justify-between items-center coupon-discount-row" style="display:none!important;">
                                <span style="color: var(--body-color, #374151)">{{__('Discount')}}</span>
                                <span class="text-green-600 font-medium coupon-discount-amount"></span>
                            </div>
                        </div>

                        <div class="border-t border-borderCS mb-6"></div>

                        {{-- Total --}}
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-secondary font-medium text-lg">{{__('Total')}}</span>
                            <span class="text-secondary font-medium text-lg total-price-display">{{amount_with_currency_symbol($order_details->price)}}</span>
                        </div>

                        {{-- Guarantee --}}
                        <div class="rounded-xl p-4" style="background-color: rgba(var(--main-color-one-rgb, 92,231,33), 0.1); border: 2px solid var(--main-color-one, #4D8700)">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 flex-shrink-0 mt-0.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <div>
                                    <h4 class="font-medium mb-1 text-secondary">{{__('Secure Checkout')}}</h4>
                                    <p class="text-sm" style="color: var(--body-color, #666666)">{{__('Your payment information is safe and secure.')}}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                    </div>{{-- end sticky --}}
                </div>
                {{-- END RIGHT COLUMN --}}

            </div>
        </div>
    </section>

    {{-- ===== LOGIN / REGISTER MODAL ===== --}}
    <div class="modal-overlay" id="loginModal">
        <div class="rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-y-auto" style="max-height:92vh; background-color: var(--section-bg-1, #ffffff)">

            {{-- Header --}}
            <div class="flex items-center justify-between px-8 py-5" style="border-bottom: 1px solid var(--extra-light-color, #f3f4f6)">
                <div>
                    {!! render_image_markup_by_attachment_id(get_static_option('site_logo'), 'class="h-8 object-contain"') !!}
                </div>
                <button type="button" class="modal-close-btn w-8 h-8 flex items-center justify-center rounded-full transition-colors" style="color: var(--body-color, #6b7280)" data-modal="loginModal">
                    <i class="icon-base ti tabler-x"></i>
                </button>
            </div>

            {{-- LOGIN PANEL --}}
            <div id="modal-login-panel" class="px-8 py-8">
                <h4 class="text-xl font-urbanist font-semibold text-secondary mb-1">{{__('Hello! Let us get started')}}</h4>
                <p class="text-sm mb-6" style="color: var(--body-color, #666666)">{{__('Sign in to continue.')}}</p>
                <div id="login-modal-msg"></div>
                <form id="login_form_order_page">
                    <div class="mb-4">
                        <label class="block text-secondary font-medium mb-2">{{__('Username')}}</label>
                        <input type="text" name="username"
                            class="w-full px-4 py-4 rounded-lg focus:outline-none"
                            style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)"
                            placeholder="{{__('Username')}}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-secondary font-medium mb-2">{{__('Password')}}</label>
                        <input type="password" name="password"
                            class="w-full px-4 py-4 rounded-lg focus:outline-none"
                            style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)"
                            placeholder="{{__('Password')}}">
                    </div>
                    <div class="flex items-center justify-between mb-6 text-sm">
                        <label class="flex items-center gap-2 text-secondary">
                            <input class="check-input" type="checkbox" name="remember">
                            {{__('Keep me signed in')}}
                        </label>
                        <a href="{{route('tenant.user.forget.password')}}" class="text-sectionC hover:underline">{{__('Forgot password?')}}</a>
                    </div>
                    <button type="button" id="login_btn"
                        class="w-full bg-primary text-white font-medium px-6 py-3.5 rounded-lg hover:opacity-90 transition-all duration-300">
                        {{__('SIGN IN')}}
                    </button>
                    <p class="text-center text-sm mt-4" style="color: var(--body-color, #6b7280)">
                        {{__('Do not have an account?')}}
                        <a href="#" id="switch-to-register" class="text-sectionC hover:underline">{{__('Create')}}</a>
                    </p>
                </form>
            </div>

            {{-- REGISTER PANEL --}}
            <div id="modal-register-panel" class="px-8 py-8" style="display:none;">
                <h4 class="text-xl font-urbanist font-semibold text-secondary mb-1">{{__('Create your account')}}</h4>
                <p class="text-sm mb-6" style="color: var(--body-color, #666666)">{{__('Fill in your details to continue.')}}</p>
                <div id="register-modal-msg"></div>
                <div class="mb-4">
                    <label class="block text-secondary font-medium mb-2">{{__('Full Name')}}</label>
                    <input type="text" id="reg_name" class="w-full px-4 py-4 rounded-lg focus:outline-none" style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)" placeholder="{{__('Your Name')}}">
                </div>
                <div class="mb-4">
                    <label class="block text-secondary font-medium mb-2">{{__('Email')}}</label>
                    <input type="email" id="reg_email" class="w-full px-4 py-4 rounded-lg focus:outline-none" style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)" placeholder="{{__('Your Email')}}">
                </div>
                <div class="mb-4">
                    <label class="block text-secondary font-medium mb-2">{{__('Phone')}}</label>
                    <input type="text" id="reg_phone" class="w-full px-4 py-4 rounded-lg focus:outline-none" style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)" placeholder="{{__('Your Phone Number')}}">
                </div>
                <div class="mb-4">
                    <label class="block text-secondary font-medium mb-2">{{__('Username')}}</label>
                    <input type="text" id="reg_username" class="w-full px-4 py-4 rounded-lg focus:outline-none" style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)" placeholder="{{__('Choose a Username')}}">
                </div>
                <div class="mb-4">
                    <label class="block text-secondary font-medium mb-2">{{__('Password')}}</label>
                    <input type="password" id="reg_password" class="w-full px-4 py-4 rounded-lg focus:outline-none" style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)" placeholder="{{__('Minimum 8 characters')}}">
                </div>
                <div class="mb-6">
                    <label class="block text-secondary font-medium mb-2">{{__('Confirm Password')}}</label>
                    <input type="password" id="reg_password_confirmation" class="w-full px-4 py-4 rounded-lg focus:outline-none" style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)" placeholder="{{__('Confirm Password')}}">
                </div>
                <button type="button" id="register_btn"
                    class="w-full bg-primary text-white font-medium px-6 py-3.5 rounded-lg hover:opacity-90 transition-all duration-300">
                    {{__('CREATE ACCOUNT')}}
                </button>
                <p class="text-center text-sm mt-4" style="color: var(--body-color, #6b7280)">
                    {{__('Already have an account?')}}
                    <a href="#" id="switch-to-login" class="text-sectionC hover:underline">{{__('Sign In')}}</a>
                </p>
            </div>

            {{-- OTP VERIFICATION PANEL --}}
            <div id="modal-otp-panel" class="px-8 py-8" style="display:none;">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3" style="background:rgba(var(--primary-rgb,12,77,84),0.1)">
                        <i class="ti tabler-shield-check text-3xl" style="color:var(--primary-color,var(--main-color-one, #0C4D54))"></i>
                    </div>
                    <h4 class="text-xl font-urbanist font-semibold text-secondary mb-1">{{__('Verify Your Email')}}</h4>
                    <p class="text-sm" style="color: var(--body-color, #6b7280)">
                        {{__('Enter the 6-digit code sent to')}} <strong id="otp-target-email" class="text-secondary"></strong>
                    </p>
                </div>

                <div id="otp-modal-msg" class="mb-3"></div>

                {{-- Countdown timer --}}
                <div class="text-center mb-4">
                    <span id="otp-timer" class="text-sm font-semibold text-green-600"></span>
                </div>

                {{-- 6 individual digit inputs --}}
                <div class="flex gap-2 justify-center mb-5" id="otp-input-group">
                    @for($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                           class="otp-digit w-11 h-13 text-center text-xl font-bold rounded-lg focus:outline-none focus:border-primary transition-colors"
                           style="height:3.25rem; border: 2px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)">
                    @endfor
                </div>

                <button type="button" id="verify_otp_btn"
                        class="w-full py-3 text-white font-semibold rounded-lg hover:opacity-90 transition-all"
                        style="background:var(--primary-color,var(--main-color-one, #0C4D54))">
                    {{__('Verify OTP')}}
                </button>

                <div class="flex items-center justify-between mt-4 text-sm">
                    <button type="button" id="resend_otp_btn" disabled
                            class="hover:underline disabled:opacity-40 disabled:cursor-not-allowed"
                            style="color:var(--primary-color,var(--main-color-one, #0C4D54))">
                        {{__('Resend')}} (<span id="resend-countdown">60</span>s)
                    </button>
                    <button type="button" id="back_to_register_btn" class="hover:underline" style="color: var(--body-color, #6b7280)">
                        &#8592; {{__('Back')}}
                    </button>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/js/helpers.js')}}"></script>
    <script src="{{global_asset('assets/common/js/toastr.min.js')}}"></script>
    <x-custom-js.landloard-unique-subdomain-check :name="'custom_subdomain'"/>

    <script>
        (function ($) {
            "use strict";
            $(document).ready(function ($) {

                // ── State ──
                let isAuthenticated = {{ $is_auth ? 'true' : 'false' }};
                let currentUserId   = {{ $is_auth && $user ? $user->id : 'null' }};
                let currentMode     = 'purchase'; // 'purchase' | 'trial'
                let pendingSubmit   = false;

                const priceCoupon = {
                    old_price:            `{{$order_details->price}}`,
                    new_price:            `{{$order_details->price}}`,
                    final_price:          `{{$order_details->price}}`,
                    type:                 '',
                    amount:               '',
                    currency:             `{{site_currency_symbol()}}`,
                    currency_position:    `{{get_static_option('site_currency_symbol_position')}}`
                };

                // ── Trial/Purchase Toggle ──
                $(document).on('click', '.mode-toggle-btn', function () {
                    let mode = $(this).data('mode');
                    if (mode === currentMode) return;
                    currentMode = mode;

                    $('.mode-toggle-btn').removeClass('active');
                    $(this).addClass('active');

                    if (mode === 'trial') {
                        $('#payment-section').slideUp(200);
                        $('#coupon-section').slideUp(200);
                        $('#submit-btn-text').text('{{__("Start Free Trial")}}');
                        $('#cta-title').text('{{__("Start your free trial today")}}');
                        $('#cta-subtitle').text('{{__("No payment required — explore for")}} {{$order_details->trial_days ?? 14}} {{__("days")}}');
                    } else {
                        $('#payment-section').slideDown(200);
                        $('#coupon-section').slideDown(200);
                        $('#submit-btn-text').text('{{__("Order Package")}}');
                        $('#cta-title').text('{{__("Ready to launch your website?")}}');
                        $('#cta-subtitle').text('{{__("Complete your purchase and go live in minutes")}}');
                    }
                });

                // ── Main Submit Button ──
                $(document).on('click', '#main-submit-btn', function (e) {
                    e.preventDefault();

                    if (!$('.terms_condition').is(':checked')) {
                        toastr.error(`{{__('To proceed, you must check the box indicating that you agree to our terms and conditions.')}}`);
                        return;
                    }

                    pendingSubmit = true;

                    if (!isAuthenticated) {
                        $('#loginModal').addClass('open');
                        $('body').css('overflow', 'hidden');
                        return;
                    }

                    proceedWithSubmit();
                });

                // ── AJAX Login ──
                $(document).on('click', '#login_btn', function (e) {
                    e.preventDefault();
                    let el       = $(this);
                    let username = $('#login_form_order_page input[name="username"]').val();
                    let password = $('#login_form_order_page input[name="password"]').val();
                    let remember = $('#login_form_order_page input[name="remember"]').is(':checked') ? 1 : 0;

                    el.text('{{__("Please Wait...")}}').prop('disabled', true);
                    $('#login-modal-msg').html('');

                    $.ajax({
                        type: 'POST',
                        url:  '{{route("landlord.user.ajax.login")}}',
                        data: { _token: '{{csrf_token()}}', username: username, password: password, remember: remember },
                        success: function (data) {
                            if (data.status === 'invalid') {
                                el.text('{{__("SIGN IN")}}').prop('disabled', false);
                                $('#login-modal-msg').html('<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 text-red-800 text-sm mb-4">' + data.msg + '</div>');
                            } else {
                                // Patch state — no page reload needed
                                isAuthenticated = true;
                                currentUserId   = data.user_id;

                                // CRITICAL: Session regenerates on login → CSRF token changes → update it everywhere
                                $('meta[name="csrf-token"]').attr('content', data.csrf_token);
                                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': data.csrf_token } });
                                $('#order-form input[name="_token"]').val(data.csrf_token);

                                // Patch form fields
                                $('#field-name').val(data.name).attr('readonly', true).addClass('bg-gray-50');
                                $('#field-email').val(data.email).attr('readonly', true).addClass('bg-gray-50');

                                // Patch trial hidden input
                                $('#trial-user-id').val(data.user_id);

                                // Show existing tenant select if it was hidden
                                $('#subdomain-select-wrap').removeClass('hidden');

                                // Close modal, continue
                                $('#loginModal').removeClass('open');
                                $('body').css('overflow', '');

                                if (pendingSubmit) {
                                    proceedWithSubmit();
                                }
                            }
                        },
                        error: function (xhr) {
                            el.text('{{__("SIGN IN")}}').prop('disabled', false);
                            let errors = xhr.responseJSON?.errors ?? {};
                            let html   = '<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 text-red-800 text-sm mb-4"><ul class="list-disc list-inside">';
                            $.each(errors, function (k, v) { html += '<li>' + v + '</li>'; });
                            html += '</ul></div>';
                            $('#login-modal-msg').html(html);
                        }
                    });
                });

                // ── Modal open/close ──
                function closeLoginModal() {
                    $('#loginModal').removeClass('open');
                    $('body').css('overflow', '');
                    $('#modal-register-panel').hide();
                    $('#modal-login-panel').show();
                    $('#login-modal-msg').html('');
                    $('#register-modal-msg').html('');
                }
                $(document).on('click', '.modal-close-btn', function () {
                    closeLoginModal();
                });
                $(document).on('click', '.modal-overlay', function (e) {
                    if ($(e.target).hasClass('modal-overlay')) {
                        closeLoginModal();
                    }
                });

                // ── Switch login ↔ register ──
                $(document).on('click', '#switch-to-register', function (e) {
                    e.preventDefault();
                    $('#modal-login-panel').hide();
                    $('#modal-register-panel').show();
                });
                $(document).on('click', '#switch-to-login', function (e) {
                    e.preventDefault();
                    $('#modal-register-panel').hide();
                    $('#modal-login-panel').show();
                });

                // ── AJAX Register with OTP verification ──
                $(document).on('click', '#register_btn', function (e) {
                    e.preventDefault();
                    let el = $(this);
                    el.text('{{__("Please Wait...")}}').prop('disabled', true);
                    $('#register-modal-msg').html('');

                    $.ajax({
                        type: 'POST',
                        url:  '{{route("landlord.user.register.otp.store")}}',
                        data: {
                            _token:                $('meta[name="csrf-token"]').attr('content'),
                            name:                  $('#reg_name').val(),
                            email:                 $('#reg_email').val(),
                            phone:                 $('#reg_phone').val(),
                            username:              $('#reg_username').val(),
                            password:              $('#reg_password').val(),
                            password_confirmation: $('#reg_password_confirmation').val(),
                            terms_condition:       'on',
                        },
                        success: function (data) {
                            el.text('{{__("CREATE ACCOUNT")}}').prop('disabled', false);
                            if (data.status === 'otp_sent') {
                                // Transition to OTP verification panel
                                $('#otp-target-email').text(data.email);
                                $('#modal-register-panel').hide();
                                $('#modal-otp-panel').show();
                                startOtpTimer(300);      // 5-minute countdown
                                startResendCooldown(60); // 60-second resend cooldown
                                $('#otp-input-group .otp-digit').first().focus();
                            } else {
                                let msg = data.msg ?? '{{__("Something went wrong. Please try again.")}}';
                                $('#register-modal-msg').html('<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 text-red-800 text-sm mb-4">' + msg + '</div>');
                            }
                        },
                        error: function (xhr) {
                            el.text('{{__("CREATE ACCOUNT")}}').prop('disabled', false);
                            let errors = xhr.responseJSON?.errors ?? {};
                            if (Object.keys(errors).length) {
                                let html = '<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 text-red-800 text-sm mb-4"><ul class="list-disc list-inside">';
                                $.each(errors, function (k, v) { html += '<li>' + v + '</li>'; });
                                html += '</ul></div>';
                                $('#register-modal-msg').html(html);
                            } else {
                                let msg = xhr.responseJSON?.message ?? '{{__("Something went wrong. Please try again.")}}';
                                $('#register-modal-msg').html('<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 text-red-800 text-sm mb-4">' + msg + '</div>');
                            }
                        }
                    });
                });

                // ── OTP: auto-advance between digit boxes ──
                $(document).on('input', '.otp-digit', function () {
                    let val = $(this).val().replace(/\D/g, '');
                    $(this).val(val);
                    if (val.length === 1) {
                        let next = $(this).next('.otp-digit');
                        if (next.length) next.focus();
                    }
                });
                $(document).on('keydown', '.otp-digit', function (e) {
                    if (e.key === 'Backspace' && !$(this).val()) {
                        $(this).prev('.otp-digit').focus();
                    }
                });

                // ── OTP: paste full 6-digit code ──
                $(document).on('paste', '.otp-digit', function (e) {
                    e.preventDefault();
                    let pasted = (e.originalEvent.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                    let digits = $('#otp-input-group .otp-digit');
                    $.each(pasted.split(''), function (i, ch) { digits.eq(i).val(ch); });
                    digits.eq(Math.min(pasted.length, 5)).focus();
                });

                // ── OTP: verify button ──
                $(document).on('click', '#verify_otp_btn', function () {
                    let el  = $(this);
                    let otp = $('#otp-input-group .otp-digit').map(function () { return $(this).val(); }).get().join('');

                    if (otp.length !== 6) {
                        $('#otp-modal-msg').html('<div class="bg-yellow-50 border border-yellow-200 rounded-xl px-6 py-3 text-yellow-800 text-sm">{{__("Please enter all 6 digits.")}}</div>');
                        return;
                    }

                    el.text('{{__("Verifying...")}}').prop('disabled', true);
                    $('#otp-modal-msg').html('');

                    $.ajax({
                        type: 'POST',
                        url:  '{{route("landlord.user.register.otp.verify")}}',
                        data: { _token: $('meta[name="csrf-token"]').attr('content'), otp: otp },
                        success: function (data) {
                            if (data.status === 'valid') {
                                // OTP verified — complete the registration flow
                                clearInterval(otpTimerInterval);
                                clearInterval(resendCooldownInterval);

                                isAuthenticated = true;
                                currentUserId   = data.user_id;

                                $('meta[name="csrf-token"]').attr('content', data.csrf_token);
                                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': data.csrf_token } });
                                $('#order-form input[name="_token"]').val(data.csrf_token);

                                $('#field-name').val(data.name).attr('readonly', true).addClass('bg-gray-50');
                                $('#field-email').val(data.email).attr('readonly', true).addClass('bg-gray-50');
                                $('#trial-user-id').val(data.user_id);
                                $('#subdomain-select-wrap').removeClass('hidden');

                                closeLoginModal();

                                if (pendingSubmit) {
                                    proceedWithSubmit();
                                }
                            } else if (data.status === 'expired') {
                                // Session gone — reset back to registration form
                                clearInterval(otpTimerInterval);
                                clearInterval(resendCooldownInterval);
                                $('#modal-otp-panel').hide();
                                $('#modal-register-panel').show();
                                $('#register-modal-msg').html('<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 text-red-800 text-sm mb-4">' + data.msg + '</div>');
                                el.text('{{__("Verify OTP")}}').prop('disabled', false);
                            } else {
                                // Wrong OTP — clear inputs, show error
                                $('#otp-modal-msg').html('<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-3 text-red-800 text-sm">' + data.msg + '</div>');
                                $('#otp-input-group .otp-digit').val('');
                                $('#otp-input-group .otp-digit').first().focus();
                                el.text('{{__("Verify OTP")}}').prop('disabled', false);
                            }
                        },
                        error: function () {
                            el.text('{{__("Verify OTP")}}').prop('disabled', false);
                            $('#otp-modal-msg').html('<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-3 text-red-800 text-sm">{{__("Request failed. Please try again.")}}</div>');
                        }
                    });
                });

                // ── OTP: resend button ──
                $(document).on('click', '#resend_otp_btn', function () {
                    let el = $(this);
                    el.prop('disabled', true);
                    $.ajax({
                        type: 'POST',
                        url:  '{{route("landlord.user.register.otp.resend")}}',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (data) {
                            if (data.status === 'resent') {
                                startOtpTimer(300);
                                startResendCooldown(60);
                                $('#otp-modal-msg').html('<div class="bg-green-50 border border-green-200 rounded-xl px-6 py-3 text-green-800 text-sm">' + data.msg + '</div>');
                            } else {
                                el.prop('disabled', false);
                                $('#otp-modal-msg').html('<div class="bg-yellow-50 border border-yellow-200 rounded-xl px-6 py-3 text-yellow-800 text-sm">' + data.msg + '</div>');
                            }
                        },
                        error: function () {
                            el.prop('disabled', false);
                        }
                    });
                });

                // ── OTP: back to registration form ──
                $(document).on('click', '#back_to_register_btn', function () {
                    clearInterval(otpTimerInterval);
                    clearInterval(resendCooldownInterval);
                    $('#modal-otp-panel').hide();
                    $('#modal-register-panel').show();
                    $('#otp-modal-msg').html('');
                    $('#otp-input-group .otp-digit').val('');
                });

                // ── OTP Timer & Resend Cooldown Helpers ──
                let otpTimerInterval, resendCooldownInterval;

                function startOtpTimer(seconds) {
                    clearInterval(otpTimerInterval);
                    let remaining = seconds;
                    function tick() {
                        if (remaining <= 0) {
                            clearInterval(otpTimerInterval);
                            $('#otp-timer').text('{{__("OTP expired — please go back and register again.")}}')
                                          .removeClass('text-green-600').addClass('text-red-500');
                            $('#verify_otp_btn').prop('disabled', true);
                            return;
                        }
                        let m = Math.floor(remaining / 60), s = remaining % 60;
                        $('#otp-timer').text('{{__("Expires in")}} ' + m + ':' + String(s).padStart(2, '0'))
                                      .removeClass('text-red-500').addClass('text-green-600');
                        remaining--;
                    }
                    tick();
                    otpTimerInterval = setInterval(tick, 1000);
                }

                function startResendCooldown(seconds) {
                    clearInterval(resendCooldownInterval);
                    $('#resend_otp_btn').prop('disabled', true);
                    let remaining = seconds;
                    function tick() {
                        $('#resend-countdown').text(remaining);
                        if (remaining <= 0) {
                            clearInterval(resendCooldownInterval);
                            $('#resend_otp_btn').prop('disabled', false);
                            $('#resend-countdown').text('');
                            return;
                        }
                        remaining--;
                    }
                    tick();
                    resendCooldownInterval = setInterval(tick, 1000);
                }

                // ── Route to correct action ──
                function proceedWithSubmit() {
                    console.log("processing submit ". currentMode);
                    pendingSubmit = false;
                    if (currentMode === 'trial') {
                        submitTrial();
                    } else {
                        submitPurchase();
                    }
                }

                // ── Progress overlay helpers ──
                let _opTimers = [];
                function opClearTimers() { _opTimers.forEach(clearTimeout); _opTimers = []; }
                function opAfter(ms, fn) { _opTimers.push(setTimeout(fn, ms)); }

                function opStepActivate(n) {
                    $('.op-pstep[data-pstep="' + n + '"]').addClass('pstep-active').removeClass('pstep-done');
                }
                function opStepDone(n) {
                    $('.op-pstep[data-pstep="' + n + '"]').removeClass('pstep-active').addClass('pstep-done');
                }
                function opSetProgress(pct, etaText) {
                    $('#opProgressBar').css('width', pct + '%');
                    $('#opProgressPct').text(pct + '%');
                    if (etaText !== undefined) $('#opProgressEta').text(etaText);
                }

                function showProgressOverlay(mode) {
                    const subdomain = $('input[name="custom_subdomain"]').val() || '';
                    const baseUrl   = '{{ $base_url }}';
                    const step1Label = mode === 'trial'
                        ? '{{__("Free Trial Activated")}}'
                        : '{{__("Payment Confirmed")}}';

                    $('.op-pstep[data-pstep="1"] .op-pstep-label').text(step1Label);
                    $('#opProgressStoreName').text(subdomain ? subdomain + '.' + baseUrl : '');
                    $('#opProgressTitle').text('{{__("Creating Your Store")}}').removeClass('op-success');
                    $('#opProgressIcon').removeClass('op-icon-done');
                    $('#opProgressEta').text('{{__("Estimated: 10–20 seconds")}}');
                    $('.op-pstep').removeClass('pstep-active pstep-done');
                    opSetProgress(0);
                    $('#storeProgressOverlay').addClass('active');
                }

                function opCompleteAllSteps(successUrl) {
                    opClearTimers();
                    for (let i = 1; i <= 6; i++) {
                        $('.op-pstep[data-pstep="' + i + '"]').removeClass('pstep-active').addClass('pstep-done');
                    }
                    opSetProgress(100, '');
                    $('#opProgressTitle').text('{{__("Your Store is Ready!")}}').addClass('op-success');
                    $('#opProgressIcon').addClass('op-icon-done');
                    let sec = 3;
                    $('#opProgressEta').text('{{__("Redirecting in")}} ' + sec + 's...');
                    const tick = setInterval(function () {
                        sec--;
                        if (sec <= 0) { clearInterval(tick); location.href = successUrl; }
                        else { $('#opProgressEta').text('{{__("Redirecting in")}} ' + sec + 's...'); }
                    }, 1000);
                }

                function opHideProgressOverlay() {
                    opClearTimers();
                    $('#storeProgressOverlay').removeClass('active');
                }

                // ── Trial Submission (AJAX) ──
                function submitTrial() {
                    let subdomain = $('input[name="custom_subdomain"]').val();
                    if (!subdomain) {
                        toastr.error('{{__("Please enter a subdomain.")}}');
                        return;
                    }

                    let submitBtn = $('#main-submit-btn');
                    submitBtn.prop('disabled', true);
                    showProgressOverlay('trial');

                    opAfter(0,    function () { opStepActivate(1); opSetProgress(5); });
                    opAfter(400,  function () { opStepDone(1); opStepActivate(2); opSetProgress(20); });
                    opAfter(1100, function () { opStepDone(2); opStepActivate(3); opSetProgress(36); });
                    opAfter(2200, function () { opStepDone(3); opStepActivate(4); opSetProgress(52); });
                    opAfter(3500, function () { opStepDone(4); opStepActivate(5); opSetProgress(68); });
                    opAfter(5000, function () { opStepDone(5); opStepActivate(6); opSetProgress(85); });

                    $.ajax({
                        type: 'POST',
                        url:  '{{route("landlord.frontend.trial.account")}}',
                        data: {
                            _token:    $('meta[name="csrf-token"]').attr('content'),
                            user_id:   currentUserId,
                            order_id:  $('#trial-order-id').val(),
                            subdomain: subdomain,
                            theme:     $('#trial-theme').val(),
                        },
                        success: function (data) {
                            if (data.type === 'success') {
                                opCompleteAllSteps(data.success_url);
                            } else {
                                opHideProgressOverlay();
                                submitBtn.prop('disabled', false);
                                toastr.error(data.msg);
                            }
                        },
                        error: function (xhr) {
                            opHideProgressOverlay();
                            submitBtn.prop('disabled', false);
                            let errors = xhr.responseJSON?.errors ?? {};
                            let msgs   = Object.values(errors).join(', ');
                            toastr.error(msgs || '{{__("Something went wrong. Please try again.")}}');
                        }
                    });
                }

                // ── Purchase Submission (form POST) ──
                function submitPurchase() {
                    $('#main-submit-btn').prop('disabled', true);
                    showProgressOverlay('purchase');
                    opAfter(0,   function () { opStepActivate(1); opSetProgress(15); });
                    opAfter(350, function () { opStepDone(1); opStepActivate(2); opSetProgress(35); });
                    opAfter(700, function () { opStepDone(2); opStepActivate(3); opSetProgress(55); $('#order-form')[0].submit(); });
                }

                // ── Coupon Apply ──
                $(document).on('click', '#coupon-form button', function () {
                    let el          = $(this);
                    let coupon_code = $('input[name=coupon]').val();
                    if (coupon_code === '') {
                        toastr.error(`{{__('Please enter a valid coupon')}}`);
                        return;
                    }

                    send_ajax_request('GET', {coupon: coupon_code}, `{{route('landlord.frontend.coupon.apply')}}?coupon=${coupon_code}`, function () {
                        el.attr('disabled', true);
                        el.text(`{{__('Applying')}}`);
                    }, function (response) {
                        if (response.type === 'success') {
                            priceCoupon.type   = response.data.discount_type;
                            priceCoupon.amount = response.data.discount_amount;
                            applyCouponPrice(priceCoupon);
                            $('.total-price-display').text(priceCoupon.final_price);
                            $('input[name=coupon]').prop('readonly', true);
                            el.text(`{{__('Applied')}}`);
                            toastr.success(`{{__('Coupon applied')}}`);
                        } else {
                            toastr.error(response.msg);
                            el.text(`{{__('Apply')}}`);
                            el.attr('disabled', false);
                        }
                    }, function () {
                        el.attr('disabled', false);
                        el.text(`{{__('Apply')}}`);
                    });
                });

                // ── Payment gateway selection ──
                var defaulGateway = $('#site_global_payment_gateway').val();
                $('.payment-gateway-wrapper ul li[data-gateway="' + defaulGateway + '"]').addClass('selected');

                let customFormParent = $('.payment_gateway_extra_field_information_wrap');
                customFormParent.children().hide();

                $(document).on('click', '.payment-gateway-wrapper > ul > li', function (e) {
                    e.preventDefault();

                    let gateway              = $(this).data('gateway');
                    let manual_transaction   = $('.manual_transaction_id');
                    let summernot_wrap       = $('.summernot_wrap');

                    customFormParent.children().hide();
                    if (gateway === 'manual_payment') {
                        manual_transaction.fadeIn().removeClass('d-none');
                        summernot_wrap.fadeIn();
                    } else {
                        manual_transaction.fadeOut().addClass('d-none');
                        summernot_wrap.fadeOut();
                        let wrapper = customFormParent.find('#' + gateway + '-parent-wrapper');
                        if (wrapper.length > 0) { wrapper.fadeIn(); }
                    }

                    $(this).addClass('selected').siblings().removeClass('selected');
                    $('.payment-gateway-wrapper').find('input').val($(this).data('gateway'));
                    $('.payment_gateway_passing_clicking_name').val($(this).data('gateway'));
                });

                // ── Subdomain dropdown toggle ──
                let custom_subdomain_wrapper = $('.custom_subdomain_wrapper');
                $(document).on('change', '#subdomain', function () {
                    $('#main-submit-btn').prop('disabled', false);
                    let val = $(this).val();
                    if (val === 'custom_domain__dd') {
                        custom_subdomain_wrapper.slideDown();
                        $('.theme-section').slideDown();
                    } else {
                        custom_subdomain_wrapper.slideUp();
                        custom_subdomain_wrapper.find('input').val('');
                        $('.theme-section').slideUp();
                    }
                });

                // ── Theme selection (updates all theme hidden inputs) ──
                $(document).on('click', '.theme-card', function () {
                    let el         = $(this);
                    let theme_slug = el.data('theme');

                    $('.theme-card').removeClass('selected_theme');
                    $('.theme-card .theme-checkmark').addClass('hidden').removeClass('flex');
                    el.addClass('selected_theme');
                    el.find('.theme-checkmark').removeClass('hidden').addClass('flex');

                    $('input#theme-slug, #trial-theme').val(theme_slug);
                });

            });
        })(jQuery);
    </script>

    {{-- Surface any server-side flash message (e.g. "Please verify your account first")
         as a toastr notification so the user knows why they were redirected back. --}}
    @if(session('msg'))
    <script>
        $(function () {
            @php $flashType = session('type') === 'danger' ? 'error' : (session('type') ?: 'info'); @endphp
            toastr.{{ $flashType }}('{!! addslashes(session('msg')) !!}');
        });
    </script>
    @endif
@endsection
