@extends(route_prefix().'admin.admin-master')
@section('title') {{__('In Progress Orders')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <x-summernote.css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-progress-clock text-info text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All In Progress Orders')}}</h3>
                <p class="text-xs text-muted">{{__('Orders currently being processed')}}</p>
            </div>
        </div>
        <a href="{{route(route_prefix().'admin.product.order.manage.all')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition whitespace-nowrap">
            <i class="mdi mdi-arrow-left text-base"></i> {{__('All Orders')}}
        </a>
    </div>

    <div class="px-4 sm:px-6 py-3 border-b border-main flex items-center gap-3">
        <select id="bulk_action_select" class="text-xs bg-secondary border border-main rounded-lg px-3 py-1.5 text-dark focus:border-primary focus:outline-none transition">
            <option value="">{{__('Bulk Action')}}</option>
            <option value="delete">{{__('Delete')}}</option>
        </select>
        <button type="button" id="bulk_action_apply_btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
            {{__('Apply')}}
        </button>
    </div>

    <div class="tw-table-wrap">
        <table class="w-full text-left" id="all_user_table">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 w-10 no-sort">
                        <input type="checkbox" class="all-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Package Name')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Package Price')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Payment Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Order Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_orders as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <input type="checkbox" class="bulk-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" name="bulk_delete[]" value="{{$data->id}}">
                    </td>
                    <td class="px-4 py-3.5"><span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span></td>
                    <td class="px-4 py-3.5"><span class="text-sm font-semibold text-dark">{{$data->package_name}}</span></td>
                    <td class="px-4 py-3.5"><span class="text-sm font-bold text-dark">{{amount_with_currency_symbol($data->package_price)}}</span></td>
                    <td class="px-4 py-3.5">
                        @if($data->payment_status == 'complete')
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">{{__($data->payment_status)}}</span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase">{{__($data->payment_status ?? 'Pending')}}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="tw-pill tw-pill-info">{{__(ucfirst(str_replace('_', ' ', $data->status)))}}</span>
                    </td>
                    <td class="px-4 py-3.5"><span class="text-xs text-muted">{{date_format($data->created_at,'d M Y')}}</span></td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">
                                <a href="{{route(route_prefix().'admin.product.order.manage.view', $data->id)}}" title="{{__('View')}}"
                                   class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:text-white hover:bg-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-eye-outline text-sm"></i>
                                </a>
                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center px-2 py-1.5 rounded-lg bg-secondary border border-main text-dark hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-dots-vertical text-sm"></i>
                                </button>
                                <div class="row-action-menu hidden">
                                    <button type="button" class="action-item user_edit_btn">
                                        <span class="action-icon bg-info-soft"><i class="mdi mdi-email-fast-outline text-info"></i></span>
                                        {{__('Send Email')}}
                                    </button>
                                    <button type="button" class="action-item order_status_change_btn" data-id="{{$data->id}}" data-status="{{$data->status}}" data-payment_status="{{$data->payment_status}}">
                                        <span class="action-icon bg-warning-soft"><i class="mdi mdi-swap-horizontal text-warning"></i></span>
                                        {{__('Update Status')}}
                                    </button>
                                    @if(!empty($data->user_id) && ($data->payment_status == 'pending' || $data->payment_status == null))
                                        <form action="{{route(route_prefix().'admin.product.order.reminder')}}" method="post" class="contents">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$data->id}}">
                                            <button type="submit" class="action-item">
                                                <span class="action-icon bg-[#f3e8ff]"><i class="mdi mdi-bell-ring-outline text-[#9333ea]"></i></span>
                                                {{__('Send Reminder')}}
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{route(route_prefix().'admin.order.invoice.generate')}}" method="POST" target="_blank" class="contents">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$data->id}}">
                                        <button type="submit" class="action-item">
                                            <span class="action-icon bg-secondary"><i class="mdi mdi-receipt-text-outline text-dark"></i></span>
                                            {{__('Invoice')}}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('tenant.admin.product-order-manage.portion.status-and-mail-send')

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-summernote.js/>
    @include('tenant.admin.product-order-manage.portion.order-table-scripts')
@endsection
