@php
use App\Models\PricePlan;
use App\Models\PaymentGateway;
use App\Enums\StatusEnums;
use App\Enums\PricePlanTypEnums;

// All published plans with relations
$_modal_plans = PricePlan::where('status', StatusEnums::PUBLISH)
    ->with(['plan_features', 'plan_themes'])
    ->orderBy('type')
    ->get();

$_modal_plans_by_type = $_modal_plans->groupBy('type');

// Theme data — all active themes
$_modal_all_themes = \App\Facades\ThemeDataFacade::getAllThemeData();
$_modal_default_theme = get_static_option('default_theme');

// Plan → theme slug map
$_plan_theme_map = [];
foreach ($_modal_plans as $_mp) {
    $_plan_theme_map[$_mp->id] = $_mp->plan_themes->pluck('theme_slug')->toArray();
}

// Base URL
$_modal_base_url = str_replace(['http://', 'https://'], '', url('/'));

// Auth user & existing shops
$_modal_user  = auth()->guard('web')->user();
$_modal_shops = [];
if ($_modal_user) {
    foreach ($_modal_user->tenant_details ?? [] as $_t) {
        if (optional($_t->payment_log?->package)->type != PricePlanTypEnums::LIFETIME) {
            $_modal_shops[] = ['id' => $_t->id, 'domain' => optional($_t->domain)->domain];
        }
    }
}

// Payment gateways HTML
$_modal_gateways_html = (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm();
$_modal_manual_gw     = PaymentGateway::where(['status' => StatusEnums::PUBLISH, 'name' => 'manual_payment'])->first();

// Trial eligibility
$_modal_user_has_trial = $_modal_user
    ? \App\Models\PaymentLogs::where('user_id', $_modal_user->id)->where('status', 'trial')->exists()
    : false;

// Type helpers
$_type_labels = [
    PricePlanTypEnums::MONTHLY  => ['label' => __('Monthly'),  'period' => '/ mo'],
    PricePlanTypEnums::YEARLY   => ['label' => __('Yearly'),   'period' => '/ yr'],
    PricePlanTypEnums::LIFETIME => ['label' => __('Lifetime'), 'period' => ''],
];
@endphp

{{-- ============================================================
     PURCHASE FLOW MODAL
     Triggered by: .open-purchase-modal[data-plan-id?]
     Steps: Plan → Theme → Store Info → Payment
     ============================================================ --}}
<div id="purchaseFlowModal"
     class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     role="dialog" aria-modal="true">

    {{-- Backdrop --}}
    <div id="pmBackdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Modal Card --}}
    <div class="relative z-10 w-full max-w-3xl max-h-[92vh] flex flex-col rounded-2xl shadow-2xl overflow-hidden
                opacity-0 translate-y-4 transition-all duration-300 ease-out" id="pmCard"
         style="background-color: var(--section-bg-1, #ffffff)">

        {{-- ── HEADER ── --}}
        <div class="relative flex items-center justify-center px-6 py-4 border-b border-borderCS shrink-0">


            <div class="text-center">
                <h2 class="text-base font-semibold text-secondary font-urbanist" id="pmTitle">{{__('Create Your Shop')}}</h2>
                <p class="text-xs" id="pmSubTitle" style="color: var(--body-color, #666666)">{{__('Step 1 of 4 — Choose a plan')}}</p>
            </div>
            <button id="pmClose" class="absolute right-4 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full transition" style="color: var(--body-color, #9ca3af)"
                <i class="ti tabler-x text-lg"></i>
            </button>
        </div>

        {{-- ── STEPPER ── --}}
        <div class="px-6 py-3 border-b border-borderCS shrink-0" id="pmStepper" style="background-color: var(--section-bg-3, #F8FAFB)">
            <div class="flex items-center justify-center gap-0 w-full max-w-xs mx-auto">
                @php
                    $pm_steps = [
                        ['icon' => 'tabler-currency-dollar', 'label' => __('Plan')],
                        ['icon' => 'tabler-palette',         'label' => __('Theme')],
                        ['icon' => 'tabler-building-store',  'label' => __('Store')],
                        ['icon' => 'tabler-credit-card',     'label' => __('Payment')],
                    ];
                @endphp
                @foreach($pm_steps as $si => $step)
                    <div class="flex items-center pm-step-item" data-step="{{ $si + 1 }}">
                        <div class="flex flex-col items-center">
                            <div class="pm-step-bubble w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold border-2 transition-all duration-300
                                        {{ $si === 0 ? 'bg-primary border-primary text-white' : 'border-borderCS' }}"
                                 @if($si !== 0) style="background-color: var(--section-bg-1, #fff); color: var(--body-color, #9ca3af)" @endif>
                                <span class="pm-step-num">{{ $si + 1 }}</span>
                                <i class="ti tabler-check pm-step-check hidden text-sm"></i>
                            </div>
                            <span class="mt-1 text-[10px] font-medium hidden sm:block pm-step-label
                                         {{ $si === 0 ? 'text-primary' : '' }}"
                                  @if($si !== 0) style="color: var(--body-color, #9ca3af)" @endif>
                                {{ $step['label'] }}
                            </span>
                        </div>
                        @if(!$loop->last)
                            <div class="pm-connector flex-1 h-0.5 bg-gray-200 mx-2 mt-0 sm:mt-[-18px] min-w-[24px] transition-all duration-500"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── BODY ── --}}
        <div class="flex-1 overflow-y-auto" id="pmMainContent">
            <div class="p-6">

                {{-- ══ STEP 1: PLAN SELECTION ══ --}}
                <div class="pm-step-content" data-step-content="1">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-secondary font-urbanist">{{__('Choose Your Plan')}}</h3>
                        <p class="text-sm mt-1" style="color: var(--body-color, #666666)">{{__('Pick the plan that fits your business. You can upgrade later.')}}</p>
                    </div>

                    {{-- Type Tabs --}}
                    <div class="flex justify-center mb-6">
                        <div class="flex gap-2 p-1 rounded-xl" style="background-color: var(--section-bg-3, #f3f4f6)">
                            @foreach([PricePlanTypEnums::MONTHLY => __('Monthly'), PricePlanTypEnums::YEARLY => __('Yearly'), PricePlanTypEnums::LIFETIME => __('Lifetime')] as $typeVal => $typeLabel)
                                @if(isset($_modal_plans_by_type[$typeVal]) && $_modal_plans_by_type[$typeVal]->count())
                                    <button type="button"
                                            class="pm-plan-tab px-4 py-1.5 rounded-lg text-sm font-medium transition-all duration-200
                                                   {{ $typeVal == PricePlanTypEnums::MONTHLY ? 'text-secondary shadow-sm' : '' }}"
                                            @if($typeVal == PricePlanTypEnums::MONTHLY) style="background-color: var(--section-bg-1, #fff)"
                                            @else style="color: var(--body-color, #6b7280)" @endif
                                            data-type="{{ $typeVal }}">
                                        {{ $typeLabel }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Plan Cards per type --}}
                    @foreach($_modal_plans_by_type as $typeVal => $plans)
                        @php
                            $periodLabel = $_type_labels[$typeVal]['period'] ?? '';
                            $typeLabel   = $_type_labels[$typeVal]['label']  ?? '';
                        @endphp
                        <div class="pm-plan-group px-5 {{ $typeVal != PricePlanTypEnums::MONTHLY ? 'hidden' : '' }}"
                             data-group="{{ $typeVal }}">
                            <div class="grid grid-cols-2 gap-3" id="pmPlanGrid-{{ $typeVal }}">
                                @foreach($plans as $plan)
                                    <div class="pm-plan-card relative flex flex-col rounded-xl cursor-pointer group overflow-hidden"
                                     style="background-color: var(--section-bg-1, #ffffff)"
                                         data-plan-id="{{ $plan->id }}"
                                         data-plan-name="{{ $plan->title }}"
                                         data-plan-price="{{ $plan->price }}"
                                         data-plan-price-display="{{ amount_with_currency_symbol($plan->price) }}"
                                         data-plan-type="{{ $typeVal }}"
                                         data-plan-type-label="{{ $typeLabel }}"
                                         data-plan-period="{{ $periodLabel }}"
                                         data-plan-themes="{{ json_encode($_plan_theme_map[$plan->id] ?? []) }}"
                                         data-plan-has-trial="{{ $plan->has_trial ?? 0 }}"
                                         data-plan-trial-days="{{ $plan->trial_days ?? 0 }}">

                                        <div class="p-4 flex flex-col flex-1">

                                            {{-- Header row: name + check --}}
                                            <div class="flex items-start justify-between gap-2 mb-2">
                                                <div class="min-w-0">
                                                    <span class="inline-flex items-center px-2 py-px rounded-full text-[10px] font-semibold tracking-wide uppercase bg-primary/10 text-primary mb-1.5">
                                                        {{ $typeLabel }}
                                                    </span>
                                                    <h4 class="text-sm font-bold text-secondary font-urbanist leading-snug truncate">{{ $plan->title }}</h4>
                                                </div>
                                                <div class="pm-plan-check w-5 h-5 rounded-full bg-primary hidden items-center justify-center shrink-0 shadow mt-0.5">
                                                    <i class="ti tabler-check text-white" style="font-size:9px"></i>
                                                </div>
                                            </div>

                                            {{-- Price --}}
                                            <div class="flex items-baseline gap-1 mb-3">
                                                <span class="text-xl font-extrabold text-secondary font-urbanist">{{ amount_with_currency_symbol($plan->price) }}</span>
                                                @if($periodLabel)
                                                    <span class="text-[11px]" style="color: var(--body-color, #9ca3af)">{{ $periodLabel }}</span>
                                                @endif
                                            </div>

                                            {{-- Divider --}}
                                            <div class="border-t border-dashed border-borderCS mb-3"></div>

                                            {{-- Features --}}
                                            <ul class="space-y-1.5 flex-1 mb-4">
                                                @if(!empty($plan->page_permission_feature))
                                                    <li class="flex items-center gap-1.5 text-xs" style="color: var(--body-color, #6b7280)">
                                                        <span class="pm-feat-dot shrink-0"></span>
                                                        <span>{{ $plan->page_permission_feature > 0 ? $plan->page_permission_feature : __('Unlimited') }} {{ __('Pages') }}</span>
                                                    </li>
                                                @endif
                                                @if(!empty($plan->product_permission_feature))
                                                    <li class="flex items-center gap-1.5 text-xs" style="color: var(--body-color, #6b7280)">
                                                        <span class="pm-feat-dot shrink-0"></span>
                                                        <span>{{ $plan->product_permission_feature > 0 ? $plan->product_permission_feature : __('Unlimited') }} {{ __('Products') }}</span>
                                                    </li>
                                                @endif
                                                @if(!empty($plan->blog_permission_feature))
                                                    <li class="flex items-center gap-1.5 text-xs" style="color: var(--body-color, #6b7280)">
                                                        <span class="pm-feat-dot shrink-0"></span>
                                                        <span>{{ $plan->blog_permission_feature > 0 ? $plan->blog_permission_feature : __('Unlimited') }} {{ __('Blogs') }}</span>
                                                    </li>
                                                @endif
                                                @foreach($plan->plan_features->take(3) as $feat)
                                                    <li class="flex items-center gap-1.5 text-xs" style="color: var(--body-color, #6b7280)">
                                                        <span class="pm-feat-dot shrink-0"></span>
                                                        <span>{{ str_replace('_', ' ', ucfirst($feat->feature_name)) }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            {{-- Select button --}}
                                            <button type="button" class="pm-plan-select-btn w-full py-2 rounded-lg text-xs font-semibold tracking-wide transition-all duration-200">
                                                {{__('Select Plan')}}
                                            </button>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Pagination --}}
                            <div class="pm-plan-pagination flex items-center justify-center gap-2 mt-4" data-group="{{ $typeVal }}"></div>
                        </div>
                    @endforeach
                </div>

                {{-- ══ STEP 2: THEME SELECTION ══ --}}
                <div class="pm-step-content hidden" data-step-content="2">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-secondary font-urbanist">{{__('Choose Your Theme')}}</h3>
                        <p class="text-sm mt-1" style="color: var(--body-color, #666666)">{{__('Pick the look for your store. You can change it later from your dashboard.')}}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4" id="pmThemeGrid">
                        @foreach($_modal_all_themes as $_th_key => $theme)
                            @php
                                $th_slug  = $theme->slug ?? $_th_key;
                                $th_data  = getIndividualThemeDetails($th_slug);
                                $th_img   = loadScreenshot($th_slug);
                                $th_name  = get_static_option_central($th_slug . '_theme_name') ?: ($th_data['name'] ?? $th_slug);
                                $th_url   = get_static_option_central($th_slug . '_theme_url');
                                $th_custom_img = get_static_option_central($th_slug . '_theme_image');
                                $th_display_img = $th_custom_img ?: $th_img;
                                $is_default = $_modal_default_theme == $th_slug;
                            @endphp
                            <div class="pm-theme-card relative flex flex-col border-2 rounded-xl overflow-hidden cursor-pointer
                                        hover:border-primary transition-all duration-200 group
                                        {{ $is_default ? 'border-primary' : 'border-borderCS' }}"
                                 style="background-color: var(--section-bg-1, #ffffff)"
                                 data-theme-slug="{{ $th_slug }}"
                                 data-theme-name="{{ $th_name }}">

                                <div class="pm-theme-check absolute top-2 right-2 w-6 h-6 rounded-full bg-primary
                                            {{ $is_default ? 'flex' : 'hidden' }} items-center justify-center z-10 shadow-sm">
                                    <i class="ti tabler-check text-white text-xs"></i>
                                </div>

                                <div class="relative overflow-hidden" style="height: 140px;">
                                    <img src="{{ $th_display_img }}" alt="{{ $th_name }}"
                                         class="w-full h-full object-cover object-top transition-transform duration-[3s] group-hover:scale-110"
                                         loading="lazy">
{{--                                    @if($th_url)--}}
{{--                                        <a href="{{ $th_url }}" target="_blank" onclick="event.stopPropagation()"--}}
{{--                                           class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-200">--}}
{{--                                            <span class="bg-white text-secondary text-xs font-semibold px-3 py-1.5 rounded-lg shadow flex items-center gap-1">--}}
{{--                                                <i class="ti tabler-external-link text-xs"></i> {{__('Preview')}}--}}
{{--                                            </span>--}}
{{--                                        </a>--}}
{{--                                    @endif--}}
                                </div>

                                <div class="px-3 py-2 border-t border-borderCS text-center" style="background-color: var(--section-bg-1, #ffffff)">
                                    <p class="text-sm font-medium text-secondary truncate">{{ $th_name }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Theme pagination --}}
                    <div class="flex items-center justify-center gap-2 mt-5" id="pmThemePagination"></div>

                    {{-- No-theme fallback --}}
                    <div id="pmNoTheme" class="hidden text-center py-10" style="color: var(--body-color, #9ca3af)">
                        <i class="ti tabler-palette text-4xl mb-2"></i>
                        <p class="text-sm">{{__('No themes available for the selected plan.')}}</p>
                    </div>
                </div>

                {{-- ══ STEP 3: STORE INFORMATION ══ --}}
                <div class="pm-step-content hidden" data-step-content="3">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-secondary font-urbanist">{{__('Set Up Your Store')}}</h3>
                        <p class="text-sm mt-1" style="color: var(--body-color, #666666)">{{__('Choose a unique name for your store. This will be your store\'s web address.')}}</p>
                    </div>

                    <div class="max-w-md mx-auto">
                        @if(!$_modal_user)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                                <i class="ti tabler-lock text-3xl text-yellow-600 mb-3 block"></i>
                                <h4 class="font-semibold text-secondary mb-2">{{__('Login Required')}}</h4>
                                <p class="text-sm text-gray-600 mb-4">{{__('You need to be logged in to create a shop.')}}</p>
                                <div class="flex gap-3 justify-center">
                                    <a href="{{ route('landlord.user.login') }}"
                                       class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-semibold hover:opacity-90 transition">
                                        {{__('Log In')}}
                                    </a>
                                    <a href="{{ route('landlord.user.register') }}"
                                       class="px-5 py-2.5 border border-primary text-primary rounded-lg text-sm font-semibold hover:bg-primary/5 transition">
                                        {{__('Register')}}
                                    </a>
                                </div>
                            </div>
                        @else
                            @if(count($_modal_shops) > 0)
                                <div class="mb-5">
                                    <label class="block text-sm font-semibold text-secondary mb-2">{{__('Renew an existing shop or create a new one')}}</label>
                                    <div class="relative">
                                        <select id="pmShopSelect"
                                                class="w-full appearance-none border border-borderCS rounded-xl px-4 py-3 text-sm text-secondary
                                                       outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary pr-10 transition"
                                                style="background-color: var(--section-bg-1, #ffffff)">
                                            <option value="custom_domain__dd">{{__('➕ Create a new shop')}}</option>
                                            @foreach($_modal_shops as $shop)
                                                <option value="{{ $shop['id'] }}">{{ $shop['domain'] }} ({{__('Renew')}})</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                            <i class="ti tabler-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div id="pmNewShopWrap">
                                <label class="block text-sm font-semibold text-secondary mb-2">{{__('Store Name')}}</label>
                                <div class="flex rounded-xl overflow-hidden border border-borderCS focus-within:ring-2 focus-within:ring-primary/30 focus-within:border-primary transition">
                                    <input type="text" id="pmSubdomainInput"
                                           class="flex-1 px-4 py-3 text-sm text-secondary outline-none placeholder-gray-400"
                                           style="background-color: var(--section-bg-1, #ffffff)"
                                           placeholder="{{ __('e.g. myshop') }}" autocomplete="off">
                                    <div class="flex items-center px-4 border-l border-gray-200 text-sm text-gray-500 whitespace-nowrap shrink-0" style="background-color: rgba(var(--main-color-two-rgb, 147, 231, 32), 0.1)">
                                        .{{ $_modal_base_url }}
                                    </div>
                                </div>
                                <div id="pmSubdomainFeedback" class="mt-2 text-sm min-h-[20px]"></div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ══ STEP 4: PAYMENT ══ --}}
                <div class="pm-step-content hidden" data-step-content="4">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-secondary font-urbanist">{{__('Payment')}}</h3>
                        <p class="text-sm mt-1" style="color: var(--body-color, #666666)">{{__('Choose your payment method and confirm your order.')}}</p>
                    </div>

                    <div class="max-w-lg mx-auto">

                        {{-- Trial/Purchase Toggle (shown only when selected plan supports trial) --}}
                        @if(!$_modal_user_has_trial)
                        <div id="pmModeToggle" class="hidden mb-5 rounded-2xl p-1.5 flex gap-1 border border-borderCS" style="background-color: var(--section-bg-3, #F8FAFB)">
                            <button type="button" class="pm-mode-btn active flex-1 py-2 px-4 rounded-xl text-sm font-semibold transition-all" data-mode="purchase">
                                <i class="ti tabler-shopping-cart mr-1 text-xs"></i>{{ __('Purchase') }}
                            </button>
                            <button type="button" class="pm-mode-btn flex-1 py-2 px-4 rounded-xl text-sm font-semibold transition-all" data-mode="trial">
                                <i class="ti tabler-rocket mr-1 text-xs"></i>{{ __('Free Trial') }}
                                <span class="pm-trial-days-label text-xs opacity-75"></span>
                            </button>
                        </div>
                        <p id="pmNoTrialNote" class="text-center text-xs text-gray-400 mb-4">
                            <i class="ti tabler-info-circle mr-1"></i>{{__('Free trial available on select plans.')}}
                        </p>
                        @else
                        <p class="text-center text-xs text-gray-400 mb-4">
                            <i class="ti tabler-circle-check mr-1 text-green-500"></i>{{__('You\'ve already used your free trial.')}}
                        </p>
                        @endif

                        {{-- Summary strip --}}
                        <div class="flex flex-wrap justify-center gap-1 mb-2 p-2 rounded-xl border border-borderCS" style="background-color: var(--section-bg-3, #F8FAFB)">
                            <div class="flex items-center gap-1 text-sm">
                                <span class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                                    <i class="ti tabler-currency-dollar text-primary text-xs"></i>
                                </span>
                                <span style="color: var(--body-color, #6b7280)">{{__('Plan:')}}</span>
                                <strong class="text-secondary pm-summary-plan-name">—</strong>
                            </div>
                            <span class="hidden sm:inline" style="color: var(--extra-light-color, #d1d5db)">|</span>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                                    <i class="ti tabler-palette text-primary text-xs"></i>
                                </span>
                                <span style="color: var(--body-color, #6b7280)">{{__('Theme:')}}</span>
                                <strong class="text-secondary pm-summary-theme-name">—</strong>
                            </div>
                            <span class="hidden sm:inline" style="color: var(--extra-light-color, #d1d5db)">|</span>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                                    <i class="ti tabler-building-store text-primary text-xs"></i>
                                </span>
                                <span style="color: var(--body-color, #6b7280)">{{__('Store:')}}</span>
                                <strong class="text-secondary pm-summary-store-name">—</strong>
                            </div>
                        </div>

                        {{-- Price total --}}
                        <div class="flex items-center justify-between rounded-xl border border-borderCS px-5 py-3 mb-5" style="background-color: var(--section-bg-1, #ffffff)">
                            <span class="text-sm font-semibold text-secondary">{{__('Total')}}</span>
                            <span class="text-lg font-extrabold text-primary pm-total-display">—</span>
                        </div>

                        {{-- Payment gateways --}}
                        <div class="mb-5" id="pmPaymentSection">
                            <label class="block text-sm font-semibold text-secondary mb-3 text-center">{{__('Payment Method')}}</label>
                            <div class="pm-gateway-area">
                                {!! $_modal_gateways_html !!}
                            </div>

                            {{-- Manual transaction fields --}}
                            <div class="pm-manual-fields hidden mt-4 space-y-3">
                                @if($_modal_manual_gw)
                                    <p class="text-xs rounded-xl px-4 py-3" style="color: var(--main-color-one, #0C4D54); background-color: rgba(var(--main-color-one-rgb, 12, 77, 84), 0.06); border: 1px solid rgba(var(--main-color-one-rgb, 12, 77, 84), 0.2)">
                                        {{ json_decode($_modal_manual_gw->credentials)->description ?? '' }}
                                    </p>
                                @endif
                                <input type="text" id="pmTransactionId"
                                       class="w-full border border-borderCS rounded-xl px-4 py-3 text-sm text-secondary placeholder-gray-400
                                              outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                       style="background-color: var(--section-bg-1, #ffffff)"
                                       placeholder="{{ __('Transaction ID') }}">
                                <input type="file" id="pmTransactionFile"
                                       class="w-full border border-borderCS rounded-xl px-4 py-3 text-sm text-secondary outline-none
                                              focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                       style="background-color: var(--section-bg-1, #ffffff)"
                                       accept="image/*">
                            </div>
                        </div>

                        {{-- Coupon --}}
                        <div class="mb-5" id="pmCouponSection">
                            <label class="block text-sm font-semibold text-secondary mb-2 text-center">
                                {{__('Have a coupon?')}}
                                <button type="button" id="pmToggleCoupon" class="ml-2 text-primary text-xs font-normal underline">
                                    {{__('Apply coupon')}}
                                </button>
                            </label>
                            <div id="pmCouponWrap" class="hidden">
                                <div class="flex gap-2">
                                    <input type="text" id="pmCouponInput"
                                           class="flex-1 border border-borderCS rounded-xl px-4 py-3 text-sm text-secondary placeholder-gray-400
                                                  outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                           style="background-color: var(--section-bg-1, #ffffff)"
                                           placeholder="{{ __('Coupon code') }}">
                                    <button type="button" id="pmApplyCoupon"
                                            class="px-5 py-3 bg-primary text-white rounded-xl text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                                        {{__('Apply')}}
                                    </button>
                                </div>
                                <div id="pmCouponFeedback" class="mt-2 text-sm min-h-[18px]"></div>
                            </div>
                        </div>

                        {{-- Terms --}}
                        <p class="text-center text-sm mb-5" style="color: var(--body-color, #6b7280)">
                            {{__('By continuing, you agree to our')}}
                            <a href="#" class="text-primary underline">{{__('Terms & Conditions')}}</a>.
                        </p>
                        <input type="checkbox" id="pmTerms" checked class="hidden">
                    </div>
                </div>

                {{-- ══ STEP 5: CREATING STORE PROGRESS ══ --}}
                <div class="pm-step-content hidden" data-step-content="5">
                    <div class="flex flex-col items-center justify-center py-8 px-4 text-center">

                        {{-- Animated store icon --}}
                        <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mb-5">
                            <i class="ti tabler-building-store text-primary text-4xl" id="pmProgressIcon"></i>
                        </div>

                        {{-- Title & store URL --}}
                        <h3 class="text-xl font-bold text-secondary font-urbanist mb-1" id="pmProgressTitle">{{__('Creating Your Store')}}</h3>
                        <p class="text-sm mb-6 min-h-[20px]">
                            <span id="pmProgressStoreName" class="font-medium text-primary"></span>
                        </p>

                        {{-- Progress bar --}}
                        <div class="w-full max-w-sm mb-6">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span id="pmProgressPct" class="font-semibold text-primary">0%</span>
                                <span id="pmProgressEta" class="italic" style="color: var(--body-color, #9ca3af)">{{__('Estimated: 10–20 seconds')}}</span>
                            </div>
                            <div class="w-full rounded-full h-2 overflow-hidden" style="background-color: var(--section-bg-3, #f3f4f6)">
                                <div id="pmProgressBar" class="h-2 rounded-full bg-primary transition-all duration-700 ease-out" style="width: 0%"></div>
                            </div>
                        </div>

                        {{-- Step checklist --}}
                        <div class="w-full max-w-sm space-y-2.5 mb-6 text-left">
                            @foreach([
                                [1, __('Payment Confirmed')],
                                [2, __('Store Created')],
                                [3, __('Database Initialized')],
                                [4, __('Installing Theme')],
                                [5, __('Configuring Store Settings')],
                                [6, __('Launching Store')],
                            ] as [$n, $label])
                            <div class="pm-pstep flex items-center gap-3" data-pstep="{{ $n }}">
                                <span class="pm-pstep-ico w-7 h-7 rounded-full border-2 border-gray-200 flex items-center justify-center shrink-0 transition-all duration-300">
                                    <i class="ti tabler-minus text-gray-300 text-[11px] pm-pstep-pending"></i>
                                    <svg class="hidden w-3.5 h-3.5 text-primary animate-spin pm-pstep-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                    <i class="ti tabler-check hidden text-white text-[11px] pm-pstep-check"></i>
                                </span>
                                <span class="text-sm pm-pstep-label transition-colors duration-300" style="color: var(--body-color, #9ca3af)">{{ $label }}</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Warning notice --}}
                        <div class="flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700 w-full max-w-sm">
                            <i class="ti tabler-alert-triangle text-amber-500 shrink-0 text-sm mt-0.5"></i>
                            <span>{{__("Please don't close this window while your store is being created.")}}</span>
                        </div>

                    </div>
                </div>

            </div>{{-- end inner padding --}}
        </div>{{-- end body --}}

        {{-- ── FOOTER NAVIGATION ── --}}
        <div class="flex items-center justify-between px-6 py-4 border-t border-borderCS shrink-0" id="pmFooter" style="background-color: var(--section-bg-1, #ffffff)">
            <button type="button" id="pmBackBtn"
                    class="flex items-center gap-2 px-5 py-2.5 border border-borderCS rounded-xl text-sm font-semibold
                           transition disabled:opacity-40 disabled:cursor-not-allowed"
                    style="color: var(--body-color, #4b5563)"
                    disabled>
                <i class="ti tabler-arrow-left text-sm"></i>
                {{__('Back')}}
            </button>

            <div class="flex items-center gap-3">
                <span class="text-xs sm:hidden" id="pmMobileStepIndicator" style="color: var(--body-color, #9ca3af)">1 / 4</span>

                <button type="button" id="pmNextBtn"
                        class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold
                               hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    {{__('Next')}}
                    <i class="ti tabler-arrow-right text-sm"></i>
                </button>

                <button type="button" id="pmSubmitBtn"
                        class="hidden flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold
                               hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="ti tabler-rocket text-sm"></i>
                    {{__('Confirm & Pay')}}
                </button>
            </div>
        </div>

    </div>{{-- end card --}}
</div>{{-- end modal --}}

{{-- ── HIDDEN FORM ── --}}
<form id="pmHiddenForm"
      action="{{ route('landlord.frontend.order.payment.form') }}"
      method="POST"
      enctype="multipart/form-data"
      class="hidden">
    @csrf
    <input type="hidden" name="name"                 id="pmFormName"            value="{{ $_modal_user?->name }}">
    <input type="hidden" name="email"                id="pmFormEmail"           value="{{ $_modal_user?->email }}">
    <input type="hidden" name="package_id"           id="pmFormPlanId">
    <input type="hidden" name="theme_slug"           id="pmFormTheme"           value="{{ $_modal_default_theme }}">
    <input type="hidden" name="subdomain"            id="pmFormSubdomain">
    <input type="hidden" name="custom_subdomain"     id="pmFormCustomSubdomain">
    <input type="hidden" name="payment_gateway"      id="pmFormGateway"         value="">
    <input type="hidden" name="selected_payment_gateway" id="pmFormSelectedGateway" value="">
    <input type="hidden" name="coupon"               id="pmFormCoupon"          value="">
</form>

<style>
/* ── Pagination visibility ── */
.pm-paged-hidden { display: none !important; }
.pm-theme-filtered-out { display: none !important; }

/* ── Modal open ── */
#purchaseFlowModal.is-open { display: flex !important; }
#purchaseFlowModal.is-open #pmCard {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

/* ── Plan card base ── */
.pm-plan-card {
    box-shadow: 0 1px 3px rgba(0,0,0,0.07), 0 0 0 1px rgba(0,0,0,0.07);
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}
.pm-plan-card:hover {
    box-shadow: 0 8px 30px -8px rgba(var(--main-color-one-rgb, 12, 77, 84), 0.70);
    transform: translateY(-3px);
}

/* ── Feature dot ── */
.pm-feat-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
    background: var(--main-color-two, #92E721);
    display: inline-block;
}

/* ── Select button default ── */
.pm-plan-select-btn {
    color: var(--main-color-one, #0C4D54);
    border: 1.5px solid rgba(var(--main-color-one-rgb, 12, 77, 84), 0.25);
    background: rgba(var(--main-color-one-rgb, 12, 77, 84), 0.04);
    letter-spacing: 0.02em;
}
.pm-plan-card:hover .pm-plan-select-btn {
    background: var(--main-color-one, #0C4D54);
    color: #fff;
    border-color: var(--main-color-one, #0C4D54);
}

/* ── Plan card selected ── */
.pm-plan-card.pm-selected {
    box-shadow: 0 0 0 2px var(--main-color-one, #0C4D54), 0 8px 24px rgba(var(--main-color-one-rgb, 12, 77, 84), 0.14) !important;
    background: linear-gradient(150deg, rgba(var(--main-color-one-rgb, 12, 77, 84), 0.05) 0%, var(--section-bg-1, #ffffff) 70%);
    transform: translateY(-2px);
}
.pm-plan-card.pm-selected .pm-plan-check { display: flex !important; }
.pm-plan-card.pm-selected .pm-plan-select-btn {
    background: var(--main-color-one, #0C4D54);
    color: #fff;
    border-color: var(--main-color-one, #0C4D54);
}

/* ── Theme card selected ── */
.pm-theme-card.pm-selected {
    border-color: var(--main-color-one, #0C4D54) !important;
    box-shadow: 0 0 0 3px rgba(var(--main-color-one-rgb, 12, 77, 84), 0.1);
}
.pm-theme-card.pm-selected .pm-theme-check { display: flex !important; }

/* ── Stepper states ── */
.pm-step-item[data-completed="true"] .pm-step-bubble {
    background-color: var(--main-color-one, #0C4D54); border-color: var(--main-color-one, #0C4D54); color: white;
}
.pm-step-item[data-completed="true"] .pm-step-num  { display: none; }
.pm-step-item[data-completed="true"] .pm-step-check { display: block !important; }
.pm-step-item[data-completed="true"] .pm-step-label { color: var(--main-color-one, #0C4D54); }
.pm-step-item[data-completed="true"] .pm-connector  { background-color: var(--main-color-one, #0C4D54); }
.pm-step-item[data-active="true"] .pm-step-bubble {
    background-color: var(--main-color-one, #0C4D54); border-color: var(--main-color-one, #0C4D54); color: white;
}
.pm-step-item[data-active="true"] .pm-step-label { color: var(--main-color-one, #0C4D54); font-weight: 600; }

/* ── Step animation ── */
.pm-step-content { animation: pmFadeIn 0.25s ease-out; }
@keyframes pmFadeIn {
    from { opacity: 0; transform: translateX(12px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* ── Subdomain feedback colors ── */
#pmSubdomainFeedback .pm-available { color: #4D8700; }
#pmSubdomainFeedback .pm-checking  { color: #92400e; }
#pmSubdomainFeedback .pm-taken     { color: #dc2626; }

/* ── Pagination buttons ── */
.pm-page-btn {
    width: 30px; height: 30px;
    border-radius: 50%;
    border: 2px solid var(--extra-light-color, #e5e7eb);
    background: var(--section-bg-1, #fff);
    font-size: 12px;
    font-weight: 600;
    color: var(--body-color, #6b7280);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.pm-page-btn:hover { border-color: var(--main-color-one, #0C4D54); color: var(--main-color-one, #0C4D54); }
.pm-page-btn.active {
    background: var(--main-color-one, #0C4D54);
    border-color: var(--main-color-one, #0C4D54);
    color: #fff;
}
.pm-page-btn-arrow {
    width: 30px; height: 30px;
    border-radius: 50%;
    border: 2px solid var(--extra-light-color, #e5e7eb);
    background: var(--section-bg-1, #fff);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
    color: var(--body-color, #6b7280);
    font-size: 13px;
}
.pm-page-btn-arrow:hover:not(:disabled) { border-color: var(--main-color-one, #0C4D54); color: var(--main-color-one, #0C4D54); }
.pm-page-btn-arrow:disabled { opacity: 0.35; cursor: default; }

/* ── Gateway wrapper — mirrors wallet page approach ── */
.pm-gateway-area .payment_getway_image { }
.pm-gateway-area .payment_getway_image ul {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}
.pm-gateway-area .payment_getway_image ul li {
    position: relative;
    width: calc(25% - 6px);
    height: 52px;
    overflow: hidden;
    box-sizing: border-box;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--extra-light-color, #e5e7eb);
    border-radius: 8px;
    background: var(--section-bg-1, #fff);
    transition: border-color 0.2s;
}
@media (min-width: 480px) {
    .pm-gateway-area .payment_getway_image ul li { width: calc(20% - 7px); }
}
.pm-gateway-area .payment_getway_image ul li:hover { border-color: var(--main-color-one, #0C4D54); }
.pm-gateway-area .payment_getway_image ul li .img-select {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
    box-sizing: border-box;
}
.pm-gateway-area .payment_getway_image ul li .img-select img,
.pm-gateway-area .payment_getway_image ul li img {
    max-width: 100%;
    max-height: 32px;
    width: auto;
    height: auto;
    object-fit: contain;
    display: block;
    margin: auto;
}
.pm-gateway-area .payment_getway_image ul li.selected { border-color: var(--main-color-one, #0C4D54); }
.pm-gateway-area .payment_getway_image ul li.selected::after {
    content: '';
    position: absolute; top: 3px; right: 3px;
    width: 14px; height: 14px;
    background-color: var(--main-color-one, #0C4D54);
    border-radius: 50%;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
    background-size: 9px; background-repeat: no-repeat; background-position: center;
}
/* Hide the extra fields wrapper generated by the helper — we handle manual fields manually */
.pm-gateway-area .payment_gateway_extra_field_information_wrap { display: none !important; }

/* ── Progress screen step states ── */
.pm-pstep.pstep-active .pm-pstep-ico   { border-color: var(--main-color-one, #0C4D54); background-color: rgba(var(--main-color-one-rgb, 12, 77, 84), 0.06); }
.pm-pstep.pstep-active .pm-pstep-pending { display: none !important; }
.pm-pstep.pstep-active .pm-pstep-spin  { display: block !important; }
.pm-pstep.pstep-active .pm-pstep-label { color: var(--main-color-one, #0C4D54); font-weight: 600; }

.pm-pstep.pstep-done .pm-pstep-ico     { border-color: var(--main-color-one, #0C4D54); background-color: var(--main-color-one, #0C4D54); }
.pm-pstep.pstep-done .pm-pstep-pending { display: none !important; }
.pm-pstep.pstep-done .pm-pstep-spin    { display: none !important; }
.pm-pstep.pstep-done .pm-pstep-check   { display: block !important; }
.pm-pstep.pstep-done .pm-pstep-label   { color: #374151; }

@keyframes pmStorePulse {
    0%, 100% { transform: scale(1); }
    50%       { transform: scale(1.12); }
}
#pmProgressIcon { animation: pmStorePulse 1.6s ease-in-out infinite; }
#pmProgressIcon.pm-icon-done { animation: none; color: #166534; }
#pmProgressTitle.pm-success  { color: #166534; }

/* ── Stepper connector default ── */
.pm-connector { background-color: var(--extra-light-color, #e5e7eb); }

/* ── Trial/Purchase mode toggle ── */
.pm-mode-btn {
    color: var(--body-color, #6b7280);
    background: transparent;
    border: none;
    cursor: pointer;
}
.pm-mode-btn.active {
    background: var(--main-color-one, #0C4D54);
    color: #fff;
    box-shadow: 0 2px 8px rgba(var(--main-color-one-rgb, 12, 77, 84), 0.18);
    border-radius: 0.75rem;
}
</style>

<script>
(function ($) {
    'use strict';

    /* ══════════════════════════════════════════
       STATE
    ══════════════════════════════════════════ */
    const PM_DEFAULT_GATEWAY = '{{ get_static_option('site_default_payment_gateway') }}';

    const PM = {
        currentStep: 1,
        totalSteps: 4,
        state: {
            planId:       null,
            planName:     null,
            planPrice:    null,
            planPriceDisplay: null,
            planType:     null,
            planTypeLabel: null,
            planThemes:   [],
            planHasTrial: false,
            planTrialDays: 0,
            themeSlug:    null,
            themeName:    null,
            subdomain:    null,
            customSubdomain: null,
            gateway:      null,
            coupon:       null,
            couponDiscount: 0,
            couponType:   null,
            isSubdomainAvailable: false,
        },
        currentMode: 'purchase', // 'purchase' | 'trial'
        userHasTrial: {{ $_modal_user_has_trial ? 'true' : 'false' }},
        subdomainTimer: null,
        isSubmitting: false,
        preselectedPlanId: null,
        // Pagination
        planPages: {},   // { typeVal: currentPage }
        themePage: 1,
    };

    const CARDS_PER_PAGE = 4;

    const $modal    = $('#purchaseFlowModal');
    const $card     = $('#pmCard');
    const $nextBtn  = $('#pmNextBtn');
    const $backBtn  = $('#pmBackBtn');
    const $submitBtn = $('#pmSubmitBtn');
    const $form     = $('#pmHiddenForm');

    /* ══════════════════════════════════════════
       PAGINATION HELPERS
    ══════════════════════════════════════════ */
    function buildPagination($container, total, perPage, currentPage, onPageChange) {
        const totalPages = Math.ceil(total / perPage);
        $container.empty();
        if (totalPages <= 1) return;

        // Prev arrow
        const $prev = $('<button class="pm-page-btn-arrow" type="button"><i class="ti tabler-chevron-left"></i></button>');
        if (currentPage === 1) $prev.prop('disabled', true);
        $prev.on('click', function () { if (currentPage > 1) onPageChange(currentPage - 1); });
        $container.append($prev);

        // Page numbers
        for (let p = 1; p <= totalPages; p++) {
            const $btn = $('<button class="pm-page-btn" type="button">' + p + '</button>');
            if (p === currentPage) $btn.addClass('active');
            $btn.on('click', (function(page){ return function(){ onPageChange(page); }; })(p));
            $container.append($btn);
        }

        // Next arrow
        const $next = $('<button class="pm-page-btn-arrow" type="button"><i class="ti tabler-chevron-right"></i></button>');
        if (currentPage === totalPages) $next.prop('disabled', true);
        $next.on('click', function () { if (currentPage < totalPages) onPageChange(currentPage + 1); });
        $container.append($next);
    }

    /* ── Plan pagination (per type) ── */
    function applyPlanPage(typeVal) {
        const page = PM.planPages[typeVal] || 1;
        const $group = $(`.pm-plan-group[data-group="${typeVal}"]`);
        const $cards = $group.find('.pm-plan-card');
        const total = $cards.length;

        $cards.each(function (i) {
            const show = i >= (page - 1) * CARDS_PER_PAGE && i < page * CARDS_PER_PAGE;
            $(this).toggleClass('pm-paged-hidden', !show);
        });

        buildPagination(
            $(`.pm-plan-pagination[data-group="${typeVal}"]`),
            total, CARDS_PER_PAGE, page,
            function (newPage) {
                PM.planPages[typeVal] = newPage;
                applyPlanPage(typeVal);
                $('#pmMainContent').scrollTop(0);
            }
        );
    }

    function initPlanPagination() {
        $('.pm-plan-group').each(function () {
            const typeVal = $(this).data('group');
            if (!PM.planPages[typeVal]) PM.planPages[typeVal] = 1;
            applyPlanPage(typeVal);
        });
    }

    /* ── Theme pagination ── */
    function applyThemePage(page) {
        PM.themePage = page;
        const $visible = $('.pm-theme-card:not(.pm-theme-filtered-out)');
        const total = $visible.length;

        $visible.each(function (i) {
            const show = i >= (page - 1) * CARDS_PER_PAGE && i < page * CARDS_PER_PAGE;
            $(this).toggleClass('pm-paged-hidden', !show);
        });

        buildPagination(
            $('#pmThemePagination'),
            total, CARDS_PER_PAGE, page,
            function (newPage) {
                applyThemePage(newPage);
                $('#pmMainContent').scrollTop(0);
            }
        );
    }

    /* ══════════════════════════════════════════
       STEP NAVIGATION
    ══════════════════════════════════════════ */
    function showStep(n) {
        PM.currentStep = n;

        // Restore chrome hidden during progress screen
        if (n <= 4) {
            $('#pmStepper').removeClass('hidden');
            $('#pmFooter').removeClass('hidden');
            $('#pmClose').removeClass('hidden');
        }

        $('.pm-step-content').addClass('hidden');
        $(`.pm-step-content[data-step-content="${n}"]`).removeClass('hidden');

        $('.pm-step-item').each(function () {
            const s = parseInt($(this).data('step'));
            $(this).removeAttr('data-active data-completed');
            if (s < n)  $(this).attr('data-completed', 'true');
            if (s === n) $(this).attr('data-active', 'true');
        });

        const _pmPrimaryColor = getComputedStyle(document.documentElement).getPropertyValue('--main-color-one').trim() || '#0C4D54';
        $('.pm-connector').each(function (i) {
            $(this).css('background-color', (i + 1) < n ? _pmPrimaryColor : '#e5e7eb');
        });

        $backBtn.prop('disabled', n === 1);
        updateNextButton();

        const titles = {
            1: ['{{__('Create Your Shop')}}',  '{{__('Step 1 of 4 — Choose a plan')}}'],
            2: ['{{__('Pick Your Theme')}}',    '{{__('Step 2 of 4 — Choose a theme')}}'],
            3: ['{{__('Store Information')}}',  '{{__('Step 3 of 4 — Set up your store')}}'],
            4: ['{{__('Payment')}}',            '{{__('Step 4 of 4 — Complete your order')}}'],
        };
        $('#pmTitle').text(titles[n][0]);
        $('#pmSubTitle').text(titles[n][1]);
        $('#pmMobileStepIndicator').text(n + ' / ' + PM.totalSteps);

        if (n === PM.totalSteps) {
            $nextBtn.addClass('hidden');
            $submitBtn.removeClass('hidden');
        } else {
            $nextBtn.removeClass('hidden');
            $submitBtn.addClass('hidden');
        }

        if (n === 4) {
            refreshPaymentSummaryStrip();
            // Sync default pre-selected gateway into state if not already set
            if (!PM.state.gateway) {
                let $preSelected = $('.pm-gateway-area .payment_getway_image ul li.selected');
                if (!$preSelected.length && PM_DEFAULT_GATEWAY) {
                    $preSelected = $('.pm-gateway-area .payment_getway_image ul li[data-gateway="' + PM_DEFAULT_GATEWAY + '"]');
                    if ($preSelected.length) $preSelected.addClass('selected');
                }
                if ($preSelected.length) {
                    PM.state.gateway = $preSelected.data('gateway');
                    $('#pmFormGateway').val(PM.state.gateway);
                    $('#pmFormSelectedGateway').val(PM.state.gateway);
                }
            }
            // Show/hide trial toggle based on plan eligibility
            const showToggle = PM.state.planHasTrial && !PM.userHasTrial;
            $('#pmModeToggle').toggleClass('hidden', !showToggle);
            $('#pmNoTrialNote').toggleClass('hidden', showToggle);
            if (showToggle) {
                $('.pm-trial-days-label').text(PM.state.planTrialDays ? '(' + PM.state.planTrialDays + ' {{__("days")}}' + ')' : '');
            }
            // If plan no longer supports trial but mode was trial, reset to purchase
            if (!showToggle && PM.currentMode === 'trial') {
                switchMode('purchase');
            }
        }
        if (n === 2) filterThemes();

        $('#pmMainContent').scrollTop(0);
    }

    function updateNextButton() {
        let valid = false;
        switch (PM.currentStep) {
            case 1: valid = !!PM.state.planId;      break;
            case 2: valid = !!PM.state.themeSlug;   break;
            case 3: valid = validateStep3();         break;
            case 4: valid = true;                   break;
        }
        $nextBtn.prop('disabled', !valid);
    }

    function validateStep3() {
        @if($_modal_user)
            const sel = $('#pmShopSelect').length ? $('#pmShopSelect').val() : 'custom_domain__dd';
            if (sel && sel !== 'custom_domain__dd') return true;
            return PM.state.isSubdomainAvailable && !!PM.state.customSubdomain;
        @else
            return false;
        @endif
    }

    /* ══════════════════════════════════════════
       OPEN / CLOSE
    ══════════════════════════════════════════ */
    function resetModal() {
        pmClearTimers();

        // Restore chrome
        $('#pmStepper').removeClass('hidden');
        $('#pmFooter').removeClass('hidden');
        $('#pmClose').removeClass('hidden');

        // Reset JS state
        PM.state = {
            planId: null, planName: null, planPrice: null, planPriceDisplay: null,
            planType: null, planTypeLabel: null, planThemes: [], planHasTrial: false,
            planTrialDays: 0, themeSlug: null, themeName: null, subdomain: null,
            customSubdomain: null, gateway: null, coupon: null, couponDiscount: 0,
            couponType: null, isSubdomainAvailable: false,
        };
        PM.isSubmitting  = false;
        PM.currentMode   = 'purchase';
        PM.planPages     = {};
        PM.themePage     = 1;

        // Visual — plan cards
        $('.pm-plan-card').removeClass('pm-selected');
        $('.pm-plan-check').removeClass('flex').addClass('hidden');

        // Visual — theme cards
        $('.pm-theme-card').removeClass('pm-selected border-primary').addClass('border-gray-200');
        $('.pm-theme-check').removeClass('flex').addClass('hidden');

        // Visual — gateway
        $('.pm-gateway-area ul li').removeClass('selected');

        // Step 3 inputs
        if ($('#pmShopSelect').length) {
            $('#pmShopSelect').val('custom_domain__dd');
            PM.state.subdomain = 'custom_domain__dd';
        }
        $('#pmNewShopWrap').show();
        $('#pmSubdomainInput').val('');
        $('#pmSubdomainFeedback').html('');

        // Coupon
        $('#pmCouponInput').val('');
        $('#pmCouponFeedback').html('');
        $('#pmCouponWrap').addClass('hidden');
        $('#pmFormCoupon').val('');
        $('.pm-total-display').text('—');

        // Submit button text
        $submitBtn.prop('disabled', false).html('<i class="ti tabler-rocket text-sm"></i> {{__("Confirm & Pay")}}');


        // Re-init plan pagination to page 1
        initPlanPagination();
    }

    function openModal(preselectedPlanId) {
        PM.preselectedPlanId = preselectedPlanId || null;
        resetModal();
        switchMode('purchase');
        $modal.addClass('is-open');
        $('body').addClass('overflow-hidden');

        if (PM.preselectedPlanId) {
            const $target = $(`.pm-plan-card[data-plan-id="${PM.preselectedPlanId}"]`);
            if ($target.length) {
                switchPlanTab($target.data('plan-type'));
                selectPlan($target);
            }
        }

        showStep(1);
    }

    function closeModal() {
        $modal.removeClass('is-open');
        $('body').removeClass('overflow-hidden');
    }

    /* ══════════════════════════════════════════
       PLAN SELECTION
    ══════════════════════════════════════════ */
    function switchPlanTab(type) {
        $('.pm-plan-tab').each(function () {
            const isActive = $(this).data('type') == type;
            $(this).toggleClass('bg-white text-secondary shadow-sm', isActive);
            $(this).toggleClass('text-gray-500', !isActive);
        });
        $('.pm-plan-group').addClass('hidden');
        $(`.pm-plan-group[data-group="${type}"]`).removeClass('hidden');
        applyPlanPage(type);
    }

    function selectPlan($c) {
        $('.pm-plan-card').removeClass('pm-selected');
        $c.addClass('pm-selected');

        PM.state.planId           = $c.data('plan-id');
        PM.state.planName         = $c.data('plan-name');
        PM.state.planPrice        = parseFloat($c.data('plan-price'));
        PM.state.planPriceDisplay = $c.data('plan-price-display');
        PM.state.planType         = $c.data('plan-type');
        PM.state.planTypeLabel    = $c.data('plan-type-label');
        PM.state.planThemes       = JSON.parse($c.attr('data-plan-themes') || '[]');
        PM.state.planHasTrial     = parseInt($c.data('plan-has-trial') || 0) === 1;
        PM.state.planTrialDays    = parseInt($c.data('plan-trial-days') || 0);

        updateNextButton();
    }

    $(document).on('click', '.pm-plan-tab', function () {
        switchPlanTab($(this).data('type'));
    });

    $(document).on('click', '.pm-plan-card', function () {
        selectPlan($(this));
    });

    /* ══════════════════════════════════════════
       THEME FILTERING & SELECTION
    ══════════════════════════════════════════ */
    function filterThemes() {
        // All themes are available to all plans — no plan-based filtering.
        $('.pm-theme-card').each(function () {
            $(this).removeClass('pm-theme-filtered-out pm-paged-hidden');
        });

        const visible = $('.pm-theme-card:not(.pm-theme-filtered-out)').length;
        $('#pmNoTheme').toggleClass('hidden', visible > 0);

        // Auto-select default theme (mirrors order-page-new behaviour)
        if (!PM.state.themeSlug) {
            const defaultSlug = '{{ $_modal_default_theme }}';
            let $toSelect = $('.pm-theme-card:not(.pm-theme-filtered-out)[data-theme-slug="' + defaultSlug + '"]');
            if (!$toSelect.length) {
                $toSelect = $('.pm-theme-card:not(.pm-theme-filtered-out)').first();
            }
            if ($toSelect.length) selectTheme($toSelect);
        }

        PM.themePage = 1;
        applyThemePage(1);
    }

    function selectTheme($c) {
        $('.pm-theme-card').removeClass('pm-selected border-primary').addClass('border-gray-200');
        $('.pm-theme-check').removeClass('flex').addClass('hidden');
        $c.addClass('pm-selected').removeClass('border-gray-200');
        $c.find('.pm-theme-check').removeClass('hidden').addClass('flex');

        PM.state.themeSlug = $c.data('theme-slug');
        PM.state.themeName = $c.data('theme-name');

        updateNextButton();
    }

    $(document).on('click', '.pm-theme-card', function () {
        selectTheme($(this));
    });

    /* ══════════════════════════════════════════
       STEP 3 — SUBDOMAIN
    ══════════════════════════════════════════ */
    @if($_modal_user)

    $(document).on('change', '#pmShopSelect', function () {
        const val = $(this).val();
        if (val === 'custom_domain__dd') {
            $('#pmNewShopWrap').show();
            PM.state.subdomain            = 'custom_domain__dd';
            PM.state.customSubdomain      = null;
            PM.state.isSubdomainAvailable = false;
        } else {
            $('#pmNewShopWrap').hide();
            PM.state.subdomain            = val;
            PM.state.customSubdomain      = val;
            PM.state.isSubdomainAvailable = true;
        }
        updateNextButton();
    });

    $(document).on('input', '#pmSubdomainInput', function () {
        clearTimeout(PM.subdomainTimer);

        let value = $(this).val().toLowerCase().replace(/\s/g, '-').replace(/([@#%&"';><,`~.*+?^=!:${}()|\[\]\/\\])/g, '-');
        $(this).val(value);

        const $fb = $('#pmSubdomainFeedback');

        if (!value) {
            $fb.html('');
            PM.state.isSubdomainAvailable = false;
            PM.state.customSubdomain = null;
            updateNextButton();
            return;
        }

        $fb.html('<span class="pm-checking flex items-center gap-1"><svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> {{__('Checking availability...')}}</span>');

        PM.subdomainTimer = setTimeout(function () {
            const siteFirst = '{{str_replace(['http://','https://'], '', url('/'))}}';
            const domain    = siteFirst.split('.').shift();
            const restricted = ['https','http','www','subdomain','domain','primary-domain','central-domain','landlord','landlords','tenant','tenants','admin','user', domain];

            if (restricted.includes(value)) {
                $fb.html('<span class="pm-taken flex items-center gap-1"><i class="ti tabler-circle-x text-sm"></i> {{__('This name is not allowed.')}}</span>');
                PM.state.isSubdomainAvailable = false;
                updateNextButton();
                return;
            }

            $.ajax({
                url:     '{{ route('landlord.subdomain.check') }}',
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data:    { subdomain: value },
                success: function () {
                    $fb.html('<span class="pm-available flex items-center gap-1"><i class="ti tabler-circle-check text-sm"></i> <strong>' + value + '.</strong>{{ $_modal_base_url }} {{__('is available!')}}</span>');
                    PM.state.isSubdomainAvailable = true;
                    PM.state.customSubdomain      = value;
                    PM.state.subdomain            = 'custom_domain__dd';
                    updateNextButton();
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.errors?.subdomain?.[0] || '{{__('This name is already taken.')}}';
                    $fb.html('<span class="pm-taken flex items-center gap-1"><i class="ti tabler-circle-x text-sm"></i>' + msg + '</span>');
                    PM.state.isSubdomainAvailable = false;
                    PM.state.customSubdomain      = null;
                    updateNextButton();
                }
            });
        }, 600);
    });

    @endif

    /* ══════════════════════════════════════════
       STEP 4 — PAYMENT GATEWAY
    ══════════════════════════════════════════ */
    $(document).on('click', '.pm-gateway-area ul li', function () {
        $('.pm-gateway-area ul li').removeClass('selected');
        $(this).addClass('selected');

        const gw = $(this).data('gateway');
        PM.state.gateway = gw;

        $('.pm-manual-fields').toggleClass('hidden', gw !== 'manual_payment');

        $('#pmFormGateway').val(gw);
        $('#pmFormSelectedGateway').val(gw);
    });

    /* ══════════════════════════════════════════
       TRIAL / PURCHASE MODE TOGGLE
    ══════════════════════════════════════════ */
    function switchMode(mode) {
        PM.currentMode = mode;
        $('.pm-mode-btn').removeClass('active');
        $('.pm-mode-btn[data-mode="' + mode + '"]').addClass('active');

        if (mode === 'trial') {
            $('#pmPaymentSection').slideUp(200);
            $('#pmCouponSection').slideUp(200);
            $submitBtn.html('<i class="ti tabler-rocket text-sm"></i> {{__("Start Free Trial")}}');
        } else {
            $('#pmPaymentSection').slideDown(200);
            $('#pmCouponSection').slideDown(200);
            $submitBtn.html('<i class="ti tabler-rocket text-sm"></i> {{__("Confirm & Pay")}}');
        }
    }

    $(document).on('click', '.pm-mode-btn', function () {
        const mode = $(this).data('mode');
        if (mode !== PM.currentMode) switchMode(mode);
    });

    // Coupon toggle
    $('#pmToggleCoupon').on('click', function () {
        $('#pmCouponWrap').toggleClass('hidden');
    });

    // Coupon apply
    $('#pmApplyCoupon').on('click', function () {
        const code = $('#pmCouponInput').val().trim();
        if (!code) return;

        const $fb = $('#pmCouponFeedback');
        $fb.html('<span class="text-gray-400">{{__('Checking...')}}</span>');

        $.get('{{ route('landlord.frontend.coupon.apply') }}', { coupon: code }, function (res) {
            if (res.type === 'success') {
                const d = res.data;
                PM.state.coupon         = code;
                PM.state.couponType     = d.discount_type;
                PM.state.couponDiscount = parseFloat(d.discount_amount);

                const base     = PM.state.planPrice || 0;
                let discount   = d.discount_type === 'percentage' ? (base * PM.state.couponDiscount / 100) : PM.state.couponDiscount;
                discount       = Math.min(discount, base);
                const final    = base - discount;

                $fb.html('<span class="text-green-600 font-medium flex items-center gap-1"><i class="ti tabler-circle-check text-sm"></i> {{__('Coupon applied! Discount:')}} -{{ site_currency_symbol() }}' + discount.toFixed(2) + '</span>');
                $('.pm-total-display').text('{{ site_currency_symbol() }}' + final.toFixed(2));
                $('#pmFormCoupon').val(code);
                showPMSuccess('{{__('Coupon applied successfully!')}}');
            } else {
                PM.state.coupon = null;
                $fb.html('<span class="text-red-500 flex items-center gap-1"><i class="ti tabler-circle-x text-sm"></i>' + (res.msg || '{{__('Invalid coupon')}}') + '</span>');
            }
        }).fail(function () {
            $fb.html('<span class="text-red-500">{{__('Failed to validate coupon.')}}</span>');
        });
    });

    /* ══════════════════════════════════════════
       SUMMARY STRIP (Step 4)
    ══════════════════════════════════════════ */
    function refreshPaymentSummaryStrip() {
        $('.pm-summary-plan-name').text(PM.state.planName || '—');
        $('.pm-summary-theme-name').text(PM.state.themeName || '—');
        const store = PM.state.customSubdomain || (PM.state.subdomain !== 'custom_domain__dd' ? PM.state.subdomain : null);
        $('.pm-summary-store-name').text(store || '—');
        $('.pm-total-display').text(PM.state.planPriceDisplay || '—');
    }

    /* ══════════════════════════════════════════
       NAVIGATION
    ══════════════════════════════════════════ */
    $nextBtn.on('click', function () {
        if (PM.currentStep < PM.totalSteps) showStep(PM.currentStep + 1);
    });

    $backBtn.on('click', function () {
        if (PM.currentStep > 1) showStep(PM.currentStep - 1);
    });

    /* ══════════════════════════════════════════
       PROGRESS SCREEN
    ══════════════════════════════════════════ */
    let _pmTimers = [];

    function pmClearTimers() { _pmTimers.forEach(clearTimeout); _pmTimers = []; }
    function pmAfter(ms, fn) { _pmTimers.push(setTimeout(fn, ms)); }

    function pmStepActivate(n) {
        $(`.pm-pstep[data-pstep="${n}"]`).addClass('pstep-active').removeClass('pstep-done');
    }
    function pmStepDone(n) {
        $(`.pm-pstep[data-pstep="${n}"]`).removeClass('pstep-active').addClass('pstep-done');
    }
    function pmSetProgress(pct, etaText) {
        $('#pmProgressBar').css('width', pct + '%');
        $('#pmProgressPct').text(pct + '%');
        if (etaText !== undefined) $('#pmProgressEta').text(etaText);
    }

    function showProgressScreen() {
        const subdomain = PM.state.customSubdomain ||
                          (PM.state.subdomain && PM.state.subdomain !== 'custom_domain__dd' ? PM.state.subdomain : '');
        const baseUrl   = '{{ $_modal_base_url }}';

        $('#pmProgressStoreName').text(subdomain ? subdomain + '.' + baseUrl : '');
        $('#pmProgressTitle').text('{{__("Creating Your Store")}}').removeClass('pm-success');
        $('#pmProgressIcon').removeClass('pm-icon-done');
        $('#pmProgressEta').text('{{__("Estimated: 10–20 seconds")}}');

        // Swap step 1 label based on mode
        const step1Label = PM.currentMode === 'trial'
            ? '{{__("Free Trial Activated")}}'
            : '{{__("Payment Confirmed")}}';
        $(`.pm-pstep[data-pstep="1"] .pm-pstep-label`).text(step1Label);

        // Reset steps & bar
        $('.pm-pstep').removeClass('pstep-active pstep-done');
        pmSetProgress(0);

        // Hide stepper, footer, close button during processing
        $('#pmStepper').addClass('hidden');
        $('#pmFooter').addClass('hidden');
        $('#pmClose').addClass('hidden');
        $('#pmTitle').text('{{__("Creating Your Store")}}');
        $('#pmSubTitle').text('');

        // Show step 5 content
        $('.pm-step-content').addClass('hidden');
        $('[data-step-content="5"]').removeClass('hidden');
        $('#pmMainContent').scrollTop(0);
    }

    function pmCompleteAllSteps(successUrl) {
        pmClearTimers();
        for (let i = 1; i <= 6; i++) {
            $(`.pm-pstep[data-pstep="${i}"]`).removeClass('pstep-active').addClass('pstep-done');
        }
        pmSetProgress(100, '');
        $('#pmProgressTitle').text('{{__("Your Store is Ready!")}}').addClass('pm-success');
        $('#pmProgressIcon').addClass('pm-icon-done');

        let sec = 3;
        $('#pmProgressEta').text('{{__("Redirecting in")}} ' + sec + 's...');
        const tick = setInterval(function () {
            sec--;
            if (sec <= 0) {
                clearInterval(tick);
                location.href = successUrl;
            } else {
                $('#pmProgressEta').text('{{__("Redirecting in")}} ' + sec + 's...');
            }
        }, 1000);
    }

    function pmRestoreFromError() {
        pmClearTimers();
        PM.isSubmitting = false;
        showStep(4);
    }

    /* ══════════════════════════════════════════
       FORM SUBMIT
    ══════════════════════════════════════════ */
    /* ── Toastr helpers ── */
    function showPMError(msg) {
        toastr.error(msg, '', { timeOut: 5000, progressBar: true, closeButton: true, escapeHtml: false });
    }
    function showPMSuccess(msg) {
        toastr.success(msg, '', { timeOut: 4000, progressBar: true, closeButton: true });
    }

    $submitBtn.on('click', function () {
        if (PM.isSubmitting) return;

        if (!$('#pmTerms').is(':checked')) {
            showPMError('{{__('Please agree to the Terms & Conditions.')}}');
            return;
        }

        // ── Trial mode: AJAX submit ──
        if (PM.currentMode === 'trial') {
            const subdomain = PM.state.customSubdomain || (PM.state.subdomain !== 'custom_domain__dd' ? PM.state.subdomain : null);
            if (!subdomain) {
                showPMError('{{__('Please enter a valid subdomain.')}}');
                return;
            }

            PM.isSubmitting = true;
            showProgressScreen();

            // Simulated step animation timeline
            pmAfter(0,    function () { pmStepActivate(1); pmSetProgress(5); });
            pmAfter(400,  function () { pmStepDone(1); pmStepActivate(2); pmSetProgress(20); });
            pmAfter(1100, function () { pmStepDone(2); pmStepActivate(3); pmSetProgress(36); });
            pmAfter(2200, function () { pmStepDone(3); pmStepActivate(4); pmSetProgress(52); });
            pmAfter(3500, function () { pmStepDone(4); pmStepActivate(5); pmSetProgress(68); });
            pmAfter(5000, function () { pmStepDone(5); pmStepActivate(6); pmSetProgress(85); });

            $.ajax({
                type: 'POST',
                url:  '{{ route('landlord.frontend.trial.account') }}',
                data: {
                    _token:    $('meta[name="csrf-token"]').attr('content'),
                    user_id:   {{ $_modal_user ? $_modal_user->id : 'null' }},
                    order_id:  PM.state.planId,
                    subdomain: subdomain,
                    theme:     PM.state.themeSlug,
                },
                success: function (data) {
                    if (data.type === 'success') {
                        pmCompleteAllSteps(data.success_url);
                    } else {
                        pmRestoreFromError();
                        $submitBtn.prop('disabled', false).html('<i class="ti tabler-rocket text-sm"></i> {{__("Start Free Trial")}}');
                        showPMError(data.msg || '{{__('Something went wrong. Please try again.')}}');
                    }
                },
                error: function (xhr) {
                    pmRestoreFromError();
                    $submitBtn.prop('disabled', false).html('<i class="ti tabler-rocket text-sm"></i> {{__("Start Free Trial")}}');
                    const errors = xhr.responseJSON?.errors ?? {};
                    const msg = Object.values(errors).flat().join(', ') || '{{__('Something went wrong. Please try again.')}}';
                    showPMError(msg);
                }
            });
            return;
        }

        // ── Purchase mode: form submit ──
        if (!PM.state.gateway) {
            showPMError('{{__('Please select a payment method.')}}');
            return;
        }

        $('#pmFormPlanId').val(PM.state.planId);
        $('#pmFormTheme').val(PM.state.themeSlug);
        $('#pmFormGateway').val(PM.state.gateway);
        $('#pmFormSelectedGateway').val(PM.state.gateway);
        $('#pmFormCoupon').val(PM.state.coupon || '');

        if (PM.state.subdomain && PM.state.subdomain !== 'custom_domain__dd') {
            $('#pmFormSubdomain').val(PM.state.subdomain);
            $('#pmFormCustomSubdomain').val('');
        } else {
            $('#pmFormSubdomain').val('custom_domain__dd');
            $('#pmFormCustomSubdomain').val(PM.state.customSubdomain);
        }

        if (PM.state.gateway === 'manual_payment') {
            const txnId    = $('#pmTransactionId').val();
            const fileInput = document.getElementById('pmTransactionFile');
            $form.find('input[name="trasaction_id"]').remove();
            $form.append($('<input type="hidden" name="trasaction_id">').val(txnId));
            if (fileInput && fileInput.files.length > 0) {
                fileInput.name = 'trasaction_attachment';
                $form.append(fileInput);
            }
        }

        PM.isSubmitting = true;
        showProgressScreen();

        // Brief animation before page navigates to payment gateway
        pmAfter(0,   function () { pmStepActivate(1); pmSetProgress(15); });
        pmAfter(350, function () { pmStepDone(1); pmStepActivate(2); pmSetProgress(35); });
        pmAfter(700, function () { pmStepDone(2); pmStepActivate(3); pmSetProgress(55); $form[0].submit(); });
    });

    /* ══════════════════════════════════════════
       TRIGGERS
    ══════════════════════════════════════════ */
    $(document).on('click', '.open-purchase-modal', function (e) {
        e.preventDefault();
        openModal($(this).data('plan-id') || null);
    });

    $('#pmBackdrop').on('click', closeModal);
    $('#pmClose').on('click', closeModal);

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $modal.hasClass('is-open')) closeModal();
    });

    /* ── Init ── */
    initPlanPagination();

    /* ── Flash messages from payment form redirect ── */
    $(function () {
        @if(session('msg'))
            @php $pmFlashType = session('type') === 'danger' ? 'error' : (session('type') ?: 'info'); @endphp
            toastr.{{ $pmFlashType }}('{!! addslashes(session('msg')) !!}', '', { timeOut: 6000, progressBar: true, closeButton: true });
        @endif
        @if(session('error'))
            toastr.error('{!! addslashes(session('error')) !!}', '', { timeOut: 6000, progressBar: true, closeButton: true });
        @endif
    });

})(jQuery);
</script>
