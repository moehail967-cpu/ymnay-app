@extends('tenant.admin.admin-master')
@section('title')
    {{__('Campaign Performance')}}
@endsection

@section('content')
<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-lg font-bold text-dark font-urbanist">{{__('Campaign Performance')}}</h1>
        <p class="text-xs text-muted mt-0.5">{{__('Revenue, discount given, and ROI per campaign')}}</p>
    </div>
    <a href="{{ route('tenant.admin.sales.dashboard') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-surface border border-main text-sm text-muted hover:text-dark transition-colors">
        <i class="mdi mdi-arrow-left text-base"></i>
        {{__('Back to Dashboard')}}
    </a>
</div>

@php
    $total_campaign_rev    = array_sum(array_column($rows, 'campaign_rev'));
    $total_regular_rev     = array_sum(array_column($rows, 'regular_rev'));
    $total_discount_given  = array_sum(array_column($rows, 'discount_given'));
    $total_units           = array_sum(array_column($rows, 'total_units'));
@endphp

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(252,79,0,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-tag-multiple-outline text-xl text-[#FC4F00]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Total Campaigns')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{ count($rows) }}</h3>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(0,121,255,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-cash-multiple text-xl text-[#0079FF]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Campaign Revenue')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{amount_with_currency_symbol($total_campaign_rev)}}</h3>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(34,166,153,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-package-variant text-xl text-[#22A699]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Total Units Sold')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{ $total_units }}</h3>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(239,68,68,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-tag-minus-outline text-xl text-red-500"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Total Discount Given')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{amount_with_currency_symbol($total_discount_given)}}</h3>
        </div>
    </div>
</div>

{{-- Campaign Table --}}
<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-[rgba(252,79,0,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-bullhorn-outline text-[#FC4F00] text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Campaigns')}}</h3>
            <p class="text-xs text-muted">{{__('Revenue vs regular price, discount given, and ROI')}}</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-main bg-[rgba(0,0,0,0.02)]">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wide">{{__('Campaign')}}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wide">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wide">{{__('Period')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase tracking-wide">{{__('Units')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase tracking-wide">{{__('Campaign Rev.')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase tracking-wide">{{__('Regular Rev.')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase tracking-wide">{{__('Discount Given')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase tracking-wide">{{__('ROI %')}}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-main">
                @forelse($rows as $row)
                @php
                    $roi = $row['regular_rev'] > 0
                        ? round((($row['campaign_rev'] - $row['regular_rev']) / $row['regular_rev']) * 100, 1)
                        : 0;
                    $statusClass = match($row['status']) {
                        'publish' => 'bg-green-100 text-green-700',
                        'ended'   => 'bg-gray-100 text-gray-600',
                        default   => 'bg-yellow-100 text-yellow-700',
                    };
                @endphp
                <tr class="hover:bg-[rgba(0,0,0,0.015)] transition-colors">
                    <td class="px-4 py-3">
                        <span class="font-semibold text-dark">{{ $row['name'] }}</span>
                        <span class="block text-xs text-muted">#{{ $row['id'] }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ ucfirst($row['status']) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-muted text-xs">
                        {{ \Carbon\Carbon::parse($row['start_date'])->format('d M Y') }}
                        <span class="mx-1">–</span>
                        {{ \Carbon\Carbon::parse($row['end_date'])->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-dark">{{ $row['total_units'] }}</td>
                    <td class="px-4 py-3 text-right font-medium text-dark">{{amount_with_currency_symbol($row['campaign_rev'])}}</td>
                    <td class="px-4 py-3 text-right text-muted">{{amount_with_currency_symbol($row['regular_rev'])}}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-medium {{ $row['discount_given'] > 0 ? 'text-red-500' : 'text-muted' }}">
                            {{amount_with_currency_symbol($row['discount_given'])}}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($roi > 0)
                            <span class="inline-flex items-center gap-1 text-green-600 font-semibold">
                                <i class="mdi mdi-trending-up text-sm"></i>+{{ $roi }}%
                            </span>
                        @elseif($roi < 0)
                            <span class="inline-flex items-center gap-1 text-red-500 font-semibold">
                                <i class="mdi mdi-trending-down text-sm"></i>{{ $roi }}%
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center gap-3 text-muted">
                            <i class="mdi mdi-bullhorn-outline text-4xl opacity-40"></i>
                            <p class="text-sm">{{__('No campaigns found.')}}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if(count($rows) > 0)
            <tfoot>
                <tr class="border-t-2 border-main bg-[rgba(0,0,0,0.03)]">
                    <td colspan="3" class="px-4 py-3 text-xs font-semibold text-muted uppercase">{{__('Totals')}}</td>
                    <td class="px-4 py-3 text-right font-bold text-dark">{{ $total_units }}</td>
                    <td class="px-4 py-3 text-right font-bold text-dark">{{amount_with_currency_symbol($total_campaign_rev)}}</td>
                    <td class="px-4 py-3 text-right font-bold text-muted">{{amount_with_currency_symbol($total_regular_rev)}}</td>
                    <td class="px-4 py-3 text-right font-bold text-red-500">{{amount_with_currency_symbol($total_discount_given)}}</td>
                    <td class="px-4 py-3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
