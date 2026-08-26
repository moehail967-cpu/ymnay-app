@extends(route_prefix().'admin.admin-master')

@section('title') {{__('All Refund Request')}} @endsection

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
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-cash-refund text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Refund Requests')}}</h3>
                <p class="text-xs text-muted">{{__('Manage product refund requests')}}</p>
            </div>
        </div>
        <a href="{{route(route_prefix().'admin.refund.chat.new')}}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
            <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('New Ticket')}}
        </a>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="refundTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14 no-sort">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Product')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($refunds as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">#{{$data->id}}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$data->product?->name ?? '—'}}</span>
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
                                    class="refund-status-btn {{$data->status == 1 ? 'is-refunded' : 'is-not-refunded'}} refund-toggle-menu">
                                {{$data->status == 1 ? __('Refunded') : __('Not Refunded')}}
                            </button>
                            <div class="refund-status-menu hidden">
                                <a href="#" class="status_change" data-id="{{$data->id}}" data-val="1">
                                    <span class="menu-dot" style="background:#7c3aed;"></span>
                                    {{__('Refunded')}}
                                </a>
                                <a href="#" class="status_change" data-id="{{$data->id}}" data-val="0">
                                    <span class="menu-dot" style="background:#ea580c;"></span>
                                    {{__('Not Refunded')}}
                                </a>
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <a href="{{route(route_prefix().'admin.refund.product.show', $data->product_id)}}"
                               class="tw-btn-icon tw-btn-icon-view" title="{{__('View')}}">
                                <i class="mdi mdi-eye-outline"></i>
                            </a>
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
    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#refundTable')) {
                $('#refundTable').DataTable({
                    "order": [[0, "desc"]],
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

            // Close dropdown on outside click
            $(document).on('click', function () {
                $('.refund-status-menu').addClass('hidden');
            });

            // Status change AJAX
            $(document).on('click', '.status_change', function (e) {
                e.preventDefault();
                var status = $(this).data('val');
                var id = $(this).data('id');
                var wrap = $(this).closest('.refund-status-wrap');
                var btn = wrap.find('.refund-status-btn');

                $.ajax({
                    type: 'post',
                    url: "{{route('tenant.admin.refund.status.change')}}",
                    data: {
                        _token: "{{csrf_token()}}",
                        status: status,
                        id: id,
                    },
                    success: function () {
                        btn.removeClass('is-refunded is-not-refunded')
                           .addClass(status == 1 ? 'is-refunded' : 'is-not-refunded')
                           .contents().first().replaceWith(status == 1 ? '{{__("Refunded")}} ' : '{{__("Not Refunded")}} ');
                        toastr.success('{{__("Status changed successfully")}}');
                    }
                });

                wrap.find('.refund-status-menu').addClass('hidden');
            });
        });
    })(jQuery);
    </script>
@endsection
