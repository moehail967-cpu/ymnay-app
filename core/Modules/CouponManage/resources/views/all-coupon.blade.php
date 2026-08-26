@extends('tenant.admin.admin-master')

@section('title') {{ __('Product Coupons') }} @endsection

@section('style')
    <x-datatable.tw-css />
    <style>
        /* ── Flatpickr calendar – clean card style ── */
        .flatpickr-calendar {
            border-radius: 14px !important;
            box-shadow: 0 8px 30px rgba(0,0,0,.12) !important;
            border: 1px solid #e5e7eb !important;
            font-family: inherit !important;
            padding: 12px !important;
            background: #fff !important;
            width: 280px !important;
        }
        .flatpickr-months {
            align-items: center;
            padding-bottom: 8px;
            border-bottom: 1px solid #f3f4f6;
            margin-bottom: 4px;
        }
        .flatpickr-months .flatpickr-month {
            height: 32px !important;
            line-height: 32px !important;
        }
        .flatpickr-current-month {
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #111827 !important;
            padding: 0 !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 600 !important;
            color: #111827 !important;
            background: transparent !important;
            border: none !important;
            font-size: 14px !important;
            appearance: auto !important;
            -webkit-appearance: auto !important;
        }
        .flatpickr-current-month input.cur-year {
            font-weight: 600 !important;
            color: #111827 !important;
            font-size: 14px !important;
        }
        .flatpickr-prev-month, .flatpickr-next-month {
            padding: 4px 8px !important;
            color: #6b7280 !important;
        }
        .flatpickr-prev-month:hover, .flatpickr-next-month:hover {
            color: #111827 !important;
        }
        .flatpickr-prev-month svg, .flatpickr-next-month svg {
            fill: currentColor !important;
        }
        .flatpickr-weekdays {
            background: transparent !important;
            margin-bottom: 2px;
        }
        .flatpickr-weekday {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #9ca3af !important;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .flatpickr-day {
            border-radius: 50% !important;
            font-size: 13px !important;
            color: #374151 !important;
            height: 34px !important;
            line-height: 34px !important;
            max-width: 34px !important;
        }
        .flatpickr-day:hover {
            background: #ede9fe !important;
            border-color: transparent !important;
            color: #111827 !important;
        }
        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: #7c3aed !important;
            border-color: #7c3aed !important;
            color: #fff !important;
            font-weight: 600 !important;
        }
        .flatpickr-day.today {
            border-color: #7c3aed !important;
            color: #7c3aed !important;
            font-weight: 600 !important;
        }
        .flatpickr-day.today:hover {
            background: #7c3aed !important;
            color: #fff !important;
        }
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #d1d5db !important;
        }
        .flatpickr-day.flatpickr-disabled:hover {
            background: transparent !important;
        }

        /* ── Validation messages ── */
        .error-message {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
            font-size: 11px;
            color: #ef4444;
            font-weight: 500;
        }
        .error-message i { font-size: 12px; flex-shrink: 0; }
        .cp-field input.is-invalid,
        .cp-field select.is-invalid { border-color: #ef4444 !important; }
        .cp-field input.is-valid,
        .cp-field select.is-valid   { border-color: #22c55e !important; }

        /* ── Code availability badges ── */
        .code-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 20px; }
        .code-badge.taken     { background: #fee2e2; color: #dc2626; }
        .code-badge.available { background: #dcfce7; color: #16a34a; }
    </style>
@endsection

@section('content')
<div class="coupon-page">

    {{-- Flash Messages --}}
    <x-landlord-flash-msg />
    <x-landlord-error-msg />

    {{-- Page Header --}}
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden mb-6">
        <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-ticket-percent-outline text-primary text-base"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('Product Coupons') }}</h3>
                <p class="text-xs text-muted">{{ __('Manage discount coupons for your store') }}</p>
            </div>
            @can('product-coupon-create')
                <button type="button"
                    onclick="openCouponAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold bg-primary text-white rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-plus text-sm"></i> {{ __('Add Coupon') }}
                </button>
            @endcan
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

        {{-- Card Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-info-soft flex items-center justify-center">
                    <i class="mdi mdi-format-list-bulleted text-info text-sm"></i>
                </div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{ __('All Coupons') }}</h4>
            </div>
            @can('product-coupon-delete')
                <div class="flex items-center gap-2">
                    <select id="bulk_option"
                        class="text-xs border border-main rounded-lg px-2 py-1.5 bg-surface text-dark focus:outline-none focus:border-primary transition">
                        <option value="">{{ __('Bulk Action') }}</option>
                        <option value="delete">{{ __('Delete') }}</option>
                    </select>
                    <button id="bulk_delete_btn"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-danger-soft border border-danger/30 rounded-lg hover:bg-danger hover:text-white transition">
                        {{ __('Apply') }}
                    </button>
                </div>
            @endcan
        </div>

        {{-- Table --}}
        <div class="tw-table-wrap">
            <table class="w-full text-sm" id="coupon_datatable">
                <thead>
                    <tr class="border-b border-main bg-secondary">
                        @can('product-coupon-delete')
                            <th class="px-4 py-3 text-left w-8 no-sort">
                                <input type="checkbox" id="select_all"
                                    class="w-4 h-4 rounded border-main accent-primary cursor-pointer">
                            </th>
                        @endcan
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-muted uppercase tracking-wider">{{ __('Title') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-muted uppercase tracking-wider">{{ __('Code') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-muted uppercase tracking-wider">{{ __('Discount') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-muted uppercase tracking-wider">{{ __('Expires') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-muted uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-bold text-muted uppercase tracking-wider no-sort">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-main">
                    @forelse($all_product_coupon as $data)
                        <tr class="hover:bg-secondary/50 transition-colors">
                            @can('product-coupon-delete')
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="bulk-checkbox w-4 h-4 rounded border-main accent-primary cursor-pointer" value="{{ $data->id }}">
                                </td>
                            @endcan
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-dark">{{ $data->title }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-primary-soft text-primary text-xs font-bold rounded-lg font-mono tracking-wider">
                                    <i class="mdi mdi-ticket-outline text-xs"></i>
                                    {{ $data->code }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-semibold text-dark">
                                    @if($data->discount_type == 'percentage')
                                        {{ $data->discount }}%
                                    @else
                                        {{ amount_with_currency_symbol($data->discount) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-muted">{{ date('d M Y', strtotime($data->expire_date)) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($data->status === 'publish')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-success-soft text-success text-xs font-semibold rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-warning-soft text-warning text-xs font-semibold rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-warning"></span>
                                        {{ __('Draft') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('product-coupon-edit')
                                        <button type="button"
                                            class="coupon-edit-trigger w-8 h-8 rounded-lg bg-primary-soft hover:bg-primary hover:text-white flex items-center justify-center transition"
                                            data-id="{{ $data->id }}"
                                            data-title="{{ $data->title }}"
                                            data-code="{{ $data->code }}"
                                            data-discount_on="{{ $data->discount_on }}"
                                            data-discount_on_details="{{ $data->discount_on_details }}"
                                            data-discount="{{ $data->discount }}"
                                            data-discount_type="{{ $data->discount_type }}"
                                            data-expire_date="{{ $data->expire_date }}"
                                            data-status="{{ $data->status }}"
                                            data-minimum_quantity="{{ $data->minimum_quantity }}"
                                            data-minimum_spend="{{ $data->minimum_spend }}"
                                            data-maximum_spend="{{ $data->maximum_spend }}"
                                            data-usage_limit_per_coupon="{{ $data->usage_limit_per_coupon }}"
                                            data-usage_limit_per_user="{{ $data->usage_limit_per_user }}"
                                            title="{{ __('Edit') }}">
                                            <i class="mdi mdi-pencil-outline text-sm"></i>
                                        </button>
                                    @endcan
                                    @can('product-coupon-delete')
                                        <button type="button"
                                            class="swal-delete w-8 h-8 rounded-lg bg-danger-soft hover:bg-danger hover:text-white flex items-center justify-center transition"
                                            data-route="{{ route('tenant.admin.product.coupon.delete', $data->id) }}"
                                            title="{{ __('Delete') }}">
                                            <i class="mdi mdi-delete-outline text-sm"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-secondary flex items-center justify-center">
                                        <i class="mdi mdi-ticket-percent-outline text-2xl text-subtle"></i>
                                    </div>
                                    <p class="text-sm text-muted">{{ __('No coupons found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- /coupon-page --}}

{{-- ══ Add Coupon Modal ══ --}}
@can('product-coupon-create')
<div id="coupon_add_modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCouponAddModal()"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col z-10">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-main flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-success-soft flex items-center justify-center">
                    <i class="mdi mdi-plus-circle-outline text-success text-sm"></i>
                </div>
                <h3 class="text-sm font-bold text-dark">{{ __('Add New Coupon') }}</h3>
            </div>
            <button type="button" onclick="closeCouponAddModal()"
                class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-secondary hover:text-dark transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>
        {{-- Modal Body --}}
        <div class="flex-1 overflow-y-auto p-6">
            <form action="{{ route('tenant.admin.product.coupon.new') }}" method="POST" id="add_coupon_form">
                @csrf
                @include('couponmanage::partials.coupon-form', ['prefix' => '', 'submit_label' => null])
            </form>
        </div>
        {{-- Modal Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main flex-shrink-0">
            <button type="button" onclick="closeCouponAddModal()"
                class="px-4 py-2 text-sm font-medium text-muted border border-main rounded-xl hover:bg-secondary transition">
                {{ __('Cancel') }}
            </button>
            <button type="submit" form="add_coupon_form"
                class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold bg-primary text-white rounded-xl hover:opacity-90 transition">
                <i class="mdi mdi-ticket-percent-outline text-base"></i>
                {{ __('Add Coupon') }}
            </button>
        </div>
    </div>
</div>
@endcan

{{-- ══ Edit Coupon Modal ══ --}}
@can('product-coupon-edit')
<div id="coupon_edit_modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCouponEditModal()"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col z-10">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-main flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center">
                    <i class="mdi mdi-ticket-percent-outline text-primary text-sm"></i>
                </div>
                <h3 class="text-sm font-bold text-dark">{{ __('Update Coupon') }}</h3>
            </div>
            <button type="button" onclick="closeCouponEditModal()"
                class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-secondary hover:text-dark transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>
        {{-- Modal Body --}}
        <div class="flex-1 overflow-y-auto p-6">
            <form action="{{ route('tenant.admin.product.coupon.update') }}" method="POST" id="edit_coupon_form">
                @csrf
                <input type="hidden" name="id" id="edit_coupon_id">
                @include('couponmanage::partials.coupon-form', ['prefix' => 'edit_', 'submit_label' => null])
            </form>
        </div>
        {{-- Modal Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main flex-shrink-0">
            <button type="button" onclick="closeCouponEditModal()"
                class="px-4 py-2 text-sm font-medium text-muted border border-main rounded-xl hover:bg-secondary transition">
                {{ __('Cancel') }}
            </button>
            <button type="submit" form="edit_coupon_form"
                class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold bg-primary text-white rounded-xl hover:opacity-90 transition">
                <i class="mdi mdi-content-save-outline text-base"></i>
                {{ __('Save Changes') }}
            </button>
        </div>
    </div>
</div>
@endcan

{{-- Product Loader Overlay --}}
<div id="coupon_product_loader" class="hidden fixed inset-0 z-[900] flex items-center justify-center bg-black/30 backdrop-blur-sm">
    <div class="bg-surface rounded-2xl shadow-xl px-8 py-6 flex flex-col items-center gap-3">
        <div class="w-10 h-10 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
        <p class="text-sm text-muted">{{ __('Loading products…') }}</p>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{ global_asset('assets/common/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ global_asset('assets/common/js/dataTables.responsive.min.js') }}"></script>
    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            $('#coupon_datatable').DataTable({
                "order": [],
                "pageLength": 10,
                'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                'language': translatedDataTable()
            });
        });
    })(jQuery);
    </script>
    <x-table.btn.swal.js />

    <script>
        /* ── Global modal functions (called via onclick) ─────────── */
        function openCouponAddModal() {
            var form = document.getElementById('add_coupon_form');
            if (form) {
                form.reset();
                form.querySelectorAll('.is-invalid').forEach(function(el){ el.classList.remove('is-invalid'); });
                form.querySelectorAll('.is-valid').forEach(function(el){ el.classList.remove('is-valid'); });
                form.querySelectorAll('.error-message, .code-status-badge').forEach(function(el){ el.remove(); });
                ['form_category','form_subcategory','form_childcategory','form_products'].forEach(function(id){
                    var el = document.getElementById(id); if(el) el.style.display = 'none';
                });
            }
            document.getElementById('coupon_add_modal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            setTimeout(function() {
                flatpickr('#expire_date', {
                    dateFormat: 'Y-m-d', altInput: true, altFormat: 'd M Y', minDate: 'today', disableMobile: true
                });
            }, 50);
        }

        function closeCouponAddModal() {
            document.getElementById('coupon_add_modal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function closeCouponEditModal() {
            document.getElementById('coupon_edit_modal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openCouponEditModal(btn) {
            var modal = document.getElementById('coupon_edit_modal');
            if (!modal) return;

            // Read data via getAttribute (most reliable)
            var dataId             = btn.getAttribute('data-id')                    || '';
            var dataTitle          = btn.getAttribute('data-title')                 || '';
            var dataCode           = btn.getAttribute('data-code')                  || '';
            var dataDiscountOn     = btn.getAttribute('data-discount_on')           || '';
            var dataDiscountOnDet  = btn.getAttribute('data-discount_on_details')   || '';
            var dataDiscount       = btn.getAttribute('data-discount')              || '';
            var dataDiscountType   = btn.getAttribute('data-discount_type')         || '';
            var dataExpireDate     = btn.getAttribute('data-expire_date')           || '';
            var dataStatus         = btn.getAttribute('data-status')                || '';
            var dataMinQty         = btn.getAttribute('data-minimum_quantity')      || '';
            var dataMinSpend       = btn.getAttribute('data-minimum_spend')         || '';
            var dataMaxSpend       = btn.getAttribute('data-maximum_spend')         || '';
            var dataLimitCoupon    = btn.getAttribute('data-usage_limit_per_coupon')|| '';
            var dataLimitUser      = btn.getAttribute('data-usage_limit_per_user')  || '';

            // Clear previous validation states
            modal.querySelectorAll('.is-invalid').forEach(function(e){ e.classList.remove('is-invalid'); });
            modal.querySelectorAll('.is-valid').forEach(function(e){ e.classList.remove('is-valid'); });
            modal.querySelectorAll('.error-message,.code-status-badge').forEach(function(e){ e.remove(); });

            // Populate fields
            function setVal(id, val) { var el = document.getElementById(id); if (el) el.value = val; }
            setVal('edit_coupon_id',              dataId);
            setVal('edit_title',                  dataTitle);
            setVal('edit_code',                   dataCode);
            setVal('edit_discount',               dataDiscount);
            setVal('edit_discount_type',          dataDiscountType);
            setVal('edit_status',                 dataStatus);
            setVal('edit_minimum_quantity',       dataMinQty);
            setVal('edit_minimum_spend',          dataMinSpend);
            setVal('edit_maximum_spend',          dataMaxSpend);
            setVal('edit_usage_limit_per_coupon', dataLimitCoupon);
            setVal('edit_usage_limit_per_user',   dataLimitUser);
            setVal('edit_discount_on',            dataDiscountOn);

            // Conditional fields: hide all first
            ['edit_form_category','edit_form_subcategory','edit_form_childcategory','edit_form_products'].forEach(function(id){
                var el = document.getElementById(id); if (el) el.style.display = 'none';
            });

            if (dataDiscountOn === 'product') {
                var parsed = [];
                try { parsed = JSON.parse(dataDiscountOnDet); } catch(e) {}
                if (typeof window._couponLoadProducts === 'function') {
                    window._couponLoadProducts('#edit_form_products select', Array.isArray(parsed) ? parsed : []);
                }
            } else if (dataDiscountOn && dataDiscountOn !== 'all' && dataDiscountOn !== 'shipping') {
                var condEl = document.getElementById('edit_form_' + dataDiscountOn);
                if (condEl) {
                    var selectEl = condEl.querySelector('select');
                    if (selectEl) {
                        // discount_on_details may be JSON-encoded e.g. "\"15\"" → parse to get raw value
                        var detailVal = dataDiscountOnDet;
                        try { detailVal = String(JSON.parse(dataDiscountOnDet)); } catch (e) {}
                        selectEl.value = detailVal;
                    }
                    condEl.style.display = '';
                }
            }

            // Show modal
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Init flatpickr after modal is visible
            setTimeout(function () {
                var inp = document.getElementById('edit_expire_date');
                if (inp) {
                    if (inp._flatpickr) inp._flatpickr.destroy();
                    flatpickr(inp, {
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'd M Y',
                        disableMobile: true,
                        defaultDate: dataExpireDate || null
                    });
                }
            }, 50);
        }
    </script>

    <script>
    $(document).ready(function () {

        /* ── Edit button (delegated so DataTable re-renders don't break it) ── */
        $(document).on('click', '.coupon-edit-trigger', function () {
            openCouponEditModal(this);
        });

        /* ── Bulk Delete ────────────────────────────────────────── */
        $('#select_all').on('change', function () {
            $('.bulk-checkbox').prop('checked', this.checked);
        });

        $('#bulk_delete_btn').on('click', function (e) {
            e.preventDefault();
            var bulkOption = $('#bulk_option').val();
            var allIds = [];
            $('.bulk-checkbox:checked').each(function () { allIds.push($(this).val()); });
            if (!allIds.length || bulkOption !== 'delete') return;

            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('Selected coupons will be permanently deleted.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{ __('Delete') }}",
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('tenant.admin.product.coupon.bulk.action') }}",
                        data: { _token: "{{ csrf_token() }}", ids: allIds },
                        success: function () {
                            toastr.success("{{ __('Coupons deleted successfully') }}");
                            setTimeout(function () { location.reload(); }, 800);
                        }
                    });
                }
            });
        });

        /* ── Validation ─────────────────────────────────────────── */
        var validationRules = {
            title:                  { required: true,  maxLength: 191, message: "{{ __('Coupon title is required') }}" },
            code:                   { required: true,  maxLength: 191, pattern: /^[A-Z0-9_-]+$/i, message: "{{ __('Letters, numbers, dash, underscore only') }}" },
            discount_on:            { required: true,  message: "{{ __('Please select what the discount applies to') }}" },
            discount:               { required: true,  min: 0, message: "{{ __('Discount is required and must be positive') }}" },
            expire_date:            { required: true,  futureDate: true, message: "{{ __('Please select a future expiration date') }}" },
            minimum_quantity:       { min: 0, integer: true,  message: "{{ __('Must be 0 or a positive whole number') }}" },
            minimum_spend:          { min: 0, decimal: true,  message: "{{ __('Must be 0 or a positive number') }}" },
            maximum_spend:          { min: 0, decimal: true,  message: "{{ __('Must be 0 or a positive number') }}" },
            usage_limit_per_coupon: { min: 0, integer: true,  message: "{{ __('Must be 0 or a positive whole number') }}" },
            usage_limit_per_user:   { min: 0, integer: true,  message: "{{ __('Must be 0 or a positive whole number') }}" }
        };

        function showError(input, message) {
            var $fg = $(input).closest('.cp-field');
            $fg.find('.error-message').remove();
            $(input).addClass('is-invalid').removeClass('is-valid');
            $fg.append('<p class="error-message"><i class="mdi mdi-alert-circle-outline"></i> ' + message + '</p>');
        }
        function clearError(input) {
            $(input).closest('.cp-field').find('.error-message').remove();
            $(input).removeClass('is-invalid').addClass('is-valid');
        }

        function validateField(input) {
            var name  = $(input).attr('name');
            var value = $(input).val();
            var baseName = name ? name.replace(/^edit_/, '') : name;
            var rules = validationRules[baseName];
            if (!rules) return true;
            if (rules.required && !value) { showError(input, rules.message); return false; }
            if (!value && !rules.required) { clearError(input); return true; }
            if (rules.maxLength && value.length > rules.maxLength) { showError(input, "{{ __('Too long') }}"); return false; }
            if (rules.pattern && !rules.pattern.test(value)) { showError(input, rules.message); return false; }
            if (rules.min !== undefined && parseFloat(value) < rules.min) { showError(input, rules.message); return false; }
            if (rules.integer && value && !Number.isInteger(parseFloat(value))) { showError(input, "{{ __('Must be a whole number') }}"); return false; }
            if (rules.futureDate && value) {
                var d = new Date(value), today = new Date();
                today.setHours(0,0,0,0);
                if (d <= today) { showError(input, "{{ __('Date must be in the future') }}"); return false; }
            }
            clearError(input); return true;
        }

        function validateMinMax(minSel, maxSel) {
            var mn = parseFloat($(minSel).val()) || 0, mx = parseFloat($(maxSel).val()) || 0;
            if (mn > 0 && mx > 0 && mn > mx) { showError($(maxSel), "{{ __('Max must be greater than min') }}"); return false; }
            if (mx > 0) clearError($(maxSel));
            return true;
        }

        function validateDiscount(discTypeSel, discSel) {
            var type = $(discTypeSel).val(), disc = parseFloat($(discSel).val()) || 0;
            if (type === 'percentage' && disc > 100) { showError($(discSel), "{{ __('Percentage cannot exceed 100%') }}"); return false; }
            if (disc > 0) clearError($(discSel));
            return true;
        }

        $('input[name], select[name]').on('input change blur', function () {
            validateField(this);
            var n = $(this).attr('name') || '';
            if (n === 'minimum_spend' || n === 'maximum_spend') validateMinMax('#minimum_spend', '#maximum_spend');
            if (n === 'discount' || n === 'discount_type') validateDiscount('#discount_type', '#discount');
            if (n === 'edit_minimum_spend' || n === 'edit_maximum_spend') validateMinMax('#edit_minimum_spend', '#edit_maximum_spend');
            if (n === 'edit_discount' || n === 'edit_discount_type') validateDiscount('#edit_discount_type', '#edit_discount');
        });

        $('form').on('submit', function (e) {
            var $form = $(this), valid = true;
            $form.find('input[name], select[name]').each(function () { if (!validateField(this)) valid = false; });
            if ($form.find('#discount_type').length)      { if (!validateDiscount('#discount_type','#discount')) valid = false; if (!validateMinMax('#minimum_spend','#maximum_spend')) valid = false; }
            if ($form.find('#edit_discount_type').length) { if (!validateDiscount('#edit_discount_type','#edit_discount')) valid = false; if (!validateMinMax('#edit_minimum_spend','#edit_maximum_spend')) valid = false; }
            if (!valid) {
                e.preventDefault();
                var $first = $form.find('.is-invalid').first();
                if ($first.length) { $first.focus(); }
                toastr.error("{{ __('Please fix the errors before submitting') }}");
            }
        });

        /* ── Coupon Code Availability Check ─────────────────────── */
        function validateCoupon(context) {
            var code = $(context).val();
            var $btn = $(context).closest('form').find('button[type=submit]');
            var $badge = $(context).siblings('.code-status-badge');
            $badge.remove();
            if (!code.length) { $btn.prop('disabled', false); return; }
            $btn.prop('disabled', true);
            $.get("{{ route('tenant.admin.product.coupon.check') }}", { code: code }).then(function (data) {
                var taken = data > 0;
                var badge = taken
                    ? '<span class="code-badge taken"><i class="mdi mdi-close-circle-outline"></i> {{ __("Already taken") }}</span>'
                    : '<span class="code-badge available"><i class="mdi mdi-check-circle-outline"></i> {{ __("Available") }}</span>';
                $(context).after('<span class="code-status-badge mt-1 block">' + badge + '</span>');
                $(context)[taken ? 'addClass' : 'removeClass']('is-invalid');
                $(context)[taken ? 'removeClass' : 'addClass']('is-valid');
                $btn.prop('disabled', taken);
            }).catch(function () { $btn.prop('disabled', false); });
        }

        $('#code').on('keyup', function () { validateCoupon(this); });
        $('#edit_code').on('keyup', function () { validateCoupon(this); });

        /* ── Discount-On Toggle ─────────────────────────────────── */
        function handleDiscountOnChange(context, prefix) {
            var p = prefix || '';
            $('#' + p + 'form_category, #' + p + 'form_subcategory, #' + p + 'form_childcategory, #' + p + 'form_products').hide();
            var val = $(context).val();
            if (val === 'category')      { $('#' + p + 'form_category').fadeIn(300); }
            else if (val === 'subcategory')   { $('#' + p + 'form_subcategory').fadeIn(300); }
            else if (val === 'childcategory') { $('#' + p + 'form_childcategory').fadeIn(300); }
            else if (val === 'product')  { window._couponLoadProducts('#' + p + 'form_products select', []); }
        }

        window._couponLoadProducts = function loadProducts(targetSel, selectedIds) {
            $('#coupon_product_loader').removeClass('hidden');
            $.get("{{ route('tenant.admin.product.coupon.products') }}").then(function (data) {
                $('#coupon_product_loader').addClass('hidden');
                var options = '';
                data.forEach(function (p) {
                    var sel = (selectedIds.indexOf(p.id) > -1 || selectedIds.indexOf(String(p.id)) > -1) ? 'selected' : '';
                    options += '<option value="' + p.id + '" ' + sel + '>' + p.name + '</option>';
                });
                var $select = $(targetSel);
                $select.html(options);
                $select.closest('.cp-conditional').fadeIn(300);
            }).catch(function () {
                $('#coupon_product_loader').addClass('hidden');
                toastr.error("{{ __('Failed to load products') }}");
            });
        }

        $('#discount_on').on('change', function () { handleDiscountOnChange(this, ''); });
        $('#edit_discount_on').on('change', function () { handleDiscountOnChange(this, 'edit_'); });

    });
    </script>
@endsection
