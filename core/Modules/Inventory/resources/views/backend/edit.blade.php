@extends('tenant.admin.admin-master')
@section('title')
    {{__('Product Inventory - ').$product->name}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{ global_asset('assets/landlord/admin/css/nice-select.css') }}">
    <x-product::variant-info.css />
@endsection

@php
    $inventory_details = true;
@endphp

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    {{-- ========== LEFT COLUMN ========== --}}
    <div class="xl:col-span-8 space-y-6">

        {{-- Header Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="px-5 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-package-variant text-primary text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Product')}} <span class="text-primary">#{{$product->id}}</span></h3>
                        <p class="text-xs text-muted">{{$product->updated_at?->format('d M Y, h:i A') ?? ''}}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{dynamicRoute($product->slug)}}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-dark border border-main hover:border-primary hover:text-primary transition">
                        <i class="mdi mdi-open-in-new text-sm"></i> {{__('View Live')}}
                    </a>
                    <a href="{{route('tenant.admin.product.edit',$product->id)}}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-dark border border-main hover:border-primary hover:text-primary transition">
                        <i class="mdi mdi-pencil-outline text-sm"></i> {{__('Edit Product')}}
                    </a>
                </div>
            </div>
        </div>

        {{-- Product Overview Card --}}
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="grid grid-cols-1 sm:grid-cols-2">
                <div class="px-5 sm:px-6 py-5 border-b sm:border-b-0 sm:border-r border-main">
                    <span class="text-[10px] font-bold text-primary uppercase tracking-widest">{{__('Price')}}</span>
                    <p class="text-xl font-bold text-dark mt-1">{{ amount_with_currency_symbol($product->price) }}</p>
                    @if($product->sale_price)
                        <p class="text-xs text-muted mt-0.5">{{__('Sale')}}: <del>{{ amount_with_currency_symbol($product->sale_price) }}</del></p>
                    @endif
                </div>
                <div class="px-5 sm:px-6 py-5">
                    <span class="text-[10px] font-bold text-primary uppercase tracking-widest">{{__('Category')}}</span>
                    <p class="text-base font-bold text-dark mt-1">{{ $product->category?->name ?? __('N/A') }}</p>
                    @if($product->subCategory)
                        <p class="text-xs text-muted mt-0.5">{{__('Sub')}}: <span class="font-semibold text-dark">{{ $product->subCategory->name }}</span></p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Inventory Form --}}
        <form action="{{ route('tenant.admin.product.inventory.update') }}" method="POST" id="update-inventory-form">
            @csrf
            <input value="{{ $product->id }}" name="product_id" type="hidden">

            {{-- Inventory Details Card --}}
            <div class="bg-surface rounded-xl shadow-main border border-main mb-6">
                <div class="px-5 sm:px-6 py-4 border-b border-main">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-cube-outline text-warning text-sm"></i>
                        </div>
                        <span class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Inventory Details')}}</span>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <x-product::product-inventory :inventory_page="false" :units="$data['units']" :inventory="$product?->inventory" :uom="$product?->uom"/>
                </div>
            </div>

            {{-- Variants & Attributes Card --}}
            @can('product-category-edit')
                <div class="bg-surface rounded-xl shadow-main border border-main mb-6">
                    <div class="px-5 sm:px-6 py-4 border-b border-main">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                                <i class="mdi mdi-tag-multiple-outline text-info text-sm"></i>
                            </div>
                            <span class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Variants & Attributes')}}</span>
                        </div>
                    </div>
                    <div class="p-5 sm:p-6">
                        <x-product::product-attribute
                                :inventorydetails="$inventory?->inventoryDetails" :colors="$product_colors"
                                :sizes="$product_sizes"
                                :allAttributes="$all_attributes"/>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-primary text-white text-sm font-bold hover:opacity-90 transition shadow-sm">
                        <i class="mdi mdi-check-circle-outline text-base"></i> {{__('Update Inventory')}}
                    </button>
                </div>
            @endcan
        </form>
    </div>

    {{-- ========== RIGHT SIDEBAR ========== --}}
    <div class="xl:col-span-4 space-y-6 xl:sticky xl:top-4 xl:self-start">

        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            {{-- Image --}}
            <div class="aspect-[4/3] bg-secondary overflow-hidden">
                {!! render_image_markup_by_attachment_id($product->image_id, 'w-full h-full object-cover', 'grid', false) !!}
            </div>

            {{-- Name --}}
            <div class="px-5 pt-4 pb-3 border-b border-main">
                <h4 class="text-sm font-bold text-dark font-urbanist leading-snug">{{ $product->name }}</h4>
            </div>

            {{-- Info Rows --}}
            <div class="divide-y divide-main text-xs">
                <div class="px-5 py-2.5 flex items-center justify-between">
                    <span class="text-muted">{{__('ID')}}</span>
                    <span class="font-bold text-primary">#{{$product->id}}</span>
                </div>
                <div class="px-5 py-2.5 flex items-center justify-between">
                    <span class="text-muted">{{__('Price')}}</span>
                    <span class="font-bold text-dark">{{ amount_with_currency_symbol($product->price) }}</span>
                </div>
                @if($product->inventory?->sku)
                    <div class="px-5 py-2.5 flex items-center justify-between">
                        <span class="text-muted">{{__('SKU')}}</span>
                        <span class="font-bold text-dark">{{$product->inventory->sku}}</span>
                    </div>
                @endif
                @if($product->inventory)
                    <div class="px-5 py-2.5 flex items-center justify-between gap-3">
                        <span class="text-muted">{{__('Stock')}}</span>
                        <div class="flex items-center gap-2">
                            <span id="current_stock_display" class="font-bold {{ $product->inventory->stock_count > 0 ? 'text-success' : 'text-danger' }}">{{$product->inventory->stock_count ?? 0}}</span>
                            <button type="button" onclick="document.getElementById('adjust_panel').classList.toggle('hidden')"
                                    class="px-2 py-0.5 rounded text-xs bg-primary-soft text-primary font-medium">
                                {{__('Adjust')}}
                            </button>
                        </div>
                    </div>
                    {{-- S20 T5: Manual adjustment panel --}}
                    <div id="adjust_panel" class="hidden px-5 pb-3 border-t border-main pt-3">
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="text-xs text-muted mb-1 block">{{__('Change (±)')}}</label>
                                <input type="number" id="adj_qty" class="w-full border border-main rounded-lg px-3 py-1.5 text-sm text-dark bg-surface" placeholder="+10 or -5">
                            </div>
                            <div>
                                <label class="text-xs text-muted mb-1 block">{{__('Reason')}}</label>
                                <select id="adj_reason" class="w-full border border-main rounded-lg px-3 py-1.5 text-sm text-dark bg-surface">
                                    <option value="manual_add">{{__('Manual Add')}}</option>
                                    <option value="damaged">{{__('Damaged')}}</option>
                                    <option value="theft">{{__('Theft')}}</option>
                                    <option value="correction">{{__('Correction')}}</option>
                                </select>
                            </div>
                        </div>
                        <input type="text" id="adj_note" class="w-full border border-main rounded-lg px-3 py-1.5 text-sm text-dark bg-surface mb-2" placeholder="{{__('Note (optional)')}}">
                        <button type="button" onclick="submitAdjustment({{ $product->inventory->id }})"
                                class="w-full px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold">
                            {{__('Save Adjustment')}}
                        </button>
                    </div>
                @endif
                {{-- S21 T5: Reorder point --}}
                @if($product->inventory)
                <div class="px-5 py-2.5 flex items-center justify-between gap-3 border-t border-main">
                    <span class="text-muted text-xs">{{__('Reorder Point')}}</span>
                    <div class="flex items-center gap-2">
                        <input type="number" id="reorder_point_input" min="0"
                               class="w-16 border border-main rounded px-2 py-0.5 text-xs text-dark bg-surface"
                               value="{{ $product->inventory->reorder_point ?? 0 }}">
                        <button type="button" onclick="saveReorderPoint({{ $product->inventory->id }})"
                                class="px-2 py-0.5 rounded text-xs bg-primary text-white">{{__('Set')}}</button>
                    </div>
                </div>
                @endif
                @if($product->category)
                    <div class="px-5 py-2.5 flex items-center justify-between">
                        <span class="text-muted">{{__('Category')}}</span>
                        <span class="font-bold text-dark">{{$product->category->name}}</span>
                    </div>
                @endif
                <div class="px-5 py-2.5 flex items-center justify-between">
                    <span class="text-muted">{{__('Updated')}}</span>
                    <span class="font-bold text-dark">{{$product->updated_at?->format('d M Y') ?? __('N/A')}}</span>
                </div>
            </div>

            {{-- Child Categories --}}
            @if($product->childCategory && $product->childCategory->count())
                <div class="px-5 py-3 border-t border-main flex flex-wrap gap-1.5">
                    @foreach($product->childCategory as $childCategory)
                        <span class="px-2 py-0.5 rounded-md bg-primary-soft text-primary text-[10px] font-semibold">{{ $childCategory->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>

<x-media-upload.tw-markup/>
@endsection

@section('scripts')
    <x-product::variant-info.js :colors="$product_colors" :sizes="$product_sizes"
                                :all-attributes="$all_attributes"/>
    <x-datatable.tw-js />
    <x-table.btn.swal.js />
    <script src="{{ global_asset('assets/landlord/admin/js/jquery.nice-select.min.js') }}"></script>
    <x-media-upload.tw-js/>
    <x-product::product-js/>
    <script>
        (function ($) {
            'use strict'

            // Remove inventory item
            $(document).on('click', '.repeater_button .remove', function () {
                if ($('.repeater_button .remove').length > 0) {
                    $(this).closest('.inventory_item').remove();
                }
            });

            // Remove selected attribute
            $(document).on('click', '.remove_details_attribute', function (e) {
                $(this).parent().parent().remove();
            });

            function send_ajax_request(request_type, request_data, url, before_send, success_response, errors) {
                $.ajax({
                    url: url,
                    type: request_type,
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}",
                    },
                    beforeSend: (typeof before_send !== "undefined" && typeof before_send === "function") ? before_send : () => {
                        return "";
                    },
                    processData: false,
                    contentType: false,
                    data: request_data,
                    success: (typeof success_response !== "undefined" && typeof success_response === "function") ? success_response : () => {
                        return "";
                    },
                    error: (typeof errors !== "undefined" && typeof errors === "function") ? errors : () => {
                        return "";
                    }
                });
            }

            $(document).on("submit", "#update-inventory-form", function (e) {
                e.preventDefault();
                let data = new FormData(e.target);

                send_ajax_request("post", data, '{{ route('tenant.admin.product.inventory.update') }}', function () {

                }, function (data) {
                    if(data.type == 'success'){
                        toastr.success(data.msg);
                    }else{
                        toastr.error(data.msg);
                    }

                }, function () {

                });
            });

            $(document).ready(function () {
                let nice_select_el = $('.nice-select');
                if (nice_select_el.length > 0) {
                    nice_select_el.niceSelect();
                }
            });
        })(jQuery)
    </script>
    <script>
    function saveReorderPoint(inventoryId) {
        var val = parseInt($('#reorder_point_input').val());
        if (isNaN(val) || val < 0) { toastr.error('{{ __("Enter a valid number") }}'); return; }
        $.ajax({
            url: '{{ route("tenant.admin.product.inventory.adjust") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', inventory_id: inventoryId, qty_change: 0, reason: 'correction', note: 'reorder_point:' + val, reorder_point: val },
            success: function () { toastr.success('{{ __("Reorder point saved") }}'); },
            error: function () { toastr.error('{{ __("Save failed") }}'); }
        });
    }
    function submitAdjustment(inventoryId) {
        var qty = parseInt($('#adj_qty').val());
        if (isNaN(qty) || qty === 0) { toastr.error('{{ __("Enter a non-zero quantity change") }}'); return; }
        $.ajax({
            url: '{{ route("tenant.admin.product.inventory.adjust") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                inventory_id: inventoryId,
                qty_change: qty,
                reason: $('#adj_reason').val(),
                note: $('#adj_note').val(),
            },
            success: function (res) {
                toastr.success(res.msg);
                var el = $('#current_stock_display');
                el.text(res.new_stock);
                el.removeClass('text-success text-danger').addClass(res.new_stock > 0 ? 'text-success' : 'text-danger');
                $('#adjust_panel').addClass('hidden');
                $('#adj_qty, #adj_note').val('');
            },
            error: function () { toastr.error('{{ __("Adjustment failed") }}'); }
        });
    }
    </script>
@endsection
