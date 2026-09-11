@extends('landlord.admin.admin-master')

@section('title')
    {{__('Dashboard')}}
@endsection

@section('style')
    <link href="{{ global_asset('assets/landlord/admin/css/update-info.css') }}" rel="stylesheet">

@endsection

@section('content')
    @inject('healthHelper', 'App\Helpers\SiteHealthHelper')

    {{-- Health Warning Alert Card --}}
    @if(!tenant() && $healthHelper->getIssues() > 0)
        <div class="alert-card flex items-start gap-4 mb-6">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mt-0.5">
                <i class="mdi mdi-alert-circle text-xl text-danger"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-bold text-dark mb-1">{{__('Issue Detection Alert')}}</h4>
                <p class="text-sm text-brand leading-relaxed">
                    {{$healthHelper->issueMessage()}}
                    {{ __('If all necessary server values are not configured correctly, it may affect the system\'s functionality.') }}
                </p>
            </div>
            <a href="{{$healthHelper->getReadMore()['route']}}"
                class="flex-shrink-0 px-4 py-3 bg-danger hover:bg-red-600 text-white text-sm font-semibold rounded-full transition-colors">
                {{$healthHelper->getReadMore()['text']}}
            </a>
        </div>
    @endif

    {{-- ===== OLD BOOTSTRAP DASHBOARD CARDS — commented out =====
    <div class="col-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row g-4">
                    ... old Bootstrap card markup with bg-gradient-danger, bg-gradient-info etc ...
                </div>
            </div>
        </div>
    </div>
    ===== END OLD BOOTSTRAP DASHBOARD CARDS ===== --}}

    <!-- Row 1: 4 Equal Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-5">

        <!-- Total Admins -->
        <div class="dash-card">
            <span class="card-badge bg-emerald-50 text-success">+12%</span>
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

        <!-- Total Shops -->
        <div class="dash-card">
            <span class="card-badge bg-gray-100 text-brand">{{__('Stable')}}</span>
            <div class="card-icon mb-4">
                <i class="mdi mdi-store text-lg text-brand"></i>
            </div>
            <p class="card-label mb-1">{{__('Shops')}}</p>
            <p class="card-value">{{number_format($all_tenants)}}</p>
        </div>

        <!-- Total Testimonials -->
        <div class="dash-card">
            <span class="card-badge bg-warning-soft text-warning">4.8 <i class="mdi mdi-star text-[10px]"></i></span>
            <div class="card-icon mb-4">
                <i class="mdi mdi-message text-lg text-brand"></i>
            </div>
            <p class="card-label mb-1">{{__('Testimonials')}}</p>
            <p class="card-value">{{number_format($total_testimonial)}}</p>
        </div>
    </div>

    <!-- Row 2: 2 Small Cards + 1 Featured Large Card -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        <!-- Price Plans -->
        <div class="dash-card">
            <div class="card-icon mb-4">
                <i class="mdi mdi-diamond text-lg text-brand"></i>
            </div>
            <p class="card-label mb-1">{{__('Price Plans')}}</p>
            <p class="card-value">{{__('Active:')}} {{$total_price_plan}}</p>
        </div>

        <!-- Active Themes -->
        @php
            $themes = getAllThemeSlug();
        @endphp
        <div class="dash-card">
            <div class="card-icon mb-4">
                <i class="mdi mdi-palette text-lg text-brand"></i>
            </div>
            <p class="card-label mb-1">{{__('Active Themes')}}</p>
            <p class="card-value">{{count($themes)}}</p>
        </div>

        <!-- Featured: Total Commission -->
        <div class="featured-card xl:col-span-2">
            <span class="absolute top-4 right-5 text-xs font-semibold text-white/60">+24% YoY</span>
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cash-multiple text-2xl text-white/70"></i>
                </div>
                <div>
                    <p class="card-label mb-1">{{__('Total Commission')}}</p>
                    <p class="card-value text-3xl mt-1">{{ amount_with_currency_symbol($totalPendingCommission) }}</p>
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

    <!-- Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">
        <div class="chart-card">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-dark font-urbanist">{{__("Amount per month in")}} {{date('Y')}}</h3>
                    <p class="text-xs text-subtle mt-1">{{__("Revenue forecast and historical performance")}}</p>
                </div>
                <div class="flex items-center gap-3 text-[11px] text-subtle">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                        {{__('ACTUAL')}}</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-200"></span>
                        {{__('PROJECTED')}}</span>
                </div>
            </div>
            <canvas id="monthlyRaised" class="monthlyRaised"></canvas>
        </div>
        <div class="chart-card">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-dark font-urbanist">{{__("Daily Activity")}}</h3>
                    <p class="text-xs text-subtle mt-1">{{__("Last 30 days usage intensity")}}</p>
                </div>
                <div
                    class="px-3 py-1.5 border border-main rounded-lg text-xs text-brand font-medium hover:border-sidebar-hover transition-colors cursor-pointer">
                    {{__('Last 30 Days')}} <i class="mdi mdi-chevron-down text-[10px] ml-1"></i>
                </div>
            </div>
            <canvas id="monthlyRaisedPerDay" class="monthlyRaisedPerDay"></canvas>
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
                    <p class="text-xs text-subtle">{{__('Latest transactions and subscriptions')}}</p>
                </div>
            </div>
            <button class="p-2 rounded-lg text-subtle hover:bg-muted transition-colors">
                <i class="mdi mdi-dots-horizontal text-lg"></i>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-theme-main bg-slate-50/30 whitespace-nowrap">
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Order ID')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('User Name')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Package')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Price')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Subdomain')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Payment')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Status')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Created')}}</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-text-theme-muted uppercase tracking-wider">
                            {{__('Renewed')}}</th>
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
                                            $initials = collect(explode(' ', $data->name))->map(fn($n) => $n[0] ?? '')->take(2)->join('');
                                            $colors = ['bg-blue-50 text-blue-600', 'bg-emerald-50 text-emerald-600', 'bg-purple-50 text-purple-600', 'bg-amber-50 text-amber-600', 'bg-rose-50 text-rose-600'];
                                            $color = $colors[$data->id % count($colors)];
                                        @endphp
                                        <span class="text-[10px] font-bold {{ $color }} uppercase">{{ $initials }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-text-theme-heading leading-tight truncate">
                                            {{$data->name}}</p>
                                        <p class="text-[11px] text-text-theme-muted leading-tight truncate">{{$data->email}}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm font-medium text-text-theme-main">{{$data->package_name}}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="text-sm font-bold text-primary">{{amount_with_currency_symbol($data->package_price)}}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-0.5 rounded bg-muted text-primary text-[10px] font-mono border border-theme-main">{{$data->tenant_id}}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    @if($data->payment_status === 'complete')
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="text-xs font-semibold text-text-theme-main">{{__('Paid')}}</span>
                                    @elseif($data->payment_status === 'pending')
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        <span class="text-xs font-semibold text-text-theme-main">{{__('Pending')}}</span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        <span
                                            class="text-xs font-semibold text-text-theme-main">{{ ucfirst($data->payment_status) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'trial' => 'bg-blue-50 text-blue-600',
                                        'complete' => 'bg-muted text-primary',
                                        'expired' => 'bg-rose-50 text-rose-600',
                                        'pending' => 'bg-amber-50 text-amber-600',
                                    ];
                                    $cls = $statusClasses[$data->status] ?? 'bg-slate-50 text-slate-600';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-tight {{ $cls }}">
                                    {{$data->status}}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-text-theme-muted">{{$data->created_at?->format('M d, Y')}}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-text-theme-muted">{{$data->updated_at?->format('M d, Y')}}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @includeWhen(count($update_info) > 0, 'landlord.admin.partials.update-info', $update_info)
@endsection

@section('scripts')
    <script src="{{asset('assets/landlord/admin/js/update-info.js')}}"></script>
    <script src="{{asset('assets/common/js/chart.js')}}"></script>
    <script src="{{asset('assets/new-landlord/admin/js/chart.js')}}"></script>
    <script>
        $(document).ready(function () {
            DashboardCharts.initMonthlyRaised(
                '{{route('landlord.admin.home.chart.data.month')}}',
                '{{csrf_token()}}',
                '{{__('Amount Received')}}'
            );

            DashboardCharts.initDailyActivity(
                '{{route('landlord.admin.home.chart.data.by.day')}}',
                '{{csrf_token()}}',
                '{{__('Amount Received')}}'
            );
        });
    </script>

@endsection
