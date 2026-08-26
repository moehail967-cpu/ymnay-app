@extends('tenant.admin.admin-master')

@section('title') {{__('All Product Colors')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

@php
    function hexToRgb($hex): array
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 4));
        $b = hexdec(substr($hex, 4, 6));
        return ['r' => $r, 'g' => $g, 'b' => $b];
    }

    function calculateLuminance($rgb): float
    {
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    function isWhitish($hex, $threshold = 0.95): bool
    {
        $rgb = hexToRgb($hex);
        $luminance = calculateLuminance($rgb);
        return $luminance >= $threshold;
    }
@endphp

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    {{-- Table Card (7 cols) --}}
    <div class="xl:col-span-7">
        <div class="bg-surface rounded-xl shadow-main border border-main">

            {{-- Card Header --}}
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-palette-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Product Colors')}}</h3>
                        <p class="text-xs text-muted">{{__('Manage product color options')}}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @can('product-color-delete')
                        <x-bulk-action permissions="product-color-delete"/>
                    @endcan
                </div>
            </div>

            {{-- Table --}}
            <div class="tw-table-wrap">
                <table class="w-full text-left" id="colorTable">
                    <thead>
                        <tr class="border-b border-main">
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                                <div class="mark-all-checkbox">
                                    <input type="checkbox" class="all-checkbox">
                                </div>
                            </th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Color')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest hidden md:table-cell">{{__('Slug')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($product_colors as $product_color)
                        <tr class="border-b border-main hover:bg-muted transition-colors">
                            <td class="px-4 py-3.5">
                                <x-bulk-delete-checkbox :id="$product_color->id"/>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-bold text-primary">#{{$product_color->id}}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-semibold text-dark">{{ $product_color->name }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="tw-color-swatch" style="background-color: {{$product_color->color_code}}; border: {{isWhitish($product_color->color_code) ? '1px solid var(--color-border-main)' : 'none'}};"></span>
                                    <span class="text-xs font-mono text-muted">{{ $product_color->color_code }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="text-sm text-muted">{{ $product_color->slug }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('product-color-edit')
                                        <button type="button"
                                                class="tw-btn-icon tw-btn-icon-edit color_edit_btn"
                                                title="{{__('Edit')}}"
                                                data-id="{{ $product_color->id }}"
                                                data-name="{{ $product_color->name }}"
                                                data-color_code="{{ $product_color->color_code }}"
                                                data-slug="{{ $product_color->slug }}">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                    @endcan
                                    @can('product-color-delete')
                                        <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                                data-route="{{ route('tenant.admin.product.colors.delete', $product_color->id) }}"
                                                title="{{__('Delete')}}">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Form Card (5 cols) --}}
    @can('product-color-create')
    <div class="xl:col-span-5">
        <div class="bg-surface rounded-xl shadow-main border border-main sticky top-4">
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('New Color')}}</h3>
                        <p class="text-xs text-muted">{{__('Create a new product color')}}</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('tenant.admin.product.colors.new') }}" method="POST">
                @csrf
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="name" placeholder="{{__('Color Name')}}" class="lnd-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Color Code')}} <span class="text-danger">*</span></label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="color_code" id="create_color_code" value="#000000" class="tw-color-input">
                            <input type="text" id="create_color_hex" value="#000000" class="lnd-input flex-1 font-mono text-sm" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Slug')}}</label>
                        <input type="text" name="slug" placeholder="{{__('Slug')}}" class="lnd-input">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Color')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan
</div>

{{-- Edit Modal --}}
@can('product-color-edit')
<div id="color_edit_modal" class="fixed inset-0 z-[999] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('color_edit_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-xl shadow-xl border border-main w-full max-w-md">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-main">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-palette-outline text-primary text-sm"></i>
                    </div>
                    <h5 class="text-sm font-bold text-dark">{{__('Edit Color')}}</h5>
                </div>
                <button type="button" onclick="closeModal('color_edit_modal')" class="text-muted hover:text-danger transition">
                    <i class="mdi mdi-close text-lg"></i>
                </button>
            </div>
            <form action="{{route('tenant.admin.product.colors.update')}}" method="post">
                @csrf
                <input type="hidden" name="id" id="color_id">
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" placeholder="{{__('Name')}}" class="lnd-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Color Code')}} <span class="text-danger">*</span></label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="color_code" id="edit_color_code" class="tw-color-input">
                            <input type="text" id="edit_color_hex" class="lnd-input flex-1 font-mono text-sm" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Slug')}}</label>
                        <input type="text" name="slug" id="edit_slug" placeholder="{{__('Slug')}}" class="lnd-input">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-3.5 border-t border-main">
                    <button type="button" onclick="closeModal('color_edit_modal')" class="px-4 py-2 rounded-lg text-sm font-medium text-muted hover:bg-secondary transition">{{__('Close')}}</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:opacity-90 transition">{{__('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Delete Form (redirect-based) --}}
<form method="post" id="swal_delete_form" class="hidden">
    @csrf
</form>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    @can('product-color-delete')
        <x-bulk-action-js :url="route('tenant.admin.product.colors.bulk.action')"/>
    @endcan
    <script>
    (function ($) {
        "use strict";

        window.openModal = function (id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };
        window.closeModal = function (id) {
            document.getElementById(id).classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#colorTable')) {
                $('#colorTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // Sync color picker with hex display (create)
            $('#create_color_code').on('input change', function () {
                $('#create_color_hex').val($(this).val());
            });

            // Edit button
            $(document).on('click', '.color_edit_btn', function () {
                var el = $(this);
                $('#color_id').val(el.data('id'));
                $('#edit_name').val(el.data('name'));
                $('#edit_color_code').val(el.data('color_code'));
                $('#edit_color_hex').val(el.data('color_code'));
                $('#edit_slug').val(el.data('slug'));
                openModal('color_edit_modal');
            });

            // Sync color picker with hex display (edit)
            $('#edit_color_code').on('input change', function () {
                $('#edit_color_hex').val($(this).val());
            });

            // SweetAlert Delete (redirect-based)
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                var btn = $(this);
                var route = btn.data('route');
                var row = btn.closest('tr');

                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You will not be able to recover this!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{__("Yes, Delete it!")}}',
                    cancelButtonText: '{{__("Cancel")}}',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        var form = $('#swal_delete_form');
                        form.attr('action', route);
                        form.submit();
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
