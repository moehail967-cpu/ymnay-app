@extends('tenant.admin.admin-master')
@section('title')
    {{__('Stock Alert Dashboard')}}
@endsection

@section('content')
<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-lg font-bold text-dark font-urbanist">{{__('Stock Alert Dashboard')}}</h1>
        <p class="text-xs text-muted mt-0.5">{{__('Products at or below :threshold units in stock', ['threshold' => $threshold])}}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('tenant.admin.product.inventory.all') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-surface border border-main text-sm text-muted hover:text-dark transition-colors">
            <i class="mdi mdi-package-variant text-base"></i>
            {{__('All Inventory')}}
        </a>
        <a href="{{ route('tenant.admin.product.inventory.settings') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-surface border border-main text-sm text-muted hover:text-dark transition-colors">
            <i class="mdi mdi-cog-outline text-base"></i>
            {{__('Settings')}}
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-package-variant-closed-remove text-xl text-red-500"></i>
        </div>
        <div>
            <p class="text-xs text-muted">{{__('Out of Stock')}}</p>
            <h3 class="text-xl font-bold text-red-500 font-urbanist">{{ $out_of_stock->count() }}</h3>
        </div>
    </div>
    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-alert-circle-outline text-xl text-yellow-500"></i>
        </div>
        <div>
            <p class="text-xs text-muted">{{__('Low Stock (≤ :n)', ['n' => $threshold])}}</p>
            <h3 class="text-xl font-bold text-yellow-600 font-urbanist">{{ $low_stock->count() }}</h3>
        </div>
    </div>
    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(252,79,0,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-alert text-xl text-[#FC4F00]"></i>
        </div>
        <div>
            <p class="text-xs text-muted">{{__('Total Needing Attention')}}</p>
            <h3 class="text-xl font-bold text-dark font-urbanist">{{ $out_of_stock->count() + $low_stock->count() }}</h3>
        </div>
    </div>
    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-reload-alert text-xl text-purple-600"></i>
        </div>
        <div>
            <p class="text-xs text-muted">{{__('Needs Reorder')}}</p>
            <h3 class="text-xl font-bold text-purple-600 font-urbanist">{{ $needs_reorder->count() }}</h3>
        </div>
    </div>
</div>

{{-- Needs Reorder --}}
@if($needs_reorder->count() > 0)
<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-reload-alert text-purple-600 text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Needs Reorder')}}</h3>
            <p class="text-xs text-muted">{{__('Stock at or below the custom reorder point you set')}}</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-main bg-[rgba(0,0,0,0.02)]">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Product')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Stock')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Reorder Point')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-main">
                @foreach($needs_reorder as $inv)
                <tr class="hover:bg-purple-50/20 transition-colors">
                    <td class="px-4 py-3 font-medium text-dark">{{ $inv->product?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700">{{ $inv->stock_count }}</span>
                    </td>
                    <td class="px-4 py-3 text-right text-muted">{{ $inv->reorder_point }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('tenant.admin.product.inventory.edit', $inv->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-primary text-white text-xs font-medium hover:bg-primary/80">
                            <i class="mdi mdi-pencil text-xs"></i> {{__('Restock')}}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Out of Stock --}}
<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-package-variant-closed-remove text-red-500 text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Out of Stock')}}</h3>
            <p class="text-xs text-muted">{{__('These products have 0 units available')}}</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-main bg-[rgba(0,0,0,0.02)]">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Product')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Stock')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Sold')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-main">
                @forelse($out_of_stock as $inv)
                <tr class="hover:bg-red-50/30 transition-colors">
                    <td class="px-4 py-3">
                        <div class="font-medium text-dark">{{ $inv->product?->name ?? '—' }}</div>
                        <div class="text-xs text-muted">SKU: {{ $inv->sku ?? '—' }}</div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600">
                            {{ $inv->stock_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-muted text-xs">{{ $inv->sold_count ?? 0 }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('tenant.admin.product.inventory.edit', $inv->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-primary text-white text-xs font-medium hover:bg-primary/80 transition-colors">
                            <i class="mdi mdi-pencil text-xs"></i> {{__('Restock')}}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-muted text-sm">
                        <i class="mdi mdi-check-circle-outline text-green-500 text-2xl block mb-2"></i>
                        {{__('No out-of-stock products')}}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Low Stock --}}
<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-alert-circle-outline text-yellow-500 text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Low Stock')}}</h3>
            <p class="text-xs text-muted">{{__('Products with 1–:n units remaining', ['n' => $threshold])}}</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-main bg-[rgba(0,0,0,0.02)]">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">{{__('Product')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Stock')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Sold')}}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-main">
                @forelse($low_stock as $inv)
                @php $pct = $inv->stock_count / max($threshold, 1); @endphp
                <tr class="hover:bg-yellow-50/30 transition-colors">
                    <td class="px-4 py-3">
                        <div class="font-medium text-dark">{{ $inv->product?->name ?? '—' }}</div>
                        <div class="text-xs text-muted">SKU: {{ $inv->sku ?? '—' }}</div>
                        <div class="mt-1 w-32 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $pct <= 0.3 ? 'bg-red-400' : 'bg-yellow-400' }}"
                                 style="width: {{ min(100, $pct * 100) }}%"></div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                            {{ $inv->stock_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-muted text-xs">{{ $inv->sold_count ?? 0 }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('tenant.admin.product.inventory.edit', $inv->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-primary text-white text-xs font-medium hover:bg-primary/80 transition-colors">
                            <i class="mdi mdi-pencil text-xs"></i> {{__('Restock')}}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-muted text-sm">
                        <i class="mdi mdi-check-circle-outline text-green-500 text-2xl block mb-2"></i>
                        {{__('All products are well-stocked')}}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
