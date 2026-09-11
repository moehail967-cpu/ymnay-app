@extends('tenant.admin.admin-master')
@section('title')
    {{__(ucfirst($page_title).' '.'Sales Report')}}
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(252,79,0,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-cart-outline text-xl text-[#FC4F00]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Number of Sales')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{$total_report['total_sale']}}</h3>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(0,121,255,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-cash-multiple text-xl text-[#0079FF]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Total Revenue')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{amount_with_currency_symbol($total_report['total_revenue'])}}</h3>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(34,166,153,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-trending-up text-xl text-[#22A699]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Total Profit')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{amount_with_currency_symbol($total_report['total_profit'])}}</h3>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main border border-main p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-[rgba(143,67,238,0.1)] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-currency-usd text-xl text-[#8F43EE]"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-muted truncate">{{__('Total Cost')}}</p>
            <h3 class="text-lg font-bold text-dark font-urbanist">{{amount_with_currency_symbol($total_report['total_cost'])}}</h3>
        </div>
    </div>
</div>

{{-- Product Sales Table --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-table-large text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__(ucfirst($page_title).' '.'Sales Report')}}</h3>
            <p class="text-xs text-muted">{{__('Detailed product sales breakdown')}}</p>
        </div>
    </div>

    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Type')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Product')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Qty')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Cost')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Price')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Profit')}}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products['items'] ?? [] as $product)
                @foreach($product ?? [] as $item)
                    <tr class="border-b border-main hover:bg-muted transition-colors">
                        <td class="px-4 py-3.5"><span class="text-[11px] font-bold text-primary">{{__('#')}} {{$item['product_id']}}</span></td>
                        <td class="px-4 py-3.5"><span class="text-xs text-muted">{{$item['sale_date']->format('d M Y')}}</span></td>
                        <td class="px-4 py-3.5">
                            <span class="tw-pill tw-pill-info">{{\App\Enums\ProductTypeEnum::getText($item['product_type'])}}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-semibold text-dark">{{$item['name']}}</span>
                            @if(!empty($item['variant']))
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @if(!empty($item['variant']['color']))
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-secondary text-[10px] text-muted">{{$item['variant']['color']}}</span>
                                    @endif
                                    @if(!empty($item['variant']['size']))
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-secondary text-[10px] text-muted">{{$item['variant']['size']}}</span>
                                    @endif
                                    @foreach($item['variant']['attributes'] as $attr_name => $attr_val)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-secondary text-[10px] text-muted">{{$attr_name}}: {{$attr_val}}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{$item['qty']}}</span></td>
                        <td class="px-4 py-3.5"><span class="text-sm text-muted">{{amount_with_currency_symbol($item['cost'])}}</span></td>
                        <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{amount_with_currency_symbol($item['price'])}}</span></td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-bold {{ $item['profit'] >= 0 ? 'text-success' : 'text-danger' }}">{{amount_with_currency_symbol($item['profit'])}}</span>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td class="px-4 py-8 text-center text-sm text-muted" colspan="8">{{__('No Data Available')}}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if(!empty($products['links']) && count($products['links']) > 2)
        <div class="px-4 sm:px-6 py-4 border-t border-main flex items-center justify-center gap-1">
            @foreach($products["links"] as $link)
                @php if($loop->iteration == 1) continue; @endphp
                <a href="{{ $link }}"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-semibold transition {{ ($loop->iteration - 1) == $products["current_page"] ? 'bg-primary text-white' : 'bg-secondary border border-main text-dark hover:bg-primary-soft hover:text-primary' }}">
                    {{ $loop->iteration - 1 }}
                </a>
            @endforeach
        </div>
    @endif
</div>

@endsection
