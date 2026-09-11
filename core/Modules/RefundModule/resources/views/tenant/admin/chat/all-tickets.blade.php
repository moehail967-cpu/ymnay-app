@extends(route_prefix().'admin.admin-master')

@section('title') {{__('All Refund Message')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="bg-surface rounded-xl shadow-main border border-main">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-message-text-outline text-info text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Refund Messages')}}</h3>
                <p class="text-xs text-muted">{{__('All refund support tickets')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @can('support-ticket-delete')
                <x-bulk-action permissions="support-ticket-delete"/>
            @endcan
            <a href="{{route(route_prefix().'admin.refund.chat.new')}}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('New Ticket')}}
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="refundTicketTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14 no-sort">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Title')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_tickets as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <x-bulk-delete-checkbox :id="$data->id"/>
                    </td>

                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">#{{$data->id}}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$data->title}}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="tw-avatar-initials" style="width:1.75rem;height:1.75rem;font-size:0.625rem;">
                                {{substr(optional($data->user)->name ?? 'A', 0, 1)}}
                            </div>
                            <span class="text-sm text-dark">{{optional($data->user)->name ?? __('Anonymous')}}</span>
                        </div>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="refund-status-wrap" data-id="{{$data->id}}">
                            <button type="button"
                                    class="ticket-status-btn {{$data->status === 'open' ? 'is-open' : 'is-close'}} refund-toggle-menu">
                                {{$data->status}}
                            </button>
                            <div class="refund-status-menu hidden">
                                <a href="#" class="status_change" data-id="{{$data->id}}" data-val="open">
                                    <span class="menu-dot" style="background:#16a34a;"></span>
                                    {{__('Open')}}
                                </a>
                                <a href="#" class="status_change" data-id="{{$data->id}}" data-val="close">
                                    <span class="menu-dot" style="background:#6b7280;"></span>
                                    {{__('Close')}}
                                </a>
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{route(route_prefix().'admin.refund.chat.view', $data->id)}}"
                               class="tw-btn-icon tw-btn-icon-view" title="{{__('View')}}">
                                <i class="mdi mdi-eye-outline"></i>
                            </a>
                            @can('support-ticket-delete')
                            <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                    data-route="{{route(route_prefix().'admin.refund.chat.delete', $data->id)}}"
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

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-bulk-action-js :url="route(route_prefix().'admin.refund.chat.bulk.action')"/>
    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#refundTicketTable')) {
                $('#refundTicketTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // Toggle status dropdown
            $(document).on('click', '.refund-toggle-menu', function (e) {
                e.stopPropagation();
                var menu = $(this).next('.refund-status-menu');
                $('.refund-status-menu').not(menu).addClass('hidden');
                menu.toggleClass('hidden');
            });

            $(document).on('click', function () {
                $('.refund-status-menu').addClass('hidden');
            });

            // Status change AJAX
            $(document).on('click', '.status_change', function (e) {
                e.preventDefault();
                var status = $(this).data('val');
                var id = $(this).data('id');
                var wrap = $(this).closest('.refund-status-wrap');
                var btn = wrap.find('.ticket-status-btn');

                $.ajax({
                    type: 'post',
                    url: "{{route('tenant.admin.refund.chat.status.change')}}",
                    data: {
                        _token: "{{csrf_token()}}",
                        status: status,
                        id: id,
                    },
                    success: function () {
                        btn.removeClass('is-open is-close')
                           .addClass(status === 'open' ? 'is-open' : 'is-close')
                           .text(status);
                        toastr.success('{{__("Status changed successfully")}}');
                    }
                });

                wrap.find('.refund-status-menu').addClass('hidden');
            });

            // SweetAlert Delete (AJAX)
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
