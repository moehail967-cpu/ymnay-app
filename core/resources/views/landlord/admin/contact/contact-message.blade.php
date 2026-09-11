@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Contact Message')}} @endsection

@section('style')
    <x-datatable.tw-css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php
    $totalMessages = count($all_contact_messages);
    $newMessages   = $all_contact_messages->where('status', 1)->count();
    $readMessages  = $totalMessages - $newMessages;
@endphp

{{-- Stats Strip --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mb-5">
    <div class="bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-email-multiple-outline text-primary text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Total Messages')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$totalMessages}}</p>
        </div>
    </div>
    <div class="bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-email-alert-outline text-success text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('New')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$newMessages}}</p>
        </div>
    </div>
    <div class="bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-[#f3f4f6] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-email-open-outline text-muted text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Read')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$readMessages}}</p>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

    {{-- Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main flex flex-wrap items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-message-text-outline text-primary text-base"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Contact Messages')}}</h3>
            <p class="text-xs text-muted">{{__('View and manage form submissions')}}</p>
        </div>
    </div>

    {{-- Bulk Action Bar --}}
    <div class="hidden px-4 sm:px-6 py-3 bg-secondary border-b border-main items-center gap-3" id="bulk-action-bar">
        <div class="flex items-center gap-2 flex-1">
            <input type="checkbox" id="bulk-select-all"
                   class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary accent-[var(--color-primary)]">
            <span class="text-xs font-semibold text-dark"><span id="bulk-count">0</span> {{__('selected')}}</span>
        </div>
        <button type="button" id="bulk-delete-btn"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-danger-soft text-danger text-xs font-semibold hover:bg-danger hover:text-white transition">
            <i class="mdi mdi-delete-outline text-sm"></i> {{__('Delete Selected')}}
        </button>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="contactMessagesTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 no-sort" style="width:40px;">
                        <input type="checkbox" class="all-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary accent-[var(--color-primary)]">
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Form Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Data')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($all_contact_messages as $data)
                @php
                    $decoded = json_decode($data->fields, true);
                    $fields = is_array($decoded) ? $decoded : [];
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    {{-- Checkbox --}}
                    <td class="px-4 py-3">
                        <input type="checkbox" name="ids[]" value="{{$data->id}}"
                               class="bulk-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary accent-[var(--color-primary)]">
                    </td>

                    {{-- ID --}}
                    <td class="px-4 py-3">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}}{{$data->id}}</span>
                    </td>

                    {{-- Form Name --}}
                    <td class="px-4 py-3">
                        <span class="text-sm font-semibold text-dark">{{optional($data->form)->title ?? '—'}}</span>
                    </td>

                    {{-- Data (truncated) --}}
                    <td class="px-4 py-3" style="max-width:320px;">
                        <div class="space-y-0.5">
                            @foreach(array_slice($fields, 0, 3) as $k => $vl)
                                <p class="text-xs text-muted truncate">
                                    <span class="font-semibold text-dark">{{ucfirst($k)}}:</span> {{Str::words($vl, 12)}}
                                </p>
                            @endforeach
                            @if(count($fields) > 3)
                                <p class="text-[10px] text-primary font-semibold">+{{count($fields) - 3}} {{__('more fields')}}</p>
                            @endif
                        </div>
                    </td>

                    {{-- Date --}}
                    <td class="px-4 py-3">
                        <span class="text-xs text-muted">{{date('d M, Y', strtotime($data->created_at))}}</span>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3">
                        @if($data->status == 1)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                <i class="mdi mdi-circle-medium text-xs"></i> {{__('New')}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#f3f4f6] text-muted text-[10px] font-bold uppercase">
                                <i class="mdi mdi-circle-medium text-xs"></i> {{__('Read')}}
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3">
                        <div class="row-action-wrap">
                            <a href="{{route(route_prefix().'admin.contact.message.view', $data->id)}}"
                               title="{{__('View')}}"
                               class="w-8 h-8 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all">
                                <i class="mdi mdi-eye-outline text-sm"></i>
                            </a>

                            <button type="button" onclick="toggleRowMenu(this)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                <i class="mdi mdi-dots-vertical text-sm"></i>
                            </button>

                            <div class="row-action-menu hidden">
                                <a href="{{route(route_prefix().'admin.contact.message.view', $data->id)}}">
                                    <span class="action-icon bg-primary-soft"><i class="mdi mdi-eye-outline text-primary"></i></span>
                                    {{__('View Details')}}
                                </a>
                                <div class="menu-divider"></div>
                                <button type="button" class="action-item action-danger swal_delete_button"
                                        data-route="{{route(route_prefix().'admin.contact.message.delete', $data->id)}}">
                                    <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                    {{__('Delete')}}
                                </button>
                            </div>
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
    <x-bulk-action-js :url="route(route_prefix().'admin.contact.message.bulk.action')"/>

    <script>
    (function ($) {
        "use strict";

        // ── Row action dropdown ──────────────────────────────────────
        window.toggleRowMenu = function (btn) {
            var menu = btn.nextElementSibling;
            var isHidden = menu.classList.contains('hidden');

            document.querySelectorAll('.row-action-menu').forEach(function (m) {
                m.classList.add('hidden');
            });

            if (isHidden) {
                var rect = btn.getBoundingClientRect();
                var menuHeight = 320;
                var spaceBelow = window.innerHeight - rect.bottom;
                var spaceAbove = rect.top;

                menu.style.right = (window.innerWidth - rect.right) + 'px';
                menu.style.left = 'auto';

                if (spaceBelow >= Math.min(menuHeight, 200) || spaceBelow >= spaceAbove) {
                    menu.style.top = (rect.bottom + 4) + 'px';
                    menu.style.bottom = 'auto';
                } else {
                    menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                    menu.style.top = 'auto';
                }

                menu.classList.remove('hidden');
            }
        };

        // close on outside click
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.row-action-wrap')) {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            }
        });

        // close on page scroll
        window.addEventListener('scroll', function (e) {
            if (e.target && e.target.closest && e.target.closest('.row-action-menu')) return;
            document.querySelectorAll('.row-action-menu').forEach(function (m) {
                m.classList.add('hidden');
            });
        }, true);

        // close on .tw-table-wrap horizontal scroll
        document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
            wrap.addEventListener('scroll', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            });
        });

        $(document).ready(function () {

            // ── DataTable init ────────────────────────────────────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#contactMessagesTable')) {
                $('#contactMessagesTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── AJAX Delete (controller returns JSON) ─────────────────
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });

                var btn   = $(this);
                var route = btn.data('route');
                var row   = btn.closest('tr');

                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("This action cannot be undone!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, delete it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: route,
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (res) {
                                if (res.type === 'danger' || res.type === 'success') {
                                    toastr.success(res.msg || '{{ __("Deleted successfully") }}');
                                    row.fadeOut(300, function () { $(this).remove(); });
                                } else {
                                    toastr.error(res.msg || '{{ __("Something went wrong") }}');
                                }
                            },
                            error: function () {
                                toastr.error('{{ __("Something went wrong") }}');
                            }
                        });
                    }
                });
            });

            // ── Bulk action checkbox logic ────────────────────────────
            var $bar       = $('#bulk-action-bar');
            var $selectAll = $('#bulk-select-all');
            var $count     = $('#bulk-count');

            function updateBar() {
                var checked = $('.bulk-checkbox:checked').length;
                $count.text(checked);
                if (checked > 0) { $bar.removeClass('hidden').addClass('flex'); }
                else { $bar.addClass('hidden').removeClass('flex'); }
            }

            $('.all-checkbox').on('change', function () {
                var isChecked = $(this).is(':checked');
                $('.bulk-checkbox').prop('checked', isChecked);
                $selectAll.prop('checked', isChecked);
                updateBar();
            });

            $selectAll.on('change', function () {
                var isChecked = $(this).is(':checked');
                $('.bulk-checkbox').prop('checked', isChecked);
                $('.all-checkbox').prop('checked', isChecked);
                updateBar();
            });

            $(document).on('change', '.bulk-checkbox', function () {
                var total   = $('.bulk-checkbox').length;
                var checked = $('.bulk-checkbox:checked').length;
                $('.all-checkbox').prop('checked', total === checked);
                $selectAll.prop('checked', total === checked);
                updateBar();
            });

        });
    })(jQuery);
    </script>
@endsection
