@extends('tenant.admin.admin-master')

@section('title')
    {{ __('Shipping Plugin') }}
@endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

{{-- Order Tracking --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-map-marker-path text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Order Tracking')}}</h3>
            <p class="text-xs text-muted">{{__('Track your order using the waybill/housebill/tracking number')}}</p>
        </div>
    </div>

    <div class="p-4 sm:p-6">
        <form action="{{route('tenant.admin.shipping.plugin.track')}}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
                        {{__('Waybill or Tracking Number')}}
                    </label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-barcode-scan text-lg text-primary"></i>
                        <input type="text"
                               id="tracking_number"
                               name="tracking_number"
                               placeholder="{{__('e.g. 6458412354')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" id="track_btn"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition disabled:opacity-60 disabled:cursor-not-allowed">
                        <i id="track_btn_icon" class="mdi mdi-magnify text-base"></i>
                        <span id="track_btn_text">{{__('Track')}}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(session('tracking_data'))
        @php $tracking_data = session('tracking_data'); @endphp
        <div class="border-t border-main px-4 sm:px-6 py-4">
            @include("shippingplugin::backend.track-table.{$tracking_data['gateway']}", $tracking_data)
        </div>
    @endif
</div>

{{-- Order Creation Statuses --}}
@if($has_orders)
<div class="bg-surface rounded-xl shadow-main border border-main">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-clipboard-list-outline text-info text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Order Creation Statuses')}}</h3>
            <p class="text-xs text-muted">{{__('All orders created through API from this platform')}}</p>
        </div>
    </div>

    <div class="tw-table-wrap">
        <table class="w-full text-left" id="shippingOrderTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Order ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Gateway')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Message')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date & Time')}}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders ?? [] as $order)
                    @php
                        $info = [];
                        $message = $order->message;
                        if ($order->status === 'success') {
                            $message = json_decode($message);
                            if ($message) {
                                $info = [
                                    'message'          => __('Order creation successful'),
                                    'shipping_order_id' => $message->order_id,
                                    'channel_order_id' => $message->channel_order_id,
                                    'shipment_id'      => $message->shipment_id,
                                ];
                            }
                        } else {
                            $array_text = str_split($message);
                            for ($i = 0; $i < count($array_text); $i++) {
                                if ($array_text[$i] != '{') { $array_text[$i] = null; } else { break; }
                            }
                            for ($i = count($array_text) - 1; $i >= 0; $i--) {
                                if ($array_text[$i] != '}') { $array_text[$i] = null; } else { break; }
                            }
                            $final_text = implode("", $array_text);
                            $message    = json_decode($final_text);
                            if ($message) {
                                $err_msg = [];
                                foreach ($message->errors ?? [] as $err) { $err_msg[] = $err; }
                                $info = [
                                    'message' => __('Order creation failed'),
                                    'info'    => __($message->message),
                                    'errors'  => current($err_msg),
                                ];
                            }
                        }
                    @endphp
                    <tr class="border-b border-main hover:bg-muted transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="text-[11px] font-bold text-primary">#{{$order->id}}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <a class="text-sm font-semibold text-primary hover:underline"
                               href="{{route('tenant.admin.product.order.manage.view', $order->order_id)}}"
                               target="_blank">
                                #{{$order->order_id}}
                            </a>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-semibold text-dark capitalize">{{$order->gateway}}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="tw-pill {{ $order->status === 'success' ? 'tw-pill-success' : 'tw-pill-danger' }} capitalize">
                                {{$order->status}}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 max-w-xs">
                            @foreach($info ?? [] as $index => $item)
                                @if(is_array($item))
                                    <div class="flex gap-1 items-start">
                                        <p class="text-xs font-semibold text-danger capitalize whitespace-nowrap">{{str_replace('_', ' ', $index)}}:</p>
                                        <ol class="text-xs text-muted list-decimal list-inside">
                                            @foreach($item ?? [] as $value)
                                                <li>{{$value}}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                @else
                                    <p class="text-xs text-dark capitalize">
                                        <span class="font-semibold">{{str_replace('_', ' ', $index)}}:</span> {{$item}}
                                    </p>
                                @endif
                            @endforeach
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="text-sm text-dark">{{$order->created_at?->format('d-m-Y H:i A')}}</p>
                            <p class="text-[11px] text-muted mt-0.5">{{$order->created_at?->diffForHumans(parts: 2)}}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-muted">
                            <div class="flex flex-col items-center gap-2">
                                <i class="mdi mdi-inbox-outline text-3xl text-subtle"></i>
                                {{__('No Data Available')}}
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-main flex justify-end">
            {!! $orders->links() !!}
        </div>
    @endif
</div>
@endif

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <script>
        (function ($) {
            "use strict";

            $('#track_btn').closest('form').on('submit', function () {
                var btn = $('#track_btn');
                btn.prop('disabled', true);
                $('#track_btn_icon').removeClass('mdi-magnify').addClass('mdi-loading mdi-spin');
                $('#track_btn_text').text('{{ __("Tracking...") }}');
            });

            $(document).ready(function () {
                if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#shippingOrderTable')) {
                    $('#shippingOrderTable').DataTable({
                        "order": [[0, "desc"]],
                        "pageLength": 10,
                        'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                    });
                }
            });
        })(jQuery);
    </script>
@endsection
