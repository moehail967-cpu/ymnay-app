@extends('tenant.admin.admin-master')
@section('title') {{__('Featured Product')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form action="{{ route('tenant.admin.featured.product.create') }}" method="post">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Main Form --}}
        <div class="lg:col-span-9">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-star-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Featured Product')}}</h3>
                        <p class="text-xs text-muted">{{__('Select products or a category to feature in the mobile app')}}</p>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">

                    {{-- Category Toggle --}}
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="category_toggle" name="category" class="sr-only peer" {{ optional($selectedProduct)->type == 'category' ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                        <span class="text-sm font-medium text-dark">{{__('Enable Category')}}</span>
                    </div>

                    {{-- Product Select --}}
                    <div id="product-list" {!! optional($selectedProduct)->type == 'category' ? 'class="hidden"' : '' !!}>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Select Products')}}</label>
                        <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <select name="featured_product[]" multiple
                                    class="w-full bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 min-h-[120px]">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ in_array($product->id, json_decode(optional($selectedProduct)->ids) ?? []) ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-1.5 text-[11px] text-muted">{{__('Hold Ctrl/Cmd to select multiple products')}}</p>
                    </div>

                    {{-- Category Select --}}
                    <div id="category-list" {!! optional($selectedProduct)->type != 'category' ? 'class="hidden"' : '' !!}>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Select Category')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-shape-outline text-lg text-primary"></i>
                            <select name="featured_category[]"
                                    class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 cursor-pointer">
                                <option value="">{{__('Select Category')}}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, json_decode(optional($selectedProduct)->ids) ?? []) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-3">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
                <div class="px-4 py-4 border-b border-main">
                    <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Actions')}}</h4>
                </div>
                <div class="p-4 space-y-5">
                    <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <i class="mdi mdi-information-outline text-info text-lg mt-0.5 shrink-0"></i>
                        <span class="text-[11px] text-dark leading-relaxed">{{__('Toggle "Enable Category" to feature a category instead of individual products.')}}</span>
                    </div>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Featured Product')}}
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>

@endsection

@section('scripts')
    <script>
    (function ($) {
        "use strict";
        $('#category_toggle').on('change', function () {
            if ($(this).is(':checked')) {
                $('#product-list').slideUp(300, function(){ $(this).addClass('hidden'); });
                $('#category-list').removeClass('hidden').hide().slideDown(300);
            } else {
                $('#category-list').slideUp(300, function(){ $(this).addClass('hidden'); });
                $('#product-list').removeClass('hidden').hide().slideDown(300);
            }
        });
    })(jQuery);
    </script>
@endsection
