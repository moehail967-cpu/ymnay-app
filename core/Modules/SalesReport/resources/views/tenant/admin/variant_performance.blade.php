@extends('tenant.admin.admin-master')
@section('title') {{__('Variant Performance Report')}} @endsection
@section('content')
<x-landlord-flash-msg/>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-lg font-bold text-dark font-urbanist">{{__('Variant Performance Report')}}</h1>
        <p class="text-xs text-muted mt-0.5">{{__('Best and worst selling color / size combinations')}}</p>
    </div>
    <a href="{{ route('tenant.admin.sales.export', 'variant') }}"
       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface border border-main text-xs text-muted hover:text-dark">
        <i class="mdi mdi-download-outline"></i> CSV
    </a>
</div>

<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-main bg-[rgba(0,0,0,0.02)]">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Variant')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Units Sold')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Revenue')}}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Performance')}}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-main">
                @forelse($rows as $i => $row)
                @php $max_qty = $rows[0]['qty'] ?? 1; @endphp
                <tr class="hover:bg-[rgba(0,0,0,0.015)] transition-colors">
                    <td class="px-4 py-3 font-medium text-dark">{{ $row['label'] }}</td>
                    <td class="px-4 py-3 text-right font-bold text-dark">{{ $row['qty'] }}</td>
                    <td class="px-4 py-3 text-right text-muted">{{amount_with_currency_symbol($row['revenue'])}}</td>
                    <td class="px-4 py-3">
                        <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: {{ min(100, ($row['qty'] / max($max_qty, 1)) * 100) }}%"></div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-muted">{{__('No variant sales data found.')}}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
