@extends('tenant.admin.admin-master')

@section('title') {{ __('Cart Detail') }} @endsection
@section('page-title') {{ __('Cart Detail') }} @endsection

@section('content')
<div class="col-12">

    {{-- Breadcrumb back link --}}
    <div class="mb-4">
        <a href="{{ route('tenant.admin.abandoned-cart.index') }}"
           class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary transition-colors">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{ __('Back to Abandoned Carts') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Cart items --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-cart-outline text-amber-500 text-base"></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark">{{ __('Cart Items') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Product') }}</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Qty') }}</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Unit Price') }}</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">{{ __('Line Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(!empty($cart->cart_items))
                            @foreach($cart->cart_items as $item)
                            @php
                                $name  = $item['name'] ?? ($item['options']['name'] ?? __('Product'));
                                $qty   = $item['qty'] ?? 1;
                                $price = $item['price'] ?? 0;
                                $total = $qty * $price;
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-medium text-dark">{{ $name }}</span>
                                    @if(!empty($item['options']))
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($item['options'] as $optKey => $optVal)
                                                @if(!in_array($optKey, ['name', 'image']))
                                                <span class="text-[10px] bg-gray-100 text-gray-600 rounded px-1.5 py-0.5">{{ $optKey }}: {{ $optVal }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="text-sm text-gray-700">{{ $qty }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="text-sm text-gray-700">{{ $cart->currency }} {{ number_format($price, 2) }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <span class="text-sm font-semibold text-dark">{{ $cart->currency }} {{ number_format($total, 2) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">{{ __('No items recorded.') }}</td>
                            </tr>
                        @endif
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 border-t border-gray-200">
                                <td colspan="3" class="px-4 py-3 text-sm font-semibold text-gray-700 text-right">{{ __('Cart Total') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-base font-bold text-dark">{{ $cart->currency }} {{ number_format($cart->cart_total, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Customer info + actions --}}
        <div class="space-y-4">

            {{-- Customer details --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-account-outline text-blue-500 text-base"></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark">{{ __('Customer Info') }}</h3>
                </div>
                <div class="divide-y divide-gray-100 text-sm">
                    <div class="px-5 py-3 flex justify-between items-start gap-3">
                        <span class="text-gray-500 shrink-0">{{ __('Email') }}</span>
                        <span class="font-medium text-dark text-right">{{ $cart->email }}</span>
                    </div>
                    <div class="px-5 py-3 flex justify-between items-center gap-3">
                        <span class="text-gray-500 shrink-0">{{ __('Status') }}</span>
                        @php
                            $badge = match($cart->status) {
                                'abandoned'  => 'text-amber-700 bg-amber-100',
                                'reminded'   => 'text-blue-700 bg-blue-100',
                                'converted'  => 'text-green-700 bg-green-100',
                                'expired'    => 'text-gray-600 bg-gray-100',
                                default      => 'text-gray-600 bg-gray-100',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $badge }}">
                            {{ ucfirst($cart->status) }}
                        </span>
                    </div>
                    <div class="px-5 py-3 flex justify-between items-center gap-3">
                        <span class="text-gray-500 shrink-0">{{ __('Reminders') }}</span>
                        <span class="font-medium text-dark">{{ $cart->reminder_count }}</span>
                    </div>
                    @if($cart->reminded_at)
                    <div class="px-5 py-3 flex justify-between items-start gap-3">
                        <span class="text-gray-500 shrink-0">{{ __('Last Reminded') }}</span>
                        <span class="font-medium text-dark text-right text-xs">{{ \Carbon\Carbon::parse($cart->reminded_at)->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                    @if($cart->converted_at)
                    <div class="px-5 py-3 flex justify-between items-start gap-3">
                        <span class="text-gray-500 shrink-0">{{ __('Converted At') }}</span>
                        <span class="font-medium text-green-600 text-right text-xs">{{ \Carbon\Carbon::parse($cart->converted_at)->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                    <div class="px-5 py-3 flex justify-between items-start gap-3">
                        <span class="text-gray-500 shrink-0">{{ __('Last Updated') }}</span>
                        <span class="font-medium text-dark text-right text-xs">{{ \Carbon\Carbon::parse($cart->updated_at)->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-2">
                <button type="button"
                        id="js-resend-btn"
                        data-url="{{ route('tenant.admin.abandoned-cart.resend', $cart->id) }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                    <i class="mdi mdi-email-send-outline text-base"></i>
                    {{ __('Send Reminder') }}
                </button>
                <button type="button"
                        id="js-delete-btn"
                        data-url="{{ route('tenant.admin.abandoned-cart.destroy', $cart->id) }}"
                        data-redirect="{{ route('tenant.admin.abandoned-cart.index') }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition">
                    <i class="mdi mdi-trash-can-outline text-base"></i>
                    {{ __('Delete') }}
                </button>
            </div>

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
(function ($) {
    "use strict";

    var csrf = '{{ csrf_token() }}';

    // Resend reminder
    $('#js-resend-btn').on('click', function () {
        var btn = $(this);
        var url = btn.data('url');
        btn.prop('disabled', true).text('{{ __("Sending...") }}');
        $.post(url, { _token: csrf }, function (res) {
            if (res.type === 'success') {
                toastr.success(res.msg);
            } else {
                toastr.error(res.msg);
            }
        }).fail(function () {
            toastr.error('{{ __("Something went wrong") }}');
        }).always(function () {
            btn.prop('disabled', false).html('<i class="mdi mdi-email-send-outline text-base"></i> {{ __("Send Reminder") }}');
        });
    });

    // Delete cart
    $('#js-delete-btn').on('click', function () {
        var btn = $(this);
        var url = btn.data('url');
        var redirect = btn.data('redirect');
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This abandoned cart record will be permanently deleted.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}',
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: csrf },
                    success: function (res) {
                        if (res.type === 'success') {
                            toastr.success(res.msg);
                            setTimeout(function () { window.location.href = redirect; }, 800);
                        } else {
                            toastr.error(res.msg);
                        }
                    },
                    error: function () {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                });
            }
        });
    });

})(jQuery);
</script>
@endsection
