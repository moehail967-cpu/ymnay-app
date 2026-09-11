@php use App\Models\PaymentLogs;
use Illuminate\Support\Facades\Auth; @endphp

@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{__('User Home')}}
@endsection

@section('title')
    {{__('User Home')}}
@endsection

@section('style')
    <style>
        .badge {
            font-size: 15px;
        }

        .payment_getway_image ul {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            align-items: stretch;
        }

        .payment_getway_image ul li {
            width: calc(100% / 5 - 8px);
            transition: 0.3s;
            border: 2px solid transparent;
            cursor: pointer;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            border-color: #ddd;
            overflow: hidden;
            height: 42px;
        }

        .payment_getway_image ul li:is(:hover, .selected) {
            border: 2px solid red;
        }

        .text-center .confirm-details--icon {
            margin-inline: auto;
        }

        .confirm-details--icon {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 50px;
            width: 50px;
            border-radius: 50%;
            background-color: var(--main-color-three);
            color: #fff;
            font-size: 24px;
        }

        .confirm-details--title {
            font-size: 24px;
        }

        .confirm-details--para {
            font-size: 16px;
        }
    </style>
@endsection

@section('section')
    @auth
        <script>
            window.auth_id = {{ auth()->id() }};
        </script>
    @else
        <script>
            window.auth_id = null;
        </script>
    @endauth

    @php
        $auth_user = Auth::guard('web')->user();
        $user = Auth::guard('web')->user();
    @endphp

    <div class="col-span-full lg:col-span-9">
        <!-- Top Header -->
        <header class="lg:sticky top-[78px] z-30 border-b rounded-t-3xl" style="background-color: var(--section-bg-1, #FFFBFB)">
            <div class="px-6 py-2.5 flex flex-col lg:flex-row gap-4 lg:gap-0 items-center justify-between">
                <div class="flex items-center w-full lg:w-auto">
                    <!-- Mobile Menu Button -->
                    <button id="menuBtn"
                        class="block lg:hidden text-sub2Title hover:text-teal-600 focus:outline-none pr-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="icon-base ti tabler-menu-2 icon-24px sm:icon-28px"></i>
                    </button>

                    <div class="ml-3 lg:ml-0 flex-1 lg:flex-none">
                        <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-gray-800 leading-tight">
                            {{__('Dashboard')}}</h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 line-clamp-1 sm:line-clamp-none">
                            {{__('Welcome back, manage your shops and orders')}}</p>
                    </div>
                </div>

                <button
                    class="w-full lg:w-auto open-purchase-modal btn-primary text-white bg-primary px-4 py-2.5 sm:px-6 lg:px-6 sm:py-2.5 lg:py-2.5 rounded-lg font-normal lg:font-medium shadow-lg text-sm sm:text-base md:text-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-2 transition-all hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] min-w-[120px]">
                    {{__('Create Shop')}}
                </button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6">

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Order Card -->
                <div
                    class="stat-card will-animate animate-fade-in delay-100 rounded-xl p-6 border"
                    style="background: rgba(var(--main-color-one-rgb, 240, 72, 83), 0.10); border-color: var(--main-color-one, #92E721);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-lg font-normal text-sub2Title mb-2">{{__('Total Order')}}</p>
                            <h3 class="text-4xl font-bold font-urbanist text-secondary mb-2">{{ $package_orders ?? 0 }}</h3>
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(var(--main-color-one-rgb, 240, 72, 83), 0.30);">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Support Tickets Card -->
                <div
                    class="stat-card will-animate animate-fade-in delay-200 rounded-xl p-6 border"
                    style="background: rgba(var(--main-color-two-rgb, 255, 128, 93), 0.10); border-color: var(--main-color-two, #FF805D);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-lg font-medium text-sub2Title mb-2">{{__('Support Tickets')}}</p>
                            <h3 class="text-4xl font-bold text-gray-800 font-display mb-2">{{ $support_tickets ?? 0 }}</h3>
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(var(--main-color-two-rgb, 255, 128, 93), 0.20);">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Website Card -->
                <div
                    class="stat-card will-animate animate-fade-in delay-200 rounded-xl p-6 border"
                    style="background: rgba(var(--review-color-rgb, 250, 190, 80), 0.10); border-color: var(--review-color, #FABE50);">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-lg font-medium text-sub2Title mb-2">{{__('Active Website')}}</p>
                            <h3 class="text-4xl font-bold text-gray-800 font-display mb-2">
                                {{ count($user->tenant_details ?? []) }}</h3>
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(var(--review-color-rgb, 250, 190, 80), 0.20);">
                            <i class="icon-base ti tabler-activity"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Your Shops Section -->
            <div class="will-animate animate-fade-in delay-400 rounded-xl p-6 mb-8 border border-borderCS" style="background-color: var(--section-bg-1, #ffffff)">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-medium text-secondary">{{__('Your Shops')}}</h3>
                        <p class="text-sm text-sub2Title mt-1">{{__('Manage and access your online stores')}}</p>
                    </div>
                    <a href="{{ route('landlord.user.shops') }}"
                       class="text-sm font-medium text-primary hover:underline flex items-center gap-1">
                        {{__('View All')}} <i class="ti tabler-arrow-right text-base"></i>
                    </a>
                </div>

                <!-- Table -->
                <div class="rounded-2xl" style="background-color: var(--section-bg-1, #ffffff)">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                            <tr class="border-b">
                                <th class="text-left py-3 px-4 text-base font-medium text-secondary">{{__('ID')}}</th>
                                <th class="text-left py-3 px-4 text-base font-medium text-secondary">{{__('Site')}}</th>
                                <th class="text-right py-3 px-4 text-base font-medium text-secondary">{{__('Actions')}}
                                </th>
                            </tr>
                            </thead>
                            <tbody id="tableBody">
                            @foreach($tenant_details ?? [] as $key => $data)
                                @php
                                    $url = '';
                                    $central = '.' . env('CENTRAL_DOMAIN');

                                    if (!empty($data->custom_domain?->custom_domain) && $data->custom_domain?->custom_domain_status == 'connected') {
                                        $custom_url = $data->custom_domain?->custom_domain;
                                        $url = tenant_url_with_protocol($custom_url);
                                    } else {
                                        $local_url = $data->id . $central;
                                        $url = tenant_url_with_protocol($local_url);
                                    }

                                    $hash_token = hash_hmac('sha512', $user->username . '_' . $data->id, $data->unique_key);
                                    $visit_url = $url;
                                    $admin_login_url = $url . '/token-login/' . $hash_token;
                                @endphp

                                <tr class="{{ !$loop->last ? 'border-b border-borderCS' : '' }}">
                                    <td class="py-4 px-4">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold text-primary"
                            style="background: rgba(var(--main-color-one-rgb, 240, 72, 83), 0.10);">
                                            {{ $key + 1 }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                                            <span class="text-base font-normal text-sub2Title">{{ $url }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <button
                                            class="action-btn text-gray-400 hover:text-sub2Title border rounded-md hover:bg-gray-50"
                                            data-visit-url="{{ $visit_url }}"
                                            data-admin-url="{{ $admin_login_url }}"
                                            data-tenant-id="{{ $data->id }}"
                                            data-delete-url="{{ route('landlord.user.shop.delete', $data->id) }}">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Platform Growth Chart -->
            <div class="will-animate animate-fade-in delay-300 rounded-xl p-6  border border-borderCS" style="background-color: var(--section-bg-1, #ffffff)">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-medium text-secondary">{{__('Platform Growth')}}</h3>
                        <p class="text-sm text-sub2Title mt-1">{{__('Store creation over the last 6 months')}}</p>
                    </div>
                    <div class="px-4 py-2 rounded-lg" style="background: rgba(var(--main-color-one-rgb, 240, 72, 83), 0.10);">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="chart-container h-[400px]">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>


        </main>
    </div>

    {{-- Dynamic modal CSS --}}
    <style>
        .modal-input:focus { border-color: var(--main-color-one) !important; box-shadow: 0 0 0 2px rgba(var(--main-color-one-rgb), 0.15) !important; }
        .modal-coupon-apply:hover { background-color: var(--secondary-color) !important; }
        .modal-discard:hover { background-color: rgba(var(--main-color-one-rgb), 0.06) !important; }
        .have-coupon-btn:hover { opacity: 0.75; }
    </style>

    {{-- Legacy modals hidden — purchase flow now handled by #purchaseFlowModal in footer --}}
    <div id="user_add_subscription" style="display:none!important" aria-hidden="true">
        <div class="fixed z-40 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] overflow-y-auto"
            style="background-color: var(--section-bg-1, #fff)">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 pt-6 pb-4">
                <h2 class="text-lg font-bold" style="color: var(--heading-color)">{{__('Create Shop')}}</h2>
                <button
                    class="btn-close-modal w-8 h-8 flex items-center justify-center rounded-full transition"
                    style="color: var(--body-color)"
                    data-modal-target="#user_add_subscription">
                    <i class="ti tabler-x text-lg"></i>
                </button>
            </div>

            <!-- Body -->
            <form action="{{route(route_prefix() . 'frontend.order.payment.form')}}" id="user_add_subscription_form"
                method="post" enctype="multipart/form-data">
                @csrf

                <div class="px-6 pb-6 flex flex-col gap-5">
                    <input type="hidden" name="subs_user_id" id="subs_user_id" value="{{$user->id}}">
                    <input type="hidden" name="package_id" id="subs_pack_id">
                    <input type="hidden" name="name" id="name" value="{{$auth_user->name}}">
                    <input type="hidden" name="email" id="email" value="{{$auth_user->email}}">
                    <input type="hidden" name="payment_gateway" value="manual_payment"
                        class="payment_gateway_passing_clicking_name">

                    <!-- Shops -->
                    <div class="flex flex-col gap-1.5">
                        <label for="subdomain" class="text-sm font-semibold" style="color: var(--heading-color)">{{__('Shops')}}</label>
                        <div class="relative">
                            <select
                                class="modal-input subdomain w-full appearance-none border rounded-xl px-4 py-3 text-sm outline-none transition bg-white pr-10 cursor-pointer"
                                style="border-color: rgba(var(--main-color-one-rgb), 0.25); color: var(--body-color)"
                                id="subdomain" name="subdomain">
                                <option value="" selected disabled>{{__('Select a shop')}}</option>
                                @foreach($user->tenant_details ?? [] as $tenant)
                                    @continue($tenant->payment_log?->package?->type == \App\Enums\PricePlanTypEnums::LIFETIME)
                                    <option value="{{$tenant->id}}">{{optional($tenant->domain)->domain}}</option>
                                @endforeach
                                <option value="custom_domain__dd">{{__('Add new shop')}}</option>
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                <i class="ti tabler-chevron-down text-base" style="color: var(--body-color)"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Add new shop -->
                    <div class="flex flex-col gap-1.5 custom_subdomain_wrapper">
                        <label for="custom-subdomain"
                            class="text-sm font-semibold" style="color: var(--heading-color)">{{__('Add new shop')}}</label>
                        <input
                            class="modal-input custom_subdomain w-full border rounded-xl px-4 py-3 text-sm outline-none transition"
                            style="border-color: rgba(var(--main-color-one-rgb), 0.25); color: var(--body-color)"
                            id="custom-subdomain" type="text" autocomplete="off" value="{{old('subdomain')}}"
                            placeholder="{{__('Shop name')}}">
                        <div id="subdomain-wrap"></div>
                    </div>

                    <!-- Package -->
                    <div class="flex flex-col gap-1.5">
                        @php
                            $price_plan = \App\Models\PricePlan::where('status', \App\Enums\StatusEnums::PUBLISH)->get();
                        @endphp
                        <label for="package" class="text-sm font-semibold" style="color: var(--heading-color)">{{__('Package')}}</label>
                        <div class="relative">
                            <select
                                class="modal-input package_id_selector w-full appearance-none border rounded-xl px-4 py-3 text-sm outline-none transition bg-white pr-10 cursor-pointer"
                                style="border-color: rgba(var(--main-color-one-rgb), 0.25); color: var(--body-color)"
                                name="package" id="package">
                                <option value="" selected disabled>{{__('Select a package')}}</option>
                                @foreach($price_plan as $plan)
                                    @php
                                        $planTypeLabel = match ((int) $plan->type) {
                                            \App\Enums\PricePlanTypEnums::LIFETIME => __('Lifetime'),
                                            \App\Enums\PricePlanTypEnums::MONTHLY => __('Monthly'),
                                            \App\Enums\PricePlanTypEnums::YEARLY => __('Yearly'),
                                            default => $plan->validity . ' ' . __('Days'),
                                        };
                                    @endphp
                                    <option value="{{$plan->id}}">{{$plan->title}} -
                                        {{ amount_with_currency_symbol($plan->price) }}
                                        / {{ $planTypeLabel }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                <i class="ti tabler-chevron-down text-base" style="color: var(--body-color)"></i>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-2">
                            <span class="coupon-result-text text-sm"></span>
                            <button class="have-coupon-btn text-sm font-medium transition"
                                style="color: var(--main-color-one)"
                                type="button">
                                {{__('Have Coupon?')}}
                            </button>
                        </div>

                        <div class="coupon-wrapper" style="display: none">
                            <div class="flex gap-2 mt-2" id="coupon-form">
                                <input type="text"
                                    class="modal-input flex-1 border rounded-xl px-4 py-3 text-sm outline-none transition"
                                    style="border-color: rgba(var(--main-color-one-rgb), 0.25); color: var(--body-color)"
                                    name="coupon" placeholder="{{__('Coupon Code')}}">
                                <button
                                    class="modal-coupon-apply px-4 py-3 rounded-xl text-sm font-semibold border transition"
                                    style="border-color: var(--main-color-one); color: var(--main-color-one)"
                                    type="button">{{__('Apply')}}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Theme (hidden by default) -->
                    <div class="flex flex-col gap-1.5" style="display: none">
                        @php
                            $themes = getAllThemeSlug();
                        @endphp
                        <label for="custom-theme" class="text-sm font-semibold" style="color: var(--heading-color)">{{__('Add Theme')}}</label>
                        <div class="relative">
                            <select
                                class="modal-input w-full appearance-none border rounded-xl px-4 py-3 text-sm outline-none transition bg-white pr-10 cursor-pointer capitalize"
                                style="border-color: rgba(var(--main-color-one-rgb), 0.25); color: var(--body-color)"
                                name="theme_slug" id="custom-theme">
                                @foreach($themes as $theme)
                                    @php
                                        $custom_theme_name = get_static_option_central("${theme}_theme_name") ?? $theme;
                                    @endphp
                                    <option value="{{$theme}}" {{$theme === get_static_option('default_theme') ? 'selected' : ''}}>{{$custom_theme_name}}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                <i class="ti tabler-chevron-down text-base" style="color: var(--body-color)"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Gateway -->
                    <div>
                        {!! \App\Helpers\PaymentGatewayRenderHelper::renderPaymentGatewayForForm() !!}
                    </div>

                    <!-- Manual Transaction -->
                    <div class="hidden manual_transaction_id">
                        @php
                            $payment_gateways = \App\Models\PaymentGateway::where(['status' => \App\Enums\StatusEnums::PUBLISH, 'name' => 'manual_payment'])->first();
                        @endphp
                        @if(!empty($payment_gateways))
                            <p class="text-sm rounded-xl px-4 py-3 mb-3 border"
                                style="color: var(--main-color-one); background-color: var(--secondary-color); border-color: rgba(var(--main-color-one-rgb), 0.25)">
                                {{json_decode($payment_gateways->credentials)->description ?? ''}}
                            </p>
                        @endif

                        <input type="text" name="trasaction_id"
                            class="modal-input w-full border rounded-xl px-4 py-3 text-sm outline-none transition mb-3"
                            style="border-color: rgba(var(--main-color-one-rgb), 0.25); color: var(--body-color)"
                            placeholder="{{__('Transaction ID')}}">

                        <input type="file" name="trasaction_attachment"
                            class="modal-input w-full border rounded-xl px-4 py-3 text-sm outline-none transition"
                            style="border-color: rgba(var(--main-color-one-rgb), 0.25); color: var(--body-color)"
                            placeholder="{{__('Transaction Attachment')}}" accept="image/*">
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center gap-3 pt-2">
                        <button type="button"
                            class="modal-discard btn-close-modal px-6 py-3 rounded-xl text-sm font-semibold border transition"
                            style="border-color: rgba(var(--main-color-one-rgb), 0.3); color: var(--body-color)"
                            data-modal-target="#user_add_subscription">
                            {{__('Discard')}}
                        </button>
                        <button type="button" id="submit-btn"
                            class="px-6 py-3 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                            {{__('Create')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="final_result" style="display:none!important" aria-hidden="true">
        <div class="fixed z-40 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
            style="background-color: var(--section-bg-1, #fff)">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 pt-6 pb-4">
                <h2 class="text-lg font-bold" style="color: var(--heading-color)">{{__('Confirm Details')}}</h2>
                <button
                    class="btn-close-modal w-8 h-8 flex items-center justify-center rounded-full transition"
                    style="color: var(--body-color)"
                    data-modal-target="#final_result">
                    <i class="ti tabler-x text-lg"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 pb-6">
                <div class="confirm-details text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto"
                        style="background-color: var(--secondary-color)">
                        <i class="las la-check text-2xl" style="color: var(--main-color-one)"></i>
                    </div>
                    <h4 class="confirm-details--title text-xl font-bold mt-3" style="color: var(--heading-color)">{{__('New Purchase')}}</h4>

                    <div class="grid grid-cols-2 gap-4 mt-4 text-left">
                        <div class="flex flex-col gap-3">
                            <p class="confirm-details--para text-sm" style="color: var(--body-color)">
                                <span class="font-semibold block" style="color: var(--heading-color)">{{__('Shop Name:')}}</span>
                                <span class="shop_name">Null</span>
                            </p>
                            <p class="confirm-details--para text-sm" style="color: var(--body-color)">
                                <span class="font-semibold block" style="color: var(--heading-color)">{{__('Package Name:')}}</span>
                                <span class="package_name">Null</span>
                            </p>
                            <p class="confirm-details--para text-sm" style="color: var(--body-color)">
                                <span class="font-semibold block" style="color: var(--heading-color)">{{__('Theme:')}}</span>
                                <span class="theme"></span>
                            </p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <p class="confirm-details--para text-sm" style="color: var(--body-color)">
                                <span class="font-semibold block" style="color: var(--heading-color)">{{__('Price:')}}</span>
                                <span class="price"></span>
                            </p>
                            <p class="confirm-details--para text-sm" style="color: var(--body-color)">
                                <span class="font-semibold block" style="color: var(--heading-color)">{{__('Validity:')}}</span>
                                <span class="validity"></span>
                            </p>
                            <p class="confirm-details--para text-sm" style="color: var(--body-color)">
                                <span class="font-semibold block" style="color: var(--heading-color)">{{__('Payment Gateway:')}}</span>
                                <span class="payment_gateway"></span>
                            </p>
                            <p class="confirm-details--para text-sm hidden coupon-row" style="color: var(--body-color)">
                                <span class="font-semibold block" style="color: var(--heading-color)">{{__('Coupon:')}}</span>
                                <span class="coupon_used"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center gap-3 pt-6">
                    <button type="button"
                        class="modal-discard btn-close-modal px-6 py-3 rounded-xl text-sm font-semibold border transition"
                        style="border-color: rgba(var(--main-color-one-rgb), 0.3); color: var(--body-color)"
                        data-modal-target="#final_result">
                        {{__('Discard')}}
                    </button>
                    <button type="button" id="final-submit"
                        class="px-6 py-3 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                        {{__('Submit')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @php

        $auth = auth()->guard('web')->user();
        $auth_id = $auth->id;
        $subdomain = request()->getHost();
        $PaymentLogs = PaymentLogs::where(['user_id' => $auth_id, 'tenant_id' => $subdomain])->latest()->first() ?? '';

    @endphp

    <!-- Action Popover -->
    <div id="action-popover"
        class="hidden fixed p-4 w-[180px] bg-white shadow-2xl rounded-2xl border border-gray-100 z-[9999]">
        <div class="flex flex-col items-center space-y-2">
            <a id="popover-visit-link" href="#" target="_blank"
                class="w-full bg-primary text-white flex items-center justify-center gap-2 text-xs px-3 py-2 rounded-lg font-medium hover:bg-[#0a3f45] transition-colors">
                <i class="ti tabler-external-link text-xs"></i>
                {{__('Visit Website')}}
            </a>
            <a id="popover-admin-link" href="#" target="_blank"
                class="w-full text-white flex items-center justify-center gap-2 text-xs px-3 py-2 rounded-lg font-medium transition-colors hover:opacity-90"
                style="background: var(--main-color-one);">
                <i class="ti tabler-login text-xs"></i>
                {{__('Admin Login')}}
            </a>
            <button id="popover-delete-btn" type="button"
                class="w-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center gap-2 text-xs px-3 py-2 rounded-lg font-medium transition-colors">
                <i class="ti tabler-trash text-xs"></i>
                {{__('Delete Shop')}}
            </button>
        </div>
    </div>

    <!-- Delete Shop Confirmation Modal -->
    <div id="delete-shop-modal" class="hidden fixed inset-0 z-[99999] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" id="delete-modal-backdrop"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 z-10">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="ti tabler-alert-triangle text-3xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">{{__('Delete Shop?')}}</h3>
                <p class="text-sm text-gray-500">
                    {{__('This action is permanent and cannot be undone. All data associated with this shop — including payment logs, media, and domain settings — will be permanently removed.')}}
                </p>
                <p class="text-sm font-semibold text-gray-700">
                    {{__('Type the shop name to confirm:')}} <span class="text-red-500 font-mono" id="delete-shop-name-hint"></span>
                </p>
                <input type="text" id="delete-confirm-input"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400 transition"
                    placeholder="{{__('Type shop name here')}}">
            </div>
            <div class="flex items-center gap-3 mt-6">
                <button type="button" id="delete-modal-cancel"
                    class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                    {{__('Cancel')}}
                </button>
                <form id="delete-shop-form" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" id="delete-modal-confirm"
                        class="w-full px-4 py-3 rounded-xl text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                        {{__('Delete Permanently')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('assets/new-landlord/js/chart.umd.min.js') }}"></script>
    <script>
        // Dynamic chart data passed from controller
        window.growthChartLabels = @json($growthLabels);
        window.growthChartData = @json($growthValues);

        (function () {
            function initGrowthChart() {
                var canvas = document.getElementById('growthChart');
                if (!canvas) {
                    console.warn('Growth chart canvas not found');
                    return;
                }

                if (typeof Chart === 'undefined') {
                    console.error('Chart.js library not loaded yet. Retrying...');
                    setTimeout(initGrowthChart, 500);
                    return;
                }

                var ctx = canvas.getContext('2d');
                var labels = window.growthChartLabels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                var data = window.growthChartData || [0, 0, 0, 0, 0, 0];

                // Ensure data array has content to prevent empty chart
                if (data.length === 0) data = [0, 0, 0, 0, 0, 0];

                var chartStyle = getComputedStyle(document.documentElement);
                var primaryColor = chartStyle.getPropertyValue('--main-color-one').trim() || '#0C4D54';
                var primaryRgb   = chartStyle.getPropertyValue('--main-color-one-rgb').trim() || '12, 77, 84';
                var gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(' + primaryRgb + ', 0.2)');
                gradient.addColorStop(1, 'rgba(' + primaryRgb + ', 0.0)');

                // Destroy existing chart if it exists (for re-initialization)
                if (window.myGrowthChart instanceof Chart) {
                    window.myGrowthChart.destroy();
                }

                window.myGrowthChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Stores',
                            data: data,
                            borderColor: primaryColor,
                            backgroundColor: gradient,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 2,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: primaryColor,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                padding: 12,
                                borderColor: primaryColor,
                                borderWidth: 1,
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                displayColors: false,
                                callbacks: {
                                    label: function (context) {
                                        return 'Stores: ' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: { color: '#6b7280', font: { size: 12 } }
                            },
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: '#f3f4f6', drawTicks: false },
                                ticks: {
                                    color: '#6b7280',
                                    font: { size: 12 },
                                    padding: 10,
                                    precision: 0,
                                    callback: function (value) { return value.toLocaleString(); }
                                }
                            }
                        }
                    }
                });
            }

            // Execute when DOM is ready or immediately if it already is
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(initGrowthChart, 300);
                });
            } else {
                setTimeout(initGrowthChart, 300);
            }
        })();
    </script>
    {{-- Legacy modal JS removed — purchase flow handled by purchase-modal.blade.php --}}
    <script>
        // Dashboard sidebar toggle only
        (function () {

            // Legacy modal JS removed — purchase flow handled by #purchaseFlowModal

            // ---- placeholder to maintain structure ----
            // Sidebar toggle
            $('.close-bars, .body-overlay').on('click', function () {
                $('.dashboard-close, .dashboard-close-main, .body-overlay').removeClass('active');
            });
            $('.sidebar-icon').on('click', function () {
                $('.dashboard-close, .dashboard-close-main, .body-overlay').addClass('active');
            });

        })(); // end IIFE
    </script>
    <script>
        // Action popover: store delete URL alongside visit/admin URLs
        document.querySelectorAll('.action-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var popover = document.getElementById('action-popover');
                var visitLink = document.getElementById('popover-visit-link');
                var adminLink = document.getElementById('popover-admin-link');
                var deleteBtn = document.getElementById('popover-delete-btn');

                visitLink.href = btn.dataset.visitUrl;
                adminLink.href = btn.dataset.adminUrl;
                deleteBtn.dataset.deleteUrl  = btn.dataset.deleteUrl;
                deleteBtn.dataset.tenantId   = btn.dataset.tenantId;

                var rect = btn.getBoundingClientRect();
                popover.style.top  = (window.scrollY + rect.bottom + 8) + 'px';
                popover.style.left = (window.scrollX + rect.right - 180) + 'px';
                popover.classList.toggle('hidden');
            });
        });

        document.addEventListener('click', function () {
            document.getElementById('action-popover').classList.add('hidden');
        });

        // Delete shop flow
        document.getElementById('popover-delete-btn').addEventListener('click', function () {
            document.getElementById('action-popover').classList.add('hidden');

            var tenantId  = this.dataset.tenantId;
            var deleteUrl = this.dataset.deleteUrl;

            document.getElementById('delete-shop-name-hint').textContent = tenantId;
            document.getElementById('delete-confirm-input').value = '';
            document.getElementById('delete-modal-confirm').disabled = true;
            document.getElementById('delete-shop-form').action = deleteUrl;

            document.getElementById('delete-shop-modal').classList.remove('hidden');
        });

        document.getElementById('delete-confirm-input').addEventListener('input', function () {
            var expected = document.getElementById('delete-shop-name-hint').textContent.trim();
            document.getElementById('delete-modal-confirm').disabled = this.value.trim() !== expected;
        });

        function closeDeleteModal() {
            document.getElementById('delete-shop-modal').classList.add('hidden');
        }

        document.getElementById('delete-modal-cancel').addEventListener('click', closeDeleteModal);
        document.getElementById('delete-modal-backdrop').addEventListener('click', closeDeleteModal);
    </script>
@endsection
