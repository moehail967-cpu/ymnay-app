@extends('tenant.admin.admin-master')
@section('title')
    {{__('Product Inventory')}}
@endsection
@section('style')
    <x-datatable.tw-css/>
@endsection
@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-package-variant-closed text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Product Inventory')}}</h3>
                <p class="text-xs text-muted">{{__('Manage stock levels for all products')}}</p>
            </div>
        </div>
    </div>

    <div class="tw-table-wrap">
        <table class="w-full text-left" id="all_user_table">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('SKU')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Stock')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Sold')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_inventory_products as $inventory)
                @continue(!$inventory->product)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5"><span class="text-[11px] font-bold text-primary">{{__('#')}} {{ $loop->iteration }}</span></td>
                    <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{ $inventory?->product?->name }}</span></td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-secondary text-xs font-mono font-semibold text-dark">{{ $inventory->sku }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        @php $stock = $inventory->stock_count ?? 0; @endphp
                        @if($stock <= 0)
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-danger-soft text-danger text-[10px] font-bold uppercase">{{__('Out of Stock')}}</span>
                        @elseif($stock <= (int)get_static_option('stock_threshold_amount', 5))
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase">{{ $stock }}</span>
                        @else
                            <span class="text-sm font-semibold text-dark">{{ $stock }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5"><span class="text-sm text-muted">{{ $inventory->sold_count ?? 0 }}</span></td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            @can('inventory-edit')
                                <a href="{{ route('tenant.admin.product.inventory.edit', $inventory->id) }}" title="{{__('Edit')}}"
                                   class="w-9 h-9 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:text-white hover:bg-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
@endsection
