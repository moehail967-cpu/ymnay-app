@php $route_name = 'landlord'; @endphp

@extends($route_name.'.admin.admin-master')
@section('title') {{__('Recurring Subscription')}} @endsection

@section('style')
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Main Form Card ───────────────────────────────────────── --}}
    <div class="lg:col-span-2">
        <div class="bg-surface rounded-xl shadow-main overflow-hidden border border-main">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-main flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-refresh-circle text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Recurring Subscription')}}</h3>
                        <p class="text-xs text-muted">{{__('Manage auto-renewal and grace period for this tenant')}}</p>
                    </div>
                </div>
                <a href="{{route('landlord.admin.tenant')}}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition whitespace-nowrap">
                    <i class="mdi mdi-arrow-left text-base"></i>
                    {{__('Back')}}
                </a>
            </div>

            {{-- Form Body --}}
            <div class="p-6 md:p-8">
                <form action="{{route('landlord.admin.tenant.recurring.update', $tenant->id)}}" method="POST">
                    @csrf

                    {{-- Section: Subscription Toggle --}}
                    <p class="text-[10px] font-bold tracking-widest text-muted uppercase mb-4">{{__('Subscription Settings')}}</p>

                    <div class="space-y-5 mb-8">

                        {{-- Enable Recurring Toggle --}}
                        <div class="flex items-center justify-between bg-secondary border border-main rounded-xl px-5 py-4">
                            <div class="flex items-center gap-3">
                                <i class="mdi mdi-repeat text-xl text-primary"></i>
                                <div>
                                    <span class="text-sm font-semibold text-dark">{{__('Enable Recurring Subscription')}}</span>
                                    <p class="text-[11px] text-muted mt-0.5">{{__('Automatically renew this tenant\'s subscription on expiry.')}}</p>
                                </div>
                            </div>
                            <label class="dr-toggle">
                                <input type="checkbox" name="recurring_enabled"
                                       @if($tenant->recurring_enabled) checked @endif>
                                <span class="dr-toggle-track"></span>
                            </label>
                        </div>

                        {{-- Grace Period --}}
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                                {{__('Payment Grace Period (Days)')}}
                            </label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition max-w-sm">
                                <i class="mdi mdi-calendar-clock text-lg text-primary"></i>
                                <input type="number"
                                       name="payment_grace_days"
                                       value="{{$tenant->payment_grace_days ?? 3}}"
                                       min="0" max="30"
                                       placeholder="{{__('e.g. 3')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 py-2.5 p-0">
                            </div>
                            <p class="text-[11px] text-muted mt-1.5 flex items-center gap-1">
                                <i class="mdi mdi-information-outline text-primary text-sm"></i>
                                {{__('Days to keep tenant active after a failed payment before suspension.')}}
                            </p>
                        </div>

                    </div>

                    {{-- Submit --}}
                    <div class="pt-6 border-t border-main">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-content-save-outline text-base"></i>
                            {{__('Save Settings')}}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- ── Right Sidebar ─────────────────────────────────────────── --}}
    <div class="space-y-5">

        {{-- Active Subscription Card --}}
        @if($tenant->razorpay_subscription_id)
        <div class="bg-surface rounded-xl shadow-main overflow-hidden border border-main">
            <div class="px-5 py-4 border-b border-main flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-check-decagram text-success text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-dark">{{__('Active Subscription')}}</p>
                    <p class="text-[10px] text-muted">{{__('Razorpay subscription linked')}}</p>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div class="bg-secondary border border-main rounded-xl px-4 py-3">
                    <p class="text-[10px] font-bold text-muted uppercase tracking-widest mb-1">{{__('Subscription ID')}}</p>
                    <p class="text-sm font-semibold text-dark font-mono break-all">{{$tenant->razorpay_subscription_id}}</p>
                </div>

                <div class="flex items-start gap-2 bg-warning-soft border border-main rounded-xl px-4 py-3">
                    <i class="mdi mdi-alert-outline text-warning text-sm mt-0.5 flex-shrink-0"></i>
                    <p class="text-[11px] text-dark leading-relaxed">{{__('Cancelling will immediately stop auto-renewal. This action cannot be undone.')}}</p>
                </div>

                <button type="button"
                        id="cancelSubscriptionBtn"
                        data-id="{{$tenant->razorpay_subscription_id}}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-danger text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-cancel text-base"></i>
                    {{__('Cancel Subscription')}}
                </button>
            </div>
        </div>
        @else
        <div class="bg-surface rounded-xl shadow-main overflow-hidden border border-main">
            <div class="px-5 py-4 border-b border-main flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-muted flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-link-off text-muted text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-dark">{{__('No Active Subscription')}}</p>
                    <p class="text-[10px] text-muted">{{__('No Razorpay subscription linked')}}</p>
                </div>
            </div>
            <div class="px-5 py-6 text-center">
                <div class="w-10 h-10 rounded-xl bg-muted flex items-center justify-center mx-auto mb-3">
                    <i class="mdi mdi-repeat-off text-muted text-lg"></i>
                </div>
                <p class="text-xs text-muted">{{__('This tenant has no active recurring subscription attached.')}}</p>
            </div>
        </div>
        @endif

        {{-- Info Card --}}
        <div class="bg-info-soft border border-main rounded-xl px-5 py-4">
            <div class="flex items-start gap-3">
                <i class="mdi mdi-information-outline text-primary text-lg mt-0.5 flex-shrink-0"></i>
                <div class="space-y-1.5">
                    <p class="text-xs font-bold text-dark">{{__('How It Works')}}</p>
                    <p class="text-[11px] text-muted leading-relaxed">{{__('When enabled, the system will attempt to charge the tenant automatically on subscription expiry using Razorpay.')}}</p>
                    <p class="text-[11px] text-muted leading-relaxed">{{__('The grace period keeps the account active for the specified number of days after a payment failure before suspension.')}}</p>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@section('scripts')
<script>
(function () {
    "use strict";

    var btn = document.getElementById('cancelSubscriptionBtn');
    if (!btn) return;

    btn.addEventListener('click', function () {
        var subscriptionId = btn.dataset.id;

        Swal.fire({
            title: '{{ __("Cancel Subscription?") }}',
            text:  '{{ __("This will immediately stop auto-renewal. This action cannot be undone.") }}',
            icon:  'warning',
            showCancelButton:  true,
            confirmButtonText: '{{ __("Yes, Cancel It") }}',
            cancelButtonText:  '{{ __("No, Keep It") }}',
            confirmButtonColor: '#ef4444',
            cancelButtonColor:  '#6b7280',
        }).then(function (result) {
            if (!result.isConfirmed) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> {{ __("Cancelling...") }}';

            fetch('{{ route('landlord.admin.tenant.recurring.cancel', $tenant->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ subscription_id: subscriptionId })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.status === 'success') {
                    Swal.fire('{{ __("Cancelled") }}', data.message, 'success')
                        .then(function () { location.reload(); });
                } else {
                    Swal.fire('{{ __("Error") }}', data.message, 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-cancel"></i> {{ __("Cancel Subscription") }}';
                }
            })
            .catch(function () {
                Swal.fire('{{ __("Error") }}', '{{ __("An error occurred. Please try again.") }}', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="mdi mdi-cancel"></i> {{ __("Cancel Subscription") }}';
            });
        });
    });
}());
</script>
@endsection
