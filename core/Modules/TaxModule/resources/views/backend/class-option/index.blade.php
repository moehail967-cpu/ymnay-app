@extends("tenant.admin.admin-master")

@section("title", __("Tax Class Options"))
@section('style')
    <style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection
@section("content")

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-tune-vertical text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Tax Class Options')}}</h3>
                <p class="text-xs text-muted">{{__('Configure tax rates for this class')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="add-tax-option inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                <i class="mdi mdi-plus text-base"></i> {{__('Add')}}
            </button>
            <button type="button" class="remove-tax-option inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-danger-soft border border-main text-danger text-sm font-semibold hover:bg-danger hover:text-white hover:border-danger transition whitespace-nowrap">
                <i class="mdi mdi-delete-outline text-base"></i> {{__('Delete')}}
            </button>
            <button type="button" class="store-tax-option inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-success-soft border border-main text-success text-sm font-semibold hover:bg-success hover:text-white hover:border-success transition whitespace-nowrap">
                <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update')}}
            </button>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-3 border-b border-main">
        <div class="flex items-start gap-2 text-xs text-muted">
            <i class="mdi mdi-information-outline text-info text-sm mt-0.5 flex-shrink-0"></i>
            <div>
                <p>{{ __("The tax will be applied to all countries if you do not select any.") }}</p>
                <p>{{ __('The "Name" and "Priority" fields are required. If the name is not provided, that row will not be stored.') }}</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <form id="tax-class-option-form" action="{{ route('tenant.admin.tax-module.tax-class-option', $taxClass->id) }}" method="post">
            @csrf
            <table class="w-full text-left min-w-[900px]" id="tax-option-table">
                <thead>
                    <tr class="border-b border-main">
                        <th class="px-3 py-3 w-10">
                            <input type="checkbox" id="select-all-text-class-option" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                        </th>
                        <th class="px-3 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">* {{__('Name')}}</th>
                        <th class="px-3 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Country')}}</th>
                        <th class="px-3 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('State')}}</th>
                        <th class="px-3 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('City')}}</th>
                        <th class="px-3 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Postal Code')}}</th>
                        <th class="px-3 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Rate (%)')}}</th>
                        <th class="px-3 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Shipping')}}</th>
                        <th class="px-3 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">* {{__('Priority')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($taxClass->classOption as $classOption)
                        <x-taxmodule::tax-class-option-row :$countries :$classOption />
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
</div>

@endsection

@section("scripts")
    <script>
        (function ($) {
            "use strict";

            $(document).on("click", ".store-tax-option", function () {
                $("#tax-class-option-form").trigger("submit");
            });

            $(document).on("click", "#select-all-text-class-option", function () {
                var isSelected = $(this).is(":checked");
                $(".tax-option-row-check").each(function () {
                    $(this).prop("checked", isSelected);
                });
            });

            $(document).on("change", "#country_id", function () {
                var el = $(this);
                var country_id = el.val();
                $.get("{{ route('tenant.admin.tax-module.country.state.info.ajax') }}?id=" + country_id, function (data) {
                    el.closest('tr').find("#state_id").html(data);
                });
            });

            $(document).on("change", "#state_id", function () {
                var el = $(this);
                var state_id = el.val();
                $.get("{{ route('tenant.admin.tax-module.state.city.info.ajax') }}?id=" + state_id, function (data) {
                    el.closest('tr').find("#city_id").html(data);
                });
            });

            $(document).on("click", ".add-tax-option", function () {
                var tr = `<x-taxmodule::tax-class-option-row :$countries />`;
                $('#tax-option-table tbody').append(tr);
            });

            $(document).on("click", ".remove-tax-option", function () {
                $(".tax-option-row-check:checked").each(function () {
                    $(this).closest('tr').remove();
                });
            });
        })(jQuery);
    </script>
@endsection
