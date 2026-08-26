@extends('tenant.admin.admin-master')

@section('title') {{__('Trashed Delivery Options')}} @endsection

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
            <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-delete-outline text-danger text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Trashed Delivery Options')}}</h3>
                <p class="text-xs text-muted">{{__('Restore or permanently delete delivery options')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @can('product-delivery-manage-delete')
                <x-bulk-action permissions="product-delivery-manage-delete"/>
            @endcan
            <a href="{{route('tenant.admin.product.delivery.option.all')}}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:border-hover transition">
                <i class="mdi mdi-arrow-left text-base"></i> {{__('Back')}}
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="trashDeliveryTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Icon')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Title')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest hidden sm:table-cell">{{__('Sub Title')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($delivery_manages as $item)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <x-bulk-delete-checkbox :id="$item->id"/>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">#{{$item->id}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="tw-icon-preview"><i class="{{$item->icon}}"></i></span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$item->title}}</span>
                    </td>
                    <td class="px-4 py-3.5 hidden sm:table-cell">
                        <span class="text-sm text-muted">{{$item->sub_title}}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('product-delivery_manage-delete')
                                <a href="{{route('tenant.admin.product.delivery.option.trash.restore', $item->id)}}"
                                   class="tw-btn-icon tw-btn-icon-approve" title="{{__('Restore')}}">
                                    <i class="mdi mdi-backup-restore"></i>
                                </a>
                                <a href="{{route('tenant.admin.product.delivery.option.trash.delete', $item->id)}}"
                                   class="tw-btn-icon tw-btn-icon-danger swal_confirm_link"
                                   title="{{__('Delete Permanently')}}">
                                    <i class="mdi mdi-delete-forever-outline"></i>
                                </a>
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
    @can('product-delivery_manage-delete')
        <x-bulk-action-js :url="route('tenant.admin.product.delivery.option.trash.bulk.action')"/>
    @endcan
    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#trashDeliveryTable')) {
                $('#trashDeliveryTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            $(document).on('click', '.swal_confirm_link', function (e) {
                e.preventDefault();
                var url = $(this).attr('href');
                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("This action cannot be undone!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{__("Yes, Delete it!")}}',
                    cancelButtonText: '{{__("Cancel")}}',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
