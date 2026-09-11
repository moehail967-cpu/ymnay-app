@extends(route_prefix().'admin.admin-master')

@section('title') {{__('Digital Product SubCategory')}} @endsection

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
                <i class="mdi mdi-shape-plus-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Digital Sub Categories')}}</h3>
                <p class="text-xs text-muted">{{__('Manage digital product sub categories')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @can('digital-category-delete')
                <x-bulk-action permissions="digital-category-delete"/>
            @endcan
            @can('digital-category-create')
                <button type="button" onclick="openModal('subcategory_create_modal')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('New Sub Category')}}
                </button>
            @endcan
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="subCategoryTable">
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
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest hidden lg:table-cell">{{__('Description')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Image')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_subcategory ?? [] as $key => $subcategory)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <x-bulk-delete-checkbox :id="$subcategory->id"/>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">#{{$subcategory->id}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$subcategory->name}}</span>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <span class="tw-pill tw-pill-info">{{$subcategory->category?->name ?? __('N/A')}}</span>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <span class="text-sm text-muted">{{ Str::limit($subcategory->description, 50) }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="tw-thumb-wrap">
                            {!! render_image_markup_by_attachment_id($subcategory->image_id) !!}
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="tw-pill {{ $subcategory->status == 1 ? 'tw-pill-success' : 'tw-pill-warning' }}">
                            {{\App\Enums\StatusEnums::getText($subcategory->status)}}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('digital-category-edit')
                                @php
                                    $image = get_attachment_image_by_id($subcategory->image_id, null, true);
                                    $img_path = $image['img_url'];
                                @endphp
                                <button type="button"
                                        class="tw-btn-icon tw-btn-icon-edit subcategory_edit_btn"
                                        title="{{__('Edit')}}"
                                        data-id="{{$subcategory->id}}"
                                        data-name="{{$subcategory->name}}"
                                        data-slug="{{$subcategory->slug}}"
                                        data-category="{{$subcategory->category_id}}"
                                        data-description="{{$subcategory->description}}"
                                        data-status="{{$subcategory->status}}"
                                        data-imageid="{{$subcategory->image_id}}"
                                        data-image="{{$img_path}}">
                                    <i class="mdi mdi-pencil-outline"></i>
                                </button>
                            @endcan
                            @can('digital-category-delete')
                                <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                        data-route="{{ route('tenant.admin.digital.product.subcategory.delete', $subcategory->id) }}"
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
@can('digital-category-create')
<div id="subcategory_create_modal" class="fixed inset-0 z-[800] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('subcategory_create_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Create Sub Category')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Add a new digital product sub category')}}</p>
                </div>
                <button type="button" onclick="closeModal('subcategory_create_modal')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>
            <form action="{{ route('tenant.admin.digital.product.subcategory.new') }}" method="post" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
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
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Category')}} <span class="text-danger">*</span></label>
                        <select name="category" class="lnd-input">
                            <option value="">{{__('Select Category')}}</option>
                            @foreach($all_category as $item)
                                <option value="{{$item->id}}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Description')}}</label>
                        <textarea name="description" class="lnd-input" rows="3" placeholder="{{__('Description')}}"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Image')}}</label>
                        <x-fields.tw-media-upload :name="'image_id'" :dimentions="'120x120'"/>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status_id" class="lnd-input">
                            <option value="1">{{ __('Publish') }}</option>
                            <option value="0">{{ __('Draft') }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                    <button type="button" onclick="closeModal('subcategory_create_modal')" class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
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
@can('digital-category-edit')
<div id="subcategory_edit_modal" class="fixed inset-0 z-[800] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('subcategory_edit_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-pencil-outline text-primary text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Update Sub Category')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Edit sub category details')}}</p>
                </div>
                <button type="button" onclick="closeModal('subcategory_edit_modal')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>
            <form action="{{ route('tenant.admin.digital.product.subcategory.update') }}" method="post" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="id" id="category_id">
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
                        <select name="category" class="lnd-input" id="edit_category">
                            @foreach($all_category as $item)
                                <option value="{{$item->id}}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Description')}}</label>
                        <textarea name="description" id="edit_description" class="lnd-input" rows="3" placeholder="{{__('Description')}}"></textarea>
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
                        <select name="status_id" class="lnd-input" id="edit_status">
                            <option value="1">{{ __('Publish') }}</option>
                            <option value="0">{{ __('Draft') }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                    <button type="button" onclick="closeModal('subcategory_edit_modal')" class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
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
    @can('digital-category-delete')
        <x-bulk-action-js :url="route('tenant.admin.digital.product.subcategory.bulk.action')"/>
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
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#subCategoryTable')) {
                $('#subCategoryTable').DataTable({
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

            // Edit button
            $(document).on('click', '.subcategory_edit_btn', function () {
                var el = $(this);
                var modal = $('#subcategory_edit_modal');

                modal.find('#category_id').val(el.data('id'));
                modal.find('#edit_name').val(el.data('name'));
                modal.find('#edit_slug').val(el.data('slug'));
                modal.find('#edit_description').val(el.data('description'));
                modal.find('#edit_status').val(el.data('status'));
                modal.find('#edit_category').val(el.data('category'));

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

                openModal('subcategory_edit_modal');
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
