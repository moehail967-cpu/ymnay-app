@extends(route_prefix().'admin.admin-master')
@section('title') {{__('WooCommerce Product Import')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-store-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Available Products')}}</h3>
                <p class="text-xs text-muted">{{__('Simple products from your WooCommerce store. Variable product support coming soon.')}}</p>
            </div>
        </div>
        <button type="button" id="import_all_btn"
                data-route="{{route('tenant.admin.woocommerce.import.all')}}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
            <i class="mdi mdi-cloud-download-outline text-base"></i>
            {{__('Import All')}}
        </button>
    </div>

    {{-- Bulk Action --}}
    @can('product-category-delete')
    <div class="px-4 sm:px-6 py-3 border-b border-main flex items-center gap-3">
        <select name="bulk_option" id="bulk_option"
                class="text-xs bg-secondary border border-main rounded-lg px-3 py-1.5 text-dark focus:border-primary focus:outline-none transition">
            <option value="">{{__('Bulk Action')}}</option>
            <option value="import">{{__('Import')}}</option>
        </select>
        <button type="button"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition"
                id="bulk_import_btn">
            {{__('Apply')}}
        </button>
    </div>
    @endcan

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="wooProductsTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <input type="checkbox" class="all-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-16">{{__('Image')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Product')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort">{{__('Summary')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_products ?? [] as $key => $product)
                @continue(count($product->variations) > 0)
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- Checkbox --}}
                    <td class="px-4 py-3.5">
                        <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-primary focus:ring-primary" name="bulk_delete[]" value="{{$product->id}}">
                    </td>

                    {{-- Image --}}
                    <td class="px-4 py-3.5">
                        @php
                            $image = current($product->images);
                            $image_url = !empty($image) ? $image->src : '';
                        @endphp
                        @if($image_url)
                            <img src="{{$image_url}}" alt="{{$product->name}}" loading="lazy"
                                 class="w-12 h-12 rounded-lg object-cover border border-main">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-secondary border border-main flex items-center justify-center">
                                <i class="mdi mdi-image-off-outline text-muted text-lg"></i>
                            </div>
                        @endif
                    </td>

                    {{-- Product Name --}}
                    <td class="px-4 py-3.5">
                        <div>
                            <span class="text-sm font-semibold text-dark block">{{$product->name}}</span>
                            <a href="{{$product->permalink}}" target="_blank"
                               class="text-[11px] text-primary hover:underline truncate block max-w-xs">
                                {{$product->permalink}}
                            </a>
                        </div>
                    </td>

                    {{-- Summary --}}
                    <td class="px-4 py-3.5">
                        <p class="text-xs text-muted line-clamp-2">{!! preg_replace("/\r|\n/", "", $product->short_description) !!}</p>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Import Loader Overlay --}}
<div id="import_loader" class="hidden fixed inset-0 z-[999] bg-black/50 backdrop-blur-sm flex items-center justify-center">
    <div class="bg-surface rounded-2xl shadow-main border border-main px-8 py-6 text-center">
        <div class="w-12 h-12 rounded-full bg-primary-soft mx-auto mb-4 flex items-center justify-center">
            <i class="mdi mdi-loading mdi-spin text-primary text-2xl"></i>
        </div>
        <h4 class="text-sm font-bold text-dark font-urbanist mb-1">{{__('Importing Products')}}</h4>
        <p class="text-xs text-muted">{{__('Please wait while we import your products...')}}</p>
    </div>
</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>

    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {

            // ── DataTable init ────────────────────────────────────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#wooProductsTable')) {
                $('#wooProductsTable').DataTable({
                    "order": [[2, "asc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── Select All Checkbox ──────────────────────────────────
            $(document).on('change', '.all-checkbox', function () {
                var checked = $(this).is(':checked');
                $('.bulk-checkbox').prop('checked', checked);
            });

            // ── Import All ───────────────────────────────────────────
            $(document).on('click', '#import_all_btn', function (e) {
                e.preventDefault();
                var btn = $(this);
                var route = btn.data('route');

                Swal.fire({
                    title: '{{ __("Do you want to import all products from WooCommerce?") }}',
                    text: '{{ __("This will import all simple products from your store.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1F51FF',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: '{{ __("Yes, Import All") }}',
                    cancelButtonText: '{{ __("Cancel") }}',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loader
                        $('#import_loader').removeClass('hidden');
                        btn.prop('disabled', true).css('opacity', '0.5');

                        $.get(route).then(function (data) {
                            $('#import_loader').addClass('hidden');
                            Swal.fire('{{ __("Imported Successfully!") }}', '', 'success');
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        }).fail(function () {
                            $('#import_loader').addClass('hidden');
                            btn.prop('disabled', false).css('opacity', '1');
                            toastr.error('{{ __("Something went wrong") }}');
                        });
                    }
                });
            });

            // ── Bulk Import ──────────────────────────────────────────
            $(document).on('click', '#bulk_import_btn', function (e) {
                e.preventDefault();
                var bulkOption = $('#bulk_option').val();
                var allChecked = $('.bulk-checkbox:checked');
                var allIds = [];

                allChecked.each(function () {
                    allIds.push($(this).val());
                });

                if (allIds.length === 0) {
                    toastr.warning('{{ __("Please select at least one product") }}');
                    return;
                }

                if (bulkOption === 'import') {
                    var btn = $(this);
                    btn.text('{{ __("Importing...") }}').prop('disabled', true);

                    $.ajax({
                        type: 'POST',
                        url: '{{ route("tenant.admin.woocommerce.import.selective") }}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: allIds,
                        },
                        success: function (data) {
                            if (data.type === 'success') {
                                toastr.success(data.msg);
                            } else {
                                toastr.error(data.msg);
                            }
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        },
                        error: function (data) {
                            btn.text('{{ __("Apply") }}').prop('disabled', false);
                            if (data.status === 550) {
                                var errorResponse = data.responseJSON;
                                $.each(errorResponse, function (index, value) {
                                    toastr.error(value);
                                });
                            } else {
                                toastr.error('{{ __("Something went wrong") }}');
                            }
                        }
                    });
                }
            });

        });
    })(jQuery);
    </script>
@endsection
