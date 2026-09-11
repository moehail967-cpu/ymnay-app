@extends('tenant.admin.admin-master')

@section('title') {{ __('Tier Pricing') }} @endsection
@section('page-title') {{ __('Tier Pricing') }} @endsection

@section('content')
<div class="col-12">

    <x-msg.flash />
    <x-msg.error />

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Tier / Bulk Pricing') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Products with quantity-based price tiers configured.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tenant.admin.tier-pricing.settings') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                <i class="mdi mdi-cog-outline text-base"></i>
                {{ __('Settings') }}
            </a>
        </div>
    </div>

    {{-- Quick add tier to a product --}}
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5 flex flex-wrap items-center gap-3">
        <i class="mdi mdi-information-outline text-blue-500 text-xl flex-shrink-0"></i>
        <span class="text-sm text-blue-700 flex-1">{{ __('Set up bulk pricing for any product by selecting it below.') }}</span>
        <form action="" method="GET" class="flex items-center gap-2 flex-shrink-0" id="goto-product-form">
            <select id="goto-product-select" name="product_id"
                    class="border border-blue-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white min-w-[200px]">
                <option value="">{{ __('Select a product…') }}</option>
                @foreach($allProducts as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            <button type="button" id="goto-btn"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                <i class="mdi mdi-pencil-outline text-base"></i>
                {{ __('Set Tiers') }}
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-layers-triple-outline text-blue-500 text-base"></i>
            </div>
            <h3 class="text-sm font-bold text-dark">{{ __('Products with Active Tiers') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Product') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Base Price') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Tiers') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-semibold text-dark">{{ $product->name }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm text-gray-600">{{ amount_with_currency_symbol($product->regular_price) }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($product->tiers as $tier)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-xs font-semibold">
                                        {{ $tier->min_qty }}+:
                                        @if($tier->discount_type === 'percentage') {{ $tier->discount_value }}%
                                        @elseif($tier->discount_type === 'flat') -{{ amount_with_currency_symbol($tier->discount_value) }}
                                        @else {{ amount_with_currency_symbol($tier->discount_value) }}
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tenant.admin.tier-pricing.manage', $product->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold hover:bg-gray-200 transition">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                    {{ __('Edit') }}
                                </a>
                                <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-semibold hover:bg-red-100 transition swal-delete"
                                        data-route="{{ route('tenant.admin.tier-pricing.delete', $product->id) }}"
                                        data-title="{{ __('Remove all tiers?') }}"
                                        data-text="{{ __('This will remove all quantity tiers for this product.') }}">
                                    <i class="mdi mdi-trash-can-outline text-sm"></i>
                                    {{ __('Remove') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-400">
                            <i class="mdi mdi-layers-triple-outline text-3xl block mb-2 text-gray-300"></i>
                            {{ __('No tier pricing configured yet. Select a product above to get started.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
document.getElementById('goto-btn').addEventListener('click', function () {
    var pid = document.getElementById('goto-product-select').value;
    if (! pid) { return; }
    window.location.href = '{{ url(route_prefix("admin") . "/tier-pricing/manage/") }}/' + pid;
});

$(document).on('click', '.swal-delete', function () {
    var route = $(this).data('route');
    var title = $(this).data('title') || '{{ __("Are you sure?") }}';
    var text  = $(this).data('text')  || '';

    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __("Yes, remove") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then(function (result) {
        if (result.isConfirmed) {
            $.post(route, {_token: '{{ csrf_token() }}'}, function () {
                location.reload();
            });
        }
    });
});
</script>
@endsection
