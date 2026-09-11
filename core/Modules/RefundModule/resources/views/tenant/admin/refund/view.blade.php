@extends(route_prefix().'admin.admin-master')

@section('title') {{__('Refund Product Details')}} @endsection

@section('style')
@endsection

@section('content')

<div class="space-y-5">

    {{-- Back Link --}}
    <a href="{{route(route_prefix().'admin.refund.all')}}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:opacity-80 transition">
        <i class="mdi mdi-arrow-left text-base"></i> {{__('All Refunds')}}
    </a>

    {{-- Order Header Card --}}
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

        <div class="refund-order-header">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-package-variant text-primary text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-dark font-urbanist">{{__('Order')}} #{{$order_product->id}}</h3>
                    <p class="text-xs text-muted">{{$order_product->created_at?->format('M d, Y')}} · {{$order_product->created_at?->format('h:i A')}}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-lg font-bold text-dark">
                {{amount_with_currency_symbol($order_product->price)}}
            </div>
        </div>

        {{-- Status Chips --}}
        <div class="px-5 py-3 border-b border-main flex flex-wrap gap-2">
            @php
                $refund_status = \Modules\RefundModule\Entities\RefundProduct::where(['status' => 1, 'order_id' => $order_product->id, 'user_id' => $order_product->user_id])->exists();
            @endphp

            <span class="refund-info-chip">
                <i class="mdi mdi-truck-delivery-outline"></i>
                {{__('Order Status')}}: <strong>{{$order_details->status}}</strong>
            </span>
            <span class="refund-info-chip">
                <i class="mdi mdi-cash-check"></i>
                {{__('Payment')}}: <strong>{{$refund_status ? __('Refunded') : __($order_details->payment_status)}}</strong>
            </span>
            @if($order_details->transaction_id)
                <span class="refund-info-chip">
                    <i class="mdi mdi-identifier"></i>
                    {{__('Txn')}}: <strong>{{$order_details->transaction_id}}</strong>
                </span>
            @endif
        </div>

        {{-- Details Grid --}}
        <div class="p-5 space-y-6">

            {{-- Billing Info --}}
            <div>
                <h4 class="text-[11px] font-bold text-muted uppercase tracking-widest mb-3">{{__('Billing Information')}}</h4>
                <div class="refund-billing-grid">
                    <div class="refund-billing-item">
                        <span class="refund-billing-label">{{__('Country')}}</span>
                        <span class="refund-billing-value">{{$order_details->getCountry?->name ?? '—'}}</span>
                    </div>
                    <div class="refund-billing-item">
                        <span class="refund-billing-label">{{__('State')}}</span>
                        <span class="refund-billing-value">{{$order_details->getState?->name ?? '—'}}</span>
                    </div>
                    <div class="refund-billing-item">
                        <span class="refund-billing-label">{{__('City')}}</span>
                        <span class="refund-billing-value">{{$order_details->city ?? '—'}}</span>
                    </div>
                    <div class="refund-billing-item">
                        <span class="refund-billing-label">{{__('Address')}}</span>
                        <span class="refund-billing-value">{{$order_details->address ?? '—'}}</span>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div>
                <h4 class="text-[11px] font-bold text-muted uppercase tracking-widest mb-3">{{__('Order Summary')}}</h4>
                <div class="bg-secondary rounded-xl border border-main overflow-hidden">
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-4 py-2.5 border-b border-main">
                        <span class="text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Product')}}</span>
                        <span class="text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Subtotal')}}</span>
                    </div>

                    {{-- Product Row --}}
                    <div class="flex items-center justify-between px-4 py-3 border-b border-main bg-surface">
                        <div class="flex items-center gap-3">
                            @if($product?->image_id)
                                <div class="w-10 h-10 rounded-lg overflow-hidden border border-main flex-shrink-0">
                                    {!! render_image_markup_by_attachment_id($product->image_id, 'w-full h-full object-cover') !!}
                                </div>
                            @endif
                            <div>
                                <span class="text-sm font-semibold text-dark block">{{$product?->name}}</span>
                                <span class="text-xs text-muted">× {{$order_product->quantity}}</span>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-dark">
                            {{amount_with_currency_symbol(($order_product->price * $order_product->quantity) ?? 0)}}
                        </span>
                    </div>

                    {{-- Total --}}
                    <div class="flex items-center justify-between px-4 py-3 border-b border-main">
                        <span class="text-sm font-bold text-dark">{{__('Total')}}</span>
                        <span class="text-sm font-bold text-primary">
                            {{amount_with_currency_symbol($order_product->price * $order_product->quantity)}}
                        </span>
                    </div>

                    {{-- Payment Method --}}
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs font-semibold text-muted">{{__('Payment Method')}}</span>
                        <span class="text-sm font-semibold text-dark">
                            {{$order_details->payment_gateway ?? __('Cash on delivery')}}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
