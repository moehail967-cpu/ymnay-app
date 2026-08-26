@extends('tenant.admin.admin-master')
@section('title') {{__('Customer Lifetime Value')}} @endsection
@section('content')
<x-landlord-flash-msg/>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-lg font-bold text-dark font-urbanist">{{__('Customer Lifetime Value')}}</h1>
        <p class="text-xs text-muted mt-0.5">{{__('Total revenue and avg. order value per customer')}}</p>
    </div>
</div>

<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-main bg-[rgba(0,0,0,0.02)]">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Customer ID')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Orders')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Total Revenue')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Avg. Order')}}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Last Order')}}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-main">
                @forelse($rows as $row)
                <tr class="hover:bg-[rgba(0,0,0,0.015)] transition-colors">
                    <td class="px-4 py-3 text-muted">#{{ $row['user_id'] }}</td>
                    <td class="px-4 py-3 text-right font-medium text-dark">{{ $row['order_count'] }}</td>
                    <td class="px-4 py-3 text-right font-bold text-dark">{{amount_with_currency_symbol($row['total_revenue'])}}</td>
                    <td class="px-4 py-3 text-right text-muted">{{amount_with_currency_symbol($row['avg_order_value'])}}</td>
                    <td class="px-4 py-3 text-muted text-xs">{{ \Carbon\Carbon::parse($row['last_order'])->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-muted">{{__('No customer order data found.')}}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
