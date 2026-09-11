@extends(route_prefix().'admin.admin-master')

@section('title') {{__('Digital Product Tax Manage')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-error-msg-tw/>
<x-flash-msg-tw/>

<div class="bg-surface rounded-xl shadow-main border border-main">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-percent-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Digital Product Tax Manage')}}</h3>
                <p class="text-xs text-muted">{{__('Manage product taxes')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @can('digital-tax-delete')
                <x-bulk-action permissions="digital-tax-delete"/>
            @endcan
            @can('digital-tax-create')
                <button type="button" onclick="openModal('tax_create_modal')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add New Tax')}}
                </button>
            @endcan
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="taxTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <div class="mark-all-checkbox"><input type="checkbox" class="all-checkbox"></div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Tax Percent (%)')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_taxes ?? [] as $key => $tax)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5"><x-bulk-delete-checkbox :id="$tax->id"/></td>
                    <td class="px-4 py-3.5"><span class="text-[11px] font-bold text-primary">#{{$tax->id}}</span></td>
                    <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{$tax->name}}</span></td>
                    <td class="px-4 py-3.5"><span class="text-sm text-main">{{$tax->tax_percentage}}%</span></td>
                    <td class="px-4 py-3.5">
                        <span class="tw-pill {{ $tax->status == 1 ? 'tw-pill-success' : 'tw-pill-warning' }}">
                            {{\App\Enums\StatusEnums::getText($tax->status)}}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('digital-tax-edit')
                                <button type="button" class="tw-btn-icon tw-btn-icon-edit tax_edit_btn"
                                        title="{{__('Edit')}}"
                                        data-id="{{$tax->id}}"
                                        data-name="{{$tax->name}}"
                                        data-percent="{{$tax->tax_percentage}}"
                                        data-status="{{$tax->status}}">
                                    <i class="mdi mdi-pencil-outline"></i>
                                </button>
                            @endcan
                            @can('digital-tax-delete')
                                <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                        data-route="{{ route('tenant.admin.digital.product.tax.delete', $tax->id) }}"
                                        title="{{__('Delete')}}">
                                    <i class="mdi mdi-delete-outline"></i>
                                </button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Create Modal --}}
@can('digital-tax-create')
<div id="tax_create_modal" class="fixed inset-0 z-[800] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('tax_create_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Add New Tax')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Create a new tax entry')}}</p>
                </div>
                <button type="button" onclick="closeModal('tax_create_modal')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>
            <form action="{{ route('tenant.admin.digital.product.tax.new') }}" method="post" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                @csrf
                <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="create-name" name="name" placeholder="{{__('Name')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Tax Percent (%)')}} <span class="text-danger">*</span></label>
                        <input type="number" class="lnd-input" id="create-percent" name="tax_percent" placeholder="{{__('Tax Percent (%)')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status_id" class="lnd-input">
                            <option value="1">{{ __('Publish') }}</option>
                            <option value="0">{{ __('Draft') }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                    <button type="button" onclick="closeModal('tax_create_modal')" class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                        <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add New')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Edit Modal --}}
@can('digital-tax-edit')
<div id="tax_edit_modal" class="fixed inset-0 z-[800] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('tax_edit_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-pencil-outline text-primary text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Update Tax')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Edit tax details')}}</p>
                </div>
                <button type="button" onclick="closeModal('tax_edit_modal')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>
            <form action="{{ route('tenant.admin.digital.product.tax.update') }}" method="post" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="id" id="edit_tax_id">
                <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" class="lnd-input" id="edit_name" name="name" placeholder="{{__('Name')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Tax Percent (%)')}} <span class="text-danger">*</span></label>
                        <input type="number" class="lnd-input" id="edit_tax_percent" name="tax_percent" placeholder="{{__('Tax Percent (%)')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status_id" class="lnd-input" id="edit_status">
                            <option value="1">{{ __('Publish') }}</option>
                            <option value="0">{{ __('Draft') }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
                    <button type="button" onclick="closeModal('tax_edit_modal')" class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    @can('digital-tax-delete')
        <x-bulk-action-js :url="route('tenant.admin.digital.product.tax.bulk.action')"/>
    @endcan

    <script>
    (function ($) {
        "use strict";

        window.openModal = function (id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };
        window.closeModal = function (id) {
            document.getElementById(id).classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#taxTable')) {
                $('#taxTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // Edit button
            $(document).on('click', '.tax_edit_btn', function () {
                var el = $(this);
                var modal = $('#tax_edit_modal');
                modal.find('#edit_tax_id').val(el.data('id'));
                modal.find('#edit_name').val(el.data('name'));
                modal.find('#edit_tax_percent').val(el.data('percent'));
                modal.find('#edit_status').val(el.data('status'));
                openModal('tax_edit_modal');
            });

            // SweetAlert Delete (JSON-based)
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                var btn = $(this);
                var route = btn.data('route');
                var row = btn.closest('tr');

                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You will not be able to recover this!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{__("Yes, Delete it!")}}',
                    cancelButtonText: '{{__("Cancel")}}',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: route,
                            data: { _token: '{{csrf_token()}}' },
                            success: function () {
                                toastr.success('{{__("Deleted successfully")}}');
                                row.fadeOut(300, function () { $(this).remove(); });
                            },
                            error: function () { toastr.error('{{__("Something went wrong")}}'); }
                        });
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
