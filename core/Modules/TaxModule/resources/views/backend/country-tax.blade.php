@extends(route_prefix().'admin.admin-master')

@section('title')
    {{__('Country Tax')}}
@endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-earth text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Country Tax')}}</h3>
                <p class="text-xs text-muted">{{__('Manage tax rates by country')}}</p>
            </div>
        </div>
        <button type="button" class="country_tax_new_btn inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
            <i class="mdi mdi-plus text-base"></i> {{__('Add Country Tax')}}
        </button>
    </div>

    @can('country-tax-delete')
        <div class="px-4 sm:px-6 py-3 border-b border-main flex items-center gap-3">
            <select id="bulk_action_select" class="text-xs bg-secondary border border-main rounded-lg px-3 py-1.5 text-dark focus:border-primary focus:outline-none transition">
                <option value="">{{__('Bulk Action')}}</option>
                <option value="delete">{{__('Delete')}}</option>
            </select>
            <button type="button" id="bulk_action_apply_btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
                {{__('Apply')}}
            </button>
        </div>
    @endcan

    <div class="tw-table-wrap">
        <table class="w-full text-left" id="all_user_table">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 w-10 no-sort">
                        <input type="checkbox" class="all-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Country')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Tax (%)')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_country_tax as $tax)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <input type="checkbox" class="bulk-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" name="bulk_delete[]" value="{{$tax->id}}">
                    </td>
                    <td class="px-4 py-3.5"><span class="text-[11px] font-bold text-primary">{{__('#')}} {{ $tax->id }}</span></td>
                    <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{ optional($tax->country)->name }}</span></td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-info-soft text-info text-xs font-bold">{{ $tax->tax_percentage }}%</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @can('country-tax-edit')
                                <button type="button"
                                   class="w-9 h-9 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:text-white hover:bg-primary hover:border-primary transition-all country_tax_edit_btn"
                                   data-id="{{$tax->id}}"
                                   data-country_id="{{$tax->country_id}}"
                                   data-tax_percentage="{{$tax->tax_percentage}}"
                                   title="{{__('Edit')}}">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </button>
                            @endcan
                            @can('country-tax-delete')
                                <button type="button"
                                        class="w-9 h-9 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:text-white hover:bg-danger hover:border-danger transition-all swal_delete_button"
                                        title="{{__('Delete')}}">
                                    <i class="mdi mdi-delete-outline text-sm"></i>
                                </button>
                                <form method="post" action="{{route('tenant.admin.tax.country.delete', $tax->id)}}" class="hidden delete-form">
                                    @csrf
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Edit Modal --}}
@can('country-tax-edit')
    <div id="country_tax_edit_modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
        <div id="country_tax_edit_backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-surface rounded-2xl shadow-2xl border border-main w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-main flex items-center justify-between">
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Update Country Tax')}}</h3>
                <button type="button" class="modal_close w-8 h-8 rounded-lg bg-secondary border border-main flex items-center justify-center text-muted hover:text-danger hover:border-danger transition">
                    <i class="mdi mdi-close text-base"></i>
                </button>
            </div>
            <form action="{{route('tenant.admin.tax.country.update')}}" method="post">
                @csrf
                <input type="hidden" name="id" id="country_tax_id">
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Country')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-earth text-lg text-primary"></i>
                            <select name="country_id" id="edit_country_id" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                @foreach ($all_countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Tax Percentage')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-percent text-lg text-primary"></i>
                            <input type="number" id="edit_tax_percentage" name="tax_percentage" placeholder="{{__('Tax Percentage')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-main flex items-center justify-end gap-3">
                    <button type="button" class="modal_close inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-muted transition">
                        {{__('Close')}}
                    </button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-6 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endcan

{{-- Add New Modal --}}
<div id="country_tax_new_modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
    <div id="country_tax_new_backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl border border-main w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Add Country Tax')}}</h3>
            <button type="button" class="modal_close w-8 h-8 rounded-lg bg-secondary border border-main flex items-center justify-center text-muted hover:text-danger hover:border-danger transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>
        <form action="{{route('tenant.admin.tax.country.new')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Country')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                        <i class="mdi mdi-earth text-lg text-primary"></i>
                        <select name="country_id" id="country_id" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            @foreach ($all_countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Tax Percentage')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-percent text-lg text-primary"></i>
                        <input type="number" name="tax_percentage" placeholder="{{__('Tax Percentage')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" step="0.01">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-main flex items-center justify-end gap-3">
                <button type="button" class="modal_close inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-muted transition">
                    {{__('Close')}}
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-6 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add New')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-table.btn.swal.js/>
    <x-bulk-action.js :route="route('tenant.admin.tax.country.bulk.action')"/>

    <script>
        (function ($) {
            "use strict";

            function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
            function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }

            $(document).on('click', '.modal_close, #country_tax_edit_backdrop, #country_tax_new_backdrop', function () {
                closeModal('country_tax_edit_modal');
                closeModal('country_tax_new_modal');
            });

            $(document).on('click', '.country_tax_new_btn', function () { openModal('country_tax_new_modal'); });

            $(document).on('click', '.country_tax_edit_btn', function () {
                var el = $(this);
                var modal = $('#country_tax_edit_modal');
                modal.find('#country_tax_id').val(el.data('id'));
                modal.find('#edit_country_id').val(el.data('country_id'));
                modal.find('#edit_tax_percentage').val(el.data('tax_percentage'));
                openModal('country_tax_edit_modal');
            });

            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                var btn = $(this);
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You will not be able to recover this!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{ __("Yes, Delete it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}',
                }).then(function (result) {
                    if (result.isConfirmed) { btn.closest('td').find('.delete-form').trigger('submit'); }
                });
            });
        })(jQuery);
    </script>
@endsection
