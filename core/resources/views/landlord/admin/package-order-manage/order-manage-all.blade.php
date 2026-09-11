@extends('landlord.admin.admin-master')
@section('title') {{__('All Orders')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <x-summernote.css/>
    <style>
        .row-action-menu {
            position: fixed; z-index: 9999; min-width: 210px; max-height: 320px; overflow-y: auto;
            background: var(--color-bg-surface, #fff); border: 1px solid var(--color-border-main, #e5e7eb);
            border-radius: 0.875rem; box-shadow: 0 8px 24px rgba(0,0,0,.10); padding: 0.375rem 0;
        }
        .row-action-menu a,
        .row-action-menu button.action-item {
            display: flex; align-items: center; gap: 0.625rem; padding: 0.5rem 1rem; font-size: 0.8125rem;
            color: var(--color-text-dark, #111827); text-decoration: none; background: transparent;
            border: none; width: 100%; text-align: left; cursor: pointer; transition: background 0.12s; white-space: nowrap;
        }
        .row-action-menu a:hover, .row-action-menu button.action-item:hover { background: var(--color-bg-muted, #f3f4f6); }
        .row-action-menu .action-icon {
            width: 1.375rem; height: 1.375rem; border-radius: 0.375rem;
            display: inline-flex; align-items: center; justify-content: center; font-size: 0.875rem; flex-shrink: 0;
        }
        .row-action-menu .menu-divider { height: 1px; background: var(--color-border-main, #e5e7eb); margin: 0.25rem 0; }
        .row-action-menu .action-danger { color: var(--color-danger, #dc2626); }
        .row-action-menu .action-danger:hover { background: #fef2f2; }
        .row-action-wrap { display: inline-flex; }
    </style>
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
                <i class="mdi mdi-package-variant text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Orders')}}</h3>
                <p class="text-xs text-muted">{{__('Manage package orders and payments')}}</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                <i class="mdi mdi-filter-outline text-lg text-primary"></i>
                <select id="filter_order" class="bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer min-w-[100px]">
                    <option value="all">{{__('All')}}</option>
                    <option value="pending" {{request()->filter == 'pending' ? 'selected' : ''}}>{{__('Pending')}}</option>
                    <option value="in_progress" {{request()->filter == 'in_progress' ? 'selected' : ''}}>{{__('In Progress')}}</option>
                    <option value="cancel" {{request()->filter == 'cancel' ? 'selected' : ''}}>{{__('Cancel')}}</option>
                    <option value="complete" {{request()->filter == 'complete' ? 'selected' : ''}}>{{__('Complete')}}</option>
                </select>
                <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
            </div>
            <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-xs font-semibold hover:opacity-90 transition" id="filter_btn">
                <i class="mdi mdi-magnify text-sm"></i> {{__('Filter')}}
            </button>
        </div>
    </div>

    {{-- Bulk Action --}}
    @can('package-order-delete')
    <div class="px-4 sm:px-6 py-3 border-b border-main flex items-center gap-3">
        <select id="bulk_option"
                class="text-xs bg-secondary border border-main rounded-lg px-3 py-1.5 text-dark focus:border-primary focus:outline-none transition">
            <option value="">{{__('Bulk Action')}}</option>
            <option value="delete">{{__('Delete')}}</option>
        </select>
        <button type="button" id="bulk_delete_btn"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
            {{__('Apply')}}
        </button>
    </div>
    @endcan

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="all_user_table">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 w-10 no-sort">
                        <input type="checkbox" class="all-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                    </th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Order')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Customer')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_orders as $data)
                @php
                    $subdomain = \App\Models\Tenant::find($data->tenant_id);
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- Checkbox --}}
                    <td class="px-4 py-3.5">
                        <input type="checkbox" class="bulk-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" name="bulk_delete[]" value="{{$data->id}}">
                    </td>

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
                    </td>

                    {{-- Order (Package + Price + Gateway) --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark block">{{$data->package_name}}</span>
                        <span class="text-xs font-bold text-dark">{{amount_with_currency_symbol($data->package_price)}}</span>
                        <span class="text-[10px] text-muted"> · {{ucwords(str_replace('_', ' ', $data->package_gateway))}}</span>
                    </td>

                    {{-- Customer (Name + Email + Subdomain) --}}
                    <td class="px-4 py-3.5">
                        <div class="tw-cell-user">
                            <div class="tw-avatar-initials">{{ strtoupper(substr($data->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div class="tw-cell-name">{{$data->name}}</div>
                                <div class="tw-cell-sub">{{$data->email}}</div>
                                @if($data->tenant_id)
                                    <span class="inline-flex items-center gap-0.5 mt-0.5 text-[10px] font-mono font-bold text-primary">
                                        <i class="mdi mdi-web text-[10px]"></i> {{$data->tenant_id}}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Date --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        @if(($data->renew_status != 0) || ($data->is_renew != 0))
                            <span class="text-xs text-muted block">{{date('d M Y', strtotime($data->created_at))}}</span>
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-primary mt-0.5">
                                <i class="mdi mdi-refresh text-[10px]"></i> {{date('d M Y', strtotime($data->start_date))}}
                            </span>
                        @else
                            <span class="text-xs text-muted">{{date('d M Y', strtotime($data->created_at))}}</span>
                        @endif
                    </td>

                    {{-- Status (Payment + Order combined) --}}
                    <td class="px-4 py-3.5">
                        <div class="flex flex-col gap-1.5">
                            {{-- Order status --}}
                            @if($data->status == 'pending')
                                <span class="tw-pill tw-pill-warning">{{__('Pending')}}</span>
                            @elseif($data->status == 'cancel')
                                <span class="tw-pill tw-pill-danger">{{__('Cancelled')}}</span>
                            @elseif($data->status == 'in_progress')
                                <span class="tw-pill tw-pill-info">{{__('In Progress')}}</span>
                            @elseif($data->status == 'trial')
                                <span class="tw-pill tw-pill-purple">{{__('Trial')}}</span>
                            @else
                                <span class="tw-pill tw-pill-success">{{__('Complete')}}</span>
                            @endif

                            {{-- Payment badge (only if different from order status) --}}
                            @if($data->payment_status == 'complete' && $data->status != 'complete')
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-success">
                                    <i class="mdi mdi-check-circle text-[10px]"></i> {{__('Paid')}}
                                </span>
                            @elseif($data->payment_status != 'complete' && $data->status != 'trial')
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-warning">
                                    <i class="mdi mdi-clock-outline text-[10px]"></i> {{__('Unpaid')}}
                                </span>
                            @endif

                            {{-- Approve link for pending payment --}}
                            @if($data->payment_status == 'pending' && $data->status != 'trial')
                                <a href="{{route('landlord.admin.package.order.payment.status.change', $data->id)}}"
                                   class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold hover:bg-success hover:text-white transition w-fit">
                                    <i class="mdi mdi-check text-[10px]"></i> {{__('Approve')}}
                                </a>
                            @endif

                            @if($data->status != 'trial' && $subdomain !== null && $data->payment_status == 'pending' && $data->renew_status != 1)
                                <p class="text-[9px] text-muted leading-tight">{{__('Duplicate order exists')}}</p>
                            @endif
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">

                                {{-- View --}}
                                <a href="{{route(route_prefix().'admin.package.order.manage.view', $data->id)}}"
                                   title="{{__('View')}}"
                                   class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:text-white hover:bg-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-eye-outline text-sm"></i>
                                </a>

                                {{-- Dropdown trigger --}}
                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center px-2 py-1.5 rounded-lg bg-secondary border border-main text-dark hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-dots-vertical text-sm"></i>
                                </button>

                                {{-- Dropdown --}}
                                <div class="row-action-menu hidden">

                                    {{-- Send Email --}}
                                    <button type="button" class="action-item user_edit_btn">
                                        <span class="action-icon bg-info-soft"><i class="mdi mdi-email-fast-outline text-info"></i></span>
                                        {{__('Send Email')}}
                                    </button>

                                    @can('package-order-edit')
                                        @if($data->status != 'trial')
                                            <button type="button" class="action-item order_status_change_btn"
                                                    data-id="{{$data->id}}"
                                                    data-status="{{$data->status}}">
                                                <span class="action-icon bg-warning-soft"><i class="mdi mdi-swap-horizontal text-warning"></i></span>
                                                {{__('Update Status')}}
                                            </button>
                                        @endif

                                        @if(!empty($data->user_id) && ($data->payment_status == 'pending' || $data->payment_status == null))
                                            <form action="{{route(route_prefix().'admin.package.order.reminder')}}" method="post" class="contents">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$data->id}}">
                                                <button type="submit" class="action-item">
                                                    <span class="action-icon bg-[#f3e8ff]"><i class="mdi mdi-bell-ring-outline text-[#9333ea]"></i></span>
                                                    {{__('Send Reminder')}}
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    {{-- Invoice --}}
                                    @if(get_lang_direction() == 'rtl')
                                        <button type="button" class="action-item btn-invoice-rtl" data-id="{{$data->id}}">
                                            <span class="action-icon bg-secondary"><i class="mdi mdi-receipt-text-outline text-dark"></i></span>
                                            {{__('Invoice')}}
                                        </button>
                                    @else
                                        <form action="{{route('landlord.package.invoice.generate')}}" method="POST" target="_blank" class="contents">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$data->id}}">
                                            <button type="submit" class="action-item">
                                                <span class="action-icon bg-secondary"><i class="mdi mdi-receipt-text-outline text-dark"></i></span>
                                                {{__('Invoice')}}
                                            </button>
                                        </form>
                                    @endif

                                    <div class="menu-divider"></div>

                                    @can('package-order-delete')
                                    <button type="button" class="action-item action-danger swal_delete_button">
                                        <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                        {{__('Delete')}}
                                    </button>
                                    @endcan
                                </div>
                            </div>

                            @can('package-order-delete')
                            <form method="post" action="{{route(route_prefix().'admin.package.order.manage.delete', $data->id)}}" class="hidden d-none delete-form">
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

@can('package-order-edit')
    @include('landlord.admin.package-order-manage.portion.status-and-mail-send')
@endcan

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-summernote.js/>
    <x-bulk-action-js :url="route(route_prefix().'admin.package.order.bulk.action')"/>
    <script>
    (function ($) {
        "use strict";

        // ── Modal helpers ────────────────────────────────────────────
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // ── Row action dropdown ──────────────────────────────────────
        window.toggleRowMenu = function (btn) {
            var menu = btn.nextElementSibling;
            var isHidden = menu.classList.contains('hidden');

            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });

            if (isHidden) {
                var rect = btn.getBoundingClientRect();
                var spaceBelow = window.innerHeight - rect.bottom;
                var spaceAbove = rect.top;

                menu.style.right = (window.innerWidth - rect.right) + 'px';
                menu.style.left  = 'auto';

                if (spaceBelow >= Math.min(360, 200) || spaceBelow >= spaceAbove) {
                    menu.style.top = (rect.bottom + 4) + 'px';
                    menu.style.bottom = 'auto';
                } else {
                    menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                    menu.style.top = 'auto';
                }
                menu.classList.remove('hidden');
            }
        };

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.row-action-wrap')) {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
            }
        });

        window.addEventListener('scroll', function (e) {
            if (e.target && e.target.closest && e.target.closest('.row-action-menu')) return;
            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
        }, true);

        document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
            wrap.addEventListener('scroll', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
            });
        });

        $(document).ready(function () {

            // ── DataTable ────────────────────────────────────────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#all_user_table')) {
                $('#all_user_table').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── Summernote ───────────────────────────────────────────
            $('.summernote').summernote({
                height: 250,
                codemirror: { theme: 'monokai' },
                callbacks: { onChange: function (contents) { $(this).prev('input').val(contents); } }
            });

            // ── Filter ───────────────────────────────────────────────
            $(document).on('click', '#filter_btn', function () {
                location.href = '{{route('landlord.admin.package.order.manage.all').'?filter='}}' + $('#filter_order').val();
            });

            // ── Order Status Change Modal ────────────────────────────
            $(document).on('click', '.order_status_change_btn', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                var el = $(this);
                $('#order_status_change_modal').find('#order_id').val(el.data('id'));
                $('#order_status_change_modal').find('#order_status').val(el.data('status'));
                openModal('order_status_change_modal');
            });
            $('.order_status_close, #order_status_backdrop').on('click', function () { closeModal('order_status_change_modal'); });

            // ── Send Mail Modal ──────────────────────────────────────
            $(document).on('click', '.user_edit_btn', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                openModal('send_mail_modal');
            });
            $('.send_mail_close, #send_mail_backdrop').on('click', function () { closeModal('send_mail_modal'); });

            // ── Delete ───────────────────────────────────────────────
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
                    confirmButtonText: '{{ __("Yes, Delete it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}',
                }).then(function (result) {
                    if (result.isConfirmed) { btn.closest('td').find('.delete-form').trigger('submit'); }
                });
            });

            // ── RTL Invoice ──────────────────────────────────────────
            $(document).on('click', '.btn-invoice-rtl', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                $.ajax({
                    type: 'POST',
                    url: '{{route('landlord.package.invoice.generate.rtl')}}',
                    data: { _token: '{{csrf_token()}}', id: $(this).data('id') },
                    success: function (data) {
                        var w = window.open('', 'new div', 'height=874,width=840');
                        w.document.write(data); w.document.close(); w.focus();
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
