@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Departments')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

@php
    $lang_slug = request()->get('lang') ?? \App\Facades\GlobalLanguage::default_slug();
@endphp

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-domain text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Departments')}}</h3>
                <p class="text-xs text-muted">{{__('Manage support ticket departments')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Language Selector --}}
            <form action="" method="get">
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-lg px-3 py-1.5">
                    <i class="mdi mdi-translate text-sm text-primary"></i>
                    <select name="lang" class="bg-transparent text-xs text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer pr-4"
                            onchange="this.form.submit()">
                        @foreach(\App\Facades\GlobalLanguage::all_languages() as $lang)
                            <option value="{{$lang->slug}}" @if($lang->slug === $lang_slug) selected @endif>{{$lang->name}}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            @can('support-ticket-department-create')
            <button type="button" onclick="document.getElementById('addDepartmentModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                <i class="mdi mdi-plus text-base"></i> {{__('Add Department')}}
            </button>
            @endcan
        </div>
    </div>

    {{-- Bulk Action --}}
    @can('support-ticket-department-delete')
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
    @endcan

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <input type="checkbox" class="all-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_category as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-primary focus:ring-primary" name="bulk_delete[]" value="{{$data->id}}">
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">#{{$data->id}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$data->name}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        @if($data->status == \App\Enums\StatusEnums::PUBLISH)
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                <i class="mdi mdi-circle-medium text-xs"></i> {{__('Publish')}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-gray-100 text-gray-500 text-[10px] font-bold uppercase">
                                <i class="mdi mdi-circle-medium text-xs"></i> {{__('Draft')}}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            @can('support-ticket-department-edit')
                            <button type="button"
                                    class="tw-btn-icon tw-btn-icon-view support_department_edit_btn"
                                    title="{{__('Edit')}}"
                                    data-id="{{$data->id}}"
                                    data-action="{{route(route_prefix().'admin.support.ticket.department.update')}}"
                                    data-name="{{$data->name}}"
                                    data-status="{{$data->status}}">
                                <i class="mdi mdi-pencil-outline"></i>
                            </button>
                            @endcan
                            <x-delete-popover permissions="support-ticket-department-delete" :url="route(route_prefix().'admin.support.ticket.department.delete', $data->id)"/>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Add Department Modal --}}
@can('support-ticket-department-create')
<div id="addDepartmentModal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative bg-surface rounded-xl shadow-xl border border-main w-full max-w-md">
        <div class="px-5 py-4 border-b border-main flex items-center justify-between">
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('New Department')}}</h3>
            <button type="button" onclick="this.closest('#addDepartmentModal').classList.add('hidden')"
                    class="w-7 h-7 rounded-lg hover:bg-muted flex items-center justify-center text-muted transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>
        <form action="{{route(route_prefix().'admin.support.ticket.department')}}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="lang" value="{{$default_lang}}">
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-tag-outline text-lg text-primary"></i>
                        <input type="text" name="name" placeholder="{{__('Department name')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" required>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2 focus-within:border-primary transition">
                        <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                        <select name="status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            <option value="{{\App\Enums\StatusEnums::PUBLISH}}">{{__('Publish')}}</option>
                            <option value="{{\App\Enums\StatusEnums::DRAFT}}">{{__('Draft')}}</option>
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-main bg-secondary rounded-b-xl">
                <button type="button" onclick="this.closest('#addDepartmentModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i> {{__('Save')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- Edit Department Modal --}}
@can('support-ticket-department-edit')
<div id="editDepartmentModal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative bg-surface rounded-xl shadow-xl border border-main w-full max-w-md">
        <div class="px-5 py-4 border-b border-main flex items-center justify-between">
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Department')}}</h3>
            <button type="button" onclick="this.closest('#editDepartmentModal').classList.add('hidden')"
                    class="w-7 h-7 rounded-lg hover:bg-muted flex items-center justify-center text-muted transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>
        <form action="#" id="editDepartmentForm" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="lang" value="{{$default_lang}}">
            <input type="hidden" name="id" id="edit_department_id" value="">
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-tag-outline text-lg text-primary"></i>
                        <input type="text" name="name" id="edit_department_name" placeholder="{{__('Department name')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" required>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2 focus-within:border-primary transition">
                        <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                        <select name="status" id="edit_department_status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            <option value="1">{{__('Publish')}}</option>
                            <option value="0">{{__('Draft')}}</option>
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-main bg-secondary rounded-b-xl">
                <button type="button" onclick="this.closest('#editDepartmentModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-check text-base"></i> {{__('Update')}}
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

        $(document).on('change', 'select[name="lang"]', function () {
            $(this).closest('form').trigger('submit');
        });

        $(document).on('click', '.support_department_edit_btn', function () {
            var el = $(this);
            var form = $('#editDepartmentForm');
            form.attr('action', el.data('action'));
            $('#edit_department_id').val(el.data('id'));
            $('#edit_department_name').val(el.data('name'));
            $('#edit_department_status').val(el.data('status'));
            document.getElementById('editDepartmentModal').classList.remove('hidden');
        });
    })(jQuery);
    </script>
@endsection
