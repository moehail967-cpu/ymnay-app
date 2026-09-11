@extends(route_prefix().'admin.admin-master')

@section('title')
    {{tenant() ? __('Pending Domain List - Domain Reseller Plugin') : __('Failed Domain List - Domain Reseller Plugin')}}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-exclamation-triangle text-danger text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{tenant() ? __('Pending Domains') : __('Failed Domains')}}</h3>
                <p class="text-xs text-muted">{{__('All your purchased domains are listed below')}}</p>
            </div>
        </div>
        <a href="{{route(route_prefix().'admin.domain-reseller.list.domain')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-sidebar-hover transition">
            <i class="las la-arrow-left"></i> {{__('Back')}}
        </a>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <div class="tw-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{__('#')}}</th>
                        <th>{{__('Tenant')}}</th>
                        <th>{{__('Email')}}</th>
                        <th>{{__('Domain')}}</th>
                        <th>{{__('Period')}}</th>
                        <th>{{__('Price')}}</th>
                        <th>{{__('Payment Status')}}</th>
                        <th>{{__('Domain Status')}}</th>
                        <th>{{__('Type')}}</th>
                        <th>{{__('Created At')}}</th>
                        @if(!tenant())
                            <th>{{__('Action')}}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($domain_list ?? [] as $index => $item)
                        @php
                            $getText = '\Modules\DomainReseller\Http\Enums\StatusEnum::getText';
                        @endphp
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$item->paymentable_tenant?->id}}</td>
                            <td>{{$item->email}}</td>
                            <td><span class="font-semibold text-dark">{{$item->domain}}</span></td>
                            <td>{{$item->period}} {{__('Year')}}</td>
                            <td>{{amount_with_currency_symbol($item->domain_price + $item->extra_fee)}}</td>
                            <td>
                                <span class="tw-pill {{$item->payment_status ? 'tw-pill-success' : 'tw-pill-danger'}}">
                                    {{$getText($item->payment_status, true)}}
                                </span>
                            </td>
                            <td>
                                <span class="tw-pill tw-pill-danger">{{$getText($item->status)}}</span>
                            </td>
                            <td>
                                <span class="text-xs text-muted">{{$item->purchase_count > 1 ? __('Renew') : __('New Purchase')}}</span>
                            </td>
                            <td>{{$item->updated_at->format('d-M-Y')}}</td>
                            @if(!tenant())
                                <td>
                                    <a href="{{route('landlord.admin.domain-reseller.list.domain.failed.complete', wrap_random_number($item->id))}}"
                                       class="re-purchase-btn inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-info text-white text-xs font-semibold hover:opacity-90 transition">
                                        {{__('Complete Action')}}
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-8">{{__('No data available')}}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        (function ($) {
            "use strict";

            $(document).on('click', '.re-purchase-btn', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: `{{__('Are you sure?')}}`,
                    text: `{{__("You won't be able to revert this!")}}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1a5c4e',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: `{{__('Continue')}}`
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = $(this).attr('href');
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
