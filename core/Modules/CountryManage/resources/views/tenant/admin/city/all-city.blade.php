@extends('tenant.admin.admin-master')
@section('title') {{__('All Cities')}} @endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/select2.min.css')}}">
    <style>.hover\:text-white:hover{color:#fff!important}</style>

@endsection

@section('content')

    <x-landlord-error-msg/>
    <x-landlord-flash-msg/>

    <div class="bg-surface rounded-xl shadow-main overflow-hidden mb-6">

        {{-- Card Header --}}
        <div class="px-5 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-city text-warning text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Cities')}}</h3>
                    <p class="text-xs text-muted">{{__('Manage cities within your states')}}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <select id="bulk_action_select"
                        class="text-xs border border-main rounded-lg px-3 py-2 bg-secondary text-dark outline-none focus:border-primary transition hidden"
                        style="display:none;">
                    <option value="">{{__('Bulk Action')}}</option>
                    <option value="delete">{{__('Delete')}}</option>
                </select>
                <button type="button" id="bulk_action_btn"
                        class="hidden items-center gap-1.5 px-3 py-2 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition-all"
                        style="display:none;">
                    <i class="mdi mdi-delete-sweep text-sm"></i> {{__('Apply')}}
                </button>

                {{-- Search --}}
                <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2 focus-within:border-primary transition">
                    <i class="mdi mdi-magnify text-muted text-base"></i>
                    <input type="text" id="string_search" placeholder="{{__('Search cities...')}}"
                           class="bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 w-32 sm:w-44">
                </div>

                <button type="button" onclick="openModal('addModal')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                    <i class="mdi mdi-plus text-base"></i>
                    {{__('Add City')}}
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="search_result">
            @include('countrymanage::tenant.admin.city.search-result')
        </div>
    </div>

    @include('countrymanage::tenant.admin.city.add-modal')
    @include('countrymanage::tenant.admin.city.edit-modal')

@endsection

@section('scripts')
    <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>
    <x-bulk-action.js :route="route('tenant.admin.city.delete.bulk.action')"/>
    @include('countrymanage::tenant.admin.city.city-js')

    <script>
    (function ($) {
        "use strict";

        function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
        function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
        window.openModal = openModal;
        window.closeModal = closeModal;

        // Swal status change
        $(document).on('click', '.swal_status_change_button', function (e) {
            e.preventDefault();
            var $btn = $(this);
            Swal.fire({
                title: '{{__("Are you sure?")}}',
                text: '{{__("You would change status any time")}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1a5c4e',
                cancelButtonColor: '#ef4444',
                confirmButtonText: '{{__("Yes, Change it!")}}'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $btn.next().find('.swal_form_submit_btn').trigger('click');
                }
            });
        });

        // Bulk check all
        $(document).on('change', '#check_all_city', function () {
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
                        url: '{{route("tenant.admin.city.delete.bulk.action")}}',
                        data: { _token: '{{csrf_token()}}', ids: ids, action: action },
                        success: function () { location.reload(); }
                    });
                }
            });
        });

    })(jQuery);
    </script>
@endsection
