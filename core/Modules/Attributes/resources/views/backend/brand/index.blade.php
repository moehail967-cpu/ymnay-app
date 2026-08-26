@extends('tenant.admin.admin-master')

@section('title') {{__('Brand Manage')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="bg-surface rounded-xl shadow-main border border-main">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-bookmark-multiple-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Brands')}}</h3>
                <p class="text-xs text-muted">{{__('Manage product brands')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @can('product-brand-manage-create')
                <button type="button" onclick="openModal('brand_create_modal')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add Brand')}}
                </button>
            @endcan
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="brandTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Logo')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest hidden md:table-cell">{{__('Title')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest hidden lg:table-cell">{{__('Description')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($delivery_manages as $item)
                @php
                    $logo = get_attachment_image_by_id($item->image_id);
                    $logo_url = !empty($logo) ? $logo['img_url'] : '';
                    $banner = get_attachment_image_by_id($item->banner_id);
                    $banner_url = !empty($banner) ? $banner['img_url'] : '';
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">#{{$item->id}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="tw-thumb-wrap">
                            {!! render_image_markup_by_attachment_id($item->image_id, class: 'w-full h-full object-cover') !!}
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$item->name}}</span>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <span class="text-sm text-dark">{{$item->title}}</span>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <span class="text-xs text-muted line-clamp-2">{{$item->description}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('product-delivery_manage-edit')
                                <button type="button"
                                        class="tw-btn-icon tw-btn-icon-edit brand_edit_btn"
                                        title="{{__('Edit')}}"
                                        data-id="{{$item->id}}"
                                        data-name="{{$item->name}}"
                                        data-slug="{{$item->slug}}"
                                        data-title="{{$item->title}}"
                                        data-description="{{$item->description}}"
                                        data-logo-id="{{ $item->image_id }}"
                                        data-logo="{{ $logo_url }}"
                                        data-banner-id="{{ $item->banner_id }}"
                                        data-banner="{{ $banner_url }}">
                                    <i class="mdi mdi-pencil-outline"></i>
                                </button>
                            @endcan
                            @can('product-delivery_manage-delete')
                                <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                        data-route="{{ route('tenant.admin.product.brand.manage.delete', $item->id) }}"
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
@can('brand-manage-create')
<div id="brand_create_modal" class="fixed inset-0 z-[800] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('brand_create_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">

            {{-- Modal Header --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Create New Brand')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Add a new product brand')}}</p>
                </div>
                <button type="button" onclick="closeModal('brand_create_modal')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <form action="{{route('tenant.admin.product.brand.manage.store')}}" method="post" enctype="multipart/form-data" id="brand_create_form" class="flex flex-col flex-1 min-h-0">
                @csrf
                <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="create-name" name="name" placeholder="{{__('Brand Name')}}">
                        <p class="text-danger text-xs mt-1 error-text" id="error-create-name"></p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Slug')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="create-slug" name="slug" placeholder="{{__('Brand Slug')}}">
                        <p class="text-danger text-xs mt-1 error-text" id="error-create-slug"></p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Title')}}</label>
                        <input type="text" class="lnd-input" name="title" placeholder="{{__('Title')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Description')}}</label>
                        <input type="text" class="lnd-input" name="description" placeholder="{{__('Brand Description (Optional)')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('URL')}}</label>
                        <input type="text" class="lnd-input" name="url" placeholder="{{__('Brand URL (Optional)')}}">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Logo')}} <span class="text-danger">*</span></label>
                        <x-fields.tw-media-upload :name="'image_id'" :dimentions="'200x200'"/>
                        <p class="text-danger text-xs mt-1 error-text" id="error-create-logo"></p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Banner')}}</label>
                        <x-fields.tw-media-upload :name="'banner_id'" :dimentions="'200x200'"/>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                    <button type="button" onclick="closeModal('brand_create_modal')" class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
                        {{__('Cancel')}}
                    </button>
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
@can('brand-manage-edit')
<div id="brand_edit_modal" class="fixed inset-0 z-[800] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('brand_edit_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">

            {{-- Modal Header --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-pencil-outline text-primary text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Update Brand')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Edit brand details')}}</p>
                </div>
                <button type="button" onclick="closeModal('brand_edit_modal')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <form action="{{route('tenant.admin.product.brand.manage.update')}}" method="post" id="brand_edit_form" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" value="" id="edit-brand-id" name="id">
                <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="edit-name" name="name" placeholder="{{__('Brand Name')}}">
                        <p class="text-danger text-xs mt-1 error-text" id="error-edit-name"></p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Slug')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="edit-slug" name="slug" placeholder="{{__('Brand Slug')}}">
                        <p class="text-danger text-xs mt-1 error-text" id="error-edit-slug"></p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Title')}}</label>
                        <input type="text" class="lnd-input" id="edit-title" name="title" placeholder="{{__('Title')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Description')}}</label>
                        <input type="text" class="lnd-input" id="edit-description" name="description" placeholder="{{__('Brand Description (Optional)')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('URL')}}</label>
                        <input type="text" class="lnd-input" id="edit-url" name="url" placeholder="{{__('Brand URL (Optional)')}}">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Logo')}} <span class="text-danger">*</span></label>
                        <div class="tw-media-upload-wrapper" id="edit_image_id_section">
                            <div class="tw-img-wrap mb-3">
                                <div class="tw-attachment-preview"></div>
                            </div>
                            <input type="hidden" name="image_id" class="tw-media-id-input" value="">
                            <button type="button" class="tw-media-open-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:opacity-90 transition" data-target="edit_image_id_section">
                                <i class="ti tabler-photo text-base"></i> {{__('Upload Image')}}
                            </button>
                            <p class="mt-1 text-xs text-gray-400">{{__('Recommended')}}: 200x200</p>
                        </div>
                        <p class="text-danger text-xs mt-1 error-text" id="error-edit-logo"></p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Banner')}}</label>
                        <div class="tw-media-upload-wrapper" id="edit_banner_id_section">
                            <div class="tw-img-wrap mb-3">
                                <div class="tw-attachment-preview"></div>
                            </div>
                            <input type="hidden" name="banner_id" class="tw-media-id-input" value="">
                            <button type="button" class="tw-media-open-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:opacity-90 transition" data-target="edit_banner_id_section">
                                <i class="ti tabler-photo text-base"></i> {{__('Upload Image')}}
                            </button>
                            <p class="mt-1 text-xs text-gray-400">{{__('Recommended')}}: 200x200</p>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                    <button type="button" onclick="closeModal('brand_edit_modal')" class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
                        {{__('Cancel')}}
                    </button>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Brand')}}
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
    <x-datatable.tw-js/>
    <x-media-upload.tw-js/>
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

        function clearErrors(container) {
            container.find('.error-text').text('');
        }

        function showError(id, message) {
            $('#' + id).text(message);
        }

        function setTwMediaPreview(section, imgUrl, imgId) {
            if (imgId) {
                section.find('.tw-img-wrap').html(
                    '<div class="tw-attachment-preview relative inline-block">' +
                    '<img src="' + imgUrl + '" alt="" class="tw-preview-img w-24 h-24 rounded-lg object-cover border border-gray-200" />' +
                    '<button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>' +
                    '</div>'
                );
                section.find('.tw-media-id-input').val(imgId);
                section.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> {{__("Change Image")}}');
            } else {
                section.find('.tw-img-wrap').html('<div class="tw-attachment-preview"></div>');
                section.find('.tw-media-id-input').val('');
                section.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> {{__("Upload Image")}}');
            }
        }

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#brandTable')) {
                $('#brandTable').DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // Slug auto-generation
            $('#create-name, #create-slug').on('keyup', function () {
                $('#create-slug').val(convertToSlug($(this).val()));
            });
            $('#edit-name, #edit-slug').on('keyup', function () {
                $('#edit-slug').val(convertToSlug($(this).val()));
            });

            // Edit button
            $(document).on('click', '.brand_edit_btn', function () {
                var el = $(this);
                var modal = $('#brand_edit_modal');

                modal.find('#edit-brand-id').val(el.data('id'));
                modal.find('#edit-name').val(el.data('name'));
                modal.find('#edit-slug').val(el.data('slug'));
                modal.find('#edit-title').val(el.data('title'));
                modal.find('#edit-description').val(el.data('description'));

                // Logo
                setTwMediaPreview(
                    modal.find('#edit_image_id_section'),
                    el.data('logo'),
                    el.data('logo-id')
                );

                // Banner
                setTwMediaPreview(
                    modal.find('#edit_banner_id_section'),
                    el.data('banner'),
                    el.data('banner-id')
                );

                openModal('brand_edit_modal');
            });

            // Create Form Validation
            $('#brand_create_form').on('submit', function (e) {
                var isValid = true;
                clearErrors($('#brand_create_modal'));

                if ($('#create-name').val().trim() === '') {
                    isValid = false;
                    showError('error-create-name', '{{ __("Name is required") }}');
                }
                if ($('#create-slug').val().trim() === '') {
                    isValid = false;
                    showError('error-create-slug', '{{ __("Slug is required") }}');
                }
                if (!$('#brand_create_modal').find('input[name="image_id"]').val()) {
                    isValid = false;
                    showError('error-create-logo', '{{ __("Logo is required") }}');
                }
                if (!isValid) e.preventDefault();
            });

            // Edit Form Validation
            $('#brand_edit_form').on('submit', function (e) {
                var isValid = true;
                clearErrors($('#brand_edit_modal'));

                if ($('#edit-name').val().trim() === '') {
                    isValid = false;
                    showError('error-edit-name', '{{ __("Name is required") }}');
                }
                if ($('#edit-slug').val().trim() === '') {
                    isValid = false;
                    showError('error-edit-slug', '{{ __("Slug is required") }}');
                }
                if (!$('#brand_edit_modal').find('input[name="image_id"]').val()) {
                    isValid = false;
                    showError('error-edit-logo', '{{ __("Logo is required") }}');
                }
                if (!isValid) e.preventDefault();
            });

            // SweetAlert Delete (JSON-based)
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
                            success: function (res) {
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
