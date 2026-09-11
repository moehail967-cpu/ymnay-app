@php use Illuminate\Support\Facades\DB; @endphp
@extends('landlord.frontend.frontend-page-master')
@section('title')
    {{__('Payment Success For:')}} {{$payment_details->package_name}}
@endsection
@section('page-title')
    {{$payment_details->package_name}}
@endsection

@section('content')
    @php
        $site_domain = DB::table('domains')->where('tenant_id', $payment_details->tenant_id)->first();
    @endphp

    <section class="my-36">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Status Alert --}}
            @if($payment_details->payment_status !== 'complete' && $payment_details->status !== 'trial')
                <div class="flex items-center justify-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-6 py-4 mb-6">
                    <i class="las la-info-circle text-xl"></i>
                    <p class="font-semibold text-sm sm:text-base">{{__('Your website is not ready yet, you will get notified by email when it is ready.')}}</p>
                </div>
            @endif

            @if($payment_details->payment_status === 'complete' || $payment_details->status === 'trial')
                <div class="flex flex-col items-center justify-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-xl px-6 py-5 mb-6 text-center">
                    <h2 class="text-xl font-bold">{{__('Your website is ready.')}}</h2>
                    <div class="flex items-center gap-2">
                        <i class="las la-info-circle text-lg"></i>
                        <p class="text-sm sm:text-base">{{__('An email has been sent to your email with credentials and instructions for you shop.')}}</p>
                    </div>
                </div>
            @endif

            {{-- Page Title --}}
            <div class="max-w-2xl mx-auto text-center mb-10">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{get_static_option('site_order_success_page_title')}}</h1>
                <p class="text-gray-500 mt-3 text-sm sm:text-base">{{get_static_option('site_order_success_page_description')}}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                {{-- Left Column: Order & Billing Details --}}
                <div class="lg:col-span-3 space-y-6">

                    {{-- Order Details Card --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4">{{__('Order Details')}}</h2>
                        <ul class="space-y-3 text-sm sm:text-base text-gray-700">
                            <li class="flex justify-between items-center py-2 border-b border-dashed border-gray-100">
                                <span class="font-semibold text-gray-600">{{__('Order ID:')}}</span>
                                <span>#{{$payment_details->id}}</span>
                            </li>
                            <li class="flex justify-between items-center py-2 border-b border-dashed border-gray-100 capitalize">
                                <span class="font-semibold text-gray-600">{{__('Payment Package:')}}</span>
                                <span>{{$payment_details->package_name}}</span>
                            </li>
                            <li class="flex justify-between items-center py-2 border-b border-dashed border-gray-100 capitalize">
                                <span class="font-semibold text-gray-600">{{__('Payment Package Type:')}}</span>
                                <span>{{ \App\Enums\PricePlanTypEnums::getText(optional($payment_details->package)->type) }}</span>
                            </li>

                            @if($payment_details->status !== 'trial')
                                <li class="flex justify-between items-center py-2 border-b border-dashed border-gray-100 capitalize">
                                    <span class="font-semibold text-gray-600">{{__('Payment Gateway:')}}</span>
                                    <span>{{ str_replace('_', ' ', $payment_details->package_gateway) }}</span>
                                </li>
                                <li class="flex justify-between items-center py-2 border-b border-dashed border-gray-100 capitalize">
                                    <span class="font-semibold text-gray-600">{{__('Payment Status:')}}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $payment_details->payment_status === 'complete' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{$payment_details->payment_status}}
                                    </span>
                                </li>
                                <li class="flex justify-between items-center py-2 border-b border-dashed border-gray-100">
                                    <span class="font-semibold text-gray-600">{{__('Transaction ID:')}}</span>
                                    <span class="font-mono text-sm">{{$payment_details->transaction_id}}</span>
                                </li>
                            @endif

                            @if(!empty($site_domain))
                                <li class="flex justify-between items-center py-2">
                                    <span class="font-semibold text-gray-600">{{__('Shop URL:')}}</span>
                                    <a href="{{tenant_url_with_protocol($site_domain->domain)}}"
                                       target="_blank"
                                       class="text-[#0E7490] hover:underline font-medium">
                                        {{$site_domain->domain}}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>

                    {{-- Billing Details Card --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4">{{__('Billing Details')}}</h2>
                        <ul class="space-y-3 text-sm sm:text-base text-gray-700">
                            <li class="flex justify-between items-center py-2 border-b border-dashed border-gray-100">
                                <span class="font-semibold text-gray-600">{{__('Name')}}</span>
                                <span>{{$payment_details->name}}</span>
                            </li>
                            <li class="flex justify-between items-center py-2">
                                <span class="font-semibold text-gray-600">{{__('Email')}}</span>
                                <span>{{$payment_details->email}}</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-3">
                        <a href="{{route('landlord.homepage')}}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-primary  text-white font-semibold rounded-xl hover:bg-[#0c6379] transition-colors text-sm">
                            <i class="las la-arrow-left"></i>
                            {{__('Back To Home')}}
                        </a>

                        @if(!empty($site_domain))
                            <a href="{{tenant_url_with_protocol($site_domain->domain)}}"
                               target="_blank"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors text-sm">
                                {{__('Open Shop')}}
                                <i class="las la-store-alt text-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Right Column: Package Summary --}}
                <div class="lg:col-span-2">
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden sticky top-28">
                        {{-- Package Header --}}
                        <div class="bg-primary from-[#0E7490] to-[#155E75] text-white p-6 text-center">
                            <h3 class="text-xl font-bold">{{ $payment_details->package_name }}</h3>
                            <div class="mt-3">
                                <span class="text-3xl font-extrabold">{{amount_with_currency_symbol($payment_details->package_price)}}</span>
                                @if($payment_details->status == 'trial')
                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20">Trial</span>
                                @endif
                            </div>
                            @if($payment_details->type)
                                <p class="text-white/70 text-sm mt-1">{{ $payment_details->type }}</p>
                            @endif
                        </div>

                        {{-- Package Features --}}
                        <div class="p-6">
                            <ul class="space-y-3">
                                @if(!empty(optional($payment_details->package)->page_permission_feature))
                                    <li class="flex items-center gap-3 text-sm text-gray-700">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                            <i class="las la-check text-xs"></i>
                                        </span>
                                        {{ __(sprintf('Page Create %s', optional($payment_details->package)->page_permission_feature > -1 ? optional($payment_details->package)->page_permission_feature : 'Unlimited' )) }}
                                    </li>
                                @endif

                                @if(!empty(optional($payment_details->package)->blog_permission_feature))
                                    <li class="flex items-center gap-3 text-sm text-gray-700">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                            <i class="las la-check text-xs"></i>
                                        </span>
                                        {{ __(sprintf('Blog Create %s', optional($payment_details->package)->blog_permission_feature > -1 ? optional($payment_details->package)->blog_permission_feature : 'Unlimited' )) }}
                                    </li>
                                @endif

                                @if(!empty(optional($payment_details->package)->service_permission_feature))
                                    <li class="flex items-center gap-3 text-sm text-gray-700">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                            <i class="las la-check text-xs"></i>
                                        </span>
                                        {{ __(sprintf('Service Create %s', optional($payment_details->package)->service_permission_feature ?? 'Unlimited' )) }}
                                    </li>
                                @endif

                                @if(!empty(optional($payment_details->package)->product_permission_feature))
                                    <li class="flex items-center gap-3 text-sm text-gray-700">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                            <i class="las la-check text-xs"></i>
                                        </span>
                                        {{ __(sprintf('Product Create %s', optional($payment_details->package)->product_permission_feature > -1 ? optional($payment_details->package)->product_permission_feature : 'Unlimited' )) }}
                                    </li>
                                @endif

                                @if(!empty(optional($payment_details->package)->storage_permission_feature))
                                    <li class="flex items-center gap-3 text-sm text-gray-700">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                            <i class="las la-check text-xs"></i>
                                        </span>
                                        {{ __(sprintf('Storage Amount %s', optional($payment_details->package)->storage_permission_feature > -1 ? optional($payment_details->package)->storage_permission_feature . ' MB' : 'Unlimited' )) }}
                                    </li>
                                @endif

                                @foreach(optional($payment_details->package)->plan_features as $key=> $item)
                                    @continue(in_array($item->feature_name, ['products', 'blog', 'pages', 'storage']))
                                    <li class="flex items-center gap-3 text-sm text-gray-700">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                            <i class="las la-check text-xs"></i>
                                        </span>
                                        {{str_replace('_', ' ', ucfirst($item->feature_name)) ?? ''}}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
