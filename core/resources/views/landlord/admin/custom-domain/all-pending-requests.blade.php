@extends('landlord.admin.admin-master')
@section('title') {{__('All Pending Custom Domain Requests')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-hourglass-half text-warning text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Pending Custom Domain Requests')}}</h3>
            <p class="text-xs text-muted">{{__('Domain requests awaiting review')}}</p>
        </div>
    </div>

    {{-- Bulk Action --}}
    <x-bulk-action-v2 permissions="package-order-delete"/>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="all_pending_domain_table">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort w-10">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox">
                        </div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Username')}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Current Domain')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Requested Domain')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($domain_infos as $data)
                <tr class="border-b border-subtle hover:bg-muted transition">
                    <td class="px-4 sm:px-6 py-3">
                        <div class="bulk-checkbox-wrapper">
                            <input type="checkbox" class="bulk-checkbox" name="bulk_delete[]" value="{{$data->id}}">
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-dark">{{$data->id}}</td>
                    <td class="px-4 py-3 text-sm text-dark">{{optional($data->user)->username}}</td>
                    <td class="hidden md:table-cell px-4 py-3 text-xs text-muted">{{optional($data)->old_domain . '.' . env('CENTRAL_DOMAIN')}}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-dark">{{$data->custom_domain}}</td>
                    <td class="px-4 py-3">
                        <span class="tw-pill tw-pill-warning">{{__('Pending')}}</span>
                    </td>
                    <td class="hidden sm:table-cell px-4 py-3 text-xs text-muted">{{date('d-m-Y', strtotime($data->updated_at))}}</td>
                    <td class="px-4 sm:px-6 py-3 text-right">
                        <div class="inline-flex items-center gap-1">
                            <button type="button"
                                    class="tw-btn-icon tw-btn-icon-edit order_status_change_btn"
                                    data-id="{{$data->id}}"
                                    data-status="{{$data->custom_domain_status}}"
                                    title="{{__('Update Status')}}">
                                <i class="las la-edit text-sm"></i>
                            </button>
                            @can('package-order-delete')
                            <x-delete-popover permissions="package-order-delete" url="{{route('landlord.admin.custom.domain.request.delete', $data->id)}}"/>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Status Change Modal --}}
<div class="cm-modal-backdrop" id="statusChangeBackdrop"></div>
<div class="cm-modal" id="statusChangeModal">
    <div class="cm-modal-dialog">
        <div class="cm-modal-header">
            <h5 class="cm-modal-title">
                <i class="las la-exchange-alt text-primary"></i> {{__('Custom Domain Status Change')}}
            </h5>
            <button type="button" class="cm-modal-close" onclick="closeModal('statusChange')">
                <i class="las la-times"></i>
            </button>
        </div>
        <form action="{{route('landlord.admin.custom.domain.status.change')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="cm-modal-body">
                <input type="hidden" name="custom_domain_id" id="custom_domain_id">
                <div>
                    <label class="lnd-label" for="custom_domain_status">{{__('Custom Domain Status')}}</label>
                    <select name="custom_domain_status" class="lnd-input" id="custom_domain_status">
                        <option value="pending">{{__('Pending')}}</option>
                        <option value="in_progress">{{__('In Progress')}}</option>
                        <option value="connected">{{__('Connected')}}</option>
                        <option value="rejected">{{__('Rejected')}}</option>
                        <option value="removed">{{__('Removed')}}</option>
                    </select>
                </div>
            </div>
            <div class="cm-modal-footer">
                <button type="button" onclick="closeModal('statusChange')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-muted border border-main hover:bg-muted transition">
                    {{__('Close')}}
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                    {{__('Change Status')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-bulk-action-js :url="route('landlord.admin.custom.domain.bulk.action')"/>
    <script>
        (function($){
            "use strict";

            /* ── Modal helpers ─────────────────────────────── */
            window.openModal = function(name) {
                document.getElementById(name + 'Backdrop').classList.add('active');
                document.getElementById(name + 'Modal').classList.add('active');
            };
            window.closeModal = function(name) {
                document.getElementById(name + 'Backdrop').classList.remove('active');
                document.getElementById(name + 'Modal').classList.remove('active');
            };

            document.querySelectorAll('.cm-modal-backdrop').forEach(function(backdrop) {
                backdrop.addEventListener('click', function() {
                    closeModal(this.id.replace('Backdrop', ''));
                });
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.cm-modal.active').forEach(function(modal) {
                        closeModal(modal.id.replace('Modal', ''));
                    });
                }
            });

            $(document).on('click', '.order_status_change_btn', function(e) {
                e.preventDefault();
                var el = $(this);
                $('#custom_domain_id').val(el.data('id'));
                $('#custom_domain_status').val(el.data('status'));
                openModal('statusChange');
            });

        })(jQuery);
    </script>
@endsection
