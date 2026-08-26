@extends('tenant.admin.admin-master')

@section('title') {{__('Product Child Category')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <style>.hover\:text-white:hover{color:#fff!important}</style>

@endsection

@php
    $statuses = \App\Models\Status::all();
@endphp

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="bg-surface rounded-xl shadow-main border border-main">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-file-tree text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Child Categories')}}</h3>
                <p class="text-xs text-muted">{{__('Manage product child categories')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @can('product-child-category-delete')
                <x-bulk-action permissions="product-child-category-delete"/>
            @endcan
            @can('product-category-delete')
                <a href="{{route('tenant.admin.product.child-category.trash.all')}}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition-all">
                    <i class="mdi mdi-delete-outline text-sm"></i> {{__('Trash')}}
                </a>
            @endcan
            @can('product-child-category-create')
                <button type="button" onclick="openModal('child_category_create_modal')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('New Child Category')}}
                </button>
            @endcan
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="childCategoryTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest hidden md:table-cell">{{__('Category')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest hidden lg:table-cell">{{__('Sub Category')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Image')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($data['all_child_category'] as $child_category)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <x-bulk-delete-checkbox :id="$child_category->id"/>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">#{{$child_category->id}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$child_category->name}}</span>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <span class="tw-pill tw-pill-info">{{ $child_category->category?->name ?? __('N/A') }}</span>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <span class="tw-pill tw-pill-purple">{{ $child_category->sub_category?->name ?? __('N/A') }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="tw-pill {{ $child_category->status?->name === 'Publish' ? 'tw-pill-success' : 'tw-pill-warning' }}">
                            {{$child_category->status?->name ?? __('N/A')}}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="tw-thumb-wrap">
                            {!! render_image_markup_by_attachment_id($child_category->image_id) !!}
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('product-child-category-edit')
                                @php
                                    $image = get_attachment_image_by_id($child_category->image_id, null, true);
                                    $img_path = $image['img_url'];
                                @endphp
                                <button type="button"
                                        class="tw-btn-icon tw-btn-icon-edit child_category_edit_btn"
                                        title="{{__('Edit')}}"
                                        data-id="{{$child_category->id}}"
                                        data-name="{{$child_category->name}}"
                                        data-slug="{{$child_category->slug}}"
                                        data-status="{{ $child_category->status_id }}"
                                        data-imageid="{!! $child_category->image_id !!}"
                                        data-image="{{ $img_path }}"
                                        data-category-id="{{$child_category->category_id}}"
                                        data-sub-category-id="{{$child_category->sub_category_id}}">
                                    <i class="mdi mdi-pencil-outline"></i>
                                </button>
                            @endcan
                            @can('product-child-category-delete')
                                <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                        data-route="{{ route('tenant.admin.product.child-category.delete', $child_category->id) }}"
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
@can('product-child-category-create')
<div id="child_category_create_modal" class="fixed inset-0 z-[800] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('child_category_create_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Add Child Category')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Create a new product child category')}}</p>
                </div>
                <button type="button" onclick="closeModal('child_category_create_modal')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>
            <form action="{{route('tenant.admin.product.child-category.new')}}" method="post" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                @csrf
                <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="create-name" name="name" placeholder="{{ __('Name') }}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Slug')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="create-slug" name="slug" placeholder="{{ __('Slug') }}">
                    </div>
                    <div class="category-wrapper">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Category')}} <span class="text-danger">*</span></label>
                        <select class="lnd-input" id="create_category_id" name="category_id">
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach ($data['all_category'] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="create-sub-category-wrapper">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Sub Category')}} <span class="text-danger">*</span></label>
                        <select class="lnd-input" id="create_sub_category" name="sub_category_id">
                            <option>{{ __('Select Sub Category') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status_id" class="lnd-input">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Image')}}</label>
                        <x-fields.tw-media-upload :name="'image_id'" :dimentions="'120x120'"/>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                    <button type="button" onclick="closeModal('child_category_create_modal')" class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
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
@can('product-child-category-edit')
<div id="child_category_edit_modal" class="fixed inset-0 z-[800] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('child_category_edit_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-pencil-outline text-primary text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Update Child Category')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Edit child category details')}}</p>
                </div>
                <button type="button" onclick="closeModal('child_category_edit_modal')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>
            <form action="{{route('tenant.admin.product.child-category.update')}}" method="post" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="id" id="child_category_id">
                <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="edit_name" name="name" placeholder="{{__('Name')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Slug')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="edit_slug" name="slug" placeholder="{{__('Slug')}}">
                    </div>
                    <div class="edit-category-wrapper">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Category')}} <span class="text-danger">*</span></label>
                        <select class="lnd-input" id="edit_category_id" name="category_id">
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach ($data['all_category'] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="edit-sub-category-wrapper">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Sub Category')}} <span class="text-danger">*</span></label>
                        <select class="lnd-input" id="edit_sub_category" name="sub_category_id">
                            <option>{{ __('Select Sub Category') }}</option>
                        </select>
                    </div>
                    <div class="edit-status-wrapper">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status_id" class="lnd-input" id="edit_status">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
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
                            <p class="mt-1 text-xs text-gray-400">{{__('Recommended')}}: 200x200</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                    <button type="button" onclick="closeModal('child_category_edit_modal')" class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
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
    <x-datatable.tw-js/>
    <x-media-upload.tw-js/>
    @can('product-child-category-delete')
        <x-bulk-action-js :url="route('tenant.admin.product.child-category.bulk.action')"/>
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
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#childCategoryTable')) {
                $('#childCategoryTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // Slug auto-generation
            $('#create-name, #create-slug').on('keyup', function () {
                $('#create-slug').val(convertToSlug($(this).val()));
            });
            $('#edit_name, #edit_slug').on('keyup', function () {
                $('#edit_slug').val(convertToSlug($(this).val()));
            });

            // Cascading: category → sub-category (create)
            $(document).on('change', '#create_category_id', function () {
                var category_id = $(this).val();
                $.ajax({
                    url: '{{ route("tenant.admin.product.subcategory.all") }}/of-category/select/' + category_id,
                    type: 'GET',
                    data: { _token: '{{ csrf_token() }}', category_id: category_id },
                    success: function (data) {
                        $('#create_sub_category').html(data.option);
                    }
                });
            });

            // Cascading: category → sub-category (edit)
            $(document).on('change', '#edit_category_id', function () {
                var category_id = $(this).val();
                $.ajax({
                    url: '{{ route("tenant.admin.product.subcategory.all") }}/of-category/select/' + category_id,
                    type: 'GET',
                    data: { _token: '{{ csrf_token() }}', category_id: category_id },
                    success: function (data) {
                        $('#edit_sub_category').html(data.option);
                    },
                    error: function () {
                        toastr.error('{{ __("An error occurred") }}');
                    }
                });
            });

            // Edit button
            $(document).on('click', '.child_category_edit_btn', function () {
                var el = $(this);
                var modal = $('#child_category_edit_modal');
                var category_id = el.data('category-id');
                var sub_category_id = el.data('sub-category-id');

                modal.find('#child_category_id').val(el.data('id'));
                modal.find('#edit_name').val(el.data('name'));
                modal.find('#edit_slug').val(el.data('slug'));
                modal.find('#edit_status').val(el.data('status'));
                modal.find('#edit_category_id').val(category_id);

                // Load sub-categories for selected category
                $.ajax({
                    url: '{{ route("tenant.admin.product.subcategory.all") }}/of-category/select/' + category_id,
                    type: 'GET',
                    data: { _token: '{{ csrf_token() }}', category_id: category_id },
                    success: function (data) {
                        $('#edit_sub_category').html(data.option);
                        $('#edit_sub_category').val(sub_category_id);
                    }
                });

                var image = el.data('image');
                var imageid = el.data('imageid');
                var section = modal.find('#edit_image_id_section');

                if (imageid) {
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

                openModal('child_category_edit_modal');
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
