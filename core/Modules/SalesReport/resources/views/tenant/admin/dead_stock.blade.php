@extends('tenant.admin.admin-master')
@section('title') {{__('Dead Stock Report')}} @endsection
@section('content')
<x-landlord-flash-msg/>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-lg font-bold text-dark font-urbanist">{{__('Dead Stock Report')}}</h1>
        <p class="text-xs text-muted mt-0.5">{{__('Products with stock but no sales in the selected period')}}</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        @foreach([30, 60, 90] as $d)
        <a href="?days={{ $d }}"
           class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ $days == $d ? 'bg-primary text-white border-primary' : 'bg-surface border-main text-muted hover:text-dark' }}">
            {{ $d }} {{__('Days')}}
        </a>
        @endforeach
        <a href="{{ route('tenant.admin.sales.export', 'dead-stock') }}"
           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface border border-main text-xs text-muted hover:text-dark">
            <i class="mdi mdi-download-outline"></i> CSV
        </a>
    </div>
</div>

<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-package-variant-remove text-red-500 text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Stagnant Products')}} — <span class="text-muted font-normal">{{ count($rows) }} {{__('found')}}</span></h3>
            <p class="text-xs text-muted">{{__('0 sales in the last :days days but still in stock', ['days' => $days])}}</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-main bg-[rgba(0,0,0,0.02)]">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Product')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Current Stock')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Total Sold (All Time)')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Days Stagnant')}}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-main">
                @forelse($rows as $row)
                <tr class="hover:bg-red-50/20 transition-colors">
                    <td class="px-4 py-3 font-medium text-dark">{{ $row['name'] }}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">{{ $row['stock'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-right text-muted">{{ $row['sold_total'] }}</td>
                    <td class="px-4 py-3 text-right text-red-500 font-semibold">{{ $row['days_stagnant'] }}+</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-muted">
                    <i class="mdi mdi-check-circle-outline text-green-500 text-3xl block mb-2"></i>
                    {{__('No dead stock found for this period.')}}
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
