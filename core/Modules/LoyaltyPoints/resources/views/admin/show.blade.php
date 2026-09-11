@extends('tenant.admin.admin-master')

@section('title') {{ __('Customer Transactions') }} @endsection
@section('page-title') {{ __('Customer Transactions') }} @endsection

@section('content')
<div class="col-12">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('tenant.admin.loyalty-points.index') }}"
               class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
                <i class="mdi mdi-arrow-left text-base"></i>
            </a>
            <div>
                <h2 class="text-base font-bold text-dark">{{ $email }}</h2>
                <p class="text-xs text-muted mt-0.5">{{ __('Loyalty transaction history') }}</p>
            </div>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-2 text-center">
            <div class="text-xl font-bold text-amber-600">{{ number_format($balance) }}</div>
            <div class="text-xs text-amber-500 font-semibold">{{ __('Current Balance') }}</div>
        </div>
    </div>

    {{-- Transaction log --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-history text-amber-500 text-base"></i>
            </div>
            <h3 class="text-sm font-bold text-dark">{{ __('Transaction History') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Type') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Note') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">{{ __('Points') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">{{ __('Balance After') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($transactions as $tx)
                    @php
                        $badge = match($tx->type) {
                            'earn'   => 'text-green-700 bg-green-100',
                            'redeem' => 'text-blue-700 bg-blue-100',
                            'expire' => 'text-gray-600 bg-gray-100',
                            'manual' => 'text-purple-700 bg-purple-100',
                            'refund' => 'text-red-700 bg-red-100',
                            default  => 'text-gray-600 bg-gray-100',
                        };
                        $sign = $tx->points > 0 ? '+' : '';
                    @endphp
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($tx->created_at)->format('M d, Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($tx->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm text-gray-600">{{ $tx->note ?: '—' }}</span>
                            @if($tx->order_id)
                                <a href="#" class="text-xs text-blue-500 ml-1">#{{ $tx->order_id }}</a>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <span class="text-sm font-bold {{ $tx->points > 0 ? 'text-green-600' : 'text-red-500' }}">
                                {{ $sign }}{{ number_format($tx->points) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <span class="text-sm font-semibold text-dark">{{ number_format($tx->balance_after) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">
                            {{ __('No transactions found.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
