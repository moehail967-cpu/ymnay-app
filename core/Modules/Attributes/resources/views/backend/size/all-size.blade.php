@extends('tenant.admin.admin-master')

@section('title') {{__('All Product Size')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    {{-- Table Card (7 cols) --}}
    <div class="xl:col-span-7">
        <div class="bg-surface rounded-xl shadow-main border border-main">

            {{-- Card Header --}}
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-resize text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Product Sizes')}}</h3>
                        <p class="text-xs text-muted">{{__('Manage product size options')}}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @can('product-size-delete')
                        <x-bulk-action permissions="product-size-delete"/>
                    @endcan
                </div>
            </div>

            {{-- Table --}}
            <div class="tw-table-wrap">
                <table class="w-full text-left" id="sizeTable">
                    <thead>
                        <tr class="border-b border-main">
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                                <div class="mark-all-checkbox">
                                    <input type="checkbox" class="all-checkbox">
                                </div>
                            </th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Size Code')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Slug')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($product_sizes as $product_size)
                        <tr class="border-b border-main hover:bg-muted transition-colors">
                            <td class="px-4 py-3.5">
                                <x-bulk-delete-checkbox :id="$product_size->id"/>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-bold text-primary">#{{$product_size->id}}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-semibold text-dark">{{$product_size->name}}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm text-dark">{{$product_size->size_code}}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm text-muted">{{$product_size->slug}}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('product-size-edit')
                                        <button type="button"
                                                class="tw-btn-icon tw-btn-icon-edit size_edit_btn"
                                                title="{{__('Edit')}}"
                                                data-id="{{$product_size->id}}"
                                                data-name="{{$product_size->name}}"
                                                data-size_code="{{$product_size->size_code}}"
                                                data-slug="{{$product_size->slug}}">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                    @endcan
                                    @can('product-size-delete')
                                        <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                                data-route="{{ route('tenant.admin.product.sizes.delete', $product_size->id) }}"
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

    {{-- Create Form Card (5 cols) --}}
    @can('product-size-create')
    <div class="xl:col-span-5">
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('New Size')}}</h3>
                        <p class="text-xs text-muted">{{__('Create a new product size')}}</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('tenant.admin.product.sizes.new') }}" method="POST">
                @csrf
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="name" placeholder="{{__('Name')}}" class="lnd-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Size Code')}}</label>
                        <input type="text" name="size_code" placeholder="{{__('Size Code')}}" class="lnd-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Slug')}}</label>
                        <input type="text" name="slug" placeholder="{{__('Slug')}}" class="lnd-input">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Size')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan
</div>

{{-- Edit Modal --}}
@can('product-size-edit')
<div id="size_edit_modal" class="fixed inset-0 z-[999] hidden">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('size_edit_modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-surface rounded-xl shadow-xl border border-main w-full max-w-md">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-main">
                <h5 class="text-sm font-bold text-dark">{{__('Edit Product Size')}}</h5>
                <button type="button" onclick="closeModal('size_edit_modal')" class="text-muted hover:text-danger transition">
                    <i class="mdi mdi-close text-lg"></i>
                </button>
            </div>
            <form action="{{ route('tenant.admin.product.sizes.update') }}" method="post">
                @csrf
                <input type="hidden" name="id" id="size_id">
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" placeholder="{{__('Name')}}" class="lnd-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Size Code')}}</label>
                        <input type="text" name="size_code" id="edit_size_code" placeholder="{{__('Size Code')}}" class="lnd-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Slug')}}</label>
                        <input type="text" name="slug" id="edit_slug" placeholder="{{__('Slug')}}" class="lnd-input">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-3.5 border-t border-main">
                    <button type="button" onclick="closeModal('size_edit_modal')" class="px-4 py-2 rounded-lg text-sm font-medium text-muted hover:bg-secondary transition">{{__('Close')}}</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:opacity-90 transition">{{__('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-bulk-action-js :url="route('tenant.admin.product.sizes.bulk.action')"/>
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
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#sizeTable')) {
                $('#sizeTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // Edit button
            $(document).on('click', '.size_edit_btn', function () {
                var el = $(this);
                $('#size_id').val(el.data('id'));
                $('#edit_name').val(el.data('name'));
                $('#edit_size_code').val(el.data('size_code'));
                $('#edit_slug').val(el.data('slug'));
                openModal('size_edit_modal');
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
                                toastr.success('{{__("Deleted successfully")}}');
                                row.fadeOut(300, function () { $(this).remove(); });
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
