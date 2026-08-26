@extends('tenant.admin.admin-master')
@section('title') {{__('Shipping Zones')}} @endsection

@section('content')

    <x-landlord-error-msg/>
    <x-landlord-flash-msg/>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left: Zones List --}}
        <div class="lg:col-span-7">
            <div class="bg-surface rounded-xl shadow-main overflow-hidden">

                {{-- Card Header --}}
                <div class="px-5 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-map-marker-radius text-primary text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Shipping Zones')}}</h3>
                            <p class="text-xs text-muted">{{__('Manage shipping zones and regions')}}</p>
                        </div>
                    </div>
                    @can('shipping-zone-delete')
                    <div class="flex items-center gap-2">
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
                    </div>
                    @endcan
                </div>

                {{-- Table --}}
                <div class="tw-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                @can('shipping-zone-delete')
                                <th class="w-10">
                                    <input type="checkbox" id="check_all_zone"
                                           class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/20">
                                </th>
                                @endcan
                                <th>{{__('#')}}</th>
                                <th>{{__('Name')}}</th>
                                <th class="text-right">{{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($all_zones as $zone)
                            <tr>
                                @can('shipping-zone-delete')
                                <td>
                                    <input type="checkbox" class="bulk-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/20"
                                           value="{{$zone->id}}">
                                </td>
                                @endcan
                                <td>
                                    <span class="text-xs text-muted font-medium">{{$loop->iteration}}</span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                                            <i class="mdi mdi-map-marker text-primary text-xs"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-dark">{{$zone->name}}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1.5">
                                        @can('shipping-zone-edit')
                                        <button type="button"
                                                class="shipping_zone_edit_btn w-8 h-8 rounded-lg bg-primary-soft text-primary flex items-center justify-center hover:bg-primary hover:text-white transition"
                                                data-id="{{$zone->id}}"
                                                data-name="{{$zone->name}}"
                                                data-country="{{optional($zone->region)->country}}"
                                                data-state="{{optional($zone->region)->state}}"
                                                title="{{__('Edit')}}">
                                            <i class="mdi mdi-pencil-outline text-sm"></i>
                                        </button>
                                        @endcan
                                        @can('shipping-zone-delete')
                                        <button type="button"
                                                class="swal-delete w-8 h-8 rounded-lg bg-danger-soft text-danger flex items-center justify-center hover:bg-danger hover:text-white transition"
                                                data-route="{{route('tenant.admin.shipping.zone.delete', $zone->id)}}"
                                                title="{{__('Delete')}}">
                                            <i class="mdi mdi-delete-outline text-sm"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <div class="flex flex-col items-center gap-2 py-8">
                                        <i class="mdi mdi-map-marker-off text-3xl text-muted"></i>
                                        <p class="text-sm text-muted">{{__('No shipping zones found')}}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: Add New Zone --}}
        @can('shipping-zone-create')
        <div class="lg:col-span-5">
            <div class="bg-surface rounded-xl shadow-main overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-main">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-plus-circle text-success text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Add New Zone')}}</h3>
                            <p class="text-xs text-muted">{{__('Create a new shipping zone')}}</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <form action="{{route('tenant.admin.shipping.zone.new')}}" method="post">
                        @csrf
                        <div class="space-y-4">

                            {{-- Zone Name --}}
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Zone Name')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                    <i class="mdi mdi-tag-text text-lg text-primary"></i>
                                    <input type="text" name="name" placeholder="{{__('Zone Name')}}"
                                           class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                                </div>
                            </div>

                            {{-- Country --}}
                            <x-fields.tw-multiselect
                                name="country"
                                id="add_country"
                                :options="$all_countries"
                                :selected="[]"
                                :placeholder="__('Select countries…')"
                                :label="__('Country')"
                            />
                            <p class="text-[11px] -mt-2 flex items-center gap-1" style="color:var(--color-warning,#f59e0b)">
                                <i class="mdi mdi-information-outline text-sm"></i>
                                {{__('First select all the desired countries.')}}
                            </p>

                            {{-- State --}}
                            <x-fields.tw-multiselect
                                name="state"
                                id="add_state"
                                :options="[]"
                                :selected="[]"
                                :placeholder="__('Select states…')"
                                :label="__('State')"
                            />

                        </div>

                        <button type="submit"
                                class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-plus text-base"></i> {{__('Add New Zone')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endcan
    </div>

    {{-- Edit Zone Modal --}}
    @can('shipping-zone-edit')
    <div id="editZoneModal" class="fixed inset-0 z-[800] hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('editZoneModal')"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md pointer-events-auto">
                <div class="px-5 py-4 border-b border-main flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center">
                            <i class="mdi mdi-pencil text-primary text-sm"></i>
                        </div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Update Shipping Zone')}}</h3>
                    </div>
                    <button onclick="closeModal('editZoneModal')" class="w-8 h-8 rounded-lg hover:bg-secondary flex items-center justify-center transition">
                        <i class="mdi mdi-close text-muted"></i>
                    </button>
                </div>
                <form action="{{route('tenant.admin.shipping.zone.update')}}" method="post">
                    @csrf
                    <input type="hidden" name="id" id="shipping_zone_id">
                    <div class="p-5 space-y-4">

                        {{-- Name --}}
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-tag-text text-lg text-primary"></i>
                                <input type="text" name="name" id="edit_name" placeholder="{{__('Name')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        {{-- Country --}}
                        <x-fields.tw-multiselect
                            name="country"
                            id="edit_country"
                            :options="$all_countries"
                            :selected="[]"
                            :placeholder="__('Select countries…')"
                            :label="__('Country')"
                        />
                        <p class="text-[11px] -mt-2 flex items-center gap-1" style="color:var(--color-warning,#f59e0b)">
                            <i class="mdi mdi-information-outline text-sm"></i>
                            {{__('First select all the desired countries.')}}
                        </p>

                        {{-- State --}}
                        <x-fields.tw-multiselect
                            name="state"
                            id="edit_state"
                            :options="[]"
                            :selected="[]"
                            :placeholder="__('Select states…')"
                            :label="__('State')"
                        />

                    </div>
                    <div class="px-5 py-4 border-t border-main flex items-center justify-end gap-2">
                        <button type="button" onclick="closeModal('editZoneModal')"
                                class="px-4 py-2 rounded-xl bg-secondary border border-main text-sm font-semibold text-dark hover:bg-muted transition">
                            {{__('Close')}}
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-check text-base"></i> {{__('Save Changes')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

@endsection

@section('scripts')
<script>
(function ($) {
    "use strict";

    var stateRoute      = '{{ route("tenant.admin.state.by.multiple.country") }}';
    var csrfToken       = '{{ csrf_token() }}';

    function openModal(id)  { document.getElementById(id)?.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
    function closeModal(id) { document.getElementById(id)?.classList.add('hidden');    document.body.classList.remove('overflow-hidden'); }
    window.openModal  = openModal;
    window.closeModal = closeModal;

    /* ── helpers ──────────────────────────────────────────────────── */
    function getSelected(selectId) {
        return Array.from(document.getElementById(selectId).selectedOptions).map(function (o) { return o.value; });
    }

    function loadStates(countryIds, targetSelectId, preSelectedIds) {
        var sel = document.getElementById(targetSelectId);
        if (!sel) return;
        sel.innerHTML = '';
        sel.dispatchEvent(new Event('tw-ms:refresh')); // clear chips
        if (!countryIds || !countryIds.length) return;

        $.post(stateRoute, { id: countryIds, _token: csrfToken }).then(function (data) {
            var preStr = (preSelectedIds || []).map(String);
            data.forEach(function (state) {
                var opt = document.createElement('option');
                opt.value    = state.id;
                opt.text     = state.name;
                opt.selected = preStr.includes(String(state.id));
                sel.appendChild(opt);
                // MutationObserver fires per appended option and reads opt.selected
            });
        });
    }

    function preSelectCountries(selectId, ids) {
        var sel = document.getElementById(selectId);
        if (!sel) return;
        var idsStr = (ids || []).map(String);
        Array.from(sel.options).forEach(function (o) {
            o.selected = idsStr.includes(o.value);
        });
        sel.dispatchEvent(new Event('tw-ms:refresh'));
    }

    $(document).ready(function () {

        /* ── Add form: country → state cascade ─────────────────────── */
        document.getElementById('add_country').addEventListener('change', function () {
            loadStates(getSelected('add_country'), 'add_state', []);
        });

        /* ── Edit button click ──────────────────────────────────────── */
        $(document).on('click', '.shipping_zone_edit_btn', function () {
            var el          = $(this);
            var id          = el.data('id');
            var name        = el.data('name');
            var countryRaw  = el.data('country');
            var stateRaw    = el.data('state');

            var countryArr = countryRaw ? (typeof countryRaw === 'string' ? JSON.parse(countryRaw) : countryRaw) : [];
            var stateArr   = stateRaw   ? (typeof stateRaw   === 'string' ? JSON.parse(stateRaw)   : stateRaw)   : [];

            $('#shipping_zone_id').val(id);
            $('#edit_name').val(name);

            preSelectCountries('edit_country', countryArr);
            loadStates(countryArr, 'edit_state', stateArr);

            openModal('editZoneModal');
        });

        /* ── Edit modal: country → state cascade ────────────────────── */
        document.getElementById('edit_country').addEventListener('change', function () {
            loadStates(getSelected('edit_country'), 'edit_state', []);
        });

        /* ── Swal delete ────────────────────────────────────────────── */
        $(document).on('click', '.swal-delete', function () {
            var $btn = $(this);
            Swal.fire({
                title: "{{ __('Do you want to delete this item?') }}",
                text: '{{__("You would not be able to revert this item!")}}',
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: '{{__("Delete")}}',
                confirmButtonColor: '#dd3333',
                cancelButtonText: "{{__('Cancel')}}",
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.post($btn.data('route'), { _token: csrfToken }).then(function (data) {
                        if (data.type === 'success' || data.success === true) {
                            Swal.fire('{{__("Deleted!")}}', data.msg, 'success');
                            setTimeout(function () { location.reload(); }, 1000);
                        } else {
                            Swal.fire('{{__("Error!")}}', data.msg ?? '{{__("Something went wrong.")}}', 'error');
                        }
                    }).catch(function () {
                        Swal.fire('{{__("Error!")}}', '{{__("An error occurred while deleting.")}}', 'error');
                    });
                }
            });
        });

        /* ── Bulk actions ───────────────────────────────────────────── */
        $(document).on('change', '#check_all_zone', function () {
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
            if (!ids.length) return;
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
                        url: '{{route("tenant.admin.shipping.zone.bulk.action")}}',
                        data: { _token: csrfToken, ids: ids, action: action },
                        success: function () { location.reload(); }
                    });
                }
            });
        });

    });
})(jQuery);
</script>
@endsection
