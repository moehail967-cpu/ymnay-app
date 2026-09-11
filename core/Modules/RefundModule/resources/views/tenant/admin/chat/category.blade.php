@extends(route_prefix().'admin.admin-master')

@section('title') {{__('All Support Ticket Category')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')
    @php
        $lang_slug = request()->get('lang') ?? \App\Facades\GlobalLanguage::default_slug();
    @endphp

<x-error-msg-tw/>
<x-flash-msg-tw/>

<div class="bg-surface rounded-xl shadow-main border border-main">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-tag-multiple-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Ticket Categories')}}</h3>
                <p class="text-xs text-muted">{{__('Manage support ticket departments')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @can('support-ticket-department-delete')
                <x-bulk-action permissions="support-ticket-department-delete"/>
            @endcan
            <form action="" method="get" class="inline-flex">
                <select name="lang" class="lnd-input text-xs py-1.5 px-3 w-auto" onchange="this.form.submit()">
                    @foreach(\App\Facades\GlobalLanguage::all_languages() as $lang)
                        <option value="{{$lang->slug}}" @if($lang->slug === $lang_slug) selected @endif>{{$lang->name}}</option>
                    @endforeach
                </select>
            </form>
            @can('support-ticket-department-create')
                <button type="button" onclick="openModal('createDeptModal')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add Department')}}
                </button>
            @endcan
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="deptTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Title')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_category as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <x-bulk-delete-checkbox :id="$data->id"/>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{$data->id}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$data->name}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        @if($data->status == \App\Enums\StatusEnums::PUBLISH)
                            <span class="tw-pill tw-pill-success">{{__('Published')}}</span>
                        @else
                            <span class="tw-pill tw-pill-warning">{{__('Draft')}}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('support-ticket-department-edit')
                            <button type="button"
                                    class="tw-btn-icon tw-btn-icon-edit support_department_edit_btn"
                                    title="{{__('Edit')}}"
                                    data-id="{{$data->id}}"
                                    data-action="{{route(route_prefix().'admin.support.ticket.department.update')}}"
                                    data-name="{{$data->name}}"
                                    data-status="{{$data->status}}">
                                <i class="mdi mdi-pencil-outline"></i>
                            </button>
                            @endcan
                            @can('support-ticket-department-delete')
                            <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                    data-route="{{route(route_prefix().'admin.support.ticket.department.delete', $data->id)}}"
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
@can('support-ticket-department-create')
<div id="createDeptModal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('createDeptModal')"></div>
    <div class="relative bg-surface rounded-2xl border border-main w-full max-w-md overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-main">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('New Department')}}</h5>
                <p class="text-[11px] text-muted">{{__('Add a ticket department')}}</p>
            </div>
            <button type="button" onclick="closeModal('createDeptModal')"
                    class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                <i class="mdi mdi-close text-muted"></i>
            </button>
        </div>
        <form action="{{route(route_prefix().'admin.support.ticket.department')}}" method="post">
            @csrf
            <input type="hidden" name="lang" value="{{$default_lang}}">
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                    <input type="text" name="name" placeholder="{{__('Department name')}}" required class="lnd-input">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                    <select name="status" class="lnd-input">
                        <option value="{{\App\Enums\StatusEnums::PUBLISH}}">{{__('Publish')}}</option>
                        <option value="{{\App\Enums\StatusEnums::DRAFT}}">{{__('Draft')}}</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main" style="background: var(--color-bg-secondary);">
                <button type="button" onclick="closeModal('createDeptModal')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- Edit Modal --}}
@can('support-ticket-department-edit')
<div id="editDeptModal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('editDeptModal')"></div>
    <div class="relative bg-surface rounded-2xl border border-main w-full max-w-md overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-main">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-pencil-outline text-primary text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Department')}}</h5>
                <p class="text-[11px] text-muted">{{__('Update department details')}}</p>
            </div>
            <button type="button" onclick="closeModal('editDeptModal')"
                    class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                <i class="mdi mdi-close text-muted"></i>
            </button>
        </div>
        <form action="#" id="support_department_categoryy_edit_modal_form" method="post">
            @csrf
            <input type="hidden" name="lang" value="{{$default_lang}}">
            <input type="hidden" name="id" class="support_department_id" value="">
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="edit_name lnd-input" placeholder="{{__('Department name')}}" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                    <select name="status" class="edit_status lnd-input">
                        <option value="1">{{__('Publish')}}</option>
                        <option value="0">{{__('Draft')}}</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main" style="background: var(--color-bg-secondary);">
                <button type="button" onclick="closeModal('editDeptModal')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
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
    <x-bulk-action-js :url="route(route_prefix().'admin.support.ticket.department.bulk.action')"/>
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
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#deptTable')) {
                $('#deptTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // Edit button
            $(document).on('click', '.support_department_edit_btn', function () {
                var el = $(this);
                var form = $('#support_department_categoryy_edit_modal_form');
                form.attr('action', el.data('action'));
                form.find('.support_department_id').val(el.data('id'));
                form.find('.edit_name').val(el.data('name'));
                form.find('.edit_status').val(el.data('status'));
                openModal('editDeptModal');
            });

            // SweetAlert Delete
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
                            success: function (res) {
                                if (res.type === 'success') {
                                    toastr.success(res.message || '{{__("Deleted successfully")}}');
                                    row.fadeOut(300, function () { $(this).remove(); });
                                } else {
                                    toastr.error(res.message || '{{__("Something went wrong")}}');
                                }
                            },
                            error: function () {
                                toastr.error('{{__("Something went wrong")}}');
                            }
                        });
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
