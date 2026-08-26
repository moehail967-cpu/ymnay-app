@extends('landlord.frontend.user.dashboard.user-master')
@section('title')
   {{__('Payment Logs')}}
@endsection

@section('page-title')
    {{__('Payment Logs')}}
@endsection

@section('section')
    @if(count($package_orders) > 0)
        <div class="custom_domain_table">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">{{__('Package Order Info')}}</th>
                    <th scope="col">{{__('Payment Status')}}</th>
                    <th scope="col">{{__('Action')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($package_orders as $data)
                    <tr>
                        <td>
                            <div class="user-dahsboard-order-info-wrap">
                                <h5 class="title">{{$data->package_name}}</h5>
                                <div class="div package_order_details_data">
                                    <small class="d-block"><strong>{{__('Order ID:')}}</strong> #{{$data->id}}</small>
                                    <small class="d-block"><strong>{{__('Order Name:')}}</strong> {{$data->tenant_id.'.'.env('CENTRAL_DOMAIN')}}</small>
                                    <small class="d-block"><strong>{{__('Package Price:')}}</strong> {{amount_with_currency_symbol($data->package_price)}}</small>

                                    <small class="d-block"><strong>{{__('Order Status:')}}</strong>
                                        @if($data->status == 'pending')
                                            <span class="alert_status_single alert alert-warning text-capitalize alert-sm alert-small">{{__($data->status)}}</span>
                                        @elseif($data->status == 'cancel')
                                            <span class="alert_status_single alert alert-danger text-capitalize alert-sm alert-small">{{__($data->status)}}</span>
                                        @elseif($data->status == 'in_progress')
                                            <span class="alert_status_single alert alert-info text-capitalize alert-sm alert-small">{{__(str_replace('_',' ',$data->status))}}</span>
                                        @elseif($data->status == 'trial')
                                            <span class="alert_status_single lert alert-primary text-capitalize alert-sm alert-small">{{__(str_replace('_',' ',$data->status))}}</span>
                                        @else
                                            <span class="alert_status_single alert alert-success text-capitalize alert-sm alert-small">{{__($data->status)}}</span>
                                        @endif
                                    </small>
                                    @php
                                        $payment = \App\Models\PaymentLogs::where(['tenant_id' => $data->id, 'status' => 'trial'])->latest()->first();
//                                        @dd($data);
                                         $start_date = date('d-m-Y',strtotime($data->created_at));
                                        $renewal_at = date('d-m-Y',strtotime($data->start_date));
                                        $expire_date = date('d-m-Y',strtotime($data?->tenant?->expire_date)) ;
                                        $end_date = date('d-m-Y',strtotime($data?->expire_date)) != null ? date('d-m-Y',strtotime($data?->expire_date)) : __('Lifetime');

                                        if (!empty($payment))
                                        {
                                            $end_date = date('d-m-Y',strtotime($payment->expire_date));
                                        }
                                        $end_date = $data?->expire_date != null ? date('d-m-Y',strtotime($data?->expire_date)) : __('Lifetime');
                                    @endphp
{{--                                    @dd($data);--}}
                                    @if(($data->renew_status != 0) || ($data->is_renew != 0))
                                        <small class="d-block"><strong>{{__('Order Date:')}}</strong> {{ $start_date }}</small>
                                        <small class="d-block"><strong>{{ __('Renewal Date:') }}</strong> {{ $renewal_at }}</small>
                                    @else
                                        <small class="d-block"><strong>{{ __('Order Date:') }}</strong> {{ $start_date }}</small>
                                    @endif

                                    @if($data->status && $data->payment_status !== 'complete' )
                                        <small class="d-block"><strong>{{ __('Expire Date:') }}</strong> {{ $expire_date ?? __('') }}</small>
                                        <small class="d-block"><strong>{{__('Renew Taken :')}}</strong> {{  $data?->tenant?->renew_status ?? 0 }}</small>
                                    @else
                                        <small class="d-block"><strong>{{ __('Expire Date:') }}</strong> {{ $end_date }}</small>
                                        <small class="d-block"><strong>{{__('Renew Taken :')}}</strong> {{ $data->renew_status ?? 0 }}</small>
                                    @endif
                                    @php
                                        $tenant = \App\Models\Tenant::where('id', $data->tenant_id)->first();
                                        $paymentLog = \App\Models\PaymentLogs::where('id', $data->id)->first();
                                        $subscriptionStatus = $paymentLog->subscription_status ?? null;
                                    @endphp
                                    @if($data->payment_status == 'complete')
                                        {{-- Razorpay Recurring Subscription Toggle --}}
                                       
                                        @if($tenant && get_static_option('razorpay_recurring_enabled'))
                                            <div class="recurring-subscription-section margin-top-10">
                                                @if(!$tenant->recurring_enabled || !$tenant->razorpay_subscription_id)
                                                    {{-- Enable Auto-Renewal Button --}}
                                                    <small class="d-block"><strong>{{__('Auto-Renewal:')}}</strong>
                                                        <span class="alert alert-danger alert_status_single alert-small">{{__('Disabled')}}</span>
                                                    </small>
{{--                                                    <form action="{{route('landlord.user.enable.recurring')}}" method="POST" class="margin-top-5">--}}
{{--                                                        @csrf--}}
{{--                                                        <button type="submit" class="btn btn-success btn-xs btn-small" title="{{__('Enable automatic subscription renewal')}}">--}}
{{--                                                            <i class="fas fa-sync-alt"></i> {{__('Enable Auto-Renewal')}}--}}
{{--                                                        </button>--}}
{{--                                                    </form>--}}
                                                @else
                                                    {{-- Active Subscription Controls --}}
                                                    <small class="d-block"><strong>{{__('Auto-Renewal:')}}</strong>
                                                        @if($subscriptionStatus == 'paused')
                                                            <span class="alert alert-warning alert_status_single alert-small">{{__('Paused')}}</span>
                                                        @elseif($subscriptionStatus == 'cancelled')
                                                            <span class="alert alert-info alert_status_single alert-small">{{__('cancelled')}}</span>
                                                        @else
                                                            <span class="alert alert-success alert_status_single alert-small">{{__('Active')}}</span>
                                                        @endif
                                                    </small>

{{--                                                    <div class="subscription-controls margin-top-5">--}}
{{--                                                        @if($subscriptionStatus == 'paused')--}}
{{--                                                            --}}{{-- Resume Button --}}
{{--                                                            <form action="{{route('landlord.user.resume.recurring')}}" method="POST" style="display: inline-block; margin-right: 5px;">--}}
{{--                                                                @csrf--}}
{{--                                                                <button type="submit" class="btn btn-success btn-xs btn-small" title="{{__('Resume subscription')}}">--}}
{{--                                                                    <i class="fas fa-play"></i> {{__('Resume')}}--}}
{{--                                                                </button>--}}
{{--                                                            </form>--}}
{{--                                                        @elseif($subscriptionStatus != 'cancelled')--}}
{{--                                                            --}}{{-- Pause Button --}}
{{--                                                            <form action="{{route('landlord.user.pause.recurring')}}" method="POST" style="display: inline-block; margin-right: 5px;"--}}
{{--                                                                  onsubmit="return confirm('{{__('Are you sure you want to pause your subscription?')}}');">--}}
{{--                                                                @csrf--}}
{{--                                                                <button type="submit" class="btn btn-info btn-xs btn-small" title="{{__('Pause subscription temporarily')}}">--}}
{{--                                                                    <i class="fas fa-pause"></i> {{__('Pause')}}--}}
{{--                                                                </button>--}}
{{--                                                            </form>--}}
{{--                                                        @endif--}}

{{--                                                        --}}{{-- Cancel Button (always show unless already cancelled) --}}
{{--                                                        @if($subscriptionStatus != 'cancelled')--}}
{{--                                                            <form action="{{route('landlord.user.disable.recurring')}}" method="POST" style="display: inline-block;"--}}
{{--                                                                  onsubmit="return confirm('{{__('Are you sure you want to cancel auto-renewal? Your subscription will remain active until the current period ends.')}}');">--}}
{{--                                                                @csrf--}}
{{--                                                                <button type="submit" class="btn btn-warning btn-xs btn-small" title="{{__('Cancel automatic subscription renewal')}}">--}}
{{--                                                                    <i class="fas fa-ban"></i> {{__('Cancel')}}--}}
{{--                                                                </button>--}}
{{--                                                            </form>--}}
{{--                                                        @endif--}}
{{--                                                    </div>--}}
                                                @endif
                                            </div>
                                        @endif

                                        <form action="{{route(route_prefix().'frontend.package.invoice.generate')}}"  method="post">
                                            @csrf
                                            <input type="hidden" name="id" id="invoice_generate_order_field" value="{{$data->id}}">
                                            <button class="btn btn-secondary btn-xs btn-small margin-top-10" type="submit">{{__('Invoice')}}</button>
                                        </form>


                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($data->payment_status != 'complete' && $data->status != 'cancel')
                                <span class="alert_status_single alert alert-warning text-capitalize alert-sm">{{__($data->payment_status)}}</span>
                                <a href="{{route(route_prefix().'frontend.order.confirm',$data->package_id)}}" class="btn btn-success btn-sm">{{__('Pay Now')}}</a>
                                <form action="{{route(route_prefix().'user.dashboard.package.order.cancel')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{$data->id}}">
                                    <button type="submit" class="btn btn-danger btn-sm margin-top-10">{{__('Cancel')}}</button>
                                </form>
                            @else
                                @php
                                    $payment_status_color = match ($data->payment_status) {
                                        'complete' => 'alert-success',
                                        'pending' => 'alert-warning',
                                        'cancel' => 'alert-danger'
                                    }
                                @endphp
                                <span class="alert_status_single alert {{$payment_status_color}} text-capitalize alert-sm" style="display: inline-block">{{__($data->payment_status)}}</span>
                            @endif
                        </td>

                        <td>
                            @if($tenant && get_static_option('razorpay_recurring_enabled'))
                                @if(!$tenant->recurring_enabled || !$tenant->razorpay_subscription_id)
                                    @if($data->payment_status != 'pending')
                                        <div class="btn-wrapper">
                                            <a href="{{route(route_prefix().'frontend.order.confirm',$data->package_id)}}" class="cmn-btn btn-success cmn-btn-small text-white" target="_blank">{{__('Renew Now')}}</a>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="blog-pagination">
            {{ $package_orders->links() }}
        </div>
    @else
        <div class="alert alert-warning">{{__('No Order Found')}}</div>
    @endif
@endsection

@section('scripts')
    <script>
        $('.close-bars, .body-overlay').on('click', function() {
            $('.dashboard-close, .dashboard-close-main, .body-overlay').removeClass('active');
        });
        $('.sidebar-icon').on('click', function() {
            $('.dashboard-close, .dashboard-close-main, .body-overlay').addClass('active');
        });
    </script>
@endsection
