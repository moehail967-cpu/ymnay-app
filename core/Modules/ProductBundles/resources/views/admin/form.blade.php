@extends('tenant.admin.admin-master')

@php $editing = isset($bundle); @endphp

@section('title') {{ $editing ? __('Edit Bundle') : __('Add Bundle') }} @endsection
@section('page-title') {{ $editing ? __('Edit Bundle') : __('Add Bundle') }} @endsection

@section('content')
<div class="col-12">

    <x-msg.flash />
    <x-msg.error />

    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('tenant.admin.product-bundles.index') }}"
           class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
            <i class="mdi mdi-arrow-left text-base"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-dark">{{ $editing ? __('Edit Bundle') : __('Create Bundle') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Bundle 2–5 products. Discount applies automatically when all are in the cart.') }}</p>
        </div>
    </div>

    <form action="{{ $editing ? route('tenant.admin.product-bundles.update', $bundle->id) : route('tenant.admin.product-bundles.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <div class="lg:col-span-2 space-y-4">

                {{-- Basic Info --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                    <h3 class="text-sm font-bold text-dark">{{ __('Bundle Details') }}</h3>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">{{ __('Bundle Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $bundle->name ?? '') }}"
                               placeholder="{{ __('e.g. Starter Pack') }}"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400" required>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">{{ __('Description') }}</label>
                        <textarea name="description" rows="2"
                                  placeholder="{{ __('Short description shown to customers…') }}"
                                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">{{ old('description', $bundle->description ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Discount --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-dark mb-4">{{ __('Discount') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">{{ __('Discount Type') }}</label>
                            <select name="discount_type"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                                <option value="percentage" {{ old('discount_type', $bundle->discount_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>{{ __('Percentage (%)') }}</option>
                                <option value="flat"       {{ old('discount_type', $bundle->discount_type ?? '') === 'flat' ? 'selected' : '' }}>{{ __('Flat Amount') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">{{ __('Discount Value') }}</label>
                            <input type="number" name="discount_value" value="{{ old('discount_value', $bundle->discount_value ?? 0) }}"
                                   min="0" step="0.01"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400" required>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">{{ __('Percentage discount applies to the combined subtotal of bundle items in the cart.') }}</p>
                </div>

                {{-- Products --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-dark mb-1">{{ __('Bundle Products') }}</h3>
                    <p class="text-xs text-gray-400 mb-4">{{ __('Select 2 to 5 products. The discount applies when all are in the cart simultaneously.') }}</p>

                    <div class="border border-gray-200 rounded-xl overflow-hidden max-h-72 overflow-y-auto">
                        @foreach($products as $product)
                            <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0">
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                       {{ in_array($product->id, old('product_ids', $bundle->product_ids ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-400 product-check">
                                <span class="text-sm text-dark">{{ $product->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div id="selection-count" class="text-xs text-gray-400 mt-2"></div>
                    @error('product_ids') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">

                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h4 class="text-sm font-bold text-dark mb-3">{{ __('Status') }}</h4>
                    <select name="status"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                        <option value="active"   {{ old('status', $bundle->status ?? 'active') === 'active'   ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ old('status', $bundle->status ?? '') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>

                <div class="bg-violet-50 rounded-xl border border-violet-100 p-5">
                    <h4 class="text-sm font-bold text-violet-800 mb-3">
                        <i class="mdi mdi-information-outline mr-1"></i>{{ __('How Bundles Work') }}
                    </h4>
                    <ol class="text-xs text-violet-700 space-y-2 list-decimal list-inside">
                        <li>{{ __('Customer adds all bundle products to their cart.') }}</li>
                        <li>{{ __('The discount is automatically applied at checkout.') }}</li>
                        <li>{{ __('Bundle card appears on each product detail page.') }}</li>
                    </ol>
                </div>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i>
                    {{ $editing ? __('Update Bundle') : __('Create Bundle') }}
                </button>

            </div>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
(function () {
    var checks = document.querySelectorAll('.product-check');
    var counter = document.getElementById('selection-count');

    function updateCount() {
        var checked = document.querySelectorAll('.product-check:checked').length;
        if (checked === 0) {
            counter.textContent = '';
        } else if (checked < 2) {
            counter.textContent = '{{ __("Select at least 2 products.") }}';
            counter.style.color = '#ef4444';
        } else if (checked > 5) {
            counter.textContent = '{{ __("Maximum 5 products allowed.") }}';
            counter.style.color = '#ef4444';
        } else {
            counter.textContent = checked + ' {{ __("products selected") }}';
            counter.style.color = '#6b7280';
        }
    }

    checks.forEach(function (c) { c.addEventListener('change', updateCount); });
    updateCount();
}());
</script>
@endsection
