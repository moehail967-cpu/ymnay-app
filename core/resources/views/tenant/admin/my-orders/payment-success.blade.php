@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Payment Received')}} @endsection

@section('content')

    <div class="max-w-lg mx-auto py-12">
        <div class="bg-surface rounded-2xl border border-main overflow-hidden text-center p-10">

            {{-- Icon --}}
            <div class="w-20 h-20 rounded-full bg-success-soft flex items-center justify-center mx-auto mb-5">
                <i class="mdi mdi-check-circle text-5xl text-success"></i>
            </div>

            {{-- Heading --}}
            <h2 class="text-xl font-extrabold text-dark font-urbanist mb-2">{{__('Payment Submitted!')}}</h2>
            <p class="text-sm text-muted mb-6">
                {{__('Your payment has been submitted. Your plan will be activated once the payment is confirmed.')}}
            </p>

            @if($log)
                <div class="bg-secondary rounded-xl border border-main p-5 text-left mb-6 space-y-2.5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted font-medium">{{__('Order ID')}}</span>
                        <span class="font-bold text-dark">#{{$log->id}}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted font-medium">{{__('Plan')}}</span>
                        <span class="font-bold text-dark">{{$log->package_name}}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted font-medium">{{__('Amount')}}</span>
                        <span class="font-bold text-primary">{{amount_with_currency_symbol($log->package_price)}}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted font-medium">{{__('Status')}}</span>
                        @php
                            $dot = match($log->payment_status) {
                                'complete' => 'bg-emerald-500',
                                default    => 'bg-amber-500',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-dark capitalize">
                            <span class="w-1.5 h-1.5 rounded-full {{$dot}}"></span>
                            {{$log->payment_status ?? __('Pending')}}
                        </span>
                    </div>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{route('tenant.my.package.order.payment.logs')}}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-sm font-semibold rounded-xl transition-colors">
                    <i class="mdi mdi-receipt-text-clock text-base"></i>
                    {{__('View Payment History')}}
                </a>
                <a href="{{route('tenant.admin.dashboard')}}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-surface border border-main text-dark text-sm font-semibold rounded-xl hover:bg-muted transition-colors">
                    <i class="mdi mdi-view-dashboard-outline text-base"></i>
                    {{__('Dashboard')}}
                </a>
            </div>

        </div>

        <p class="text-xs text-center text-muted mt-4">
            {{__('If your plan is not activated within a few minutes, please contact support.')}}
        </p>
    </div>

@endsection
