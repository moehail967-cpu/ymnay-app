@extends('tenant.admin.admin-master')
@section('title') {{__('Country')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

    <x-landlord-error-msg/>
    <x-landlord-flash-msg/>

    <div class="bg-surface rounded-xl shadow-main overflow-hidden mb-6">

        {{-- Card Header --}}
        <div class="px-5 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-earth text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Countries')}}</h3>
                    <p class="text-xs text-muted">{{__('Manage countries for your store')}}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @can('country-delete')
                    <select id="bulk_action_select"
                            class="text-xs border border-main rounded-lg px-3 py-2 bg-secondary text-dark outline-none focus:border-primary transition hidden"
                            style="display:none;">
                        <option value="">{{__('Bulk Action')}}</option>
                        <option value="delete">{{__('Delete')}}</option>
                    </select>
                    <button type="button" id="bulk_action_btn"
                            class="hidden items-center gap-1.5 px-3 py-2 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition-all"
                            style="display:none;">
                        <i class="mdi mdi-delete-sweep text-sm"></i>
                        {{__('Apply')}}
                    </button>
                @endcan
                @can('country-create')
                    <button type="button" onclick="openModal('country_new_modal')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                        <i class="mdi mdi-plus text-base"></i>
                        {{__('Add Country')}}
                    </button>
                @endcan
            </div>
        </div>

        {{-- Table --}}
        <div class="tw-table-wrap">
            <table class="w-full text-left border-collapse" id="countryTable">
                <thead>
                    <tr class="border-b border-main">
                        @can('country-delete')
                            <th class="px-5 py-3 w-10 no-sort">
                                <input type="checkbox" id="check_all_country"
                                       class="w-4 h-4 rounded border-main text-primary focus:ring-primary cursor-pointer">
                            </th>
                        @endcan
                        <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Name')}}</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                        <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($all_countries as $country)
                    <tr class="border-b border-main hover:bg-muted/40 transition-colors">
                        @can('country-delete')
                            <td class="px-5 py-3.5">
                                <input type="checkbox" name="ids[]" value="{{$country->id}}"
                                       class="bulk-checkbox w-4 h-4 rounded border-main text-primary focus:ring-primary cursor-pointer">
                            </td>
                        @endcan
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-bold text-primary">#{{$loop->iteration}}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                                    <i class="mdi mdi-flag text-primary text-sm"></i>
                                </div>
                                <span class="text-sm font-semibold text-dark">{{$country->name}}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($country->status === 'publish')
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-xs font-semibold text-dark">{{__('Publish')}}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span class="text-xs font-semibold text-dark">{{__('Draft')}}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                @can('country-edit')
                                    <button type="button"
                                            title="{{__('Edit')}}"
                                            class="country_edit_btn w-8 h-8 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:bg-primary hover:text-white hover:border-primary transition-all"
                                            data-id="{{$country->id}}"
                                            data-name="{{$country->name}}"
                                            data-status="{{$country->status}}">
                                        <i class="mdi mdi-pencil-outline text-sm"></i>
                                    </button>
                                @endcan
                                @can('country-delete')
                                    <button type="button" title="{{__('Delete')}}"
                                            class="swal_delete_button w-8 h-8 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:bg-danger hover:text-white hover:border-danger transition-all">
                                        <i class="mdi mdi-delete-outline text-sm"></i>
                                    </button>
                                    <form method="post" action="{{route('tenant.admin.country.delete', $country->id)}}" class="hidden">
                                        @csrf
                                        <button type="submit" class="swal_form_submit_btn"></button>
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
    @can('country-edit')
        <div id="country_edit_modal" class="fixed inset-0 z-[800] hidden">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('country_edit_modal')"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
                <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md pointer-events-auto">
                    <div class="px-6 py-4 border-b border-main flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center">
                                <i class="mdi mdi-earth text-primary text-sm"></i>
                            </div>
                            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Update Country')}}</h3>
                        </div>
                        <button type="button" onclick="closeModal('country_edit_modal')"
                                class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center text-muted hover:text-dark transition">
                            <i class="mdi mdi-close text-base"></i>
                        </button>
                    </div>
                    <form action="{{route('tenant.admin.country.update')}}" method="post">
                        @csrf
                        <input type="hidden" name="id" id="country_id">
                        <div class="px-6 py-5 space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                    <i class="mdi mdi-flag-outline text-lg text-primary"></i>
                                    <input type="text" id="edit_name" name="name" placeholder="{{__('Country name')}}"
                                           class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                    <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                                    <select name="status" id="edit_status"
                                            class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                        <option value="publish">{{__('Publish')}}</option>
                                        <option value="draft">{{__('Draft')}}</option>
                                    </select>
                                    <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-main flex justify-end gap-2">
                            <button type="button" onclick="closeModal('country_edit_modal')"
                                    class="px-4 py-2 rounded-xl bg-secondary border border-main text-sm font-semibold text-dark hover:bg-muted transition">
                                {{__('Cancel')}}
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                                <i class="mdi mdi-check text-base"></i>
                                {{__('Save Changes')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- Create Modal --}}
    @can('country-create')
        <div id="country_new_modal" class="fixed inset-0 z-[800] hidden">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('country_new_modal')"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
                <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md pointer-events-auto">
                    <div class="px-6 py-4 border-b border-main flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-success-soft flex items-center justify-center">
                                <i class="mdi mdi-earth-plus text-success text-sm"></i>
                            </div>
                            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Add New Country')}}</h3>
                        </div>
                        <button type="button" onclick="closeModal('country_new_modal')"
                                class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center text-muted hover:text-dark transition">
                            <i class="mdi mdi-close text-base"></i>
                        </button>
                    </div>
                    <form action="{{route('tenant.admin.country.new')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="px-6 py-5 space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Name')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                    <i class="mdi mdi-flag-outline text-lg text-primary"></i>
                                    <input type="text" name="name" placeholder="{{__('Country name')}}"
                                           class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                    <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                                    <select name="status"
                                            class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                        <option value="publish">{{__('Publish')}}</option>
                                        <option value="draft">{{__('Draft')}}</option>
                                    </select>
                                    <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-main flex justify-end gap-2">
                            <button type="button" onclick="closeModal('country_new_modal')"
                                    class="px-4 py-2 rounded-xl bg-secondary border border-main text-sm font-semibold text-dark hover:bg-muted transition">
                                {{__('Cancel')}}
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                                <i class="mdi mdi-plus text-base"></i>
                                {{__('Add Country')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-table.btn.swal.js/>
    <x-bulk-action.js :route="route('tenant.admin.country.bulk.action')"/>

    <script>
    (function ($) {
        "use strict";

        function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
        function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
        window.openModal = openModal;
        window.closeModal = closeModal;

        $(document).ready(function () {
            $('#countryTable').DataTable();

            // Edit button
            $(document).on('click', '.country_edit_btn', function () {
                var el = $(this);
                $('#country_id').val(el.data('id'));
                $('#edit_name').val(el.data('name'));
                $('#edit_status').val(el.data('status'));
                openModal('country_edit_modal');
            });

            // Check all
            $('#check_all_country').on('change', function () {
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

            // Bulk action
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
                            url: '{{route("tenant.admin.country.bulk.action")}}',
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
