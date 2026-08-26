@extends(route_prefix().'admin.admin-master')
@section('title')
    {{__('Badge Management')}}
@endsection
@section('style')
    <x-datatable.tw-css/>
    <style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection
@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Badge List --}}
    <div class="lg:col-span-8">
        <div class="bg-surface rounded-xl shadow-main border border-main mb-6">
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-shield-star-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Badges')}}</h3>
                        <p class="text-xs text-muted">{{__('Manage product badges and labels')}}</p>
                    </div>
                </div>
                <a href="{{route('tenant.admin.badge.trash')}}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-danger-soft border border-main text-danger text-sm font-semibold hover:bg-danger hover:text-white hover:border-danger transition whitespace-nowrap">
                    <i class="mdi mdi-delete-outline text-base"></i> {{__('Trash')}}
                </a>
            </div>

            @can('badge-delete')
                <div class="px-4 sm:px-6 py-3 border-b border-main flex items-center gap-3">
                    <select id="bulk_action_select" class="text-xs bg-secondary border border-main rounded-lg px-3 py-1.5 text-dark focus:border-primary focus:outline-none transition">
                        <option value="">{{__('Bulk Action')}}</option>
                        <option value="delete">{{__('Delete')}}</option>
                    </select>
                    <button type="button" id="bulk_action_apply_btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
                        {{__('Apply')}}
                    </button>
                </div>
            @endcan

            <div class="tw-table-wrap">
                <table class="w-full text-left" id="all_user_table">
                    <thead>
                        <tr class="border-b border-main">
                            <th class="px-4 py-3 w-10 no-sort">
                                <input type="checkbox" class="all-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                            </th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Image')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($badges as $badge)
                        <tr class="border-b border-main hover:bg-muted transition-colors">
                            <td class="px-4 py-3.5">
                                <input type="checkbox" class="bulk-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" name="bulk_delete[]" value="{{$badge->id}}">
                            </td>
                            <td class="px-4 py-3.5"><span class="text-[11px] font-bold text-primary">{{__('#')}} {{$loop->iteration}}</span></td>
                            <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{$badge->name}}</span></td>
                            <td class="px-4 py-3.5">
                                <div class="w-10 h-10 rounded-lg overflow-hidden border border-main bg-secondary flex items-center justify-center">
                                    {!! render_image_markup_by_attachment_id($badge->image, 'w-full h-full object-contain') !!}
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($badge->status === 'active')
                                    <span class="tw-pill tw-pill-success">{{__('Active')}}</span>
                                @else
                                    <span class="tw-pill tw-pill-warning">{{__('Inactive')}}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('badge-edit')
                                        @php
                                            $img = get_attachment_image_by_id($badge->image);
                                            $img_url = !empty($img) ? $img['img_url'] : '';
                                        @endphp
                                        <button type="button"
                                           class="w-9 h-9 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:text-white hover:bg-primary hover:border-primary transition-all badge_edit_btn"
                                           data-id="{{$badge->id}}"
                                           data-name="{{$badge->name}}"
                                           data-status="{{$badge->status}}"
                                           data-image_id="{{$badge->image}}"
                                           data-image_url="{{$img_url}}"
                                           data-route="{{route('tenant.admin.badge.update', $badge->id)}}"
                                           title="{{__('Edit')}}">
                                            <i class="mdi mdi-pencil-outline text-sm"></i>
                                        </button>
                                    @endcan
                                    @can('badge-delete')
                                        <button type="button"
                                                class="w-9 h-9 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:text-white hover:bg-danger hover:border-danger transition-all swal_delete_button"
                                                title="{{__('Delete')}}">
                                            <i class="mdi mdi-delete-outline text-sm"></i>
                                        </button>
                                        <form method="post" action="{{route('tenant.admin.badge.delete', $badge->id)}}" class="hidden delete-form">
                                            @csrf
                                        </form>
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

    {{-- Add New Badge --}}
    @can('badge-create')
        <div class="lg:col-span-4">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden lg:sticky lg:top-[80px]">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Add New Badge')}}</h3>
                        <p class="text-xs text-muted">{{__('Create a new product badge')}}</p>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <form action="{{route('tenant.admin.badge.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                    <i class="mdi mdi-tag-outline text-lg text-primary"></i>
                                    <input type="text" name="name" placeholder="{{__('Badge name')}}"
                                           class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                    <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                                    <select name="status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                        <option value="" selected disabled>{{__('Select status')}}</option>
                                        <option value="active">{{__('Active')}}</option>
                                        <option value="in_active">{{__('Inactive')}}</option>
                                    </select>
                                    <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Badge Image')}}</label>
                                <x-fields.tw-media-upload name="image" dimentions="120x120 px"/>
                            </div>

                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                                <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add New Badge')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
</div>

{{-- Edit Modal --}}
@can('badge-edit')
    <div id="badge_edit_modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
        <div id="badge_edit_backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-surface rounded-2xl shadow-2xl border border-main w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-main flex items-center justify-between">
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Update Badge')}}</h3>
                <button type="button" class="badge_edit_close w-8 h-8 rounded-lg bg-secondary border border-main flex items-center justify-center text-muted hover:text-danger hover:border-danger transition">
                    <i class="mdi mdi-close text-base"></i>
                </button>
            </div>
            <form action="" method="post" id="badge_edit_modal_form">
                @csrf
                <input type="hidden" name="id" id="badge_id">
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-tag-outline text-lg text-primary"></i>
                            <input type="text" id="edit_name" name="name" placeholder="{{__('Badge name')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                            <select name="status" id="edit_status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="" selected disabled>{{__('Select status')}}</option>
                                <option value="active">{{__('Active')}}</option>
                                <option value="in_active">{{__('Inactive')}}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Badge Image')}}</label>
                        <x-fields.tw-media-upload name="modal_badge_image" dimentions="120x120 px"/>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-main flex items-center justify-end gap-3">
                    <button type="button" class="badge_edit_close inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-muted transition">
                        {{__('Close')}}
                    </button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-6 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endcan

<x-media-upload.tw-markup/>
@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-table.btn.swal.js/>
    <x-media-upload.tw-js/>
    @can('badge-delete')
        <x-bulk-action.js :route="route('tenant.admin.badge.bulk.action.delete')"/>
    @endcan

    <script>
        (function ($) {
            "use strict";

            // Rename modal media input so controller receives 'image'
            $('#modal_badge_image_section').find('.tw-media-id-input').attr('name', 'image');

            function openModal(id) {
                document.getElementById(id).classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
            function closeModal(id) {
                document.getElementById(id).classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            $(document).on('click', '.badge_edit_btn', function () {
                var el = $(this);
                var id = el.data('id');
                var name = el.data('name');
                var image = el.data('image_url');
                var image_id = el.data('image_id');
                var status = el.data('status');
                var action = el.data('route');

                var form = $('#badge_edit_modal_form');
                form.attr('action', action);
                form.find('#badge_id').val(id);
                form.find('#edit_name').val(name);
                form.find('#edit_status').val(status);

                var $wrapper = form.find('#modal_badge_image_section');
                if (image_id && image_id !== '') {
                    $wrapper.find('.tw-media-id-input').val(image_id);
                    $wrapper.find('.tw-img-wrap').html(
                        '<div class="tw-attachment-preview relative inline-block">' +
                        '<img src="' + image + '" alt="" class="tw-preview-img w-24 h-24 rounded-lg object-cover border border-gray-200" />' +
                        '<button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>' +
                        '</div>'
                    );
                    $wrapper.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> {{ __("Change Image") }}');
                } else {
                    $wrapper.find('.tw-media-id-input').val('');
                    $wrapper.find('.tw-img-wrap').html('<div class="tw-attachment-preview"></div>');
                    $wrapper.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> {{ __("Upload Image") }}');
                }
                openModal('badge_edit_modal');
            });

            $('.badge_edit_close, #badge_edit_backdrop').on('click', function () {
                closeModal('badge_edit_modal');
            });

            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                var btn = $(this);
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You will not be able to recover this!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{ __("Yes, Delete it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        btn.closest('td').find('.delete-form').trigger('submit');
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
