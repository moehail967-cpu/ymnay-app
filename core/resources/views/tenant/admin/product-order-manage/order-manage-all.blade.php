@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Orders')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <x-summernote.css/>
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
                <i class="mdi mdi-cart-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Orders')}}</h3>
                <p class="text-xs text-muted">{{__('Manage product orders and payments')}}</p>
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

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="all_user_table">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 w-10 no-sort">
                        <input type="checkbox" class="all-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                    </th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Customer')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Amount')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Commission')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Gateway')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_orders as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- Checkbox --}}
                    <td class="px-4 py-3.5">
                        <input type="checkbox" class="bulk-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" name="bulk_delete[]" value="{{$data->id}}">
                    </td>

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
                    </td>

                    {{-- Customer --}}
                    <td class="px-4 py-3.5">
                        <div class="tw-cell-user">
                            <div class="tw-avatar-initials">{{ strtoupper(substr($data->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div class="tw-cell-name">{{$data->name}}</div>
                                <div class="tw-cell-sub">{{$data->email}}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Amount --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-bold text-dark">{{amount_with_currency_symbol($data->total_amount)}}</span>
                    </td>

                    {{-- Commission --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{ amount_with_currency_symbol(calculate_commission($data, $commissionSetting)) }}</span>
                    </td>

                    {{-- Gateway --}}
                    <td class="px-4 py-3.5">
                        <span class="text-xs text-dark capitalize">{{$data->checkout_type !== 'cod' ? __($data->payment_gateway) : __('Cash On Delivery')}}</span>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3.5">
                        <div class="flex flex-col gap-1.5">
                            @if($data->status == 'pending')
                                <span class="tw-pill tw-pill-warning">{{__('Pending')}}</span>
                            @elseif($data->status == 'cancel')
                                <span class="tw-pill tw-pill-danger">{{__('Cancelled')}}</span>
                            @elseif($data->status == 'in_progress')
                                <span class="tw-pill tw-pill-info">{{__('In Progress')}}</span>
                            @else
                                <span class="tw-pill tw-pill-success">{{__('Complete')}}</span>
                            @endif

                            @if($data->payment_status === 'success')
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-success">
                                    <i class="mdi mdi-check-circle text-[10px]"></i> {{__('Paid')}}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-warning">
                                    <i class="mdi mdi-clock-outline text-[10px]"></i> {{__('Unpaid')}}
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- Date --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="text-xs text-muted">{{$data->created_at->format('d M Y')}}</span>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">

                                {{-- View --}}
                                <a href="{{route('tenant.admin.product.order.manage.view', $data->id)}}"
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

                                    @if($data->status !== 'cancel')
                                        <button type="button" class="action-item order_status_change_btn"
                                                data-id="{{$data->id}}"
                                                data-status="{{$data->status}}"
                                                data-payment_status="{{$data->payment_status}}">
                                            <span class="action-icon bg-warning-soft"><i class="mdi mdi-swap-horizontal text-warning"></i></span>
                                            {{__('Update Status')}}
                                        </button>
                                    @endif

                                    @if(!empty($data->user_id) && ($data->payment_status == 'pending' || $data->payment_status == null))
                                        <form action="{{route(route_prefix().'admin.product.order.reminder')}}" method="post" class="contents">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$data->id}}">
                                            <button type="submit" class="action-item">
                                                <span class="action-icon bg-[#f3e8ff]"><i class="mdi mdi-bell-ring-outline text-[#9333ea]"></i></span>
                                                {{__('Send Reminder')}}
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Invoice --}}
                                    <form action="{{route(route_prefix().'admin.order.invoice.generate')}}" method="POST" target="_blank" class="contents">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$data->id}}">
                                        <button type="submit" class="action-item">
                                            <span class="action-icon bg-secondary"><i class="mdi mdi-receipt-text-outline text-dark"></i></span>
                                            {{__('Invoice')}}
                                        </button>
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

@include('tenant.admin.product-order-manage.portion.status-and-mail-send')

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-summernote.js/>
    <x-bulk-action-js :url="route(route_prefix().'admin.product.order.bulk.action')"/>
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
                location.href = '{{route('tenant.admin.product.order.manage.all').'?filter='}}' + $('#filter_order').val();
            });

            // ── Order Status Change Modal ────────────────────────────
            $(document).on('click', '.order_status_change_btn', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                var el = $(this);
                var form = $('#order_status_change_modal');
                form.find('#order_id').val(el.data('id'));
                form.find('#order_status option[value="' + el.data('status') + '"]').attr('selected', true);

                if (el.data('payment_status') === 'success') {
                    form.find('#payment_status_wrap').hide();
                    form.find('#payment_status').removeAttr('name');
                } else {
                    form.find('#payment_status_wrap').show();
                    form.find('#payment_status').attr('name', 'payment_status');
                    form.find('#payment_status option[value="' + el.data('payment_status') + '"]').attr('selected', true);
                }

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
        });
    })(jQuery);
    </script>
@endsection
