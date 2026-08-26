@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Support Tickets')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <link rel="stylesheet" href="{{ global_asset('assets/new-landlord/admin/css/components/support-ticket.css') }}">
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
                <i class="mdi mdi-ticket-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Support Tickets')}}</h3>
                <p class="text-xs text-muted">{{__('Manage and respond to support requests')}}</p>
            </div>
        </div>
        @can('support-ticket-create')
        <a href="{{route(route_prefix().'admin.support.ticket.new')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
            <i class="mdi mdi-plus text-base"></i>
            {{__('New Ticket')}}
        </a>
        @endcan
    </div>

    {{-- Bulk Action --}}
    @can('support-ticket-delete')
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
        <table class="w-full text-left" id="ticketsTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <input type="checkbox" class="all-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                    </th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Ticket')}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Department')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Priority')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_tickets as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- Checkbox --}}
                    <td class="px-4 py-3.5">
                        <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-primary focus:ring-primary" name="bulk_delete[]" value="{{$data->id}}">
                    </td>

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
                    </td>

                    {{-- Ticket (Title + User) --}}
                    <td class="px-4 py-3.5">
                        <a href="{{route(route_prefix().'admin.support.ticket.view', $data->id)}}" class="text-sm font-semibold text-dark hover:text-primary transition block">{{$data->title}}</a>
                        <span class="text-xs text-muted">{{optional($data->user)->name ?? __('Anonymous')}}</span>
                    </td>

                    {{-- Department --}}
                    <td class="hidden lg:table-cell px-4 py-3.5">
                        <span class="text-xs font-medium text-dark">{{optional($data->department)->name ?? __('N/A')}}</span>
                    </td>

                    {{-- Priority --}}
                    <td class="px-4 py-3.5">
                        <div class="relative inline-block priority-dropdown">
                            <button type="button" class="ticket-priority-btn is-{{$data->priority}} priority-trigger" data-id="{{$data->id}}">
                                {{__($data->priority)}} <i class="mdi mdi-chevron-down text-[10px]"></i>
                            </button>
                            <div class="priority-menu hidden absolute z-50 mt-1 bg-surface border border-main rounded-xl shadow-main py-1 min-w-[120px]">
                                <button type="button" class="change_priority w-full text-left px-3 py-1.5 text-xs font-medium text-dark hover:bg-muted transition" data-id="{{$data->id}}" data-val="low">{{__('Low')}}</button>
                                <button type="button" class="change_priority w-full text-left px-3 py-1.5 text-xs font-medium text-dark hover:bg-muted transition" data-id="{{$data->id}}" data-val="medium">{{__('Medium')}}</button>
                                <button type="button" class="change_priority w-full text-left px-3 py-1.5 text-xs font-medium text-dark hover:bg-muted transition" data-id="{{$data->id}}" data-val="high">{{__('High')}}</button>
                                <button type="button" class="change_priority w-full text-left px-3 py-1.5 text-xs font-medium text-dark hover:bg-muted transition" data-id="{{$data->id}}" data-val="urgent">{{__('Urgent')}}</button>
                            </div>
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3.5">
                        <div class="relative inline-block status-dropdown">
                            <button type="button" class="ticket-status-btn is-{{$data->status}} status-trigger" data-id="{{$data->id}}">
                                {{__($data->status)}} <i class="mdi mdi-chevron-down text-[10px]"></i>
                            </button>
                            <div class="status-menu hidden absolute z-50 mt-1 bg-surface border border-main rounded-xl shadow-main py-1 min-w-[100px]">
                                <button type="button" class="status_change w-full text-left px-3 py-1.5 text-xs font-medium text-dark hover:bg-muted transition" data-id="{{$data->id}}" data-val="open">{{__('Open')}}</button>
                                <button type="button" class="status_change w-full text-left px-3 py-1.5 text-xs font-medium text-dark hover:bg-muted transition" data-id="{{$data->id}}" data-val="close">{{__('Close')}}</button>
                            </div>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{route(route_prefix().'admin.support.ticket.view', $data->id)}}"
                               title="{{__('View')}}"
                               class="tw-btn-icon tw-btn-icon-view">
                                <i class="mdi mdi-eye-outline"></i>
                            </a>
                            <x-delete-popover permissions="support-ticket-delete" :url="route(route_prefix().'admin.support.ticket.delete', $data->id)"/>
                        </div>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-bulk-action-js :url="route(route_prefix().'admin.support.ticket.bulk.action')"/>
    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#ticketsTable')) {
                $('#ticketsTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // Priority dropdown
            $(document).on('click', '.priority-trigger', function (e) {
                e.stopPropagation();
                $('.priority-menu, .status-menu').addClass('hidden');
                $(this).siblings('.priority-menu').toggleClass('hidden');
            });

            $(document).on('click', '.change_priority', function (e) {
                e.preventDefault();
                var priority = $(this).data('val');
                var id = $(this).data('id');
                var btn = $(this).closest('.priority-dropdown').find('.priority-trigger');
                btn.removeClass('is-low is-medium is-high is-urgent').addClass('is-' + priority).html(priority + ' <i class="mdi mdi-chevron-down text-[10px]"></i>');
                $(this).closest('.priority-menu').addClass('hidden');

                $.ajax({
                    type: 'post',
                    url: "{{route(route_prefix().'admin.support.ticket.priority.change')}}",
                    data: { _token: "{{csrf_token()}}", priority: priority, id: id }
                });
            });

            // Status dropdown
            $(document).on('click', '.status-trigger', function (e) {
                e.stopPropagation();
                $('.priority-menu, .status-menu').addClass('hidden');
                $(this).siblings('.status-menu').toggleClass('hidden');
            });

            $(document).on('click', '.status_change', function (e) {
                e.preventDefault();
                var status = $(this).data('val');
                var id = $(this).data('id');
                var btn = $(this).closest('.status-dropdown').find('.status-trigger');
                btn.removeClass('is-open is-close').addClass('is-' + status).html(status + ' <i class="mdi mdi-chevron-down text-[10px]"></i>');
                $(this).closest('.status-menu').addClass('hidden');

                $.ajax({
                    type: 'post',
                    url: "{{route(route_prefix().'admin.support.ticket.status.change')}}",
                    data: { _token: "{{csrf_token()}}", status: status, id: id }
                });
            });

            // Close dropdowns on outside click
            $(document).on('click', function () {
                $('.priority-menu, .status-menu').addClass('hidden');
            });
        });
    })(jQuery);
    </script>
@endsection
