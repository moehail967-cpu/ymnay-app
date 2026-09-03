@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Order Details')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php
    $ymnayCustomFields = json_decode((string) $order->custom_fields, true);
    $ymnayWalletPayment = is_array($ymnayCustomFields) ? ($ymnayCustomFields['ymnay_manual_wallet'] ?? null) : null;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Main Content --}}
    <div class="lg:col-span-8 space-y-6">

        {{-- Order Header Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-package-variant text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Order')}} <span class="text-primary">#{{$order->id}}</span></h3>
                    <p class="text-xs text-muted">{{date_format($order->created_at, 'd M Y, h:i A')}}</p>
                </div>
                <div class="ml-auto">
                    <a href="{{route(route_prefix().'admin.package.order.manage.all')}}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                        <i class="mdi mdi-arrow-left text-base"></i> {{__('All Orders')}}
                    </a>
                </div>
            </div>

            {{-- Package & Payment Grid --}}
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Package --}}
                    <div class="bg-secondary border border-main rounded-xl px-4 py-3.5">
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{__('Package')}}</span>
                        <span class="text-base font-bold text-dark block">{{$order->package_name}}</span>
                        <span class="text-lg font-extrabold text-primary">{{amount_with_currency_symbol($order->package_price)}}</span>
                    </div>

                    {{-- Gateway --}}
                    <div class="bg-secondary border border-main rounded-xl px-4 py-3.5">
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{__('Payment Gateway')}}</span>
                        <span class="text-base font-bold text-dark block capitalize">{{str_replace('_', ' ', $order->package_gateway)}}</span>
                        <span class="text-xs text-muted">{{__('Subdomain:')}}</span>
                        <span class="text-xs font-mono font-bold text-primary">{{$order->tenant_id}}</span>
                    </div>

                </div>
            </div>
        </div>

        {{-- Customer Info Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-account-outline text-info text-sm"></i>
                </div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Customer')}}</h4>
            </div>

            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-0.5">{{__('Name')}}</span>
                        <span class="text-sm font-semibold text-dark">{{$order->name}}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-0.5">{{__('Email')}}</span>
                        <span class="text-sm font-semibold text-dark">{{$order->email}}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-0.5">{{__('Subdomain')}}</span>
                        <span class="text-sm font-mono font-semibold text-primary">{{$order->tenant_id}}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-0.5">{{__('Order Date')}}</span>
                        <span class="text-sm font-semibold text-dark">{{date_format($order->created_at, 'd M Y')}}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Custom Fields --}}
        @if(!empty($all_custom_fields))
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#f3e8ff] flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-form-textbox text-[#9333ea] text-sm"></i>
                </div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Custom Fields')}}</h4>
            </div>

            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                    @foreach($all_custom_fields ?? [] as $key => $field)
                        @continue($key === 'ymnay_manual_wallet' || is_array($field) || is_object($field))
                        <div>
                            <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-0.5">{{ ucfirst($key) }}</span>
                            <span class="text-sm text-dark">{{$field}}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($ymnayWalletPayment)
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-3.5 border-b border-main"><h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Manual Wallet Payment')}}</h4></div>
            <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><span class="block text-[10px] font-bold text-muted uppercase">{{__('Wallet')}}</span><span class="text-sm font-semibold text-dark">{{$ymnayWalletPayment['wallet']['name'] ?? '—'}}</span></div>
                <div><span class="block text-[10px] font-bold text-muted uppercase">{{__('Review status')}}</span><span class="text-sm font-semibold text-dark">{{__($ymnayWalletPayment['review_status'] ?? 'pending')}}</span></div>
                <div class="sm:col-span-2"><span class="block text-[10px] font-bold text-muted uppercase">{{__('Instructions snapshot')}}</span><p class="text-sm text-dark whitespace-pre-line">{{$ymnayWalletPayment['wallet']['description'] ?? ''}}</p></div>
            </div>
        </div>
        @endif

        {{-- Transaction & Attachment --}}
        @if($order->status != 'trial')
            @php $attachments = 'assets/landlord/uploads/payment_attachments/'.$order->attachments; @endphp

            @if($order->transaction_id || (!is_dir($attachments) && file_exists($attachments)))
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-3.5 border-b border-main flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-receipt-text-outline text-success text-sm"></i>
                    </div>
                    <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Transaction')}}</h4>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="flex flex-wrap gap-6">
                        @if($order->transaction_id)
                        <div>
                            <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{__('Transaction ID')}}</span>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-secondary border border-main text-sm font-mono font-bold text-dark">
                                {{$order->transaction_id}}
                            </span>
                        </div>
                        @endif

                        @if(!is_dir($attachments) && file_exists($attachments))
                        <div>
                            <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{__('Attachment')}}</span>
                            <a href="{{global_asset($attachments)}}" target="_blank" class="inline-block group">
                                <img class="w-28 h-20 object-cover rounded-xl border-2 border-main group-hover:border-primary transition shadow-sm"
                                     src="{{global_asset($attachments)}}" alt="{{__('Payment Attachment')}}">
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-4 space-y-6">

        {{-- Payment Status --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
            <div class="px-4 py-3.5 border-b border-main">
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Status')}}</h4>
            </div>

            <div class="p-4 space-y-3">
                {{-- Payment --}}
                @if($order->payment_status == 'complete')
                    <div class="flex items-center gap-3 bg-success-soft border border-green-200 rounded-xl px-4 py-3">
                        <div class="w-9 h-9 rounded-full bg-success flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-check text-white text-base"></i>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-success uppercase">{{__('Paid')}}</span>
                            <span class="text-[11px] text-muted">{{__('Payment received')}}</span>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 bg-warning-soft border border-yellow-200 rounded-xl px-4 py-3">
                        <div class="w-9 h-9 rounded-full bg-warning flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-clock-outline text-white text-base"></i>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-warning uppercase">{{$order->payment_status ? __($order->payment_status) : __('Pending')}}</span>
                            <span class="text-[11px] text-muted">{{__('Awaiting payment')}}</span>
                        </div>
                    </div>
                @endif

                {{-- Order Status --}}
                <div class="flex items-center gap-3 bg-secondary border border-main rounded-xl px-4 py-3">
                    <div class="w-9 h-9 rounded-full bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-list-status text-primary text-base"></i>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-dark uppercase">{{__(ucfirst(str_replace('_', ' ', $order->status)))}}</span>
                        <span class="text-[11px] text-muted">{{__('Order status')}}</span>
                    </div>
                </div>
            </div>

            @if($ymnayWalletPayment && $order->payment_status !== 'complete' && $order->status !== 'cancel')
            <div class="px-4 pb-4 space-y-3 border-t border-main pt-4">
                <form method="post" action="{{route('ymnaycustom.landlord.wallets.orders.approve',$order)}}">
                    @csrf
                    <button class="w-full px-4 py-2.5 rounded-xl bg-success text-white font-semibold" onclick="return confirm('{{__('Confirm that the payment was received and activate the package?')}}')">{{__('Approve Payment and Activate Package')}}</button>
                </form>
                <form method="post" action="{{route('ymnaycustom.landlord.wallets.orders.reject',$order)}}" class="space-y-2">
                    @csrf
                    <textarea name="rejection_reason" required maxlength="1000" class="lnd-input" rows="3" placeholder="{{__('Rejection reason shown to the customer')}}"></textarea>
                    <button class="w-full px-4 py-2.5 rounded-xl bg-danger text-white font-semibold" onclick="return confirm('{{__('Reject this payment?')}}')">{{__('Reject Payment')}}</button>
                </form>
            </div>
            @endif

            {{-- Quick Info --}}
            <div class="px-4 pb-4 space-y-2">
                <div class="flex items-center justify-between py-2 border-t border-main">
                    <span class="text-[11px] text-muted font-medium">{{__('Order ID')}}</span>
                    <span class="text-xs font-bold text-primary">#{{$order->id}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-main">
                    <span class="text-[11px] text-muted font-medium">{{__('Date')}}</span>
                    <span class="text-xs font-semibold text-dark">{{date_format($order->created_at, 'd M Y')}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-main">
                    <span class="text-[11px] text-muted font-medium">{{__('Amount')}}</span>
                    <span class="text-xs font-bold text-dark">{{amount_with_currency_symbol($order->package_price)}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-main">
                    <span class="text-[11px] text-muted font-medium">{{__('Gateway')}}</span>
                    <span class="text-xs font-semibold text-dark capitalize">{{str_replace('_', ' ', $order->package_gateway)}}</span>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
