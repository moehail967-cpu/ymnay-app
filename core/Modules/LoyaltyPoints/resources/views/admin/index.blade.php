@extends('tenant.admin.admin-master')

@section('title') {{ __('Loyalty Points') }} @endsection
@section('page-title') {{ __('Loyalty Points') }} @endsection

@section('content')
<div class="col-12">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Customer Points') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('View and manage loyalty point balances.') }}</p>
        </div>
        <a href="{{ route('tenant.admin.loyalty-points.settings') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
            <i class="mdi mdi-cog-outline text-base"></i>
            {{ __('Settings') }}
        </a>
    </div>

    {{-- Customer table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-star-circle-outline text-amber-500 text-base"></i>
            </div>
            <h3 class="text-sm font-bold text-dark">{{ __('All Customers') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Customer') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">{{ __('Balance') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">{{ __('Total Earned') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">{{ __('Total Redeemed') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Last Activity') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-medium text-dark">{{ $customer->email }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold text-amber-700 bg-amber-100">
                                {{ number_format($customer->balance) }} {{ __('pts') }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <span class="text-sm text-green-600 font-semibold">+{{ number_format($customer->total_earned) }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <span class="text-sm text-gray-500">{{ number_format($customer->total_redeemed) }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($customer->last_activity)->diffForHumans() }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('tenant.admin.loyalty-points.show', $customer->user_id) }}"
                                   class="w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-600 hover:text-white hover:bg-blue-500 hover:border-blue-500 transition-all"
                                   title="{{ __('View Transactions') }}">
                                    <i class="mdi mdi-eye text-sm"></i>
                                </a>
                                <button type="button"
                                        class="js-adjust w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-600 hover:text-white hover:bg-amber-500 hover:border-amber-500 transition-all"
                                        data-id="{{ $customer->user_id }}"
                                        data-url="{{ route('tenant.admin.loyalty-points.adjust', $customer->user_id) }}"
                                        title="{{ __('Adjust Points') }}">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">
                            <i class="mdi mdi-star-circle-outline text-3xl block mb-2 text-gray-300"></i>
                            {{ __('No loyalty point activity yet.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Adjust Modal --}}
<div id="lp-adjust-modal" class="fixed inset-0 z-[800] hidden items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6">
        <h3 class="text-base font-bold text-dark mb-4">{{ __('Adjust Points') }}</h3>
        <form id="lp-adjust-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="lp-adjust-url" name="_url" value="">

            <div class="mb-4">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">{{ __('Type') }}</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="add" checked class="accent-amber-500"> {{ __('Add') }}
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="deduct" class="accent-red-500"> {{ __('Deduct') }}
                    </label>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">{{ __('Points') }}</label>
                <input type="number" name="points" id="lp-adjust-points" min="1" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400" required>
            </div>

            <div class="mb-5">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">{{ __('Note (optional)') }}</label>
                <input type="text" name="note" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400" placeholder="{{ __('Reason…') }}">
            </div>

            <div id="lp-adjust-msg" class="text-sm mb-3 hidden"></div>

            <div class="flex gap-2 justify-end">
                <button type="button" id="lp-adjust-cancel"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    {{ __('Cancel') }}
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(session('success')) toastr.success("{{ session('success') }}"); @endif
    @if(session('error'))   toastr.error("{{ session('error') }}");     @endif
});
</script>
<script>
(function ($) {
    "use strict";

    var modal   = $('#lp-adjust-modal');
    var form    = $('#lp-adjust-form');
    var msg     = $('#lp-adjust-msg');
    var urlFld  = $('#lp-adjust-url');

    $(document).on('click', '.js-adjust', function () {
        urlFld.val($(this).data('url'));
        msg.addClass('hidden').text('');
        form[0].reset();
        modal.removeClass('hidden').addClass('flex');
    });

    $('#lp-adjust-cancel').on('click', function () {
        modal.addClass('hidden').removeClass('flex');
    });

    modal.on('click', function (e) {
        if ($(e.target).is(modal)) modal.addClass('hidden').removeClass('flex');
    });

    form.on('submit', function (e) {
        e.preventDefault();
        var btn = form.find('[type=submit]');
        btn.prop('disabled', true);
        $.post(urlFld.val(), form.serialize(), function (res) {
            if (res.type === 'success') {
                toastr.success(res.msg);
                modal.addClass('hidden').removeClass('flex');
                location.reload();
            } else {
                msg.text(res.msg).removeClass('hidden text-green-600').addClass('text-red-600');
            }
        }).fail(function () {
            msg.text('{{ __("Something went wrong") }}').removeClass('hidden text-green-600').addClass('text-red-600');
        }).always(function () { btn.prop('disabled', false); });
    });

})(jQuery);
</script>
@endsection
