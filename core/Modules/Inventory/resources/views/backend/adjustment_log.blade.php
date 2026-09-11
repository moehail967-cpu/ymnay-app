@extends('tenant.admin.admin-master')
@section('title')
    {{__('Inventory Adjustment Log')}}
@endsection

@section('content')
<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-lg font-bold text-dark font-urbanist">{{__('Inventory Adjustment Log')}}</h1>
        <p class="text-xs text-muted mt-0.5">{{__('All manual and system stock changes')}}</p>
    </div>
    <a href="{{ route('tenant.admin.product.inventory.all') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-surface border border-main text-sm text-muted hover:text-dark transition-colors">
        <i class="mdi mdi-arrow-left text-base"></i>
        {{__('Back to Inventory')}}
    </a>
</div>

<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-[rgba(0,121,255,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-history text-[#0079FF] text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Adjustment History')}}</h3>
            <p class="text-xs text-muted">{{__('All stock adjustments with reason and note')}}</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-main bg-[rgba(0,0,0,0.02)]">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Product')}}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Reason')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Before')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Change')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('After')}}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Note')}}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Date')}}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-main">
                @forelse($logs as $log)
                <tr class="hover:bg-[rgba(0,0,0,0.015)] transition-colors">
                    <td class="px-4 py-3 font-medium text-dark">{{ $log->product?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $reasonColors = [
                                'manual_add'   => 'bg-green-100 text-green-700',
                                'damaged'      => 'bg-red-100 text-red-600',
                                'theft'        => 'bg-red-100 text-red-600',
                                'correction'   => 'bg-yellow-100 text-yellow-700',
                                'purchase'     => 'bg-blue-100 text-blue-700',
                                'return'       => 'bg-purple-100 text-purple-700',
                                'campaign_sale'=> 'bg-orange-100 text-orange-700',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $reasonColors[$log->reason] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst(str_replace('_', ' ', $log->reason)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-muted">{{ $log->qty_before }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $log->qty_change >= 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $log->qty_change >= 0 ? '+' : '' }}{{ $log->qty_change }}
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-dark">{{ $log->qty_after }}</td>
                    <td class="px-4 py-3 text-muted text-xs">{{ $log->note ?? '—' }}</td>
                    <td class="px-4 py-3 text-muted text-xs">{{ $log->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center">
                        <i class="mdi mdi-history text-4xl text-muted opacity-40 block mb-2"></i>
                        <p class="text-sm text-muted">{{__('No adjustments recorded yet.')}}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-main">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
