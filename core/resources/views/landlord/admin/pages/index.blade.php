@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Pages')}} @endsection

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
                <i class="mdi mdi-file-document-multiple-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Pages')}}</h3>
                <p class="text-xs text-muted">{{__('Manage your website pages')}}</p>
            </div>
        </div>
        @can('page-create')
        <a href="{{route(route_prefix().'admin.pages.create')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
            <i class="mdi mdi-plus text-base"></i>
            {{__('Create New Page')}}
        </a>
        @endcan
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="allPagesTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14 no-sort">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Title')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Created')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_pages as $page)
                @php
                    $selected_page = get_static_option('home_page');
                    $is_home = $page->id == $selected_page;
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$page->id}}</span>
                    </td>

                    {{-- Title --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-dark">{{ $page->title }}</span>
                            @if($is_home)
                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-info-soft text-info text-[9px] font-bold uppercase">
                                    <i class="mdi mdi-home text-[9px]"></i> {{__('Home')}}
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3.5">
                        @if($page->status === 1)
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[10px]"></i> {{__('Published')}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase">
                                <i class="mdi mdi-pencil-outline text-[10px]"></i> {{__('Draft')}}
                            </span>
                        @endif
                    </td>

                    {{-- Created --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="text-xs text-muted">{{$page->created_at->format('D, d-m-y')}}</span>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">

                                {{-- Edit icon --}}
                                @can('page-edit')
                                <a href="{{route(route_prefix().'admin.pages.edit', $page->id)}}"
                                   title="{{__('Edit')}}"
                                   class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:text-white hover:bg-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </a>
                                @endcan

                                {{-- View icon --}}
                                <a href="{{route(route_prefix().'dynamic.page', $page->slug)}}" target="_blank"
                                   title="{{__('View in frontend')}}"
                                   class="w-9 h-9 mr-1 rounded-lg bg-info-soft border border-main flex items-center justify-center hover:text-white hover:bg-info hover:border-info transition-all">
                                    <i class="mdi mdi-eye-outline text-sm"></i>
                                </a>

                                {{-- Dropdown trigger --}}
                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-dots-vertical text-sm"></i>
                                </button>

                                {{-- Dropdown panel --}}
                                <div class="row-action-menu hidden">

                                    {{-- Page Builder --}}
                                    @if($page->page_builder === 1 || $page->use_page_builder)
                                        <a href="{{route(route_prefix().'admin.page-builder.edit', $page->id)}}">
                                            <span class="action-icon bg-[#f3e8ff]"><i class="mdi mdi-cog-outline text-[#9333ea]"></i></span>
                                            {{__('Edit with Page-Builder')}}
                                        </a>
                                    @endif

                                    {{-- Set as Home --}}
                                    <button type="button" class="action-item swal_change_button">
                                        <span class="action-icon bg-info-soft"><i class="mdi mdi-home-outline text-info"></i></span>
                                        {{__('Set as Home')}}
                                    </button>

                                    @if(tenant())
                                        {{-- Download Layout --}}
                                        <a href="{{route(route_prefix().'admin.pages.download', $page->id)}}">
                                            <span class="action-icon bg-success-soft"><i class="mdi mdi-download text-success"></i></span>
                                            {{__('Download Layout')}}
                                        </a>

                                        {{-- Upload Layout --}}
                                        <button type="button" class="action-item upload-layout-btn" data-id="{{$page->id}}">
                                            <span class="action-icon bg-warning-soft"><i class="mdi mdi-upload text-warning"></i></span>
                                            {{__('Upload Layout')}}
                                        </button>
                                    @endif

                                    @if(!$is_home)
                                        <div class="menu-divider"></div>

                                        {{-- Delete --}}
                                        @can('page-delete')
                                        <button type="button" class="action-item action-danger swal_delete_button">
                                            <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                            {{__('Delete Page')}}
                                        </button>
                                        <form method="post" action="{{route(route_prefix().'admin.pages.delete', $page->id)}}" class="hidden d-none">
                                            @csrf
                                            <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                                        </form>
                                        @endcan
                                    @endif
                                </div>
                            </div>

                            {{-- Hidden set-as-home form --}}
                            <form method="post" action="{{route(route_prefix().'admin.general.page.settings.home')}}" class="swal_change_form hidden d-none">
                                <input type="hidden" name="_token" value="{{csrf_token()}}">
                                <input type="hidden" name="home_page" value="{{$page->id}}">
                                <button type="submit" class="swal_change_submit_btn hidden d-none"></button>
                            </form>
                        </div>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Upload Layout Modal (tenant only) --}}
@if(tenant())
<div id="upload_layout_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="upload_layout_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-md overflow-hidden border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-upload text-warning text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Upload Page Layout')}}</h5>
                <p class="text-[11px] text-muted">{{__('Upload a JSON layout file')}}</p>
            </div>
            <button type="button" id="upload_layout_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-warning-soft hover:text-warning transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <form action="{{route('tenant.admin.pages.upload')}}" method="POST" enctype="multipart/form-data" id="upload_layout_form">
            @csrf
            <div class="px-6 py-5 space-y-4">
                <input type="hidden" name="page_id" value="">

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Upload JSON File')}}</label>
                    <div class="bg-secondary border border-main rounded-xl px-4 py-3 focus-within:border-primary transition">
                        <input type="file" name="page_layout" class="text-sm text-dark file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-main file:text-sm file:font-medium file:bg-surface file:text-dark hover:file:bg-primary-soft hover:file:text-primary cursor-pointer">
                    </div>
                </div>

                <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                    <i class="mdi mdi-alert-circle-outline text-warning text-lg mt-0.5 shrink-0"></i>
                    <span class="text-[12px] text-dark leading-relaxed">{{__('Previous layout along with its data will be removed permanently if you upload this new layout!')}}</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" id="upload_layout_cancel"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-success rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-upload text-base"></i>
                    {{__('Upload')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <script>
    (function ($) {
        "use strict";

        // ── Modal helpers ────────────────────────────────────────────────
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // ── Row action dropdown ──────────────────────────────────────────
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
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#allPagesTable')) {
                $('#allPagesTable').DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── Upload Layout Modal ──────────────────────────────────
            $(document).on('click', '.upload-layout-btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let page_id = $(this).data('id');
                $('#upload_layout_form input[name=page_id]').val(page_id);
                openModal('upload_layout_modal');
            });
            $('#upload_layout_close, #upload_layout_cancel, #upload_layout_backdrop').on('click', function () {
                closeModal('upload_layout_modal');
            });

            $(document).on('submit', '#upload_layout_form', function (e) {
                e.preventDefault();
                let form = $(this);
                Swal.fire({
                    title: '<strong style="color:red">{{ __("Are you sure?") }}</strong>',
                    text: '{{ __("Previous layout along with its data will be removed permanently if you upload this new layout!") }}',
                    icon: 'warning',
                    iconColor: 'red',
                    showCancelButton: true,
                    confirmButtonColor: '#1F51FF',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: '{{__("Yes, upload it!")}}',
                    cancelButtonText: "{{__('Cancel')}}",
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form[0].submit();
                    }
                });
            });

            // ── Set as Home ──────────────────────────────────────────
            $(document).on('click', '.swal_change_button', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let btn = $(this);
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You can revert this item anytime!") }}',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#1F51FF',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{__("Yes, Change it!")}}',
                    cancelButtonText: "{{__('Cancel')}}",
                }).then(function (result) {
                    if (result.isConfirmed) {
                        btn.closest('td').find('form.swal_change_form').trigger('submit');
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
