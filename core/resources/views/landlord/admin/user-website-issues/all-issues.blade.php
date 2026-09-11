@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All User Website Issues')}} @endsection

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
            <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-alert-decagram-outline text-warning text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All User Website Issues')}}</h3>
                <p class="text-xs text-muted">{{__('Domain and database creation issues')}}</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="allIssuesTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Issue Type')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Domain')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Domain Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_issues as $data)
                @php
                    $userName = $data->tenant?->payment_log?->user?->name ?? '—';
                    $userId   = $data->tenant?->payment_log?->user?->id ?? '—';
                    $domainStatusText = \App\Enums\DomainCreateStatusEnum::getText($data->domain_create_status);
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
                    </td>

                    {{-- User --}}
                    <td class="px-4 py-3.5">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-dark truncate leading-tight">{{$userName}}</p>
                            <p class="text-[11px] text-muted mt-0.5">{{__('ID:')}} {{$userId}}</p>
                        </div>
                    </td>

                    {{-- Issue Type --}}
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-warning-soft text-warning text-[11px] font-bold border border-main">
                            <i class="mdi mdi-alert-outline text-[10px]"></i>
                            {{ ucfirst($data->issue_type) }}
                        </span>
                    </td>

                    {{-- Domain --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="text-xs text-muted font-mono">{{ $data->tenant_id }}</span>
                    </td>

                    {{-- Domain Status --}}
                    <td class="px-4 py-3.5">
                        @if($data->domain_create_status == 0)
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-danger-soft text-danger text-[10px] font-bold uppercase">
                                <i class="mdi mdi-close-circle text-[10px]"></i> {{$domainStatusText}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[10px]"></i> {{$domainStatusText}}
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">

                                {{-- Trigger button --}}
                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-cog-outline text-sm"></i>
                                    {{__('Actions')}}
                                    <i class="mdi mdi-chevron-down text-sm"></i>
                                </button>

                                {{-- Dropdown panel --}}
                                <div class="row-action-menu hidden">

                                    {{-- View Details --}}
                                    <button type="button" class="action-item view_issue_button"
                                            data-id="{{$data->id}}"
                                            data-user_id="{{$userId}}"
                                            data-user_name="{{$userName}}"
                                            data-issue_type="{{ ucfirst($data->issue_type)}}"
                                            data-domain="{{$data->tenant_id}}"
                                            data-domain_status="{{$domainStatusText}}"
                                            data-expected_database_name="{{env('TENANT_DATABASE_PREFIX').$data->tenant_id}}"
                                            data-description="{{$data->description}}">
                                        <span class="action-icon bg-info-soft"><i class="mdi mdi-eye-outline text-info"></i></span>
                                        {{__('View Details')}}
                                    </button>

                                    @if($data->domain_create_status == 0)
                                        {{-- Generate Domain --}}
                                        <button type="button" class="action-item generate_domain_btn" data-id="{{$data->id}}">
                                            <span class="action-icon bg-success-soft"><i class="mdi mdi-play-circle-outline text-success"></i></span>
                                            {{__('Generate')}}
                                        </button>

                                        {{-- Set Database Manually --}}
                                        <button type="button" class="action-item manual_database_btn" data-exception_id="{{$data->id}}">
                                            <span class="action-icon bg-primary-soft"><i class="mdi mdi-database-cog-outline text-primary"></i></span>
                                            {{__('Set Database Manually')}}
                                        </button>

                                        <div class="menu-divider"></div>

                                        {{-- Delete --}}
                                        <button type="button" class="action-item action-danger swal_delete_button" data-id="{{$data->id}}">
                                            <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                            {{__('Delete')}}
                                        </button>
                                        <form method="post" action="{{route('landlord.admin.tenant.exception.delete', $data->id)}}" class="hidden d-none">
                                            @csrf
                                            <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Hidden generate form --}}
                            @if($data->domain_create_status == 0)
                                <form action="{{route('landlord.admin.failed.domain.generate')}}" method="post"
                                      class="hidden d-none generate-form-{{$data->id}}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$data->id}}">
                                    <button type="submit" class="hidden d-none"></button>
                                </form>
                            @endif
                        </div>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- ===== TAILWIND MODALS ===== --}}

{{-- View Issue Details Modal --}}
<div id="view_details_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="view_details_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-md overflow-hidden border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-information-outline text-info text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Issue Details')}}</h5>
                <p class="text-[11px] text-muted">{{__('Full breakdown of the reported issue')}}</p>
            </div>
            <button type="button" id="view_details_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-info-soft hover:text-info transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <div class="px-6 py-5 issue-details-body max-h-[65vh] overflow-y-auto">
            {{-- Populated dynamically via JS --}}
        </div>

        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
            <button type="button" id="view_details_cancel"
                    class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                {{__('Close')}}
            </button>
        </div>
    </div>
</div>

{{-- Set Database Manually Modal --}}
<div id="manual_db_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="manual_db_backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-md overflow-hidden border border-main">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-database-cog-outline text-primary text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Set Database Manually')}}</h5>
                <p class="text-[11px] text-muted">{{__('Configure database connection for this tenant')}}</p>
            </div>
            <button type="button" id="manual_db_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-primary-soft hover:text-primary transition">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        <form action="{{route('landlord.admin.failed.database.generate')}}" id="manual_database_form" method="post" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-5 space-y-4">
                <input type="hidden" class="exception_id" name="exception_id" value="">

                {{-- Database Name --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Database Name')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-database-outline text-lg text-primary"></i>
                        <input type="text" name="database_name" id="database-name" autocomplete="off"
                               placeholder="{{__('Database name')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                {{-- Toggle: Use Database Username & Password --}}
                <div class="flex items-center justify-between bg-secondary border border-main rounded-xl px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <i class="mdi mdi-account-key-outline text-lg text-primary"></i>
                        <span class="text-sm text-dark">{{__('Use Database Username & Password')}}</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="dbUserSwitch" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full after:h-5 after:w-5
                                    after:transition-all peer-checked:after:translate-x-full
                                    after:shadow-sm"></div>
                    </label>
                </div>

                {{-- DB User Fields (hidden by default) --}}
                <div id="dbUserFields" class="space-y-3" style="display:none;">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Database Username')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-account-outline text-lg text-primary"></i>
                            <input type="text" name="database_user_name" id="database_user_name" autocomplete="off"
                                   placeholder="{{__('Database User name')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Database Password')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-lock-outline text-lg text-primary"></i>
                            <input type="password" name="database_user_password" id="database_user_password" autocomplete="off"
                                   placeholder="{{__('Database User password')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                </div>

                {{-- Info Note --}}
                <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                    <i class="mdi mdi-information-outline text-primary text-lg mt-0.5 shrink-0"></i>
                    <span class="text-[12px] text-dark leading-relaxed">{{__('Set your database name here.')}}</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                <button type="button" id="manual_db_cancel"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i>
                    {{__('Submit')}}
                </button>
            </div>
        </form>
    </div>
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
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

        function escHtml(str) {
            if (str == null) return '';
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
        }

        $(document).ready(function () {

            // ── DataTable init ────────────────────────────────────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#allIssuesTable')) {
                $('#allIssuesTable').DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── View Issue Details Modal ──────────────────────────────
            $(document).on('click', '.view_issue_button', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let el = $(this);

                let markup = `
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('User Information')}}</p>
                            <div class="bg-secondary rounded-xl border border-main p-3 space-y-1.5">
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-identifier text-primary"></i> <span class="text-muted w-28 shrink-0">{{__('Issue ID')}}</span> ${escHtml(el.data('id'))}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-account-outline text-primary"></i> <span class="text-muted w-28 shrink-0">{{__('User ID')}}</span> ${escHtml(el.data('user_id'))}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-account text-primary"></i> <span class="text-muted w-28 shrink-0">{{__('User Name')}}</span> ${escHtml(el.data('user_name'))}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Issue Information')}}</p>
                            <div class="bg-secondary rounded-xl border border-main p-3 space-y-1.5">
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-alert-outline text-warning"></i> <span class="text-muted w-28 shrink-0">{{__('Issue Type')}}</span> ${escHtml(el.data('issue_type'))}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-web text-primary"></i> <span class="text-muted w-28 shrink-0">{{__('Domain')}}</span> ${escHtml(el.data('domain'))}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-check-decagram text-primary"></i> <span class="text-muted w-28 shrink-0">{{__('Domain Status')}}</span> ${escHtml(el.data('domain_status'))}</p>
                                <p class="text-xs text-dark flex items-center gap-2"><i class="mdi mdi-database-outline text-primary"></i> <span class="text-muted w-28 shrink-0">{{__('Expected DB')}}</span> <span class="font-mono">${escHtml(el.data('expected_database_name'))}</span></p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Description')}}</p>
                            <div class="bg-secondary rounded-xl border border-main p-3">
                                <p class="text-xs text-dark leading-relaxed">${escHtml(el.data('description'))}</p>
                            </div>
                        </div>
                    </div>
                `;

                $('#view_details_modal .issue-details-body').html(markup);
                openModal('view_details_modal');
            });
            $('#view_details_close, #view_details_cancel, #view_details_backdrop').on('click', function () {
                closeModal('view_details_modal');
            });

            // ── Generate Domain ────────────────────────────────────────
            $(document).on('click', '.generate_domain_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let id = $(this).data('id');
                $('.generate-form-' + id).submit();
            });

            // ── Manual Database Modal ──────────────────────────────────
            $(document).on('click', '.manual_database_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let exception_id = $(this).data('exception_id');
                $('#manual_database_form .exception_id').val(exception_id);
                openModal('manual_db_modal');
            });
            $('#manual_db_close, #manual_db_cancel, #manual_db_backdrop').on('click', function () {
                closeModal('manual_db_modal');
            });

            // ── DB User Switch Toggle ──────────────────────────────────
            document.getElementById('dbUserSwitch').addEventListener('change', function () {
                document.getElementById('dbUserFields').style.display = this.checked ? 'block' : 'none';
            });

        });
    })(jQuery);
    </script>
@endsection
