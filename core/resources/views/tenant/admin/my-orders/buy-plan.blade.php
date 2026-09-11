@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Buy / Upgrade Plan')}} @endsection

@section('content')

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-dark font-urbanist">{{__('Buy / Upgrade Plan')}}</h1>
            <p class="text-sm text-muted mt-0.5">{{__('Choose a plan that fits your needs. You will be taken to the secure checkout page.')}}</p>
        </div>
        <a href="{{route('tenant.my.package.order.payment.logs')}}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-subtle bg-surface border border-main rounded-xl hover:bg-muted transition-colors">
            <i class="mdi mdi-receipt-text-clock text-base"></i>
            {{__('Payment History')}}
        </a>
    </div>

    {{-- Current Plan Banner --}}
    @if($current_log)
        @php
            $isLifetime = !(tenant() && tenant()->expire_date);
        @endphp
        <div class="featured-card mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-shield-check text-2xl text-white/80"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-white/50 mb-0.5">{{__('Current Active Plan')}}</p>
                        <h2 class="text-lg font-extrabold text-white leading-tight">{{$current_log->package_name}}</h2>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                    <div>
                        <p class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">{{__('Price')}}</p>
                        <p class="text-base font-bold text-white mt-0.5">{{amount_with_currency_symbol($current_log->package_price)}}</p>
                    </div>
                    <div class="w-px h-8 bg-white/15 hidden sm:block"></div>
                    <div>
                        <p class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">{{__('Expires')}}</p>
                        <p class="text-base font-bold mt-0.5 {{ $isLifetime ? 'text-emerald-300' : 'text-white' }}">
                            {{ $isLifetime ? __('Lifetime') : optional(tenant()->expire_date)->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Plans Grid --}}
    @if($plans->isEmpty())
        <div class="dash-card text-center py-16">
            <i class="mdi mdi-package-variant-closed text-5xl text-muted mb-3 block"></i>
            <p class="text-sm font-semibold text-subtle">{{__('No plans are available at the moment.')}}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                @php
                    $isCurrent = ($plan->id == $current_package_id);
                    $typeBadge = match((int) $plan->type) {
                        0 => ['label' => __('Monthly'), 'class' => 'bg-blue-50 text-blue-600 border-blue-200'],
                        1 => ['label' => __('Yearly'),  'class' => 'bg-violet-50 text-violet-600 border-violet-200'],
                        2 => ['label' => __('Lifetime'),'class' => 'bg-emerald-50 text-emerald-600 border-emerald-200'],
                        default => ['label' => __('Plan'), 'class' => 'bg-gray-50 text-gray-600 border-gray-200'],
                    };
                @endphp

                <div class="bg-surface rounded-2xl border-2 {{ $isCurrent ? 'border-primary shadow-lg' : 'border-main' }} overflow-hidden flex flex-col transition-shadow hover:shadow-md relative">

                    {{-- Current badge --}}
                    @if($isCurrent)
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-primary text-white text-[10px] font-bold uppercase tracking-wider">
                                <i class="mdi mdi-check-circle text-xs"></i>
                                {{__('Current')}}
                            </span>
                        </div>
                    @endif

                    {{-- Card header --}}
                    <div class="px-6 pt-6 pb-4 border-b border-main">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h3 class="text-base font-bold text-dark font-urbanist leading-tight pr-16">{{$plan->title}}</h3>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{$typeBadge['class']}}">
                            {{$typeBadge['label']}}
                        </span>

                        {{-- Price --}}
                        <div class="mt-4 flex items-baseline gap-1">
                            @if($plan->price == 0)
                                <span class="text-3xl font-extrabold text-dark font-urbanist">{{__('Free')}}</span>
                            @else
                                <span class="text-3xl font-extrabold text-dark font-urbanist">{{amount_with_currency_symbol($plan->price)}}</span>
                            @endif
                        </div>

                        {{-- Limits --}}
                        <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1">
                            @if(!empty($plan->page_permission_feature))
                                <span class="text-xs text-muted flex items-center gap-1">
                                    <i class="mdi mdi-file-document-outline text-subtle text-sm"></i>
                                    {{ $plan->page_permission_feature == -1 ? __('Unlimited') : $plan->page_permission_feature }} {{__('Pages')}}
                                </span>
                            @endif
                            @if(!empty($plan->product_permission_feature))
                                <span class="text-xs text-muted flex items-center gap-1">
                                    <i class="mdi mdi-cube-outline text-subtle text-sm"></i>
                                    {{ $plan->product_permission_feature == -1 ? __('Unlimited') : $plan->product_permission_feature }} {{__('Products')}}
                                </span>
                            @endif
                            @if(!empty($plan->blog_permission_feature))
                                <span class="text-xs text-muted flex items-center gap-1">
                                    <i class="mdi mdi-post-outline text-subtle text-sm"></i>
                                    {{ $plan->blog_permission_feature == -1 ? __('Unlimited') : $plan->blog_permission_feature }} {{__('Blogs')}}
                                </span>
                            @endif
                            @if(!empty($plan->storage_permission_feature))
                                <span class="text-xs text-muted flex items-center gap-1">
                                    <i class="mdi mdi-folder-outline text-subtle text-sm"></i>
                                    {{ $plan->storage_permission_feature == -1 ? __('Unlimited') : $plan->storage_permission_feature }} MB {{__('Storage')}}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Features list --}}
                    <div class="px-6 py-4 flex-1">
                        @if($plan->plan_features->isNotEmpty())
                            <p class="text-[10px] font-bold uppercase tracking-widest text-muted mb-3">{{__('Features')}}</p>
                            <ul class="space-y-2">
                                @foreach($plan->plan_features as $feature)
                                    <li class="flex items-center gap-2 text-sm text-dark">
                                        <i class="mdi mdi-check-circle text-success text-base flex-shrink-0"></i>
                                        <span class="capitalize">{{str_replace('_', ' ', $feature->feature_name)}}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-muted italic">{{__('No additional features listed.')}}</p>
                        @endif
                    </div>

                    {{-- CTA --}}
                    <div class="px-6 pb-6">
                        @if($isCurrent)
                            <div class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary-soft text-primary text-sm font-semibold rounded-xl border border-primary/20">
                                <i class="mdi mdi-check-decagram text-base"></i>
                                {{__('Active Plan')}}
                            </div>
                        @else
                            <a href="{{route('tenant.my.package.checkout', $plan->id)}}"
                               class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-primary-hover text-white text-sm font-semibold rounded-xl transition-colors">
                                <i class="mdi mdi-cart-outline text-base"></i>
                                @if($current_package_id && $plan->price > optional($current_log)->package_price)
                                    {{__('Upgrade to This Plan')}}
                                @elseif($current_package_id)
                                    {{__('Switch to This Plan')}}
                                @else
                                    {{__('Buy Now')}}
                                @endif
                            </a>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Info note --}}
        <div class="mt-6 flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-5 py-4">
            <i class="mdi mdi-information-outline text-blue-500 text-xl flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-blue-700">
                {{__('Clicking "Buy Now" or "Upgrade" will take you to the checkout page where you can select a payment method. Your existing plan remains active until the new one is confirmed.')}}
            </p>
        </div>
    @endif

@endsection
