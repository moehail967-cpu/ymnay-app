@extends('tenant.admin.admin-master')

@section('title') {{ __('Product Bundles') }} @endsection
@section('page-title') {{ __('Product Bundles') }} @endsection

@section('content')
<div class="col-12">

    <x-msg.flash />
    <x-msg.error />

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Product Bundles') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Bundle products together and offer automatic cart discounts.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tenant.admin.product-bundles.settings') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                <i class="mdi mdi-cog-outline text-base"></i>
                {{ __('Settings') }}
            </a>
            <a href="{{ route('tenant.admin.product-bundles.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                <i class="mdi mdi-plus text-base"></i>
                {{ __('Add Bundle') }}
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-package-variant-closed text-violet-500 text-base"></i>
            </div>
            <h3 class="text-sm font-bold text-dark">{{ __('All Bundles') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Bundle') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Discount') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Products') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Sold') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bundles as $bundle)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5">
                            <div class="text-sm font-semibold text-dark">{{ $bundle->name }}</div>
                            @if($bundle->description)
                                <div class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $bundle->description }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-violet-50 text-violet-700 text-xs font-bold">
                                <i class="mdi mdi-tag-outline text-sm"></i>
                                @if($bundle->discount_type === 'percentage')
                                    {{ $bundle->discount_value }}%
                                @else
                                    {{ amount_with_currency_symbol($bundle->discount_value) }}
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm text-gray-600">{{ count($bundle->product_ids) }} {{ __('items') }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-semibold text-dark">{{ number_format($bundle->sold_count) }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($bundle->status === 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold text-green-700 bg-green-100">{{ __('Active') }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold text-gray-600 bg-gray-100">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tenant.admin.product-bundles.edit', $bundle->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold hover:bg-gray-200 transition">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                    {{ __('Edit') }}
                                </a>
                                <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-semibold hover:bg-red-100 transition swal-delete"
                                        data-route="{{ route('tenant.admin.product-bundles.destroy', $bundle->id) }}"
                                        data-title="{{ __('Delete Bundle?') }}"
                                        data-text="{{ __('This will permanently remove the bundle.') }}">
                                    <i class="mdi mdi-trash-can-outline text-sm"></i>
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">
                            <i class="mdi mdi-package-variant-closed text-3xl block mb-2 text-gray-300"></i>
                            {{ __('No bundles yet. Create your first bundle!') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($bundles->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $bundles->links() }}
        </div>
        @endif
    </div>

</div>

@endsection

@section('scripts')
<script>
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
        confirmButtonText: '{{ __("Yes, delete it") }}',
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
