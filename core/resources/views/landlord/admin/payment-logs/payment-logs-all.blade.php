@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Payment Logs')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <style>
        .row-action-menu {
            position: fixed;
            z-index: 9999;
            min-width: 210px;
            max-height: 320px;
            overflow-y: auto;
            background: var(--color-bg-surface, #fff);
            border: 1px solid var(--color-border-main, #e5e7eb);
            border-radius: 0.875rem;
            box-shadow: 0 8px 24px rgba(0,0,0,.10);
            padding: 0.375rem 0;
        }
        .row-action-menu a,
        .row-action-menu button.action-item {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
            color: var(--color-text-dark, #111827);
            text-decoration: none;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: background 0.12s;
            white-space: nowrap;
        }
        .row-action-menu a:hover,
        .row-action-menu button.action-item:hover { background: var(--color-bg-muted, #f3f4f6); }
        .row-action-menu .action-icon {
            width: 1.375rem;
            height: 1.375rem;
            border-radius: 0.375rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            flex-shrink: 0;
        }
        .row-action-menu .menu-divider {
            height: 1px;
            background: var(--color-border-main, #e5e7eb);
            margin: 0.25rem 0;
        }
        .row-action-menu .action-danger { color: var(--color-danger, #dc2626); }
        .row-action-menu .action-danger:hover { background: #fef2f2; }
        .row-action-wrap { display: inline-flex; }
    </style>
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
                <i class="mdi mdi-credit-card-clock-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Payment Logs')}}</h3>
                <p class="text-xs text-muted">{{__('Track and manage all payment transactions')}}</p>
            </div>
        </div>
    </div>

    {{-- Bulk Action --}}
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

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="paymentLogsTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <input type="checkbox" class="all-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                    </th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Payer Info')}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Package')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Amount')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Gateway')}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Subdomain')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($payment_logs as $data)
                @continue($data->status === 'trial')
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- Checkbox --}}
                    <td class="px-4 py-3.5">
                        <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-primary focus:ring-primary" name="bulk_delete[]" value="{{$data->id}}">
                    </td>

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
                    </td>

                    {{-- Payer Info --}}
                    <td class="px-4 py-3.5">
                        <div class="tw-cell-user">
                            <div class="tw-avatar-initials">
                                {{ strtoupper(substr($data->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="tw-cell-name">{{$data->name}}</div>
                                <div class="tw-cell-sub">{{$data->email}}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Package Name --}}
                    <td class="hidden lg:table-cell px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$data->package_name}}</span>
                    </td>

                    {{-- Amount --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-bold text-dark">{{amount_with_currency_symbol($data->package_price)}}</span>
                    </td>

                    {{-- Gateway --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="tw-pill tw-pill-info">
                            <i class="mdi mdi-credit-card-outline text-[10px] mr-0.5"></i>
                            {{ucwords(str_replace('_', ' ', $data->package_gateway))}}
                        </span>
                    </td>

                    {{-- Subdomain --}}
                    <td class="hidden lg:table-cell px-4 py-3.5">
                        @if($data->tenant_id)
                            <span class="text-xs font-mono text-muted bg-secondary px-2 py-1 rounded-lg">{{$data->tenant_id}}</span>
                        @else
                            <span class="text-xs text-muted italic">{{__('N/A')}}</span>
                        @endif
                    </td>

                    {{-- Payment Status --}}
                    <td class="px-4 py-3.5">
                        @if($data->payment_status == 'pending')
                            <span class="tw-pill tw-pill-warning">
                                <i class="mdi mdi-clock-outline text-[10px] mr-0.5"></i> {{__('Pending')}}
                            </span>
                        @else
                            <span class="tw-pill tw-pill-success">
                                <i class="mdi mdi-check-circle text-[10px] mr-0.5"></i> {{__('Complete')}}
                            </span>
                        @endif
                    </td>

                    {{-- Date --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="text-xs text-muted">{{date_format($data->created_at, 'd M Y')}}</span>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <div class="row-action-wrap">

                                {{-- View --}}
                                <a href="{{route(route_prefix().'admin.package.order.manage.view', $data->id)}}"
                                   title="{{__('View Details')}}"
                                   class="tw-btn-icon tw-btn-icon-view mr-1">
                                    <i class="mdi mdi-eye-outline"></i>
                                </a>

                                {{-- Approve (manual payment only) --}}
                                @if($data->package_gateway == 'manual_payment' && $data->payment_status == 'pending')
                                    <x-approve-popover-tw url="{{route(route_prefix().'admin.payment.approve', $data->id)}}"/>
                                @endif

                                {{-- More Actions --}}
                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center gap-1.5 px-2 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all ml-1">
                                    <i class="mdi mdi-dots-vertical text-sm"></i>
                                </button>

                                {{-- Dropdown --}}
                                <div class="row-action-menu hidden">
                                    <a href="{{route(route_prefix().'admin.package.order.manage.view', $data->id)}}">
                                        <span class="action-icon bg-[#eef2ff]"><i class="mdi mdi-eye-outline text-[#6366f1]"></i></span>
                                        {{__('View Details')}}
                                    </a>

                                    <div class="menu-divider"></div>

                                    <button type="button" class="action-item action-danger swal_delete_button">
                                        <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                        {{__('Delete Log')}}
                                    </button>
                                    <form method="post" action="{{route(route_prefix().'admin.payment.delete', $data->id)}}" class="hidden d-none">
                                        @csrf
                                        <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

@include('landlord.admin.package-order-manage.portion.status-and-mail-send')

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <script>
    (function ($) {
        "use strict";

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

        $(document).ready(function () {

            // ── DataTable ────────────────────────────────────────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#paymentLogsTable')) {
                $('#paymentLogsTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── Delete via dropdown ──────────────────────────────────
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let btn = $(this);
                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You won\'t be able to revert this!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{__("Yes, delete it!")}}',
                    cancelButtonText: '{{__("Cancel")}}'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        btn.closest('td').find('form').trigger('submit');
                    }
                });
            });

            // ── Approve manual payment ───────────────────────────────
            $(document).on('click', '.swal_change_approve_payment_button', function (e) {
                e.preventDefault();
                let btn = $(this);
                Swal.fire({
                    title: '{{__("Are you sure to approve this payment?")}}',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#1a5c4e',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{__("Yes, approve it!")}}',
                    cancelButtonText: '{{__("Cancel")}}'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        btn.next('form').find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            // ── Bulk Delete ──────────────────────────────────────────
            $(document).on('click', '#bulk_delete_btn', function (e) {
                e.preventDefault();
                var bulkOption = $('#bulk_option').val();
                var allCheckbox = $('.bulk-checkbox:checked');
                if (bulkOption === '' || allCheckbox.length === 0) return;

                var allIds = [];
                allCheckbox.each(function () { allIds.push($(this).val()); });

                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You are about to delete selected items!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{__("Yes, delete!")}}',
                    cancelButtonText: '{{__("Cancel")}}'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{route(route_prefix()."admin.payment.bulk.action")}}',
                            type: 'POST',
                            data: { _token: '{{csrf_token()}}', ids: allIds },
                            success: function () { location.reload(); }
                        });
                    }
                });
            });

            // ── Select all checkbox ──────────────────────────────────
            $(document).on('change', '.all-checkbox', function () {
                var isChecked = $(this).is(':checked');
                $('.bulk-checkbox').prop('checked', isChecked);
            });
        });
    })(jQuery);
    </script>
@endsection
