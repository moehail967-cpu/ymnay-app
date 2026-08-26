@extends('tenant.admin.admin-master')

@section('title')
    {{__('Dashboard')}}
@endsection

@section('content')

    {{-- Current Package Info Banner --}}
    @if(!empty($current_package))
        <div class="alert-card flex items-start gap-4 mb-6">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mt-0.5">
                <i class="mdi mdi-package-variant text-xl text-info"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-bold text-dark mb-1">
                    {{__('Current Package :')}} {{$current_package->package_name}}
                    @if(optional(tenant()->payment_log)->status == 'trial')
                        <span class="font-semibold capitalize">( {{optional(tenant()->payment_log)->status}} )</span>

                    @else
                        @if(!is_null(optional($current_package->package)->type))
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-warning-soft text-warning ml-1">
                                {{ \App\Enums\PricePlanTypEnums::getText(optional($current_package->package)->type) }}
                            </span>
                        @endif
                    @endif
                </h4>
                <p class="text-sm text-brand leading-relaxed">
                    @if(optional(tenant()->payment_log)->status == 'trial')
                        <span class="font-semibold">{{__('Expire Date :')}} {{tenant()->expire_date->format('d-m-Y')}}</span>
                    @else
                        @if(tenant()->expire_date != null)
                            <span class="font-semibold">{{__('Expire Date :')}} {{tenant()->expire_date->format('d-m-Y')}}</span>
                        @else
                            <span class="font-semibold">{{__('Expire Date :')}} {{__('Lifetime')}}</span>
                        @endif
                    @endif
                </p>
            </div>
{{--            @if(optional(tenant()->payment_log)->status == 'trial' || tenant()->expire_date != null)--}}
                <a href="{{route('tenant.my.package.order.buy.plan')}}"
                    class="flex-shrink-0 px-4 py-3 bg-primary hover:bg-primary-hover text-white text-sm font-semibold rounded-full transition-colors">
                    {{__('Upgrade Plan')}}
                </a>
{{--            @endif--}}
        </div>
    @endif

    <!-- Row 1: 4 Equal Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-5">

        <!-- Total Admins -->
        <div class="dash-card">
            <div class="card-icon mb-4">
                <i class="mdi mdi-shield-check text-lg text-brand"></i>
            </div>
            <p class="card-label mb-1">{{__('Total Admins')}}</p>
            <p class="card-value">{{$total_admin}}</p>
        </div>

        <!-- Total Users -->
        <div class="dash-card">
            @php
                $userBadge = $total_user > 1000 ? '+' . number_format($total_user / 1000, 1) . 'k' : '+' . $total_user;
            @endphp
            <span class="card-badge bg-blue-50 text-info">{{$userBadge}}</span>
            <div class="card-icon mb-4">
                <i class="mdi mdi-account text-lg text-brand"></i>
            </div>
            <p class="card-label mb-1">{{__('Total Users')}}</p>
            <p class="card-value">{{number_format($total_user)}}</p>
        </div>

        <!-- Total Products -->
        <div class="dash-card">
            <div class="card-icon mb-4">
                <i class="mdi mdi-diamond text-lg text-brand"></i>
            </div>
            <p class="card-label mb-1">{{__('Total Products')}}</p>
            <p class="card-value">{{number_format($total_products)}}</p>
        </div>

        <!-- Total Orders -->
        <div class="dash-card">
            <div class="card-icon mb-4">
                <i class="mdi mdi-cart text-lg text-brand"></i>
            </div>
            <p class="card-label mb-1">{{__('Total Orders')}}</p>
            <p class="card-value">{{number_format($total_orders)}}</p>
        </div>
    </div>

    <!-- Row 2: 2 Small Cards + 1 Featured Large Card -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        <!-- Total Blogs -->
        <div class="dash-card">
            <div class="card-icon mb-4">
                <i class="mdi mdi-note-text text-lg text-brand"></i>
            </div>
            <p class="card-label mb-1">{{__('Total Blogs')}}</p>
            <p class="card-value">{{number_format($all_blogs)}}</p>
        </div>

        <!-- Total Sale -->
        <div class="dash-card">
            <div class="card-icon mb-4">
                <i class="mdi mdi-cash text-lg text-brand"></i>
            </div>
            <p class="card-label mb-1">{{__('Total Sale')}}</p>
            <p class="card-value">{{ amount_with_currency_symbol($total_sale) }}</p>
        </div>

        <!-- Featured: Total Commission -->
        <div class="featured-card xl:col-span-2">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cash-multiple text-2xl text-white/70"></i>
                </div>
                <div>
                    <p class="card-label mb-1">{{__('Total Commission')}}</p>
                    <p class="card-value text-3xl mt-1">{{ amount_with_currency_symbol(each_shop_calculate_total_commission()) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-6 mt-5 pt-4 border-t border-white/10">
                <div>
                    <p class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">{{__('Completed')}}</p>
                    <p class="text-lg font-bold text-white mt-0.5">
                        {{ amount_with_currency_symbol($totalCompleteCommission) }}</p>
                </div>
                <div class="w-px h-10 bg-white/15"></div>
                <div>
                    <p class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">{{__('Pending')}}</p>
                    <p class="text-lg font-bold text-emerald-300 mt-0.5">
                        {{ amount_with_currency_symbol($totalPendingCommission) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Order Logs -->
    <div class="bg-surface rounded-xl overflow-hidden mb-6 shadow-main">
        <div class="px-6 py-4 border-b border-subtle flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center">
                    <i class="mdi mdi-clipboard text-success text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Recent Order Logs')}}</h3>
                    <p class="text-xs text-subtle">{{__('Latest transactions and orders')}}</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-theme-main bg-slate-50/30 whitespace-nowrap">
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Order ID')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Customer')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Total Amount')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Status')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Payment')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Gateway')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Created')}}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-theme-main">
                    @foreach($recent_order_logs as $key => $data)
                        <tr class="hover:bg-muted/40 transition-colors group">
                            <td class="px-4 py-3">
                                <span class="text-sm font-semibold whitespace-nowrap">#{{$data->id}}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-slate-50 flex items-center justify-center border border-theme-main overflow-hidden shadow-sm flex-shrink-0">
                                        @php
                                            $initials = collect(explode(' ', $data->name ?? ''))->map(fn($n) => $n[0] ?? '')->take(2)->join('');
                                            $colors = ['bg-blue-50 text-blue-600', 'bg-emerald-50 text-emerald-600', 'bg-purple-50 text-purple-600', 'bg-amber-50 text-amber-600', 'bg-rose-50 text-rose-600'];
                                            $color = $colors[$data->id % count($colors)];
                                        @endphp
                                        <span class="text-[10px] font-bold {{ $color }} uppercase">{{ $initials }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-text-theme-heading leading-tight truncate">
                                            {{$data->name ?? ''}}</p>
                                        <p class="text-[11px] text-text-theme-muted leading-tight truncate">{{$data->email}}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="text-sm font-bold text-primary">{{amount_with_currency_symbol($data->total_amount)}}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-50 text-amber-600',
                                        'complete' => 'bg-muted text-primary',
                                        'completed' => 'bg-muted text-primary',
                                        'cancel' => 'bg-rose-50 text-rose-600',
                                    ];
                                    $cls = $statusClasses[$data->status] ?? 'bg-slate-50 text-slate-600';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-tight {{ $cls }}">
                                    {{$data->status}}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    @php
                                        $payment_status_color = match ($data->payment_status){
                                            'success' => 'bg-emerald-500',
                                            'pending' => 'bg-amber-500',
                                            'failed' => 'bg-rose-500',
                                            default => 'bg-slate-400'
                                        };
                                    @endphp
                                    <span class="w-2 h-2 rounded-full {{$payment_status_color}}"></span>
                                    <span class="text-xs font-semibold text-text-theme-main">{{ ucfirst($data->payment_status) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm font-medium text-text-theme-main">{{str_replace('_',' ',$data->payment_gateway)}}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-text-theme-muted">{{$data->created_at->diffForHumans()}}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
