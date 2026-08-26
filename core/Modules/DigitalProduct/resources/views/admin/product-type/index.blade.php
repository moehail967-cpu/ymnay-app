@extends('tenant.admin.admin-master')

@section('title') {{__('Digital Product Type')}} @endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/select2.min.css')}}">
    <x-datatable.tw-css/>

    <style>
        .select2-container {
            display: block;
            width: 100%;
            z-index: 1055;
        }
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            height: 50px;
            line-height: 50px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #444;
            line-height: 50px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
            position: absolute;
            top: 1px;
            right: 1px;
            width: 20px;
            line-height: 50px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #ddd transparent transparent transparent;
            top: 25px;
        }
    </style>
@endsection

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="bg-surface rounded-xl shadow-main border border-main">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-package-variant text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Digital Product Types')}}</h3>
                <p class="text-xs text-muted">{{__('Manage digital product types')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @can('product-category-delete')
                <x-bulk-action permissions="product-category-delete"/>
            @endcan
            @can('product-category-create')
                <button type="button" onclick="openModal('product_type_create_modal')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('New Product Type')}}
                </button>
            @endcan
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="productTypeTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Type')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Extensions')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Image')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($digital_product_types as $item)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <x-bulk-delete-checkbox :id="$item->id"/>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">#{{$item->id}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$item->name}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-dark">{{\App\Enums\DigitalProductTypeEnums::getText($item->product_type)}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-dark">{{str_replace(['[',']','"'], ' ', $item->extensions)}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="tw-thumb-wrap">
                            {!! render_image_markup_by_attachment_id($item->image_id ?? '') !!}
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="tw-pill {{ \App\Enums\StatusEnums::getText($item->status) === 'Publish' ? 'tw-pill-success' : 'tw-pill-warning' }}">
                            {{\App\Enums\StatusEnums::getText($item->status)}}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('product-category-edit')
                                @php
                                    $image = get_attachment_image_by_id($item->image_id);
                                    $img_path = !empty($image) ? $image['img_url'] : '';
                                @endphp
                                <button type="button"
                                        class="tw-btn-icon tw-btn-icon-edit product_type_edit_btn"
                                        title="{{__('Edit')}}"
                                        data-id="{{$item->id}}"
                                        data-name="{{$item->name}}"
                                        data-slug="{{$item->slug}}"
                                        data-product_type="{{$item->product_type}}"
                                        data-extensions="{{str_replace(['[',']','"'], ' ', $item->extensions)}}"
                                        data-all_extensions="{{json_encode($extensions[$item->product_type] ?? [])}}"
                                        data-status="{{$item->status}}"
                                        data-imageid="{{$item->image_id}}"
                                        data-image="{{$img_path}}">
                                    <i class="mdi mdi-pencil-outline"></i>
                                </button>
                            @endcan
                            @can('product-category-delete')
                                <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                        data-route="{{ route('tenant.admin.digital.product.type.delete', $item->id) }}"
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

{{-- Create Modal --}}
@can('digital-product-type-create')
<div id="product_type_create_modal" class="fixed inset-0 z-[800] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('product_type_create_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Create Product Type')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Add a new digital product type')}}</p>
                </div>
                <button type="button" onclick="closeModal('product_type_create_modal')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>
            <form action="{{ route('tenant.admin.digital.product.type.new') }}" method="post" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                @csrf
                <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="create-name" name="name" placeholder="{{__('Name')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Slug')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="create-slug" name="slug" placeholder="{{__('Slug')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Product Type')}} <span class="text-danger">*</span></label>
                        <select name="type" class="lnd-input product-type" id="create_product_type">
                            <option value="" selected disabled>{{__('Select a product type')}}</option>
                            @foreach($types as $index => $type)
                                <option value="{{ $index }}">{{ __($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Product Extension')}}</label>
                        <select name="extensions[]" class="lnd-input extensions select2" id="create_extensions" multiple>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status" class="lnd-input" id="create_status">
                            <option value="1">{{ __('Public') }}</option>
                            <option value="0">{{ __('Draft') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Image')}}</label>
                        <x-fields.tw-media-upload :name="'image_id'" :dimentions="'120x120'"/>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                    <button type="button" onclick="closeModal('product_type_create_modal')" class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                        <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add New')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Edit Modal --}}
@can('digital-product-type-edit')
<div id="product_type_edit_modal" class="fixed inset-0 z-[800] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('product_type_edit_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-pencil-outline text-primary text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Update Product Type')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Edit product type details')}}</p>
                </div>
                <button type="button" onclick="closeModal('product_type_edit_modal')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>
            <form action="{{ route('tenant.admin.digital.product.type.update') }}" method="post" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="id" id="product_type_id">
                <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="edit_name" name="name" placeholder="{{__('Name')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Slug')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="edit_slug" name="slug" placeholder="{{__('Slug')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Product Type')}} <span class="text-danger">*</span></label>
                        <select name="type" class="lnd-input product-type" id="edit_product_type">
                            @foreach($types as $index => $type)
                                <option value="{{ $index }}">{{ __($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Product Extension')}}</label>
                        <select name="extensions[]" class="lnd-input extensions select2" id="edit_extensions" multiple>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Image')}}</label>
                        <div class="tw-media-upload-wrapper" id="edit_image_id_section">
                            <div class="tw-img-wrap mb-3">
                                <div class="tw-attachment-preview"></div>
                            </div>
                            <input type="hidden" name="image_id" class="tw-media-id-input" value="">
                            <button type="button" class="tw-media-open-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:opacity-90 transition" data-target="edit_image_id_section">
                                <i class="ti tabler-photo text-base"></i> {{__('Upload Image')}}
                            </button>
                            <p class="mt-1 text-xs text-gray-400">{{__('Recommended')}}: 120x120</p>
                        </div>
                    </div>
                    <div class="edit-status-wrapper">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status" class="lnd-input" id="edit_status">
                            <option value="1">{{ __('Public') }}</option>
                            <option value="0">{{ __('Draft') }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                    <button type="button" onclick="closeModal('product_type_edit_modal')" class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>
    <x-datatable.tw-js/>
    <x-media-upload.tw-js/>
    <x-digitalproduct::table.status-js/>
    @can('product-category-delete')
        <x-bulk-action-js :url="route('tenant.admin.digital.product.type.bulk.action')"/>
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

        function convertToSlug(text) {
            return text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        }

        $(document).ready(function () {
            // DataTable init
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#productTypeTable')) {
                $('#productTypeTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // Select2 init
            $('.select2').select2({
                placeholder: `{{__('Product type extensions')}}`,
                language: {
                    noResults: function () {
                        return "{{__('No result found')}}"
                    }
                }
            });

            // Slug auto-generation (create)
            $(document).on('keyup', '#create-name', function () {
                $('#create-slug').val(convertToSlug($(this).val()));
            });

            // Product type change - load extensions
            $(document).on('change', 'select.product-type', function (e) {
                e.preventDefault();
                let type = $(this).val();

                $.ajax({
                    'method': 'POST',
                    'url': `{{route('tenant.admin.digital.product.type.extensions')}}`,
                    'data': {
                        '_token': '{{csrf_token()}}',
                        'type': type
                    },
                    success: function (data) {
                        let select = $('select.extensions');
                        select.children().remove();

                        $.each(data.data, function (index, value) {
                            select.append(`<option value="${value}">${value}</option>`);
                        });
                    },
                    error: function () {}
                });
            });

            // Edit button handler
            $(document).on('click', '.product_type_edit_btn', function () {
                var el = $(this);
                var id = el.data('id');
                var name = el.data('name');
                var slug = el.data('slug');
                var product_type = el.data('product_type');
                var allExtensions = el.data('all_extensions');
                var extensions = el.data('extensions');
                var extensionsArray = extensions.toString().split(',');
                var status = el.data('status');

                var modal = $('#product_type_edit_modal');

                modal.find('#product_type_id').val(id);
                modal.find('#edit_name').val(name);
                modal.find('#edit_slug').val(slug);
                modal.find('#edit_product_type').val(product_type);
                modal.find('#edit_status').val(status);

                // Load extensions
                var select = modal.find('select.extensions');
                select.children().remove();
                $.each(allExtensions, function (index, value) {
                    select.append(`<option value="${value}">${value}</option>`);
                });

                $.each(extensionsArray, function (index, value) {
                    modal.find('#edit_extensions option[value="' + value.trim() + '"]').attr('selected', true);
                });

                // Handle image
                var image = el.data('image');
                var imageid = el.data('imageid');
                var section = modal.find('#edit_image_id_section');

                if (imageid && imageid !== '') {
                    section.find('.tw-img-wrap').html(
                        '<div class="tw-attachment-preview relative inline-block">' +
                        '<img src="' + image + '" alt="" class="tw-preview-img w-24 h-24 rounded-lg object-cover border border-gray-200" />' +
                        '<button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>' +
                        '</div>'
                    );
                    section.find('.tw-media-id-input').val(imageid);
                    section.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> {{__("Change Image")}}');
                } else {
                    section.find('.tw-img-wrap').html('<div class="tw-attachment-preview"></div>');
                    section.find('.tw-media-id-input').val('');
                    section.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> {{__("Upload Image")}}');
                }

                openModal('product_type_edit_modal');
            });

            // SweetAlert Delete (AJAX-based)
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
                        $.ajax({
                            type: 'POST',
                            url: route,
                            data: { _token: '{{csrf_token()}}' },
                            success: function () {
                                toastr.success('{{__("Deleted successfully")}}');
                                row.fadeOut(300, function () { $(this).remove(); });
                            },
                            error: function () {
                                toastr.error('{{__("Something went wrong")}}');
                            }
                        });
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
