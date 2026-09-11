@extends('landlord.admin.admin-master')

@section('title') {{__('Price Plan Coupon')}} @endsection

@section('style')
    <x-datatable.tw-css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Table Card (Full Width) --}}
<div class="bg-surface rounded-xl shadow-main border border-main">

    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-ticket-percent-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Coupons')}}</h3>
                <p class="text-xs text-muted">{{__('Manage your price plan coupons')}}</p>
            </div>
        </div>
        @can('product-coupon-create')
        <button type="button" id="open_coupon_create_modal"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
            <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add New Coupon')}}
        </button>
        @endcan
    </div>

    <div class="tw-table-wrap">
        <table class="w-full text-left" id="allCouponTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14 no-sort">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name & Code')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Discount')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Expire Date')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_coupon as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark block">{{$data->name}}</span>
                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded bg-secondary border border-main text-[10px] font-mono font-bold text-primary">
                            <i class="mdi mdi-content-copy text-[10px]"></i> {{$data->code}}
                        </span>
                    </td>

                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">
                            @if($data->discount_type == \App\Enums\LandlordCouponType::Percentage)
                                {{$data->discount_amount}}%
                            @else
                                {{amount_with_currency_symbol($data->discount_amount)}}
                            @endif
                        </span>
                    </td>

                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="text-xs text-muted">{{ $data->expire_date ? date('d M Y', strtotime($data->expire_date)) : '—' }}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        @if($data->status == 1)
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[10px]"></i> {{__('Published')}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase">
                                <i class="mdi mdi-pencil-outline text-[10px]"></i> {{__('Draft')}}
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">

                                @can('product-coupon-edit')
                                <button type="button"
                                        class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:text-white hover:bg-primary hover:border-primary transition-all category_edit_btn"
                                        title="{{__('Edit')}}"
                                        data-id="{{$data->id}}"
                                        data-name="{{$data->name}}"
                                        data-code="{{$data->code}}"
                                        data-description="{{$data->description}}"
                                        data-discount_amount="{{$data->discount_amount}}"
                                        data-discount_type="{{$data->discount_type}}"
                                        data-expire_date="{{$data->expire_date}}"
                                        data-status="{{$data->status}}">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </button>
                                @endcan

                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-dots-vertical text-sm"></i>
                                </button>

                                <div class="row-action-menu hidden">
                                    @can('product-coupon-edit')
                                    <button type="button" class="action-item category_edit_btn"
                                            data-id="{{$data->id}}"
                                            data-name="{{$data->name}}"
                                            data-code="{{$data->code}}"
                                            data-description="{{$data->description}}"
                                            data-discount_amount="{{$data->discount_amount}}"
                                            data-discount_type="{{$data->discount_type}}"
                                            data-expire_date="{{$data->expire_date}}"
                                            data-status="{{$data->status}}">
                                        <span class="action-icon bg-primary-soft"><i class="mdi mdi-pencil-outline text-primary"></i></span>
                                        {{__('Edit Coupon')}}
                                    </button>
                                    @endcan

                                    <div class="menu-divider"></div>

                                    @can('product-coupon-delete')
                                    <button type="button" class="action-item action-danger swal_delete_button"
                                            data-route="{{route('landlord.admin.coupon.delete', $data->id)}}">
                                        <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                        {{__('Delete Coupon')}}
                                    </button>
                                    @endcan
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

{{-- Create Modal --}}
@can('product-coupon-create')
<div id="coupon_create_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="coupon_create_backdrop"></div>
    <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">

        <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Add New Coupon')}}</h5>
                <p class="text-[11px] text-muted">{{__('Create a new discount coupon')}}</p>
            </div>
            <button type="button" id="coupon_create_close"
                    class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                <i class="mdi mdi-close text-muted"></i>
            </button>
        </div>

        <form action="{{route('landlord.admin.coupon.store')}}" method="post" class="flex-1 flex flex-col overflow-hidden">
            @csrf

            <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Title')}} <span class="text-danger">*</span></label>
                    <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                        <i class="mdi mdi-tag-outline text-primary"></i>
                        <input type="text" id="title" name="name" value="{{old('title')}}" placeholder="{{__('Coupon title')}}" required
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Code')}} <span class="text-danger">*</span></label>
                    <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                        <i class="mdi mdi-barcode text-primary"></i>
                        <input type="text" id="code" name="code" value="{{old('code')}}" placeholder="{{__('SUMMER2024')}}" required
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 font-mono uppercase">
                    </div>
                    <span id="status_text" class="text-xs text-danger mt-1" style="display: none"></span>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Description')}} <span class="text-[10px] text-muted font-normal normal-case">({{__('Optional')}})</span></label>
                    <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                        <i class="mdi mdi-text-short text-primary"></i>
                        <input type="text" id="description" name="description" value="{{old('description')}}" placeholder="{{__('Short description')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Amount')}} <span class="text-danger">*</span></label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-percent-outline text-primary"></i>
                            <input type="number" id="discount" name="discount_amount" value="{{old('discount_amount')}}" placeholder="{{__('10')}}" required
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Type')}} <span class="text-danger">*</span></label>
                        <select name="discount_type" id="discount_type" required class="lnd-input">
                            <option value="{{\App\Enums\LandlordCouponType::Percentage}}">{{__("Percentage")}}</option>
                            <option value="{{\App\Enums\LandlordCouponType::Amount}}">{{__("Amount")}}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Expire Date')}}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-calendar-outline text-primary"></i>
                            <input type="date" id="expire_date" name="expire_date" placeholder="{{__('Select date')}}" required
                                   class="flatpickr flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status" id="status" required class="lnd-input">
                            <option value="1">{{__("Publish")}}</option>
                            <option value="0">{{__("Draft")}}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                <button type="button" id="coupon_create_cancel"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit" id="coupon_create_btn"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                    <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add Coupon')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- Edit Modal --}}
@can('product-coupon-edit')
<div id="coupon_edit_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="coupon_edit_backdrop"></div>
    <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">

        <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-pencil-outline text-primary text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Update Coupon')}}</h5>
                <p class="text-[11px] text-muted">{{__('Edit coupon details')}}</p>
            </div>
            <button type="button" id="coupon_edit_close"
                    class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                <i class="mdi mdi-close text-muted"></i>
            </button>
        </div>

        <form action="{{route('landlord.admin.coupon.update')}}" method="post" class="flex-1 flex flex-col overflow-hidden">
            @csrf
            <input type="hidden" name="id" id="coupon_id">

            <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Title')}} <span class="text-danger">*</span></label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-tag-outline text-primary"></i>
                            <input type="text" id="edit_title" name="name" placeholder="{{__('Title')}}" required
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Code')}} <span class="text-danger">*</span></label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-barcode text-primary"></i>
                            <input type="text" id="edit_code" name="code" placeholder="{{__('Code')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 font-mono uppercase">
                        </div>
                        <span id="edit_status_text" class="text-xs text-danger mt-1" style="display: none"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Description')}} <span class="text-[10px] text-muted font-normal normal-case">({{__('Optional')}})</span></label>
                    <div class="bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                        <textarea id="edit_description" name="description" rows="2" placeholder="{{__('Short description')}}"
                                  class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-none"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Amount')}} <span class="text-danger">*</span></label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-percent-outline text-primary"></i>
                            <input type="number" id="edit_discount" name="discount_amount" placeholder="{{__('Amount')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Type')}} <span class="text-danger">*</span></label>
                        <select name="discount_type" id="edit_discount_type" class="lnd-input">
                            <option value="{{\App\Enums\LandlordCouponType::Percentage}}">{{__("Percentage")}}</option>
                            <option value="{{\App\Enums\LandlordCouponType::Amount}}">{{__("Amount")}}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Expire Date')}}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-calendar-outline text-primary"></i>
                            <input type="date" id="edit_expire_date" name="expire_date" placeholder="{{__('Select date')}}"
                                   class="flatpickr flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status" id="edit_status" class="lnd-input">
                            <option value="1">{{__("Publish")}}</option>
                            <option value="0">{{__("Draft")}}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                <button type="button" id="coupon_edit_cancel"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit" id="coupon_edit_btn"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-unique-checker selector="input[name=code]" table="coupons" column="code" disable-btn="true" disable-btn-selector="#coupon_create_btn, #coupon_edit_btn"/>

    <script>
    (function ($) {
        "use strict";

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

            flatpickr(".flatpickr", {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
            });

            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#allCouponTable')) {
                $('#allCouponTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── SweetAlert Delete (AJAX) ──────────────────────────────
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                var btn = $(this);
                var route = btn.data('route');
                var row = btn.closest('tr');

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
                        $.ajax({
                            type: 'POST',
                            url: route,
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (res) {
                                if (res.type === 'success') {
                                    toastr.success(res.message || '{{ __("Deleted successfully") }}');
                                    row.fadeOut(300, function () { $(this).remove(); });
                                } else {
                                    toastr.error(res.message || '{{ __("Something went wrong") }}');
                                }
                            },
                            error: function () {
                                toastr.error('{{ __("Something went wrong") }}');
                            }
                        });
                    }
                });
            });

            // ── Create Modal ──────────────────────────────────────────
            $('#open_coupon_create_modal').on('click', function () {
                openModal('coupon_create_modal');
            });

            $('#coupon_create_close, #coupon_create_cancel, #coupon_create_backdrop').on('click', function () {
                closeModal('coupon_create_modal');
            });

            // ── Edit Modal Open ───────────────────────────────────────
            $(document).on('click', '.category_edit_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });

                var el = $(this);
                var modal = $('#coupon_edit_modal');

                modal.find('#coupon_id').val(el.data('id'));
                modal.find('#edit_title').val(el.data('name'));
                modal.find('#edit_code').val(el.data('code'));
                modal.find('#edit_description').val(el.data('description'));
                modal.find('#edit_discount').val(el.data('discount_amount'));
                modal.find('#edit_discount_type').val(el.data('discount_type'));
                modal.find('#edit_status').val(el.data('status'));

                flatpickr(modal.find('#edit_expire_date')[0], {
                    defaultDate: el.data('expire_date'),
                    altInput: true,
                    altFormat: "F j, Y",
                    dateFormat: "Y-m-d",
                });

                openModal('coupon_edit_modal');
            });

            $('#coupon_edit_close, #coupon_edit_cancel, #coupon_edit_backdrop').on('click', function () {
                closeModal('coupon_edit_modal');
            });
        });
    })(jQuery);
    </script>
@endsection
