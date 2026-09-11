@extends('tenant.admin.admin-master')

@section('title') {{ __('Abandoned Carts') }} @endsection
@section('page-title') {{ __('Abandoned Carts') }} @endsection

@section('content')
<div class="col-12">

    {{-- Flash messages --}}
    <x-msg.flash />
    <x-msg.error />

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Abandoned Cart Recovery') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Track and recover abandoned carts from your customers.') }}</p>
        </div>
        <a href="{{ route('tenant.admin.abandoned-cart.settings') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">
            <i class="mdi mdi-cog-outline text-base"></i>
            {{ __('Settings') }}
        </a>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="text-2xl font-bold text-gray-900">{{ $total }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ __('Total Abandoned') }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="text-2xl font-bold text-green-600">{{ $converted }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ __('Converted') }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="text-2xl font-bold text-blue-600">{{ $rate }}%</div>
            <div class="text-sm text-gray-500 mt-1">{{ __('Recovery Rate') }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($revenue, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ __('Revenue Recovered') }}</div>
        </div>
    </div>

    {{-- Cart table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-cart-remove text-amber-500 text-base"></i>
            </div>
            <h3 class="text-sm font-bold text-dark">{{ __('All Abandoned Carts') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Email') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Cart Total') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Reminders') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Last Activity') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($carts as $cart)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-medium text-dark">{{ $cart->email }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-semibold text-dark">{{ $cart->currency }} {{ number_format($cart->cart_total, 2) }}</span>
                        </td>
                        <td class="px-4 py-3.5">
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
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm text-gray-600">{{ $cart->reminder_count }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($cart->updated_at)->diffForHumans() }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- View --}}
                                <a href="{{ route('tenant.admin.abandoned-cart.show', $cart->id) }}"
                                   class="w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-600 hover:text-white hover:bg-blue-500 hover:border-blue-500 transition-all"
                                   title="{{ __('View Cart') }}">
                                    <i class="mdi mdi-eye text-sm"></i>
                                </a>
                                {{-- Resend --}}
                                <button type="button"
                                        class="js-resend w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-600 hover:text-white hover:bg-indigo-500 hover:border-indigo-500 transition-all"
                                        data-id="{{ $cart->id }}"
                                        data-url="{{ route('tenant.admin.abandoned-cart.resend', $cart->id) }}"
                                        title="{{ __('Resend Reminder') }}">
                                    <i class="mdi mdi-email-send-outline text-sm"></i>
                                </button>
                                {{-- Delete --}}
                                <button type="button"
                                        class="js-delete w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-600 hover:text-white hover:bg-red-500 hover:border-red-500 transition-all"
                                        data-id="{{ $cart->id }}"
                                        data-url="{{ route('tenant.admin.abandoned-cart.destroy', $cart->id) }}"
                                        title="{{ __('Delete') }}">
                                    <i class="mdi mdi-trash-can-outline text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">
                            <i class="mdi mdi-cart-remove text-3xl block mb-2 text-gray-300"></i>
                            {{ __('No abandoned carts found.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($carts->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $carts->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
(function ($) {
    "use strict";

    var csrf = '{{ csrf_token() }}';

    // Resend reminder
    $(document).on('click', '.js-resend', function () {
        var btn = $(this);
        var url = btn.data('url');
        btn.prop('disabled', true);
        $.post(url, { _token: csrf }, function (res) {
            if (res.type === 'success') {
                toastr.success(res.msg);
            } else {
                toastr.error(res.msg);
            }
        }).fail(function () {
            toastr.error('{{ __("Something went wrong") }}');
        }).always(function () {
            btn.prop('disabled', false);
        });
    });

    // Delete cart
    $(document).on('click', '.js-delete', function () {
        var btn = $(this);
        var url = btn.data('url');
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
                            btn.closest('tr').fadeOut(300, function () { $(this).remove(); });
                            toastr.success(res.msg);
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
