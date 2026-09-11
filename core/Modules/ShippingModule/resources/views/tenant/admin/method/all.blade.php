@extends('tenant.admin.admin-master')
@section('title') {{__('Shipping Methods')}} @endsection

@section('content')

    <x-landlord-error-msg/>
    <x-landlord-flash-msg/>

    <div class="bg-surface rounded-xl shadow-main overflow-hidden mb-6">

        {{-- Card Header --}}
        <div class="px-5 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-truck-fast text-info text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Shipping Methods')}}</h3>
                    <p class="text-xs text-muted">{{__('Manage your shipping methods and costs')}}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @can('shipping-method-delete')
                <select id="bulk_action_select"
                        class="text-xs border border-main rounded-lg px-3 py-2 bg-secondary text-dark outline-none focus:border-primary transition hidden appearance-none"
                        style="display:none;">
                    <option value="">{{__('Bulk Action')}}</option>
                    <option value="delete">{{__('Delete')}}</option>
                </select>
                <button type="button" id="bulk_action_btn"
                        class="hidden items-center gap-1.5 px-3 py-2 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition-all"
                        style="display:none;">
                    <i class="mdi mdi-delete-sweep text-sm"></i> {{__('Apply')}}
                </button>
                @endcan

                @can('shipping-method-list')
                <a href="{{ route('tenant.admin.shipping.method.new') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                    <i class="mdi mdi-plus text-base"></i>
                    {{__('Create Method')}}
                </a>
                @endcan
            </div>
        </div>

        {{-- Table --}}
        <div class="tw-table-wrap">
            <table>
                <thead>
                    <tr>
                        @can('shipping-method-delete')
                        <th class="w-10">
                            <input type="checkbox" id="check_all_method"
                                   class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/20">
                        </th>
                        @endcan
                        <th>{{__('#')}}</th>
                        <th>{{__('Title')}}</th>
                        <th>{{__('Zone')}}</th>
                        <th>{{__('Tax')}}</th>
                        <th>{{__('Status')}}</th>
                        <th>{{__('Cost')}}</th>
                        <th>{{__('Min. Order')}}</th>
                        <th class="text-right">{{__('Action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($all_shipping_methods as $method)
                    <tr>
                        @can('shipping-method-delete')
                        <td>
                            <input type="checkbox" class="bulk-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/20"
                                   value="{{$method->id}}">
                        </td>
                        @endcan
                        <td>
                            <span class="text-xs text-muted font-medium">{{ $loop->iteration }}</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                                    <i class="mdi mdi-truck text-info text-xs"></i>
                                </div>
                                <span class="text-sm font-semibold text-dark">{{ optional($method->options)->title }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-secondary text-xs font-medium text-muted">
                                <i class="mdi mdi-map-marker-radius text-xs"></i>
                                {{ optional($method->zone)->name }}
                            </span>
                        </td>
                        <td>
                            @if(optional($method->options)->tax_status)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-warning">
                                    <i class="mdi mdi-cash text-sm"></i> {{__('Taxable')}}
                                </span>
                            @else
                                <span class="text-xs text-muted">{{__('None')}}</span>
                            @endif
                        </td>
                        <td>
                            @if(optional($method->options)->status == 1)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{__('Active')}}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> {{__('Draft')}}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm font-semibold text-dark">{{ amount_with_currency_symbol(optional($method->options)->cost) }}</span>
                        </td>
                        <td>
                            <span class="text-sm text-muted">{{ amount_with_currency_symbol(optional($method->options)->minimum_order_amount) }}</span>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1.5">
                                @can('shipping-method-make-default')
                                    @if (!$method->is_default)
                                    <form action="{{ route('tenant.admin.shipping.method.make.default') }}" method="post" class="inline">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $method->id }}">
                                        <button type="submit"
                                                class="px-2.5 py-1 rounded-lg bg-warning-soft text-warning text-[11px] font-semibold hover:bg-warning hover:text-white transition"
                                                title="{{__('Make Default')}}">
                                            {{__('Make Default')}}
                                        </button>
                                    </form>
                                    @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-success-soft text-success text-[11px] font-semibold">
                                        <i class="mdi mdi-check-circle text-xs"></i> {{__('Default')}}
                                    </span>
                                    @endif
                                @endcan
                                @can('shipping-method-edit')
                                <a href="{{ route('tenant.admin.shipping.method.edit', $method->id) }}"
                                   class="w-8 h-8 rounded-lg bg-primary-soft text-primary flex items-center justify-center hover:bg-primary hover:text-white transition"
                                   title="{{__('Edit')}}">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </a>
                                @endcan
                                @can('shipping-method-delete')
                                    @if (!$method->is_default)
                                    <button type="button"
                                            class="swal_status_change_button w-8 h-8 rounded-lg bg-danger-soft text-danger flex items-center justify-center hover:bg-danger hover:text-white transition"
                                            title="{{__('Delete')}}">
                                        <i class="mdi mdi-delete-outline text-sm"></i>
                                    </button>
                                    <form action="{{route('tenant.admin.shipping.method.delete', $method->id)}}" method="post" class="hidden">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="swal_form_submit_btn"></button>
                                    </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="flex flex-col items-center gap-2 py-8">
                                <i class="mdi mdi-truck-remove text-3xl text-muted"></i>
                                <p class="text-sm text-muted">{{__('No shipping methods found')}}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {

            // Swal delete confirmation (form submit)
            $(document).on('click', '.swal_status_change_button', function (e) {
                e.preventDefault();
                var $btn = $(this);
                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You would not be able to revert this item!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dd3333',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{__("Yes, Delete it!")}}'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $btn.next('form').find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            // Bulk check all
            $(document).on('change', '#check_all_method', function () {
                $('.bulk-checkbox').prop('checked', this.checked);
                toggleBulkUI();
            });
            $(document).on('change', '.bulk-checkbox', toggleBulkUI);

            function toggleBulkUI() {
                var checked = $('.bulk-checkbox:checked').length;
                if (checked > 0) {
                    $('#bulk_action_select, #bulk_action_btn').removeClass('hidden').css('display', '');
                } else {
                    $('#bulk_action_select, #bulk_action_btn').addClass('hidden');
                }
            }

            $('#bulk_action_btn').on('click', function () {
                var action = $('#bulk_action_select').val();
                if (!action) return;
                var ids = [];
                $('.bulk-checkbox:checked').each(function () { ids.push($(this).val()); });
                if (ids.length === 0) return;
                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1a5c4e',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: '{{__("Yes, do it!")}}'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: '{{route("tenant.admin.shipping.method.bulk.action")}}',
                            data: { _token: '{{csrf_token()}}', ids: ids, action: action },
                            success: function () { location.reload(); }
                        });
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
