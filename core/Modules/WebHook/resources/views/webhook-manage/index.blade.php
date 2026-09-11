@extends('landlord.admin.admin-master')

@section('title')
    {{ __('All Web Hooks') }}
@endsection

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
                <i class="mdi mdi-webhook text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All WebHooks')}}</h3>
                <p class="text-xs text-muted">{{__('Configure webhooks based on various events')}}</p>
            </div>
        </div>
        <button type="button" class="open_add_webhook_modal inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
            <i class="mdi mdi-plus text-base"></i>
            {{__('Add New WebHook')}}
        </button>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="allWebHooksTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14 no-sort">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('URL')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Method')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Events')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_webhook as $webk)
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$webk->id}}</span>
                    </td>

                    {{-- Name --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$webk->name}}</span>
                    </td>

                    {{-- URL --}}
                    <td class="px-4 py-3.5">
                        <code class="text-xs text-muted bg-secondary px-1.5 py-0.5 rounded" style="max-width: 200px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: middle;">{{$webk->url}}</code>
                    </td>

                    {{-- Method --}}
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-info-soft text-info text-[10px] font-bold uppercase">{{$webk->method_type}}</span>
                    </td>

                    {{-- Events --}}
                    <td class="px-4 py-3.5">
                        <div class="flex flex-wrap gap-1">
                            @foreach($webk->events->pluck("event_name")->toArray() as $evt)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-secondary border border-main text-muted">{{$evt}}</span>
                            @endforeach
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3.5">
                        @if($webk->status === \App\Enums\StatusEnums::PUBLISH)
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

                                <button type="button"
                                        data-action="{{route('webhook.update',$webk->id)}}"
                                        data-settings="{{json_encode(['id' => $webk->id,'url' => $webk->url,'name' => $webk->name,'method_type' => $webk->method_type,'events' => $webk->events->pluck("event_name")->toArray(), 'status' => $webk->status])}}"
                                        class="btn_webhook_edit w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:text-white hover:bg-primary hover:border-primary transition-all"
                                        title="{{__('Edit')}}">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </button>

                                {{-- Dropdown trigger --}}
                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-dots-vertical text-sm"></i>
                                </button>

                                {{-- Dropdown panel --}}
                                <div class="row-action-menu hidden">
                                    <button type="button"
                                            data-action="{{route('webhook.update',$webk->id)}}"
                                            data-settings="{{json_encode(['id' => $webk->id,'url' => $webk->url,'name' => $webk->name,'method_type' => $webk->method_type,'events' => $webk->events->pluck("event_name")->toArray(), 'status' => $webk->status])}}"
                                            class="btn_webhook_edit">
                                        <span class="action-icon bg-primary-soft"><i class="mdi mdi-pencil-outline text-primary"></i></span>
                                        {{__('Edit WebHook')}}
                                    </button>

                                    <div class="menu-divider"></div>

                                    <button type="button" class="action-item action-danger swal_delete_button">
                                        <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                        {{__('Delete WebHook')}}
                                    </button>
                                </div>
                            </div>

                            {{-- Hidden delete form --}}
                            <form method="post" action="{{route('webhook.destroy',$webk->id)}}" class="hidden d-none">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                            </form>
                        </div>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Add New WebHook Modal --}}
<div id="add_webhook_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm webhook-modal-overlay"></div>
    <div class="relative bg-surface rounded-2xl border border-main w-full max-w-md overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
        <div class="px-5 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-primary-soft">
                    <i class="mdi mdi-plus-circle-outline text-sm text-primary"></i>
                </div>
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('New WebHook')}}</h5>
            </div>
            <button type="button" class="webhook-modal-close w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                <i class="mdi mdi-close text-muted"></i>
            </button>
        </div>
        <form action="{{route("webhook.store")}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}} <span class="text-danger">*</span></label>
                    <input type="text" class="w-full border border-main rounded-xl px-3 py-2 text-sm text-dark bg-secondary outline-none focus:border-primary transition" name="name" placeholder="{{__('WebHook name')}}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('URL')}} <span class="text-danger">*</span></label>
                    <input type="url" class="w-full border border-main rounded-xl px-3 py-2 text-sm text-dark bg-secondary outline-none focus:border-primary transition" name="url" placeholder="{{__('https://example.com/webhook')}}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Method')}}</label>
                    <select name="method_type" class="w-full border border-main rounded-xl px-3 py-2 text-sm text-dark bg-secondary outline-none focus:border-primary transition cursor-pointer">
                        <option value="GET">{{__('GET')}}</option>
                        <option value="POST">{{__('POST')}}</option>
                    </select>
                </div>

                @if(is_null(tenant()))
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Events')}}</label>
                        <div class="space-y-2">
                            @foreach(['user:register' => 'User Register', 'user:login' => 'User Login', 'user:delete' => 'User Delete'] as $val => $lbl)
                                <label class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-secondary border border-main cursor-pointer hover:border-primary transition group">
                                    <span class="text-xs font-semibold text-muted group-hover:text-dark transition">{{__($lbl)}}</span>
                                    <input type="checkbox" name="event[]" value="{{$val}}" class="w-4 h-4 rounded border-2 border-main accent-primary">
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                    <select name="status" class="w-full border border-main rounded-xl px-3 py-2 text-sm text-dark bg-secondary outline-none focus:border-primary transition cursor-pointer">
                        <option value="{{\App\Enums\StatusEnums::PUBLISH}}">{{__('Publish')}}</option>
                        <option value="{{\App\Enums\StatusEnums::DRAFT}}">{{__('Draft')}}</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main" style="background: var(--color-bg-secondary);">
                <button type="button" class="webhook-modal-close px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit WebHook Modal --}}
<div id="edit_webhook_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm webhook-modal-overlay"></div>
    <div class="relative bg-surface rounded-2xl border border-main w-full max-w-md overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
        <div class="px-5 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-primary-soft">
                    <i class="mdi mdi-pencil-outline text-sm text-primary"></i>
                </div>
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Edit WebHook')}}</h5>
            </div>
            <button type="button" class="webhook-modal-close w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                <i class="mdi mdi-close text-muted"></i>
            </button>
        </div>
        <form action="#" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}} <span class="text-danger">*</span></label>
                    <input type="text" class="w-full border border-main rounded-xl px-3 py-2 text-sm text-dark bg-secondary outline-none focus:border-primary transition" name="name" placeholder="{{__('WebHook name')}}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('URL')}} <span class="text-danger">*</span></label>
                    <input type="url" class="w-full border border-main rounded-xl px-3 py-2 text-sm text-dark bg-secondary outline-none focus:border-primary transition" name="url" placeholder="{{__('https://example.com/webhook')}}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Method')}}</label>
                    <select name="method_type" class="w-full border border-main rounded-xl px-3 py-2 text-sm text-dark bg-secondary outline-none focus:border-primary transition cursor-pointer">
                        <option value="GET">{{__('GET')}}</option>
                        <option value="POST">{{__('POST')}}</option>
                    </select>
                </div>

                @if(is_null(tenant()))
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Events')}}</label>
                        <div class="space-y-2">
                            @foreach(['user:register' => 'User Register', 'user:login' => 'User Login', 'user:delete' => 'User Delete'] as $val => $lbl)
                                <label class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-secondary border border-main cursor-pointer hover:border-primary transition group">
                                    <span class="text-xs font-semibold text-muted group-hover:text-dark transition">{{__($lbl)}}</span>
                                    <input type="checkbox" name="event[]" value="{{$val}}" class="w-4 h-4 rounded border-2 border-main accent-primary">
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                    <select name="status" class="w-full border border-main rounded-xl px-3 py-2 text-sm text-dark bg-secondary outline-none focus:border-primary transition cursor-pointer">
                        <option value="{{\App\Enums\StatusEnums::PUBLISH}}">{{__('Publish')}}</option>
                        <option value="{{\App\Enums\StatusEnums::DRAFT}}">{{__('Draft')}}</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main" style="background: var(--color-bg-secondary);">
                <button type="button" class="webhook-modal-close px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

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

            // ── DataTable init ────────────────────────────────────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#allWebHooksTable')) {
                $('#allWebHooksTable').DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── SweetAlert Delete ─────────────────────────────────────
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let btn = $(this);
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
                        btn.closest('td').find('form').trigger('submit');
                    }
                });
            });

            // ── Edit webhook: populate modal ──────────────────────────
            $(document).on("click", ".btn_webhook_edit", function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });

                let el = $(this);
                let allSettings = el.data('settings');
                let modalContainer = $('#edit_webhook_modal');

                modalContainer.find('form').attr('action', el.data('action'));
                modalContainer.find('input[name="name"]').val(allSettings.name);
                modalContainer.find('input[name="url"]').val(allSettings.url);
                modalContainer.find('select[name="method_type"]').val(allSettings.method_type);
                modalContainer.find('select[name="status"]').val(allSettings.status);

                let allEvents = modalContainer.find('input[name="event[]"]');
                $.each(allEvents, function (index, item) {
                    item.checked = allSettings.events.includes(item.value);
                });

                $('#edit_webhook_modal').removeClass('hidden');
            });

            // ── Open add modal ────────────────────────────────────────
            $(document).on('click', '.open_add_webhook_modal', function () {
                $('#add_webhook_modal').removeClass('hidden');
            });

            // ── Close modals ──────────────────────────────────────────
            $(document).on('click', '.webhook-modal-close, .webhook-modal-overlay', function () {
                $(this).closest('.fixed').addClass('hidden');
            });

            $(document).on('keydown', function (e) {
                if (e.key === 'Escape') {
                    $('#add_webhook_modal, #edit_webhook_modal').addClass('hidden');
                }
            });
        });
    })(jQuery);
    </script>
@endsection
