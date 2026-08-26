@extends('tenant.admin.admin-master')

@section('title') {{__('All Campaign')}} @endsection

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
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-bullhorn-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Campaigns')}}</h3>
                <p class="text-xs text-muted">{{__('Manage your product campaigns')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @can('campaign-delete')
                <x-bulk-action permissions="campaign-delete"/>
            @endcan
            @can('campaign-create')
                <a href="{{ route('tenant.admin.campaign.new') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('New Campaign')}}
                </a>
            @endcan
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="campaignTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-10 no-sort">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14 no-sort">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Campaign')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Duration')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__('Products')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__('Sold')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_campaigns as $campaign)
                @php
                    $computed = $campaign->computed_status;
                    $total_sold = $campaign->products->sum('sold_count');
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <x-bulk-delete-checkbox :id="$campaign->id"/>
                    </td>

                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">#{{$campaign->id}}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="campaign-table-img flex-shrink-0">
                                {!! render_image_markup_by_attachment_id($campaign?->campaignImage?->id, 'w-full h-full object-cover rounded-lg') !!}
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-dark block">{{$campaign->title}}</span>
                                @if($campaign->discount_value > 0)
                                    <span class="text-[10px] text-muted">
                                        {{ $campaign->discount_type === 'percentage' ? $campaign->discount_value.'%' : amount_with_currency_symbol($campaign->discount_value) }}
                                        {{ __('off') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3.5">
                        <span class="text-xs text-dark block">{{ $campaign->start_date?->format('M d, Y') ?? '—' }}</span>
                        <span class="text-[10px] text-muted">→ {{ $campaign->end_date?->format('M d, Y') ?? '—' }}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        @php
                            $statusClass = match($computed) {
                                'active'   => 'tw-pill-success',
                                'upcoming' => 'tw-pill-info',
                                'ended'    => 'tw-pill-muted',
                                default    => 'tw-pill-warning',
                            };
                            $statusLabel = match($computed) {
                                'active'   => __('Active'),
                                'upcoming' => __('Upcoming'),
                                'ended'    => __('Ended'),
                                default    => __('Draft'),
                            };
                        @endphp
                        <span class="tw-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>

                    <td class="px-4 py-3.5 text-right">
                        <span class="text-sm font-semibold text-dark">{{ $campaign->products->count() }}</span>
                    </td>

                    <td class="px-4 py-3.5 text-right">
                        <span class="text-sm font-semibold text-dark">{{ $total_sold }}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('frontend.products.campaign', ['id' => $campaign->id, 'slug' => \Str::slug($campaign->title)]) }}"
                               target="_blank" class="tw-btn-icon tw-btn-icon-view" title="{{__('Preview')}}">
                                <i class="mdi mdi-eye-outline"></i>
                            </a>
                            @can('campaign-edit')
                                <a href="{{ route('tenant.admin.campaign.edit', $campaign->id) }}"
                                   class="tw-btn-icon tw-btn-icon-edit" title="{{__('Edit')}}">
                                    <i class="mdi mdi-pencil-outline"></i>
                                </a>
                            @endcan
                            @if($campaign->id != 1)
                                @can('campaign-delete')
                                    <button type="button" class="tw-btn-icon tw-btn-icon-danger swal_delete_button"
                                            data-route="{{ route('tenant.admin.campaign.delete', $campaign->id) }}"
                                            title="{{__('Delete')}}">
                                        <i class="mdi mdi-delete-outline"></i>
                                    </button>
                                @endcan
                            @endif
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
    <x-bulk-action-js :url="route('tenant.admin.campaign.bulk.action')"/>
    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#campaignTable')) {
                $('#campaignTable').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

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
