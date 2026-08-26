@php
    $route_name = is_null(tenant()) ? 'landlord' : 'tenant';
@endphp
@extends($route_name.'.admin.admin-master')

@section('title')
    {{__('Languages')}}
@endsection

@section('style')
    <x-datatable.tw-css/>
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/admin/css/components/languages.css')}}">
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
                    <i class="mdi mdi-translate text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Languages')}}</h3>
                    <p class="text-xs text-muted">{{__('Manage available languages for your site')}}</p>
                </div>
            </div>
            @can('language-create')
                <button type="button" class="lang_add_btn inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                    <i class="mdi mdi-plus text-base"></i>
                    {{__('Add Language')}}
                </button>
            @endcan
        </div>

        @if(tenant())
            <div class="px-4 sm:px-6 py-2.5 border-b border-main" style="background: var(--color-info-bg);">
                <p class="text-xs font-medium" style="color: var(--color-info);"><i class="mdi mdi-information-outline mr-1"></i>{{__('Landlord will generate translations for each languages')}}</p>
            </div>
        @endif

        {{-- Table --}}
        <div class="tw-table-wrap">
            <table class="w-full text-left" id="allLanguagesTable">
                <thead>
                    <tr class="border-b border-main">
                        <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14 no-sort">{{__('ID')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                        <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Direction')}}</th>
                        <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Slug')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($all_lang as $data)
                    <tr class="border-b border-main hover:bg-muted transition-colors">

                        {{-- ID --}}
                        <td class="hidden md:table-cell px-4 py-3.5">
                            <span class="text-[11px] font-bold text-primary">#{{$data->id}}</span>
                        </td>

                        {{-- Name --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-dark">{{$data->name}}</span>
                                @if($data->default == 1)
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full bg-success-soft text-success text-[9px] font-bold uppercase">
                                        <i class="mdi mdi-check-circle text-[10px]"></i> {{__('Default')}}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Direction --}}
                        <td class="hidden sm:table-cell px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $data->direction == 1 ? 'bg-warning-soft text-warning' : 'bg-primary-soft text-primary' }}">
                                {{strtoupper(\App\Enums\LanguageEnums::getdirection($data->direction))}}
                            </span>
                        </td>

                        {{-- Slug --}}
                        <td class="hidden sm:table-cell px-4 py-3.5">
                            <code class="text-xs font-mono px-1.5 py-0.5 rounded bg-secondary text-brand">{{$data->slug}}</code>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3.5">
                            @if($data->status == 1)
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                    <i class="mdi mdi-check-circle text-[10px]"></i> {{__('Active')}}
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

                                    <a href="{{route(route_prefix().'admin.languages.words.all',$data->slug)}}"
                                       title="{{__('Edit Words')}}"
                                       class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:text-white hover:bg-primary hover:border-primary transition-all">
                                        <i class="mdi mdi-pencil-outline text-sm"></i>
                                    </a>

                                    {{-- Dropdown trigger --}}
                                    <button type="button" onclick="toggleRowMenu(this)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                        <i class="mdi mdi-dots-vertical text-sm"></i>
                                    </button>

                                    {{-- Dropdown panel --}}
                                    <div class="row-action-menu hidden">
                                        <a href="{{route(route_prefix().'admin.languages.words.all',$data->slug)}}">
                                            <span class="action-icon bg-primary-soft"><i class="mdi mdi-pencil-outline text-primary"></i></span>
                                            {{__('Edit All Words')}}
                                        </a>

                                        @can('language-edit')
                                            <button type="button" class="action-item lang_edit_btn"
                                                    data-id="{{$data->id}}"
                                                    data-name="{{$data->name}}"
                                                    data-slug="{{$data->slug}}"
                                                    data-status="{{$data->status}}"
                                                    data-direction="{{$data->direction}}">
                                                <span class="action-icon bg-info-soft"><i class="mdi mdi-cog-outline text-info"></i></span>
                                                {{__('Edit Language')}}
                                            </button>

                                            <button type="button" class="action-item lang_clone_btn"
                                                    data-id="{{$data->id}}">
                                                <span class="action-icon bg-success-soft"><i class="mdi mdi-content-copy text-success"></i></span>
                                                {{__('Clone Language')}}
                                            </button>

                                            @if($data->default != 1)
                                                <button type="button" class="action-item swal_change_language_button">
                                                    <span class="action-icon bg-warning-soft"><i class="mdi mdi-star-outline text-warning"></i></span>
                                                    {{__('Set as Default')}}
                                                </button>
                                            @endif
                                        @endcan

                                        @can('language-delete')
                                            @if($data->default != 1)
                                                <div class="menu-divider"></div>
                                                <button type="button" class="action-item action-danger swal_delete_button">
                                                    <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                                    {{__('Delete Language')}}
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </div>

                                {{-- Hidden forms --}}
                                @can('language-edit')
                                    @if($data->default != 1)
                                        <form method="post" action="{{route(route_prefix().'admin.languages.default',$data->id)}}" class="hidden d-none lang-default-form">
                                            @csrf
                                            <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                                        </form>
                                    @endif
                                @endcan
                                @can('language-delete')
                                    @if($data->default != 1)
                                        <form method="post" action="{{route(route_prefix().'admin.languages.delete',$data->id)}}" class="hidden d-none lang-delete-form">
                                            @csrf
                                            <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{-- Add New Language Modal --}}
    @can('language-create')
        <div id="language_add_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm lang-modal-overlay"></div>
            <div class="relative bg-surface rounded-2xl border border-main w-full max-w-md overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
                <div class="px-5 py-4 border-b border-main flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center">
                            <i class="mdi mdi-plus-circle-outline text-primary text-sm"></i>
                        </div>
                        <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Add New Language')}}</h5>
                    </div>
                    <button type="button" class="lang-modal-close w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                        <i class="mdi mdi-close text-muted"></i>
                    </button>
                </div>
                <form action="{{route(route_prefix().'admin.languages.new')}}" method="post" enctype="multipart/form-data" class="new_language_form">
                    @csrf
                    <div class="p-5 space-y-4">
                        <input type="hidden" name="name">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Language')}}</label>
                            @if(!tenant())
                                <select name="language_select" class="lang-select">
                                    @include('landlord.admin.languages.partials.language-options')
                                </select>
                            @else
                                @php $languages = get_static_option_central('central_language'); @endphp
                                <select name="language_select" class="lang-select">
                                    @foreach(json_decode($languages) ?? [] as $language)
                                        <option value="{{$language->slug}}" lang="{{$language->slug}}">{{__($language->name)}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Direction')}}</label>
                            <select name="direction" class="lang-select">
                                <option value="0">{{__('LTR (Left to Right)')}}</option>
                                <option value="1">{{__('RTL (Right to Left)')}}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                            <select name="status" class="lang-select">
                                <option value="1">{{__('Publish')}}</option>
                                <option value="0">{{__('Draft')}}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Slug')}}</label>
                            <input type="text" class="lang-input" readonly name="slug" placeholder="{{__('Auto-generated')}}">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main" style="background: var(--color-bg-secondary);">
                        <button type="button" class="lang-modal-close px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background: var(--color-primary);">{{__('Add Language')}}</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    {{-- Edit Language Modal --}}
    @can('language-edit')
        <div id="language_item_edit_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm lang-modal-overlay"></div>
            <div class="relative bg-surface rounded-2xl border border-main w-full max-w-md overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
                <div class="px-5 py-4 border-b border-main flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center">
                            <i class="mdi mdi-pencil-outline text-primary text-sm"></i>
                        </div>
                        <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Language')}}</h5>
                    </div>
                    <button type="button" class="lang-modal-close w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                        <i class="mdi mdi-close text-muted"></i>
                    </button>
                </div>
                <form action="{{route(route_prefix().'admin.languages.update')}}" class="edit_language_form" method="post">
                    <div class="p-5 space-y-4">
                        @csrf
                        <input type="hidden" name="id" id="lang_id" value="">
                        <input type="hidden" name="name">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Language')}}</label>
                            <select name="language_select" class="lang-select">
                                @if(!tenant())
                                    @include('landlord.admin.languages.partials.language-options')
                                @else
                                    @php $languages = get_static_option_central('central_language'); @endphp
                                    @foreach(json_decode($languages) ?? [] as $language)
                                        <option value="{{$language->slug}}" lang="{{$language->slug}}">{{__($language->name)}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Direction')}}</label>
                            <select name="direction" id="edit_direction" class="lang-select">
                                <option value="0">{{__('LTR')}}</option>
                                <option value="1">{{__('RTL')}}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                            <select name="status" id="edit_status" class="lang-select">
                                <option value="1">{{__('Publish')}}</option>
                                <option value="0">{{__('Draft')}}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Slug')}}</label>
                            <input type="text" class="lang-input" id="edit_slug" name="slug" readonly>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main" style="background: var(--color-bg-secondary);">
                        <button type="button" class="lang-modal-close px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background: var(--color-primary);">{{__('Save Changes')}}</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    {{-- Clone Language Modal --}}
    @can('language-edit')
        <div id="language_item_clone_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm lang-modal-overlay"></div>
            <div class="relative bg-surface rounded-2xl border border-main w-full max-w-md overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
                <div class="px-5 py-4 border-b border-main flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--color-success-bg);">
                            <i class="mdi mdi-content-copy text-sm" style="color: var(--color-success);"></i>
                        </div>
                        <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Clone to New Language')}}</h5>
                    </div>
                    <button type="button" class="lang-modal-close w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                        <i class="mdi mdi-close text-muted"></i>
                    </button>
                </div>
                <div class="px-5 py-2.5 border-b border-main" style="background: var(--color-info-bg);">
                    <p class="text-xs font-medium" style="color: var(--color-info);"><i class="mdi mdi-information-outline mr-1"></i>{{__('It will copy all content of static sections, header slider, key features, contact info, support info, pages, menus')}}</p>
                </div>
                <form action="{{route(route_prefix().'admin.languages.clone')}}" method="post">
                    <div class="p-5 space-y-4">
                        @csrf
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="name">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Language')}}</label>
                            <select name="language_select" class="lang-select">
                                @if(!tenant())
                                    @include('landlord.admin.languages.partials.language-options')
                                @else
                                    @php $languages = get_static_option_central('central_language'); @endphp
                                    @foreach(json_decode($languages) ?? [] as $language)
                                        <option value="{{$language->slug}}" lang="{{$language->slug}}">{{__($language->name)}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Direction')}}</label>
                            <select name="direction" class="lang-select">
                                <option value="0">{{__('LTR')}}</option>
                                <option value="1">{{__('RTL')}}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                            <select name="status" class="lang-select">
                                <option value="1">{{__('Publish')}}</option>
                                <option value="0">{{__('Draft')}}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Slug')}}</label>
                            <input type="text" class="lang-input" name="slug" readonly>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main" style="background: var(--color-bg-secondary);">
                        <button type="button" class="lang-modal-close px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background: var(--color-primary);">{{__('Clone Language')}}</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <script>
    (function ($) {
        "use strict";

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

        document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
            wrap.addEventListener('scroll', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            });
        });

        $(document).ready(function () {

            // ── DataTable init ─────────────────────────────────────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#allLanguagesTable')) {
                $('#allLanguagesTable').DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── Language select => auto-fill name + slug ─────────────
            $(document).on('change', 'select[name="language_select"]', function () {
                var el = $(this);
                var name = el.find('option[value="' + el.val() + '"]').text();
                el.closest('form').find('input[name="name"]').val(name);
                el.closest('form').find('input[name="slug"]').val(el.val());
            });

            // ── Open add modal ───────────────────────────────────────
            $(document).on('click', '.lang_add_btn', function () {
                $('#language_add_modal').removeClass('hidden');
            });

            // ── Open edit modal ──────────────────────────────────────
            $(document).on('click', '.lang_edit_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                var el = $(this);
                var modal = $('#language_item_edit_modal');
                modal.find('#lang_id').val(el.data('id'));
                modal.find('input[name="name"]').val(el.data('name'));
                modal.find('select[name="language_select"]').val(el.data('slug'));
                modal.find('#edit_slug').val(el.data('slug'));
                modal.find('#edit_direction').val(el.data('direction'));
                modal.find('#edit_status').val(el.data('status'));
                modal.removeClass('hidden');
            });

            // ── Open clone modal ─────────────────────────────────────
            $(document).on('click', '.lang_clone_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                var modal = $('#language_item_clone_modal');
                modal.find('input[name="id"]').val($(this).data('id'));
                modal.removeClass('hidden');
            });

            // ── Close modals ─────────────────────────────────────────
            $(document).on('click', '.lang-modal-close, .lang-modal-overlay', function () {
                $(this).closest('.fixed').addClass('hidden');
            });

            // ── SweetAlert: Set as Default ───────────────────────────
            $(document).on('click', '.swal_change_language_button', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                var btn = $(this);
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("This will change the default language.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1a5c4e',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{__("Yes, Change it!")}}',
                    cancelButtonText: "{{__('Cancel')}}",
                }).then(function (result) {
                    if (result.isConfirmed) {
                        btn.closest('td').find('.lang-default-form').trigger('submit');
                    }
                });
            });

            // ── SweetAlert: Delete ───────────────────────────────────
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                var btn = $(this);
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You will not be able to recover this!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{__("Yes, Delete it!")}}',
                    cancelButtonText: "{{__('Cancel')}}",
                }).then(function (result) {
                    if (result.isConfirmed) {
                        btn.closest('td').find('.lang-delete-form').trigger('submit');
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
