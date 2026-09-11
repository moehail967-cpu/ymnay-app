@extends('tenant.admin.admin-master')
@section('title') {{__('Inventory Turnover Report')}} @endsection
@section('content')
<x-landlord-flash-msg/>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-lg font-bold text-dark font-urbanist">{{__('Inventory Turnover Report')}}</h1>
        <p class="text-xs text-muted mt-0.5">{{__('Units sold ÷ current stock — higher = faster moving')}}</p>
    </div>
    <a href="{{ route('tenant.admin.sales.export', 'inventory') }}"
       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface border border-main text-xs text-muted hover:text-dark">
        <i class="mdi mdi-download-outline"></i> CSV
    </a>
</div>

<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-main bg-[rgba(0,0,0,0.02)]">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Product')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Current Stock')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Total Sold')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Turnover Rate')}}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Speed')}}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-main">
                @forelse($rows as $row)
                @php
                    $speedColor = match($row['speed']) {
                        'fast'   => 'bg-green-100 text-green-700',
                        'medium' => 'bg-yellow-100 text-yellow-700',
                        default  => 'bg-red-100 text-red-600',
                    };
                @endphp
                <tr class="hover:bg-[rgba(0,0,0,0.015)] transition-colors">
                    <td class="px-4 py-3 font-medium text-dark">{{ $row['name'] }}</td>
                    <td class="px-4 py-3 text-right text-muted">{{ $row['stock'] }}</td>
                    <td class="px-4 py-3 text-right text-muted">{{ $row['sold'] }}</td>
                    <td class="px-4 py-3 text-right font-bold text-dark">{{ $row['turnover'] }}×</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $speedColor }}">
                            {{ ucfirst($row['speed']) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-muted">{{__('No inventory data found.')}}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
