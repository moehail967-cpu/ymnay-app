@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Testimonial')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <style>.hover\:text-white:hover{color:#fff!important}</style>
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
                    <i class="mdi mdi-comment-quote-outline text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Testimonial')}}</h3>
                    <p class="text-xs text-muted">{{__('Manage customer testimonials')}}</p>
                </div>
            </div>
            @can('testimonial-create')
                <button type="button" onclick="openModal('new_testimonial_modal')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                    <i class="mdi mdi-plus text-base"></i>
                    {{__('Add New Testimonial')}}
                </button>
            @endcan
        </div>

        {{-- Bulk Action --}}
        @can('testimonial-delete')
            <div class="px-4 sm:px-6 py-3 border-b border-main flex items-center gap-3">
                <select name="bulk_option" id="bulk_option"
                        class="text-xs bg-secondary border border-main rounded-lg px-3 py-1.5 text-dark focus:border-primary focus:outline-none transition">
                    <option value="">{{__('Bulk Action')}}</option>
                    <option value="delete">{{__('Delete')}}</option>
                </select>
                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition"
                        id="bulk_delete_btn">
                    {{__('Apply')}}
                </button>
            </div>
        @endcan

        {{-- Table --}}
        <div class="tw-table-wrap">
            <table class="w-full text-left" id="testimonialTable">
                <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <input type="checkbox" class="all-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort">{{__('Image')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Designation')}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Company')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($all_testimonials as $data)
                    @php
                        $testimonial_img = get_attachment_image_by_id($data->image, null, true);
                        $img_url = $testimonial_img['img_url'] ?? '';
                    @endphp
                    <tr class="border-b border-main hover:bg-muted transition-colors">

                        {{-- Checkbox --}}
                        <td class="px-4 py-3.5">
                            <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-primary focus:ring-primary" name="bulk_delete[]" value="{{$data->id}}">
                        </td>

                        {{-- ID --}}
                        <td class="px-4 py-3.5">
                            <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
                        </td>

                        {{-- Image --}}
                        <td class="px-4 py-3.5">
                            @if($img_url)
                                <img src="{{$img_url}}" alt="{{$data->name}}" class="w-10 h-10 rounded-lg object-cover border border-main">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-secondary border border-main flex items-center justify-center">
                                    <i class="mdi mdi-account-outline text-muted text-lg"></i>
                                </div>
                            @endif
                        </td>

                        {{-- Name --}}
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-semibold text-dark">{{$data->name}}</span>
                        </td>

                        {{-- Designation --}}
                        <td class="hidden md:table-cell px-4 py-3.5">
                            <span class="text-xs text-muted">{{$data->designation}}</span>
                        </td>

                        {{-- Company --}}
                        <td class="hidden md:table-cell px-4 py-3.5">
                            <span class="text-xs text-muted">{{$data->company}}</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3.5">
                            @if($data->status == \App\Enums\StatusEnums::PUBLISH)
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[10px]"></i> {{__('Published')}}
                            </span>
                            @else
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase">
                                <i class="mdi mdi-pencil-outline text-[10px]"></i> {{__('Draft')}}
                            </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end">
                                <div class="row-action-wrap">

                                    {{-- Edit icon --}}
                                    @can('testimonial-edit')
                                        <button type="button"
                                                class="testimonial_edit_btn w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:text-white hover:bg-primary hover:border-primary transition-all"
                                                title="{{__('Edit')}}"
                                                data-id="{{$data->id}}"
                                                data-action="{{route(route_prefix().'admin.testimonial.update')}}"
                                                data-name="{{$data->name}}"
                                                data-status="{{$data->status}}"
                                                data-rating="{{$data->rating}}"
                                                data-description="{{$data->description}}"
                                                data-designation="{{$data->designation}}"
                                                data-company="{{$data->company}}"
                                                data-imageid="{{$data->image}}"
                                                data-image="{{$img_url}}">
                                            <i class="mdi mdi-pencil-outline text-sm"></i>
                                        </button>
                                    @endcan

                                    {{-- Dropdown trigger --}}
                                    <button type="button" onclick="toggleRowMenu(this)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                        <i class="mdi mdi-dots-vertical text-sm"></i>
                                    </button>

                                    {{-- Dropdown panel --}}
                                    <div class="row-action-menu hidden">
                                        @can('testimonial-edit')
                                            <button type="button" class="action-item clone_btn" data-id="{{$data->id}}">
                                                <span class="action-icon bg-[#f3e8ff]"><i class="mdi mdi-content-copy text-[#9333ea]"></i></span>
                                                {{__('Clone')}}
                                            </button>
                                        @endcan

                                        <div class="menu-divider"></div>

                                        @can('testimonial-delete')
                                            <button type="button" class="action-item action-danger swal_delete_button">
                                                <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                                {{__('Delete')}}
                                            </button>
                                        @endcan
                                    </div>
                                </div>

                                {{-- Hidden clone form --}}
                                @can('testimonial-edit')
                                    <form method="post" action="{{route(route_prefix().'admin.testimonial.clone')}}" class="clone_form hidden d-none">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{$data->id}}">
                                        <button type="submit" class="clone_submit_btn hidden d-none"></button>
                                    </form>
                                @endcan

                                {{-- Hidden delete form --}}
                                @can('testimonial-delete')
                                    <form method="post" action="{{route(route_prefix().'admin.testimonial.delete', $data->id)}}" class="hidden d-none">
                                        @csrf
                                        <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
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

    {{-- Add New Testimonial Modal --}}
    @can('testimonial-create')
        <div id="new_testimonial_modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('new_testimonial_modal')"></div>
            <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-lg overflow-hidden border border-main max-h-[90vh] flex flex-col">

                {{-- Header --}}
                <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-comment-plus-outline text-primary text-base"></i>
                    </div>
                    <div class="flex-1">
                        <h5 class="text-sm font-bold text-dark font-urbanist">{{__('New Testimonial')}}</h5>
                        <p class="text-[11px] text-muted">{{__('Add a new customer testimonial')}}</p>
                    </div>
                    <button type="button" onclick="closeModal('new_testimonial_modal')"
                            class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-primary-soft hover:text-primary transition">
                        <i class="mdi mdi-close text-lg"></i>
                    </button>
                </div>

                {{-- Body --}}
                <form action="{{route(route_prefix().'admin.testimonial.store')}}" method="post" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden">
                    @csrf
                    <input type="hidden" name="lang" value="{{$default_lang}}">
                    <div class="px-6 py-5 space-y-4 overflow-y-auto flex-1">

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-account-outline text-lg text-primary"></i>
                                <input type="text" name="name" value="{{old('name')}}" placeholder="{{__('Enter name')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Designation')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-briefcase-outline text-lg text-primary"></i>
                                <input type="text" name="designation" value="{{old('designation')}}" placeholder="{{__('Enter designation')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Company')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-domain text-lg text-primary"></i>
                                <input type="text" name="company" value="{{old('company')}}" placeholder="{{__('Enter company')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Description')}}</label>
                            <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <textarea name="description" rows="3" placeholder="{{__('Enter testimonial text...')}}"
                                  class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-y">{{old('description')}}</textarea>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                                <select name="status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                    <option value="{{\App\Enums\StatusEnums::PUBLISH}}">{{__('Publish')}}</option>
                                    <option value="{{\App\Enums\StatusEnums::DRAFT}}">{{__('Draft')}}</option>
                                </select>
                                <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Image')}}</label>
                            <x-fields.tw-media-upload name="image" dimentions="{{__('360x360 px image recommended')}}"/>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary flex-shrink-0">
                        <button type="button" onclick="closeModal('new_testimonial_modal')"
                                class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                            {{__('Cancel')}}
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                            <i class="mdi mdi-check text-base"></i>
                            {{__('Save Changes')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    {{-- Edit Testimonial Modal --}}
    @can('testimonial-edit')
        <div id="testimonial_edit_modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('testimonial_edit_modal')"></div>
            <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-lg overflow-hidden border border-main max-h-[90vh] flex flex-col">

                {{-- Header --}}
                <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-pencil-outline text-warning text-base"></i>
                    </div>
                    <div class="flex-1">
                        <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Testimonial')}}</h5>
                        <p class="text-[11px] text-muted">{{__('Update testimonial details')}}</p>
                    </div>
                    <button type="button" onclick="closeModal('testimonial_edit_modal')"
                            class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-warning-soft hover:text-warning transition">
                        <i class="mdi mdi-close text-lg"></i>
                    </button>
                </div>

                {{-- Body --}}
                <form action="#" id="testimonial_edit_modal_form" method="post" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden">
                    @csrf
                    <input type="hidden" name="lang" value="{{$default_lang}}">
                    <input type="hidden" name="id" class="testimonial_id" value="">
                    <div class="px-6 py-5 space-y-4 overflow-y-auto flex-1">

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-account-outline text-lg text-primary"></i>
                                <input type="text" name="name" placeholder="{{__('Enter name')}}"
                                       class="edit_name flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Designation')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-briefcase-outline text-lg text-primary"></i>
                                <input type="text" name="designation" placeholder="{{__('Enter designation')}}"
                                       class="edit_designation flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Company')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-domain text-lg text-primary"></i>
                                <input type="text" name="company" placeholder="{{__('Enter company')}}"
                                       class="edit_company flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Description')}}</label>
                            <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <textarea name="description" rows="3" placeholder="{{__('Enter testimonial text...')}}"
                                  class="edit_description w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-y"></textarea>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                                <select name="status" class="edit_status flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                    <option value="{{\App\Enums\StatusEnums::PUBLISH}}">{{__('Publish')}}</option>
                                    <option value="{{\App\Enums\StatusEnums::DRAFT}}">{{__('Draft')}}</option>
                                </select>
                                <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Image')}}</label>
                            <input type="hidden" name="image" class="edit_image_sync" value="">
                            <x-fields.tw-media-upload name="edit_image" dimentions="{{__('360x360 px image recommended')}}"/>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary flex-shrink-0">
                        <button type="button" onclick="closeModal('testimonial_edit_modal')"
                                class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                            {{__('Cancel')}}
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                            <i class="mdi mdi-content-save-outline text-base"></i>
                            {{__('Save Changes')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <x-media-upload.tw-markup/>
@endsection

@section('scripts')
    <x-media-upload.tw-js/>
    <x-datatable.tw-js/>
    <x-bulk-action-js :url="route(route_prefix().'admin.testimonial.bulk.action')" />

    <script>
        (function ($) {
            "use strict";

            // ── Modal helpers ────────────────────────────────────────────
            window.openModal = function (id) {
                document.getElementById(id).classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };
            window.closeModal = function (id) {
                document.getElementById(id).classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            // ── Row action dropdown ──────────────────────────────────────
            window.toggleRowMenu = function (btn) {
                var menu = btn.nextElementSibling;
                var isHidden = menu.classList.contains('hidden');

                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });

                if (isHidden) {
                    var rect       = btn.getBoundingClientRect();
                    var menuHeight = 320;
                    var spaceBelow = window.innerHeight - rect.bottom;
                    var spaceAbove = rect.top;

                    menu.style.right = (window.innerWidth - rect.right) + 'px';
                    menu.style.left  = 'auto';

                    if (spaceBelow >= Math.min(menuHeight, 200) || spaceBelow >= spaceAbove) {
                        menu.style.top    = (rect.bottom + 4) + 'px';
                        menu.style.bottom = 'auto';
                    } else {
                        menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                        menu.style.top    = 'auto';
                    }

                    menu.classList.remove('hidden');
                }
            };

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.row-action-wrap')) {
                    document.querySelectorAll('.row-action-menu').forEach(function (m) {
                        m.classList.add('hidden');
                    });
                }
            });

            window.addEventListener('scroll', function (e) {
                if (e.target && e.target.closest && e.target.closest('.row-action-menu')) return;
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            }, true);

            // close on .tw-table-wrap horizontal scroll
            document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
                wrap.addEventListener('scroll', function () {
                    document.querySelectorAll('.row-action-menu').forEach(function (m) {
                        m.classList.add('hidden');
                    });
                });
            });

            $(document).ready(function () {

                // ── DataTable init ────────────────────────────────────────
                if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#testimonialTable')) {
                    $('#testimonialTable').DataTable({
                        "order": [[1, "desc"]],
                        "pageLength": 10,
                        "deferRender": true,
                        "processing": true,
                        'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                        'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                    });
                }

                // ── Edit Testimonial ──────────────────────────────────────
                $(document).on('click', '.testimonial_edit_btn', function () {
                    var el = $(this);
                    var form = $('#testimonial_edit_modal_form');

                    form.attr('action', el.data('action'));
                    form.find('.testimonial_id').val(el.data('id'));
                    form.find('.edit_name').val(el.data('name'));
                    form.find('.edit_description').val(el.data('description'));
                    form.find('.edit_designation').val(el.data('designation'));
                    form.find('.edit_company').val(el.data('company'));
                    form.find('.edit_status option[value="' + el.data('status') + '"]').attr('selected', true);

                    var imageid = el.data('imageid');
                    var image = el.data('image');
                    var section = $('#edit_image_section');

                    // Reset image preview
                    section.find('.tw-attachment-preview').html('').removeClass('relative inline-block');
                    section.find('.tw-media-id-input').val('');
                    section.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> Upload Image');

                    if (imageid && image) {
                        section.find('.tw-attachment-preview').html(
                            '<img src="' + image + '" alt="" class="tw-preview-img w-24 h-24 rounded-lg object-cover border border-gray-200" />' +
                            '<button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>'
                        ).addClass('relative inline-block');
                        section.find('.tw-media-id-input').val(imageid);
                        section.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> Change Image');
                    }

                    // Sync to hidden image input
                    form.find('.edit_image_sync').val(imageid || '');

                    openModal('testimonial_edit_modal');
                });

                // ── Sync edit_image → image on form submit ────────────────
                $(document).on('submit', '#testimonial_edit_modal_form', function () {
                    var val = $('#edit_image_section').find('.tw-media-id-input').val();
                    $(this).find('.edit_image_sync').val(val);
                });

                // ── Clone ─────────────────────────────────────────────────
                $(document).on('click', '.clone_btn', function (e) {
                    e.preventDefault();
                    document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                    $(this).closest('td').find('.clone_form .clone_submit_btn').trigger('click');
                });

                // ── Delete Confirmation ───────────────────────────────────
                $(document).on('click', '.swal_delete_button', function (e) {
                    e.preventDefault();
                    document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                    var btn = $(this);
                    Swal.fire({
                        title: '{{ __('Are you sure?') }}',
                        text: '{{ __('You would not be able to revert this item!') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1F51FF',
                        cancelButtonColor: '#D2042D',
                        confirmButtonText: '{{__("Yes, delete it!")}}',
                        cancelButtonText: "{{__('Cancel')}}",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            btn.closest('td').find('form:last .swal_form_submit_btn').trigger('click');
                        }
                    });
                });

                // ── Close modals on Escape ────────────────────────────────
                $(document).on('keydown', function (e) {
                    if (e.key === 'Escape') {
                        closeModal('new_testimonial_modal');
                        closeModal('testimonial_edit_modal');
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
