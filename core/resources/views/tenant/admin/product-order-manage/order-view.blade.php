@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Order Details')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php $order_meta = json_decode($order->payment_meta); @endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Main Content --}}
    <div class="lg:col-span-8 space-y-6">

        {{-- Order Header Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cart-outline text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Order')}} <span class="text-primary">#{{$order->id}}</span></h3>
                    <p class="text-xs text-muted">{{$order->created_at?->format('d M Y, h:i A')}}</p>
                </div>
                <div class="ml-auto">
                    <a href="{{route(route_prefix().'admin.product.order.manage.all')}}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                        <i class="mdi mdi-arrow-left text-base"></i> {{__('All Orders')}}
                    </a>
                </div>
            </div>

            {{-- Amount & Payment Grid --}}
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-secondary border border-main rounded-xl px-4 py-3.5">
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{__('Total Amount')}}</span>
                        <span class="text-lg font-extrabold text-primary">{{amount_with_currency_symbol($order->total_amount)}}</span>
                    </div>
                    <div class="bg-secondary border border-main rounded-xl px-4 py-3.5">
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{__('Payment Gateway')}}</span>
                        <span class="text-base font-bold text-dark block capitalize">
                            {{$order->checkout_type !== 'cod' ? __($order->payment_gateway) : __('Cash On Delivery')}}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Billing Info Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-account-outline text-info text-sm"></i>
                </div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Billing Information')}}</h4>
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
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-0.5">{{__('Phone')}}</span>
                        <span class="text-sm font-semibold text-dark">{{$order->phone}}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-0.5">{{__('Country')}}</span>
                        <span class="text-sm font-semibold text-dark">{{$order->getCountry?->name}}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-0.5">{{__('State')}}</span>
                        <span class="text-sm font-semibold text-dark">{{$order->state ?? '—'}}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-0.5">{{__('City')}}</span>
                        <span class="text-sm font-semibold text-dark">{{$order->city ?? '—'}}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-0.5">{{__('Address')}}</span>
                        <span class="text-sm font-semibold text-dark">{{$order->address}}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($order->message)
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-message-text-outline text-warning text-sm"></i>
                </div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Order Note')}}</h4>
            </div>
            <div class="p-4 sm:p-6">
                <p class="text-sm text-dark">{{$order->message}}</p>
            </div>
        </div>
        @endif

        {{-- Order Items --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#f3e8ff] flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-package-variant text-[#9333ea] text-sm"></i>
                </div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Order Items')}}</h4>
            </div>
            <div class="divide-y divide-main">
                @foreach(json_decode($order->order_details) ?? [] as $product)
                <div class="px-4 sm:px-6 py-4 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl border border-main overflow-hidden flex-shrink-0 bg-secondary">
                        {!! render_image_markup_by_attachment_id($product->options?->image, 'w-full h-full object-cover') !!}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-dark truncate">{{ $product?->name }}</p>
                        <div class="flex flex-wrap gap-x-3 gap-y-0.5 mt-1 text-[11px] text-muted">
                            @if(!empty($product->options?->color_name))
                                <span>{{__('Color')}}: <strong class="text-dark">{{$product->options?->color_name}}</strong></span>
                            @endif
                            @if(!empty($product->options?->size_name))
                                <span>{{__('Size')}}: <strong class="text-dark">{{$product->options?->size_name}}</strong></span>
                            @endif
                            @foreach($product->options?->attributes ?? [] as $key => $value)
                                <span>{{$key}}: <strong class="text-dark">{{$value}}</strong></span>
                            @endforeach
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-xs text-muted">&times; {{ $product->qty }}</span>
                        <p class="text-sm font-bold text-dark">{{ amount_with_currency_symbol(($product->price * $product->qty) ?? 0) }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Order Summary --}}
            <div class="border-t border-main px-4 sm:px-6 py-4 space-y-2">
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-xs text-muted font-medium">{{__('Subtotal')}}</span>
                    <span class="text-sm font-semibold text-dark">{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span>
                </div>
                @php
                    $coupon = [];
                    $coupon_amount = '';
                    if ($order->coupon) {
                        $coupon = \Modules\CouponManage\Entities\ProductCoupon::where('code', $order->coupon)->first();
                        $coupon_amount = $coupon->discount_type == 'percentage' ? $coupon->discount.'%' : amount_with_currency_symbol($coupon->discount);
                    }
                @endphp
                @if($coupon)
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-xs text-muted font-medium">{{__('Coupon Discount')}}</span>
                    <span class="text-sm font-semibold text-success">-{{ $coupon_amount }}</span>
                </div>
                @endif
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-xs text-muted font-medium">{{__('Tax')}}</span>
                    <span class="text-sm font-semibold text-dark">+{{ amount_with_currency_symbol($order_meta->product_tax) }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-xs text-muted font-medium">{{__('Shipping Cost')}}</span>
                    <span class="text-sm font-semibold text-dark">+{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-main">
                    <span class="text-sm font-bold text-dark">{{__('Total')}}</span>
                    <span class="text-base font-extrabold text-primary">{{ amount_with_currency_symbol($order_meta->total) }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-4 space-y-6">

        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
            <div class="px-4 py-3.5 border-b border-main">
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Status')}}</h4>
            </div>

            <div class="p-4 space-y-3">
                {{-- Payment Status --}}
                @php
                    $refund_status = \Modules\RefundModule\Entities\RefundProduct::where(['status' => 1, 'order_id' => $order->id, 'user_id' => $order->user_id])->exists();
                @endphp
                @if($refund_status)
                    <div class="flex items-center gap-3 bg-danger-soft border border-red-200 rounded-xl px-4 py-3">
                        <div class="w-9 h-9 rounded-full bg-danger flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-cash-refund text-white text-base"></i>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-danger uppercase">{{__('Refunded')}}</span>
                            <span class="text-[11px] text-muted">{{__('Payment refunded')}}</span>
                        </div>
                    </div>
                @elseif($order->payment_status === 'success')
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

            {{-- Quick Info --}}
            <div class="px-4 pb-4 space-y-2">
                <div class="flex items-center justify-between py-2 border-t border-main">
                    <span class="text-[11px] text-muted font-medium">{{__('Order ID')}}</span>
                    <span class="text-xs font-bold text-primary">#{{$order->id}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-main">
                    <span class="text-[11px] text-muted font-medium">{{__('Date')}}</span>
                    <span class="text-xs font-semibold text-dark">{{$order->created_at?->format('d M Y')}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-main">
                    <span class="text-[11px] text-muted font-medium">{{__('Amount')}}</span>
                    <span class="text-xs font-bold text-dark">{{amount_with_currency_symbol($order->total_amount)}}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-main">
                    <span class="text-[11px] text-muted font-medium">{{__('Payment Method')}}</span>
                    <span class="text-xs font-semibold text-dark capitalize">{{__($order->payment_gateway) ?? __('Cash on Delivery')}}</span>
                </div>
                @if($order->transaction_id)
                <div class="flex items-center justify-between py-2 border-t border-main">
                    <span class="text-[11px] text-muted font-medium">{{__('Transaction ID')}}</span>
                    <span class="text-xs font-mono font-bold text-dark">{{$order->transaction_id}}</span>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>

@endsection
