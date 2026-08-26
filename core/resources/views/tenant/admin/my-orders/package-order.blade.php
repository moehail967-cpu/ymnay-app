@extends(route_prefix() . 'admin.admin-master')
@section('title') {{__('My Payment Logs')}} @endsection

@section('style')
    <x-datatable.tw-css />
    <style>
        .hover\:text-white:hover {
            color: #fff !important
        }
    </style>
@endsection

@section('content')

    <x-landlord-error-msg />
    <x-landlord-flash-msg />

    @php
        $package = tenant()?->payment_log?->package;
        $features = $package?->plan_features;
        $themes = $package?->plan_themes;
        $payment_gateways = $package?->plan_payment_gateways;
        $isLifetime = !(tenant() && tenant()->expire_date);
        $expireDate = $current_package->expire_date;
    @endphp

    {{-- Hero: Active Plan Banner --}}
    <div class="featured-card mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-shield-home text-2xl text-white/80"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/50 mb-1">{{__('Active Plan')}}</p>
                    <h2 class="text-xl font-extrabold text-white leading-tight tracking-tight">
                        {{$current_package->package_name}}</h2>
                </div>
            </div>
            <a href="{{route('tenant.my.package.order.buy.plan')}}"
                class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-white/15 hover:bg-white/25 text-white text-sm font-semibold rounded-xl backdrop-blur-sm transition-all border border-white/10">
                <i class="mdi mdi-cart-plus text-base"></i>
                {{__('Upgrade Plan')}}
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-x-8 gap-y-3 mt-5 pt-4 border-t border-white/10">
            <div>
                <p class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">{{__('Price')}}</p>
                <p class="text-lg font-bold text-white mt-0.5">
                    {{amount_with_currency_symbol($current_package->package_price)}}</p>
            </div>
            <div class="w-px h-10 bg-white/15 hidden sm:block"></div>
            <div>
                <p class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">{{__('Expires')}}</p>
                <p class="text-lg font-bold mt-0.5 {{ $isLifetime ? 'text-emerald-300' : 'text-white' }}">
                    {{ $isLifetime ? __('Lifetime') : $expireDate?->format('d M Y') }}
                </p>
            </div>
            @if(!$isLifetime && $expireDate)
                <div class="w-px h-10 bg-white/15 hidden sm:block"></div>
                <div>
                    <p class="text-[10px] text-white/40 uppercase tracking-wider font-semibold">{{__('Remaining')}}</p>
                    <p class="text-lg font-bold text-emerald-300 mt-0.5">{{$expireDate->diffForHumans(parts: 2)}}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Plan Inclusions --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">

        {{-- Features --}}
        <div class="dash-card !p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="card-icon !w-9 !h-9 !mb-0">
                        <i class="mdi mdi-puzzle text-base text-brand"></i>
                    </div>
                    <p class="card-label !mb-0">{{__('Features')}}</p>
                </div>
                <span
                    class="text-[10px] font-bold text-primary bg-primary-soft px-2 py-0.5 rounded-full">{{count($features ?? [])}}</span>
            </div>
            <div class="flex flex-wrap gap-1.5">
                @forelse($features ?? [] as $feature)
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-secondary border border-main text-[11px] font-medium text-dark capitalize">
                        <i class="mdi mdi-check-circle text-success text-xs"></i>
                        {{str_replace('_', ' ', $feature->feature_name)}}
                    </span>
                @empty
                    <p class="text-xs text-muted italic">{{__('No features included')}}</p>
                @endforelse
            </div>
        </div>

        {{-- Payment Gateways --}}
        <div class="dash-card !p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="card-icon !w-9 !h-9 !mb-0">
                        <i class="mdi mdi-credit-card text-base text-brand"></i>
                    </div>
                    <p class="card-label !mb-0">{{__('Gateways')}}</p>
                </div>
                <span
                    class="text-[10px] font-bold text-primary bg-primary-soft px-2 py-0.5 rounded-full">{{count($payment_gateways ?? [])}}</span>
            </div>
            <div class="flex flex-wrap gap-1.5">
                @forelse($payment_gateways ?? [] as $gateway)
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-secondary border border-main text-[11px] font-medium text-dark capitalize">
                        <i class="mdi mdi-check-circle text-success text-xs"></i>
                        {{str_replace('_', ' ', $gateway->payment_gateway_name)}}
                    </span>
                @empty
                    <p class="text-xs text-muted italic">{{__('No gateways included')}}</p>
                @endforelse
            </div>
        </div>

        {{-- Themes --}}
        <div class="dash-card !p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="card-icon !w-9 !h-9 !mb-0">
                        <i class="mdi mdi-palette text-base text-brand"></i>
                    </div>
                    <p class="card-label !mb-0">{{__('Themes')}}</p>
                </div>
                <span
                    class="text-[10px] font-bold text-primary bg-primary-soft px-2 py-0.5 rounded-full">{{count($themes ?? [])}}</span>
            </div>
            <div class="flex flex-wrap gap-1.5">
                @forelse($themes ?? [] as $theme)
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-secondary border border-main text-[11px] font-medium text-dark capitalize">
                        <i class="mdi mdi-check-circle text-success text-xs"></i>
                        {{str_replace('_', ' ', $theme->theme_slug)}}
                    </span>
                @empty
                    <p class="text-xs text-muted italic">{{__('No themes included')}}</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="bg-surface rounded-xl shadow-main overflow-hidden mb-6">

        {{-- Card Header --}}
        <div class="px-5 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-receipt-text-clock text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Payment History')}}</h3>
                    <p class="text-xs text-muted">{{__('Your subscription orders and invoices')}}</p>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="tw-table-wrap">
            <table class="w-full text-left border-collapse" id="packageOrderTable">
                <thead>
                    <tr class="border-b border-main">
                        <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}
                        </th>
                        <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Package')}}
                        </th>
                        <th
                            class="hidden md:table-cell px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">
                            {{__('Period')}}</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Order')}}
                        </th>
                        <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Payment')}}
                        </th>
                        <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">
                            {{__('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($package_orders ?? [] as $key => $data)
                        @php
                            $isPending = ($data->payment_status == 'pending' || $data->payment_status == null) && $data->status != 'cancel';
                            $isComplete = $data->payment_status == 'complete';
                        @endphp
                        <tr class="border-b border-main hover:bg-muted/40 transition-colors">

                            {{-- ID --}}
                            <td class="px-5 py-4">
                                <span class="text-xs font-bold text-primary">#{{$data->id}}</span>
                            </td>

                            {{-- Package --}}
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-dark leading-tight">{{$data->package_name}}</p>
                                <p class="text-xs font-semibold text-brand mt-1">
                                    {{amount_with_currency_symbol($data->package_price)}}</p>
                                {{-- Mobile period --}}
                                <div class="md:hidden mt-1.5 text-[11px] text-muted space-y-0.5">
                                    <p>{{date_format($data->created_at, 'd M Y')}} &rarr;
                                        {{$data->expire_date ?? __('Lifetime')}}</p>
                                </div>
                            </td>

                            {{-- Period --}}
                            <td class="hidden md:table-cell px-5 py-4">
                                <p class="text-xs text-dark font-medium">{{date_format($data->created_at, 'd M Y')}}</p>
                                @if($data->start_date)
                                    <p class="text-[11px] text-muted mt-0.5">{{__('Start:')}} {{$data->start_date}}</p>
                                @endif
                                <p class="text-[11px] text-muted mt-0.5">{{__('Expire:')}}
                                    {{$data->expire_date ?? __('Lifetime')}}</p>
                            </td>

                            {{-- Order Status --}}
                            <td class="px-5 py-4">
                                @php
                                    $orderDot = match ($data->status) {
                                        'pending' => 'bg-amber-500',
                                        'cancel' => 'bg-rose-500',
                                        'in_progress' => 'bg-blue-500',
                                        default => 'bg-emerald-500',
                                    };
                                @endphp
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full {{$orderDot}}"></span>
                                    <span
                                        class="text-xs font-semibold text-dark capitalize">{{str_replace('_', ' ', $data->status)}}</span>
                                </div>
                            </td>

                            {{-- Payment Status --}}
                            <td class="px-5 py-4">
                                @php
                                    $payDot = match ($data->payment_status) {
                                        'complete' => 'bg-emerald-500',
                                        'pending', null => 'bg-amber-500',
                                        default => 'bg-slate-400',
                                    };
                                @endphp
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full {{$payDot}}"></span>
                                    <span
                                        class="text-xs font-semibold text-dark capitalize">{{$data->payment_status ?? __('Pending')}}</span>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($isPending)
                                        <a href="{{route('landlord.frontend.order.confirm', $data->package_id)}}" target="_blank"
                                            title="{{__('Pay Now')}}"
                                            class="w-8 h-8 rounded-lg bg-success-soft border border-main flex items-center justify-center text-success hover:bg-success hover:text-white hover:border-success transition-all">
                                            <i class="mdi mdi-cash-fast text-sm"></i>
                                        </a>
                                        <form action="{{route('tenant.admin.package.order.cancel')}}" method="post" class="inline">
                                            @csrf
                                            <input type="hidden" name="package_id" value="{{$data->id}}">
                                            <button type="submit" title="{{__('Cancel Order')}}"
                                                class="w-8 h-8 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:bg-danger hover:text-white hover:border-danger transition-all">
                                                <i class="mdi mdi-close text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($isComplete)
                                        <form action="{{route(route_prefix() . 'my.package.invoice.generate')}}" method="post"
                                            class="inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$data->id}}">
                                            <button type="submit" title="{{__('Download Invoice')}}"
                                                class="w-8 h-8 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:bg-primary hover:text-white hover:border-primary transition-all">
                                                <i class="mdi mdi-file-download-outline text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{route('landlord.frontend.plan.order', $data->package_id)}}" target="_blank"
                                        title="{{__('Renew Plan')}}"
                                        class="w-8 h-8 rounded-lg bg-info-soft border border-main flex items-center justify-center text-info hover:bg-info hover:text-white hover:border-info transition-all">
                                        <i class="mdi mdi-autorenew text-sm"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
    <x-datatable.tw-js />
@endsection