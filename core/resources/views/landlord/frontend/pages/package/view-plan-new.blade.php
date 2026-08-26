@extends('landlord.frontend.frontend-page-master')

@section('title')
    {{$order_details->title}}
@endsection

@section('page-title')
    {{__('View Plan')}} {{' : '.$order_details->title}}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/toastr.css')}}">
    <style>
        .loader.loader_page_single {
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            background: rgba(var(--section-bg-1-rgb, 255, 255, 255), .9);
            position: fixed;
            display: none;
        }
        .loader_bottom_title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translateX(-50%);
            margin-top: 80px;
            width: 100%;
            text-align: center;
        }
        /* Theme cards */
        .theme-card.selected_theme {
            border-color: var(--sectionC, #0C4D54);
        }
        .theme-card.selected_theme .theme-checkmark {
            display: flex !important;
        }
        /* FAQ accordion */
        .faq-answer {
            display: none;
        }
        .faq-answer.open {
            display: block;
        }
        .faq-icon-open  { display: none; }
        .faq-icon-close { display: block; }
        .faq-item.active .faq-icon-open  { display: block; }
        .faq-item.active .faq-icon-close { display: none; }
        /* Trial/Login modal overlay */
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
        #create-trial-account-button[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }
        /* Theme image auto-scroll on hover */
        .theme-img-scroll {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        .theme-img-scroll img {
            width: 100%;
            height: auto;
            min-height: 200px;
            display: block;
            transform: translateY(0);
            transition: transform 4s ease-in-out;
        }
        .theme-card:hover .theme-img-scroll img {
            transform: translateY(calc(-100% + 200px));
        }
    </style>
@endsection

@section('content')
    @php
        $user_lang = get_user_lang();
        $user = Auth::guard('web')->user();
    @endphp

    {{-- Loader --}}
    <div class="loader loader_page_single">
        <div class="loader_wrapper">
            <div class="loader-01">
                <span class="loader__icon loader__icon--one"></span>
                <span class="loader__icon loader__icon--two"></span>
                <span class="loader__icon loader__icon--three"></span>
                <span class="loader__icon loader__icon--four"></span>
            </div>
            <h3 class="loader_bottom_title"></h3>
        </div>
    </div>

    <section class="py-16" style="background-color: var(--section-bg-3, #F4F8FB)">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Page heading --}}
            <div class="text-center mb-12">
                <h2 class="text-3xl font-urbanist font-semibold text-secondary">{{$order_details->title}}</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                {{-- ===== LEFT SIDEBAR: Price card + Limitations ===== --}}
                <div class="lg:col-span-1 flex flex-col gap-6">

                    {{-- Price Card --}}
                    <div class="border border-borderCS rounded-2xl p-8" style="background-color: var(--section-bg-1, #F8FAFB)">
                        <div class="mb-6">
                            <div class="flex items-baseline gap-1 mb-2">
                                <span class="text-5xl font-urbanist font-semibold text-secondary">
                                    {{amount_with_currency_symbol($order_details->price)}}
                                </span>
                            </div>
                            <p class="text-sm" style="color: var(--body-color, #666666)">{{\App\Enums\PricePlanTypEnums::getText($order_details->type)}}</p>
                        </div>

                        <div class="border-t border-borderCS mb-6"></div>

                        {{-- CTA Button --}}
                        @if($trial)
                            @php
                                $modal_target = Auth::guard('web')->check() ? 'trialModal' : 'loginModal';
                            @endphp
                            <button type="button"
                                data-modal="{{$modal_target}}"
                                class="modal-open-btn w-full bg-primary text-white font-medium px-6 py-3.5 rounded-lg hover:opacity-90 transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="icon-base ti tabler-rocket"></i>
                                {{__('Start Trial')}}
                            </button>
                        @else
                            <a href="{{route('landlord.frontend.plan.order', $order_details->id)}}"
                               class="w-full bg-primary text-white font-medium px-6 py-3.5 rounded-lg hover:opacity-90 transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="icon-base ti tabler-shopping-cart"></i>
                                {{__('Buy Now')}}
                            </a>
                        @endif

                        <div class="border-t border-gray-200 mt-6 mb-6"></div>

                        {{-- Limitations --}}
                        <h3 class="text-base font-medium text-secondary mb-4">{{__('Limitations')}}</h3>
                        <div class="space-y-3">
                            @if(!empty($order_details->page_permission_feature))
                                @php
                                    $page_permission_feature = $order_details->page_permission_feature > 0 ? $order_details->page_permission_feature : __('Unlimited');
                                @endphp
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 flex-shrink-0" style="color: var(--body-color, #374151)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span style="color: var(--body-color, #374151)">{{ __(sprintf('Page %s', $page_permission_feature))}}</span>
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
                                    <span style="color: var(--body-color, #374151)">{{__(sprintf('Product %s', $product_permission_feature))}}</span>
                                </div>
                            @endif

                            @if(!empty($order_details->storage_permission_feature))
                                @php
                                    $storage_permission_feature = $order_details->storage_permission_feature > 0 ? [$order_details->storage_permission_feature, 'MB'] : [__('Unlimited'), ''];
                                @endphp
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 flex-shrink-0" style="color: var(--body-color, #374151)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span style="color: var(--body-color, #374151)">{{__(sprintf('Storage %s %s', current($storage_permission_feature), last($storage_permission_feature)))}}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Features Card --}}
                    @if($order_details->plan_features->count() > 0)
                        <div class="border border-borderCS rounded-2xl p-8" style="background-color: var(--section-bg-1, #F8FAFB)">
                            <h3 class="text-base font-medium text-secondary mb-4">{{__('Features')}}</h3>
                            <div class="space-y-3">
                                @foreach($order_details->plan_features as $item)
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--body-color, #374151)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span style="color: var(--body-color, #374151)">{{__(str_replace('_', ' ', ucwords($item->feature_name))) ?? ''}}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- ===== RIGHT CONTENT: Description + FAQ + Themes ===== --}}
                <div class="lg:col-span-2 flex flex-col gap-8">

                    {{-- Description --}}
                    @if($order_details->description)
                        <div class="border border-borderCS rounded-2xl p-8" style="background-color: var(--section-bg-1, #F8FAFB)">
                            <h3 class="text-xl font-urbanist font-semibold text-secondary mb-4">{{__('About This Plan')}}</h3>
                            <p class="leading-7 text-justify" style="color: var(--body-color, #666666)">{{$order_details->description}}</p>
                        </div>
                    @endif

                    {{-- FAQ Accordion --}}
                    @php
                        $faq_items = !empty($order_details->faq) ? unserialize($order_details->faq, ['class' => false]) : ['title' => []];
                        $rand_number = rand(9999, 99999999);
                    @endphp
                    @if(!empty(current($faq_items['title'])))
                        <div class="border border-borderCS rounded-2xl p-8" style="background-color: var(--section-bg-1, #F8FAFB)">
                            <h3 class="text-xl font-urbanist font-semibold text-secondary mb-6">{{__('General Questions')}}</h3>
                            <div class="space-y-3" id="faq_{{$rand_number}}" itemscope itemtype="https://schema.org/FAQPage">
                                @foreach($faq_items['title'] as $index => $faq)
                                    <div class="faq-item {{$index === 0 ? 'active' : ''}} border border-borderCS rounded-xl overflow-hidden"
                                         itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">

                                        <button type="button"
                                            class="faq-toggle w-full flex items-center justify-between px-6 py-4 transition-colors text-left"
                                            style="background-color: var(--section-bg-1, #ffffff)"
                                            itemprop="name">
                                            <span class="font-medium text-secondary">{{purify_html($faq)}}</span>
                                            <span class="flex-shrink-0 ml-4">
                                                <i class="icon-base ti tabler-minus faq-icon-open text-primary"></i>
                                                <i class="icon-base ti tabler-plus faq-icon-close" style="color: var(--body-color, #9ca3af)"></i>
                                            </span>
                                        </button>

                                        <div class="faq-answer {{$index === 0 ? 'open' : ''}} px-6 pb-5"
                                             style="background-color: var(--section-bg-1, #ffffff)"
                                             itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                            <div class="border-t border-borderCS pt-4 leading-7" style="color: var(--body-color, #666666)" itemprop="text">
                                                {{purify_html($faq_items['description'][$index] ?? '')}}
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- ===== THEME SELECTION (full width) ===== --}}
            @php
                $themes = \App\Facades\ThemeDataFacade::getAllThemeData();
                $default_theme = get_static_option('default_theme');
            @endphp
            @if(count($themes) > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-urbanist font-semibold text-secondary mb-6">{{__('Select Theme')}}</h2>
                    <div class="p-8 rounded-xl border border-borderCS" style="background-color: var(--section-bg-1, #f8fafb)">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 theme-row">
                            @foreach($themes as $theme)
                                @php
                                    $theme_slug = $theme->slug;
                                    $theme_data = getIndividualThemeDetails($theme_slug);
                                    $theme_image = loadScreenshot($theme_slug);
                                    $theme_custom_name = get_static_option_central($theme_data['slug'].'_theme_name');
                                    $theme_custom_url = get_static_option_central($theme_data['slug'].'_theme_url');
                                    $theme_custom_image = get_static_option_central($theme_data['slug'].'_theme_image');
                                    $display_name = !empty($theme_custom_name) ? $theme_custom_name : $theme_data['name'];
                                    $display_image = !empty($theme_custom_image) ? $theme_custom_image : $theme_image;
//                                    $is_first = $loop->first;
//                                    if ($is_first) { $default_theme = $theme_slug; }
                                $default_selected_theme = $default_theme === $theme_slug;
//                                dd($default_selected_theme);
                                @endphp

                                <div class="group rounded-xl overflow-hidden border-2 duration-300 hover:border-primary theme-card {{$default_selected_theme ? 'selected_theme border-primary' : 'border-borderCS'}}"
                                     style="background-color: var(--section-bg-1, #ffffff)"
                                     data-theme="{{$theme_data['slug']}}"
                                     data-name="{{$display_name}}"
                                     style="cursor:pointer; position:relative;">

                                    {{-- Image --}}
                                    <div class="theme-img-scroll">
                                        <img src="{{$display_image}}" alt="{{$display_name}}">
                                        {{-- Checkmark badge --}}
                                        <div class="theme-checkmark {{$default_selected_theme ? 'flex' : 'hidden'}} absolute top-4 right-4 bg-primary w-8 h-8 rounded-full items-center justify-center shadow-lg z-20">
                                            <i class="icon-base ti tabler-check text-white"></i>
                                        </div>
                                    </div>

                                    {{-- Card Body --}}
                                    <div class="relative">
                                        {{-- Preview Overlay --}}
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all duration-300 ease-out z-10">
                                            @if(!empty($theme_custom_url))
                                                <a href="{{$theme_custom_url}}" target="_blank"
                                                   class="w-40 hover:bg-primary text-secondary hover:text-white py-3 rounded-xl font-semibold shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm"
                                                   style="background-color: var(--section-bg-3, #f3f4f6)"
                                                   onclick="event.stopPropagation()">
                                                    <i class="icon-base ti tabler-external-link"></i>
                                                    <span>{{__('Preview')}}</span>
                                                </a>
                                            @endif
                                        </div>
                                        {{-- Card Content --}}
                                        <div class="p-4 group-hover:opacity-0 transition-opacity duration-300 ease-out" style="background-color: var(--section-bg-1, #ffffff)">
                                            <h3 class="text-base font-medium text-secondary">{{$display_name}}</h3>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>

    {{-- ===== LOGIN MODAL ===== --}}
    <div class="modal-overlay" id="loginModal">
        <div class="rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden" style="background-color: var(--section-bg-1, #ffffff)">
            {{-- Header --}}
            <div class="flex items-center justify-between px-8 py-5 border-b border-borderCS">
                <div>
                    {!! render_image_markup_by_attachment_id(get_static_option('site_logo'), 'class="h-8 object-contain"') !!}
                </div>
                <button type="button" class="modal-close-btn w-8 h-8 flex items-center justify-center rounded-full transition-colors" style="color: var(--body-color, #6b7280)" data-modal="loginModal">
                    <i class="icon-base ti tabler-x"></i>
                </button>
            </div>
            {{-- Body --}}
            <div class="px-8 py-8">
                <h4 class="text-xl font-urbanist font-semibold text-secondary mb-1">{{__('Hello! Let us get started')}}</h4>
                <p class="text-sm mb-6" style="color: var(--body-color, #666666)">{{__('Sign in to continue.')}}</p>

                <x-error-msg/>
                <x-flash-msg/>
                <div id="msg-wrapper"></div>
                <div class="error-box hidden mb-4">
                    <ul class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 text-red-800 text-sm list-disc list-inside"></ul>
                </div>

                <form action="" method="post" id="login_form_order_page">
                    <div class="mb-4">
                        <label class="block text-secondary font-medium mb-2">{{__('Username')}}</label>
                        <input type="email" name="username"
                            class="w-full px-4 py-4 rounded-lg focus:outline-none text-secondary"
                            style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff)"
                            placeholder="{{__('Username')}}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-secondary font-medium mb-2">{{__('Password')}}</label>
                        <input type="password" name="password"
                            class="w-full px-4 py-4 rounded-lg focus:outline-none text-secondary"
                            style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff)"
                            placeholder="{{__('Password')}}">
                    </div>
                    <div class="flex items-center justify-between mb-6 text-sm">
                        <label class="flex items-center gap-2 text-secondary">
                            <input class="check-input" type="checkbox" id="check15">
                            {{__('Keep me signed in')}}
                        </label>
                        <a href="{{route('tenant.user.forget.password')}}" class="text-sectionC hover:underline">{{__('Forgot password?')}}</a>
                    </div>
                    <button type="submit" id="login_btn"
                        class="w-full bg-primary text-white font-medium px-6 py-3.5 rounded-lg hover:opacity-90 transition-all duration-300">
                        {{__('SIGN IN')}}
                    </button>
                    <p class="text-center text-sm mt-4" style="color: var(--body-color, #6b7280)">
                        {{__('Do not have an account?')}}
                        <a href="{{route('landlord.user.register')}}?p={{$order_details->id}}" class="text-sectionC hover:underline">{{__('Create')}}</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== TRIAL MODAL ===== --}}
    @if(Auth::guard('web')->check())
        <div class="modal-overlay" id="trialModal">
            <div class="rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden" style="background-color: var(--section-bg-1, #ffffff)">
                {{-- Header --}}
                <div class="flex items-center justify-between px-8 py-5 border-b border-borderCS">
                    <h5 class="text-lg font-urbanist font-semibold text-secondary">{{__('Start Trial')}}</h5>
                    <button type="button" class="modal-close-btn w-8 h-8 flex items-center justify-center rounded-full transition-colors" style="color: var(--body-color, #6b7280)" data-modal="trialModal">
                        <i class="icon-base ti tabler-x"></i>
                    </button>
                </div>
                {{-- Body --}}
                <div class="px-8 py-8">
                    <div class="error-wrap mb-4"></div>

                    @php
                        $site_name = env('APP_URL');
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-8">
                        {{-- Personal Info --}}
                        <div>
                            <h5 class="text-base font-medium text-secondary mb-4 pb-2 border-b border-borderCS">{{__('Personal Information')}}</h5>
                            <div class="space-y-3 text-sm" style="color: var(--body-color, #374151)">
                                <p><span class="font-medium text-secondary">{{__('Name:')}}</span> {{$user->name}}</p>
                                <p><span class="font-medium text-secondary">{{__('Email:')}}</span> {{$user->email}}</p>
                                <div class="mt-4">
                                    <label class="block font-medium text-secondary mb-2">{{__('Subdomain')}}</label>
                                    <input class="w-full px-4 py-3 rounded-lg focus:outline-none text-sm text-secondary"
                                           style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff)"
                                           type="text" name="subdomain" autocomplete="off" value=""
                                           placeholder="{{__('example')}}">
                                    <div id="subdomain-wrap" class="mt-1"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Package Info --}}
                        <div>
                            <h5 class="text-base font-medium text-secondary mb-4 pb-2 border-b border-borderCS">{{__('Package Information')}}</h5>
                            <div class="space-y-3 text-sm" style="color: var(--body-color, #374151)">
                                <p><span class="font-medium text-secondary">{{__('Plan:')}}</span> {{$order_details->title}}</p>
                                <p><span class="font-medium text-secondary">{{__('Price:')}}</span> {{float_amount_with_currency_symbol($order_details->price)}}</p>
                                <p><span class="font-medium text-secondary">{{__('Trial:')}}</span> <span class="text-capitalize">{{$order_details->trial_days}} {{__('days')}}</span></p>
                                <p class="modal_theme"><span class="font-medium text-secondary">{{__('Theme:')}}</span> <span class="ml-1"></span></p>
                            </div>
                        </div>
                    </div>

                    <form action="" method="POST">
                        <input type="hidden" name="user_id" id="user-id" value="{{$user->id}}">
                        <input type="hidden" name="order_id" id="order-id" value="{{$order_details->id}}">
                        <input type="hidden" name="theme_slug" id="theme-slug" value="{{$default_theme ?? get_static_option('default_theme')}}">

                        <div class="flex justify-end">
                            <button type="button" id="create-trial-account-button"
                                class="bg-primary text-white font-medium px-8 py-3.5 rounded-lg hover:opacity-90 transition-all duration-300 flex items-center gap-2">
                                <i class="icon-base ti tabler-rocket"></i>
                                {{__('Create Website')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script src="{{global_asset('assets/common/js/toastr.min.js')}}"></script>
    <x-custom-js.landloard-unique-subdomain-check :name="'subdomain'"/>

    <script>
        (function ($) {
            "use strict";
            $(document).ready(function ($) {

                // ---- Modal open/close ----
                $(document).on('click', '.modal-open-btn', function () {
                    let target = $(this).data('modal');
                    $('#' + target).addClass('open');
                    $('body').css('overflow', 'hidden');
                });

                $(document).on('click', '.modal-close-btn', function () {
                    let target = $(this).data('modal');
                    $('#' + target).removeClass('open');
                    $('body').css('overflow', '');
                });

                // Close on backdrop click
                $(document).on('click', '.modal-overlay', function (e) {
                    if ($(e.target).hasClass('modal-overlay')) {
                        $(this).removeClass('open');
                        $('body').css('overflow', '');
                    }
                });

                // ---- FAQ accordion ----
                $(document).on('click', '.faq-toggle', function () {
                    let item = $(this).closest('.faq-item');
                    let answer = item.find('.faq-answer');

                    if (item.hasClass('active')) {
                        item.removeClass('active');
                        answer.removeClass('open').slideUp(200);
                    } else {
                        $('.faq-item').removeClass('active');
                        $('.faq-answer').removeClass('open').slideUp(200);
                        item.addClass('active');
                        answer.addClass('open').slideDown(200);
                    }
                });

                // ---- Login AJAX ----
                $(document).on('click', '#login_btn', function (e) {
                    e.preventDefault();
                    var formContainer = $('#login_form_order_page');
                    var el = $(this);
                    var username = formContainer.find('input[name="username"]').val();
                    var password = formContainer.find('input[name="password"]').val();
                    var remember = formContainer.find('input[name="remember"]').val();

                    el.text('{{__("Please Wait")}}');

                    $.ajax({
                        type: 'post',
                        url: "{{route('landlord.user.ajax.login')}}",
                        data: {
                            _token: "{{csrf_token()}}",
                            username: username,
                            password: password,
                            remember: remember,
                        },
                        success: function (data) {
                            if (data.status == 'invalid') {
                                el.text('{{__("Login")}}');
                                formContainer.find('#msg-wrapper').html('<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 text-red-800 text-sm mb-4">' + data.msg + '</div>');
                            } else {
                                formContainer.find('#msg-wrapper').html('');
                                el.text('{{__("Login Success.. Redirecting ..")}}');
                                location.reload();
                            }
                        },
                        error: function (data) {
                            var response = data.responseJSON.errors;
                            var errorBox = $('.error-box');
                            errorBox.find('li').remove();
                            errorBox.removeClass('hidden');
                            $.each(response, function (value, index) {
                                errorBox.find('ul').append('<li>' + index + '</li>');
                            });
                            el.text('{{__("Login")}}');
                        }
                    });
                });

                // ---- Create trial account ----
                $(document).on('click', '#create-trial-account-button', function (e) {
                    e.preventDefault();
                    var timer = "";
                    var submit_button = $('#create-trial-account-button');
                    var text = [
                        '{{__('Creating Account...')}}',
                        '{{__('Creating Database...')}}',
                        '{{__('Creating Designs...')}}',
                        '{{__('Getting Ready...')}}',
                        '{{__('Your Account is Ready...')}}'
                    ];
                    let i = 1;

                    function buttonLoop(isRunning) {
                        if (isRunning) {
                            timer = setTimeout(function () {
                                submit_button.text(text[i]);
                                if (i < 5) { buttonLoop(true); }
                                i++;
                            }, 1500);
                        } else {
                            clearTimeout(timer);
                        }
                    }

                    let user_id   = $('#user-id').val();
                    let order_id  = $('#order-id').val();
                    let subdomain = $('input[name=subdomain]').val();
                    let theme     = $('input[name=theme_slug]').val();
                    let loader    = $('.loader');
                    let formContainer = $('#trialModal .px-8');

                    $.ajax({
                        type: 'post',
                        url: "{{route('landlord.frontend.trial.account')}}",
                        data: {
                            _token: "{{csrf_token()}}",
                            user_id: user_id,
                            order_id: order_id,
                            subdomain: subdomain,
                            theme: theme
                        },
                        beforeSend: function () {
                            loader.show();
                            loader.find('.loader_bottom_title').text('{!! __("Your trial account is on its way. Why don\'t you grab a coffee?") !!}');
                            submit_button.prop('disabled', true);
                            submit_button.text(text[0]);
                            buttonLoop(true);
                        },
                        success: function (data) {
                            buttonLoop(false);
                            if (data.type === 'success') {
                                loader.find('.loader_bottom_title').fadeOut();
                                loader.find('.loader_bottom_title').fadeIn();
                                loader.find('.loader_bottom_title').text('{!! __("Yeaah! Your account is ready. Lets check it out") !!}');
                                setTimeout(function () {
                                    location.href = data.success_url;
                                }, 3000);
                            } else if (data.type === 'danger') {
                                loader.hide();
                                formContainer.find('.error-wrap').html('<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 text-red-800 text-sm mb-4">' + data.msg + '</div>');
                                $('input[name=subdomain]').val('');
                                submit_button.text('{{__('Create Website')}}');
                                submit_button.prop('disabled', false);
                            }
                        },
                        error: function (data) {
                            loader.hide();
                            let i = 0;
                            buttonLoop(false);
                            submit_button.text('{{__('Create Website')}}');
                            submit_button.prop('disabled', false);
                            var response = data.responseJSON.errors;
                            var errorHtml = '<div class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 text-red-800 text-sm mb-4"><ul class="list-disc list-inside">';
                            $.each(response, function (value, index) {
                                errorHtml += '<li><span>' + (++i) + '.</span> ' + index + '</li>';
                            });
                            errorHtml += '</ul></div>';
                            formContainer.find('.error-wrap').html(errorHtml);
                        }
                    });
                });

                // ---- Theme selection ----
                let row = $('.theme-row');
                let selectedThemeName = row.find('.theme-card.selected_theme').data('name') || row.find('.theme-card').first().data('name');
                $('p.modal_theme').find('span:last-child').text(selectedThemeName);

                $(document).on('click', '.theme-card', function (e) {
                    let el = $(this);
                    let theme_slug = el.data('theme');
                    let theme_name = el.data('name');

                    $('.theme-card').removeClass('selected_theme');
                    $('.theme-card .theme-checkmark').addClass('hidden').removeClass('flex');

                    el.addClass('selected_theme');
                    el.find('.theme-checkmark').removeClass('hidden').addClass('flex');

                    $('input#theme-slug').val(theme_slug);
                    $('p.modal_theme').find('span:last-child').text(theme_name);
                });

            });
        })(jQuery);
    </script>
@endsection
