@extends('tenant.admin.admin-master')
@section('title') {{ __('Affiliate Payouts') }} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Record Payout Form --}}
    <div class="lg:col-span-1">
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cash-plus text-success text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('Record Payout') }}</h3>
                    <p class="text-xs text-muted">{{ __('Deducted from affiliate balance') }}</p>
                </div>
            </div>
            <div class="p-5">
                @if($affiliates->isEmpty())
                    <div class="py-6 text-center">
                        <i class="mdi mdi-account-cash-outline text-3xl text-subtle block mb-2"></i>
                        <p class="text-sm text-muted">{{ __('No affiliates with outstanding balance.') }}</p>
                    </div>
                @else
                <form action="{{ route('tenant.admin.affiliate.payouts.process') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1.5">{{ __('Affiliate') }}</label>
                        <select name="affiliate_id" required id="affiliateSelect" onchange="updateBalance(this)"
                            class="w-full bg-secondary border border-main rounded-xl px-3 py-2.5 text-sm text-dark outline-none focus:border-primary transition">
                            <option value="">{{ __('Select affiliate…') }}</option>
                            @foreach($affiliates as $a)
                                <option value="{{ $a->id }}" data-balance="{{ $a->balance }}">
                                    {{ $a->name }} — {{ amount_with_currency_symbol($a->balance) }}
                                </option>
                            @endforeach
                        </select>
                        <p id="balanceHint" class="text-[11px] text-muted mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1.5">{{ __('Amount') }}</label>
                        <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-currency-usd text-muted text-base"></i>
                            <input type="number" name="amount" step="0.01" min="0.01" required
                                class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none" placeholder="0.00">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1.5">{{ __('Method') }}</label>
                        <select name="method" required
                            class="w-full bg-secondary border border-main rounded-xl px-3 py-2.5 text-sm text-dark outline-none focus:border-primary transition">
                            <option value="manual">{{ __('Manual') }}</option>
                            <option value="bank">{{ __('Bank Transfer') }}</option>
                            <option value="paypal">{{ __('PayPal') }}</option>
                            <option value="crypto">{{ __('Crypto') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1.5">{{ __('Reference') }}</label>
                        <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-tag-outline text-muted text-base"></i>
                            <input type="text" name="reference"
                                class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none"
                                placeholder="{{ __('Transaction ID, etc.') }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1.5">{{ __('Notes') }}</label>
                        <textarea name="notes" rows="2"
                            class="w-full bg-secondary border border-main rounded-xl px-3 py-2.5 text-sm text-dark placeholder-gray-400 outline-none focus:border-primary transition resize-none"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-cash-check text-base"></i> {{ __('Record Payout') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Payout History --}}
    <div class="lg:col-span-2">
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-history text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('Payout History') }}</h3>
                    <p class="text-xs text-muted">{{ __('All recorded payouts') }}</p>
                </div>
            </div>
            <div class="tw-table-wrap">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-main">
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Affiliate') }}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Amount') }}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Method') }}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Reference') }}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payouts as $p)
                        <tr class="border-b border-main hover:bg-muted transition-colors">
                            <td class="px-4 py-3.5">
                                <p class="text-sm font-semibold text-dark">{{ $p->affiliate_name }}</p>
                                <p class="text-[11px] text-muted mt-0.5">{{ $p->affiliate_email }}</p>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-bold text-dark">{{ amount_with_currency_symbol($p->amount) }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-secondary border border-main text-xs font-semibold text-dark capitalize">{{ $p->method }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs text-muted">{{ $p->reference ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs text-muted">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center">
                                <i class="mdi mdi-cash-check text-4xl text-subtle block mb-2"></i>
                                <p class="text-sm text-muted">{{ __('No payouts recorded yet.') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payouts->hasPages())
            <div class="px-4 py-3 border-t border-main">
                {{ $payouts->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateBalance(select) {
    var opt = select.options[select.selectedIndex];
    var balance = opt.dataset.balance;
    var hint = document.getElementById('balanceHint');
    hint.textContent = balance ? '{{ __("Available balance:") }} ' + parseFloat(balance).toFixed(2) : '';
}
</script>
@endsection
