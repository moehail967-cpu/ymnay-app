@extends('tenant.admin.admin-master')
@section('title') {{ __('Affiliate Settings') }} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="max-w-2xl">
    <div class="bg-surface rounded-xl shadow-main border border-main">
        <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-cog-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('Affiliate Programme Settings') }}</h3>
                <p class="text-xs text-muted">{{ __('Configure commission rates and programme behaviour') }}</p>
            </div>
        </div>

        <form action="{{ route('tenant.admin.affiliate.settings.save') }}" method="POST" class="p-5 sm:p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{ __('Commission Rate (%)') }}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-percent text-muted text-base"></i>
                    <input type="number" name="commission_rate" step="0.01" min="0" max="100" required
                        value="{{ old('commission_rate', $settings['commission_rate']) }}"
                        class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
                <p class="text-[11px] text-muted mt-1">{{ __('Percentage of the order subtotal paid as commission.') }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{ __('Referral Cookie Duration (days)') }}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-cookie-outline text-muted text-base"></i>
                    <input type="number" name="cookie_days" min="1" max="365" required
                        value="{{ old('cookie_days', $settings['cookie_days']) }}"
                        class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
                <p class="text-[11px] text-muted mt-1">{{ __("How long the referral is tracked in the visitor's browser.") }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{ __('Minimum Payout Amount') }}</label>
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                    <i class="mdi mdi-currency-usd text-muted text-base"></i>
                    <input type="number" name="min_payout" step="0.01" min="0" required
                        value="{{ old('min_payout', $settings['min_payout']) }}"
                        class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                </div>
                <p class="text-[11px] text-muted mt-1">{{ __('Affiliates must reach this balance before requesting a payout.') }}</p>
            </div>

            <div class="flex items-center justify-between py-3 px-4 bg-secondary rounded-xl border border-main">
                <div>
                    <p class="text-sm font-semibold text-dark">{{ __('Auto-approve Commissions') }}</p>
                    <p class="text-[11px] text-muted mt-0.5">{{ __('Approve commissions automatically on order completion.') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer ml-4 flex-shrink-0">
                    <input type="hidden" name="auto_approve" value="0">
                    <input type="checkbox" name="auto_approve" value="1" class="sr-only peer"
                        {{ $settings['auto_approve'] === '1' ? 'checked' : '' }}>
                    <div class="w-10 h-5 bg-gray-300 peer-checked:bg-primary rounded-full transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div class="flex items-start gap-3 p-4 bg-primary-soft rounded-xl border border-primary/20">
                <i class="mdi mdi-shield-check text-primary text-lg flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-xs font-bold text-primary">{{ __('Self-purchase protection is always on') }}</p>
                    <p class="text-[11px] text-muted mt-0.5">{{ __('Affiliates never earn commission on their own orders, regardless of settings.') }}</p>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i> {{ __('Save Settings') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
