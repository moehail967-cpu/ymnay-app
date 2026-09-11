@extends('tenant.admin.admin-master')

@section('title') {{__('Delivery Options')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <style>.hover\:text-white:hover{color:#fff!important}</style>

@endsection

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    {{-- Table Card (8 cols) --}}
    <div class="xl:col-span-8">
        <div class="bg-surface rounded-xl shadow-main border border-main">

            {{-- Card Header --}}
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-truck-delivery-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Delivery Options')}}</h3>
                        <p class="text-xs text-muted">{{__('Manage product delivery methods')}}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @can('product-delivery-manage-delete')
                        <x-bulk-action permissions="product-delivery-manage-delete"/>
                        <a href="{{route('tenant.admin.product.delivery.option.trash.all')}}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition-all">
                            <i class="mdi mdi-delete-outline text-sm"></i> {{__('Trash')}}
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Table --}}
            <div class="tw-table-wrap">
                <table class="w-full text-left" id="deliveryTable">
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
                                    @can('product-delivery_manage-edit')
                                        <button type="button"
                                                class="tw-btn-icon tw-btn-icon-edit delivery_edit_btn"
                                                title="{{__('Edit')}}"
                                                data-id="{{$item->id}}"
                                                data-title="{{$item->title}}"
                                                data-sub-title="{{$item->sub_title}}"
                                                data-icon="{{$item->icon}}">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                    @endcan
                                    @can('product-delivery_manage-delete')
                                        <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                                data-route="{{ route('tenant.admin.product.delivery.option.delete', $item->id) }}"
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
    </div>

    {{-- Create Form Card (4 cols) --}}
    @can('product-delivery_manage-create')
    <div class="xl:col-span-4">
        <div class="bg-surface rounded-xl shadow-main border border-main sticky top-4">
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Add Delivery Option')}}</h3>
                        <p class="text-xs text-muted">{{__('Create a new delivery method')}}</p>
                    </div>
                </div>
            </div>
            <form action="{{route('tenant.admin.product.delivery.option.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Title')}} <span class="text-danger">*</span></label>
                        <input type="text" name="title" placeholder="{{__('Title')}}" class="lnd-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Sub Title')}}</label>
                        <input type="text" name="sub_title" placeholder="{{__('Sub Title')}}" class="lnd-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Icon Class')}}</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="icon" id="create_icon" placeholder="{{__('e.g. mdi mdi-truck')}}" class="lnd-input flex-1">
                            <span class="tw-icon-preview" id="create_icon_preview"><i class="mdi mdi-truck-delivery-outline"></i></span>
                        </div>
                        <p class="text-[10px] text-muted mt-1">{{__('Use MDI icon class name')}}</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add New')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan
</div>

{{-- Edit Modal --}}
@can('product-delivery_manage-edit')
<div id="delivery_edit_modal" class="fixed inset-0 z-[999] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('delivery_edit_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-xl shadow-xl border border-main w-full max-w-md">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-main">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-truck-delivery-outline text-primary text-sm"></i>
                    </div>
                    <h5 class="text-sm font-bold text-dark">{{__('Edit Delivery Option')}}</h5>
                </div>
                <button type="button" onclick="closeModal('delivery_edit_modal')" class="text-muted hover:text-danger transition">
                    <i class="mdi mdi-close text-lg"></i>
                </button>
            </div>
            <form action="{{route('tenant.admin.product.delivery.option.update')}}" method="post">
                @csrf
                <input type="hidden" name="id" id="delivery_id">
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Title')}} <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_title" placeholder="{{__('Title')}}" class="lnd-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Sub Title')}}</label>
                        <input type="text" name="sub_title" id="edit_sub_title" placeholder="{{__('Sub Title')}}" class="lnd-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Icon Class')}}</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="icon" id="edit_icon" placeholder="{{__('e.g. mdi mdi-truck')}}" class="lnd-input flex-1">
                            <span class="tw-icon-preview" id="edit_icon_preview"><i class="mdi mdi-truck-delivery-outline"></i></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-3.5 border-t border-main">
                    <button type="button" onclick="closeModal('delivery_edit_modal')" class="px-4 py-2 rounded-lg text-sm font-medium text-muted hover:bg-secondary transition">{{__('Close')}}</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:opacity-90 transition">{{__('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Delete Form (redirect-based) --}}
<form method="post" id="swal_delete_form" class="hidden">
    @csrf
</form>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    @can('product-delivery_manage-delete')
        <x-bulk-action-js :url="route('tenant.admin.product.delivery.option.bulk.action')"/>
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
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#deliveryTable')) {
                $('#deliveryTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // Live icon preview (create)
            $('#create_icon').on('keyup change', function () {
                var val = $(this).val().trim();
                $('#create_icon_preview').html('<i class="' + (val || 'mdi mdi-truck-delivery-outline') + '"></i>');
            });

            // Edit button
            $(document).on('click', '.delivery_edit_btn', function () {
                var el = $(this);
                $('#delivery_id').val(el.data('id'));
                $('#edit_title').val(el.data('title'));
                $('#edit_sub_title').val(el.data('sub-title'));
                var icon = el.data('icon') || '';
                $('#edit_icon').val(icon);
                $('#edit_icon_preview').html('<i class="' + (icon || 'mdi mdi-truck-delivery-outline') + '"></i>');
                openModal('delivery_edit_modal');
            });

            // Live icon preview (edit)
            $('#edit_icon').on('keyup change', function () {
                var val = $(this).val().trim();
                $('#edit_icon_preview').html('<i class="' + (val || 'mdi mdi-truck-delivery-outline') + '"></i>');
            });

            // SweetAlert Delete (redirect-based)
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                var btn = $(this);
                var route = btn.data('route');

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
                        var form = $('#swal_delete_form');
                        form.attr('action', route);
                        form.submit();
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
