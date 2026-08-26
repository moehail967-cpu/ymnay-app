
@extends('landlord.frontend.user.dashboard.user-master')

@section('title')
    {{ __('Wallet') }}
@endsection

@section('page-title')
    {{ __('Wallet') }}
@endsection

@section('style')
    <style>
        .deposit-button{
            background-color: var(--main-color-two);
            border: var(--main-color-two);
            padding: 10px 10px;
        }
        .deposit-button:hover{
            background-color: var(--main-color-one);
            border: var(--main-color-one);
        }
        .deposit-button i{
            font-size: 23px;
            margin-right: 10px;
            vertical-align: text-bottom;
        }
        .withdraw-button {
            background-color: #28a745;
            border: #28a745;
            padding: 10px 10px;
        }
        .withdraw-button:hover {
            background-color: #218838;
            border: #218838;
        }
        .confirm-bottom-content{
            margin-top: 30px;
        }
        .payment-gateway-wrapper ul {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin: 0;
            padding: 0;
            flex-basis: 100%;
            width: 100%;
            margin-left: 30px;
        }
        .payment-gateway-wrapper li {
            display: flex;
            margin-left: 10px;
            width: calc(100%/4);
            border: 3px solid transparent;
            cursor: pointer;
        }
        .payment-gateway-wrapper li .img-select{
            width: 100%;
            height: 50px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .payment-gateway-wrapper li.selected{
            border: 3px solid var(--main-color-two);
            border-radius: 5px;
        }
        .payment-gateway-wrapper li img {
            width: 100%;
        }
        .deposit-table tbody tr td:nth-child(2), .deposit-table tbody tr td:nth-child(3){
            text-transform: capitalize;
        }
        .payment_attachment{
            width: 100px;
        }
        .commission-table {
            margin-top: 40px;
        }
        .btn-pay-commission {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
            padding: 10px 10px;
        }
        .btn-pay-commission:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #000;
        }
        .badge-paid {
            background-color: #28a745;
        }
        .badge-unpaid {
            background-color: #dc3545;
        }
        .gateway-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fff;
        }

        .gateway-card:hover {
            border: 1px solid var(--main-color-two);
            /*box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);*/
            transform: translateY(-2px);
        }

        .gateway-card.selected {
            border: 2px solid var(--main-color-two);

        }

        .gateway-icon {
            width: 50px;
            height: 50px;
            background: var(--main-color-two);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .gateway-icon i {
            /*font-size: 24px;*/
            color: white;
        }

        .gateway-check {
            /*font-size: 24px;*/
            color: #e0e0e0;
            transition: all 0.3s;
        }

        .gateway-card.selected .gateway-check {
            color: var(--main-color-two);
        }

        .gateway-info h6 {
            /*font-weight: 600;*/
            color: #333;
        }

        .gateway-specific-fields {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .form-control:focus {
            box-shadow: none;
            border: 1px solid var(--main-color-two);
        }

        /* Validation Styles */
        .form-control.is-valid {
            border-color: #28a745;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .valid-feedback {
            color: #28a745;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 12px;
            margin-top: 0.25rem;
        }

        .valid-feedback i,
        .invalid-feedback i {
            margin-right: 4px;
        }
        /* Submit Button States */
        #submitWithdrawBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        #submitWithdrawBtn.btn-success {
            background: var(--main-color-two) !important;
            border-color: var(--main-color-two) !important;
            /*animation: pulse 2s infinite;*/
        }
        .form-control.is-invalid:focus, .was-validated .form-control:invalid:focus ,
        .form-control.is-valid:focus, .was-validated .form-control:valid:focus {
            box-shadow: none;
        }

        /*@keyframes pulse {*/
        /*    0%, 100% {*/
        /*        transform: scale(1);*/
        /*        box-shadow: 0 0 0 0 var(--main-color-two);*/
        /*    }*/
        /*    50% {*/
        /*        transform: scale(1.02);*/
        /*        box-shadow: 0 0 0 8px rgba(0, 0, 0, 0);*/
        /*    }*/
        /*}*/
    </style>
@endsection
 @php
    use App\Helpers\PaymentGatewayRenderHelper;
    $gateways = PaymentGatewayRenderHelper::getActiveGateways();
@endphp
@section('section')
    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif

{{--    @if($errors->any())--}}
{{--        <div class="alert alert-danger mt-3">--}}
{{--            <ul class="mb-0">--}}
{{--                @foreach($errors->all() as $error)--}}
{{--                    <li>{{ $error }}</li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        </div>--}}
{{--    @endif--}}
    <!-- Dashboard area Starts -->
    <div class="row">
        <!-- Wallet Balance -->
        <div class="col-12 col-md-6 orders-child pb-2">
            <div class="single-orders">
                <div class="orders-flex-content">
                    <div class="icon">
                        <i class="las la-wallet"></i>
                    </div>
                    <div class="contents">
                        <h2 class="order-titles">
                            {{ float_amount_with_currency_symbol($balance->balance ?? 0) }}
                        </h2>
                        <span class="order-para">{{ __('Wallet Balance') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Earnings -->
        <div class="col-12 col-md-6 orders-child pb-2">
            <div class="single-orders">
                <div class="orders-flex-content">
                    <div class="icon">
                        <i class="las la-hand-holding-usd"></i>
                    </div>
                    <div class="contents">
                        <h2 class="order-titles">
                            {{ float_amount_with_currency_symbol($totalEarning) }}
                        </h2>
                        <span class="order-para">{{ __('Total Earnings') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Earnings (After Commission) -->
        <div class="col-12 col-md-6 orders-child pb-2">
            <div class="single-orders">
                <div class="orders-flex-content">
                    <div class="icon">
                        <i class="las la-coins"></i>
                    </div>
                    <div class="contents">
                        <h2 class="order-titles">
                            {{ float_amount_with_currency_symbol($netEarning) }}
                        </h2>
                        <span class="order-para">{{ __('Net Earnings') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Commission Deducted -->
        <div class="col-12 col-md-6 orders-child pb-2">
            <div class="single-orders">
                <div class="orders-flex-content">
                    <div class="icon">
                        <i class="las la-minus-circle"></i>
                    </div>
                    <div class="contents">
                        <h2 class="order-titles">
                            {{ float_amount_with_currency_symbol($totalEarning - $netEarning) }}
                        </h2>
                        <span class="order-para">{{ __('Total Commission') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paid Commission -->
        <div class="col-12 col-md-6 orders-child pb-2">
            <div class="single-orders">
                <div class="orders-flex-content">
                    <div class="icon">
                        <i class="las la-check-circle"></i>
                    </div>
                    <div class="contents">
                        <h2 class="order-titles">{{ float_amount_with_currency_symbol($paidCommission) }}</h2>
                        <span class="order-para">{{ __('Paid Commission') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unpaid Commission -->
        <div class="col-12 col-md-6 orders-child pb-2">
            <div class="single-orders">
                <div class="orders-flex-content">
                    <div class="icon">
                        <i class="las la-exclamation-circle"></i>
                    </div>
                    <div class="contents">
                        <h2 class="order-titles">{{ float_amount_with_currency_symbol($unpaidCommission) }}</h2>
                        <span class="order-para">{{ __('Unpaid Commission') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <div class="dashboard-settings margin-top-55 d-flex justify-content-end">
    <div class="dashboard-settings text-end">
        <button type="button" class="btn btn-primary deposit-button me-2" data-bs-toggle="modal"
                data-bs-target="#payoutRequestModal">
            <i class="las la-wallet"></i> {{ __('Deposit') }}
        </button>
{{--        @if(commissionSettingType())--}}
            @if(!empty($balance) && $balance->balance > 1)
                <button type="button" class="btn btn-success withdraw-button" data-bs-toggle="modal"
                        data-bs-target="#withdrawRequestModal">
                    <i class="las la-money-bill-wave"></i> {{ __('Withdraw') }}
                </button>
            @endif
{{--        @else--}}
            @if($unpaidCommission > 0)
                <button class="btn btn-pay-commission pay-commission-btn"
                        data-id="all"
                        data-amount="{{ $unpaidCommission }}">
                    <i class="las la-credit-card"></i> {{ __('Pay All Commissions') }}
                </button>
            @endif
{{--        @endif--}}
    </div>

</div>

<!-- Conditional Table Display -->
<div class="single-dashboard-order mt-2">
    {{-- Wallet History Table --}}
        <div class="table-responsive table-responsive--md">
            <div>
            <h2 class="dashboards-title">{{ __('Wallet History') }}</h2>
            <div class="notice-board">
                <p class="text-primary">{{ __('You can deposit to your wallet and withdraw your earnings.') }}</p>
            </div>
        </div>
        <table class="custom--table deposit-table">
            <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Payment Gateway') }}</th>
                <th>{{ __('Payment Status') }}</th>
                <th>{{ __('Deposit Amount') }}</th>
                <th>{{ __('Deposit Date') }}</th>
                <th>{{ __('Payment Image') }}</th>
            </tr>
            </thead>
            <tbody >
            @forelse($wallet_histories as $history)
                <tr>
                    <td data-label="{{ __('ID') }}">{{ $history->id }}</td>
                    <td data-label="{{ __('Payment Gateway') }}">
                        @php
                            $payment_gateway = str_replace('_', ' ', $history->payment_gateway);
                        @endphp
                        {{ $payment_gateway }}
                    </td>
                    <td data-label="{{ __('Payment Status') }}">{{ $history->payment_status }}</td>
                    <td data-label="{{ __('Request Amount') }}">{{ float_amount_with_currency_symbol($history->amount) }}</td>
                    <td data-label="{{ __('Request Date') }}">{{ $history->created_at->diffForHumans() }}</td>
                    <td data-label="{{ __('Payment Image') }}">
                        @if(empty($history->manual_payment_image))
                            {{ __('No Image') }}
                        @else
                            <img class="rounded payment_attachment"
                                    src="{{ asset('assets/landlord/uploads/deposit_payment_attachments/'.$history->manual_payment_image) }}"
                                    alt="payment-image">
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center mt-2">{{ __('No wallet history found') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="blog-pagination margin-top-55">
            <div class="custom-pagination mt-4 mt-lg-5">
                {!! $wallet_histories->links() !!}
            </div>
        </div>


{{--    @if(commissionSettingType())--}}
{{--        <div>--}}
{{--            <h2 class="dashboards-title">{{ __('Withdraw History') }}</h2>--}}
{{--            <div class="notice-board">--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        --}}{{-- withdraw Log Table --}}
{{--            <table class="custom--table commission-table">--}}
{{--                <thead>--}}
{{--                <tr>--}}
{{--                    <th>{{ __('ID') }}</th>--}}
{{--                    <th>{{ __('Amount') }}</th>--}}
{{--                    <th>{{ __('Status') }}</th>--}}
{{--                    <th>{{ __('Method') }}</th>--}}
{{--                    <th>{{ __('Date') }}</th>--}}
{{--                </tr>--}}
{{--                </thead>--}}
{{--                <tbody class="">--}}
{{--                @forelse($withdraw_request_logs as $log)--}}
{{--                --}}{{-- @dd($log) --}}
{{--                    <tr>--}}
{{--                        <td data-label="{{ __('ID') }}">{{ $log->id }}</td>--}}
{{--                        <td data-label="{{ __('Amount') }}">{{ float_amount_with_currency_symbol($log->amount) }}</td>--}}
{{--                        <td data-label="{{ __('Status') }}">--}}
{{--                            <span class="badge bg-{{--}}
{{--                                $log->status == 'complete' ? 'success' :--}}
{{--                                ($log->status == 'cancel' ? 'danger' : 'warning')--}}
{{--                            }}">--}}
{{--                                {{ ucfirst($log->status) }}--}}
{{--                            </span>--}}
{{--                        </td>--}}
{{--                        <td data-label="{{ __('Method') }}">{{ $log->payment_method ?? 'N/A' }}</td>--}}
{{--                        <td data-label="{{ __('Date') }}">{{ $log->created_at->diffForHumans() }}</td>--}}
{{--                    </tr>--}}
{{--                @empty--}}
{{--                    <tr>--}}
{{--                        <td colspan="6" class="text-center mt-2">{{ __('No commission payment logs found') }}</td>--}}
{{--                    </tr>--}}
{{--                @endforelse--}}
{{--                </tbody>--}}
{{--            </table>--}}

{{--            <div class="blog-pagination margin-top-55">--}}
{{--                <div class="custom-pagination mt-4 mt-lg-5">--}}
{{--                     {!! $withdraw_request_logs->links() !!}--}}
{{--                    --}}{{-- Add pagination for commission logs if needed --}}
{{--                </div>--}}
{{--            </div>--}}
{{--    @else--}}
{{--        <div>--}}
{{--            <h2 class="dashboards-title">{{ __('Commission pay History') }}</h2>--}}
{{--            <div class="notice-board">--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        --}}{{-- Commission Payment Log Table --}}
{{--            <table class="custom--table commission-table">--}}
{{--                <thead>--}}
{{--                <tr>--}}
{{--                    <th>{{ __('ID') }}</th>--}}
{{--                    <th>{{ __('Order ID') }}</th>--}}
{{--                    <th>{{ __('Commission Amount') }}</th>--}}
{{--                    <th>{{ __('Payment Status') }}</th>--}}
{{--                    <th>{{ __('Payment Method') }}</th>--}}
{{--                    <th>{{ __('Payment Date') }}</th>--}}
{{--                </tr>--}}
{{--                </thead>--}}
{{--                <tbody class="">--}}
{{--                @forelse($commission_payment_logs as $log)--}}
{{--                --}}{{-- @dd($log) --}}
{{--                    <tr>--}}
{{--                        <td data-label="{{ __('ID') }}">{{ $log->id }}</td>--}}
{{--                        <td data-label="{{ __('Order ID') }}">{{ $log->order_id ?? 'N/A' }}</td>--}}
{{--                        <td data-label="{{ __('Commission Amount') }}">{{ float_amount_with_currency_symbol($log->amount) }}</td>--}}
{{--                        <td data-label="{{ __('Payment Status') }}">--}}
{{--                            <span class="badge bg-{{ $log->payment_status == 'complete' ? 'success' : 'warning' }}">--}}
{{--                                {{ ucfirst($log->payment_status) }}--}}
{{--                            </span>--}}
{{--                        </td>--}}
{{--                        <td data-label="{{ __('Payment Method') }}">{{ $log->payment_gateway ?? 'N/A' }}</td>--}}
{{--                        <td data-label="{{ __('Payment Date') }}">{{ $log->created_at->diffForHumans() }}</td>--}}
{{--                    </tr>--}}
{{--                @empty--}}
{{--                    <tr>--}}
{{--                        <td colspan="6" class="text-center mt-2">{{ __('No commission payment logs found') }}</td>--}}
{{--                    </tr>--}}
{{--                @endforelse--}}
{{--                </tbody>--}}
{{--            </table>--}}

{{--            <div class="blog-pagination margin-top-55">--}}
{{--                <div class="custom-pagination mt-4 mt-lg-5">--}}
{{--                     {!! $commission_payment_logs->links() !!}--}}
{{--                    --}}{{-- Add pagination for commission logs if needed --}}
{{--                </div>--}}
{{--            </div>--}}
{{--    @endif--}}

    </div>
</div>

    <!-- Deposit Modal -->
    <div class="modal fade" id="payoutRequestModal" tabindex="-1" role="dialog" aria-labelledby="editModal"
         aria-hidden="true">
        <form action="{{ route('landlord.user.wallet.deposit') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-warning">{{ __('Deposit to Wallet') }}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label for="">{{ __('Deposit Amount') }}</label>
                        <input type="number" class="form-control mt-2" name="amount" required
                               placeholder="{{ __('Enter Deposit Amount') }}">
                        <div class="confirm-bottom-content">
                            <div class="confirm-payment payment-border">
                                <div class="single-checkbox">
                                    <div class="checkbox-inlines">
                                        <label class="checkbox-label" for="check2">


                                            {!! PaymentGatewayRenderHelper::renderPaymentGatewayForForm($gateways) !!}
                                        </label>
                                    </div>

                                    <div class="form-group single-input d-none manual_transaction_id mt-4">
                                        @php
                                            $payment_gateways = \App\Models\PaymentGateway::where(['status' => \App\Enums\StatusEnums::PUBLISH, 'name' => 'manual_payment'])->first();
                                        @endphp
                                        @if(!empty($payment_gateways))
                                            <p class="alert alert-info">{{json_decode($payment_gateways->credentials)->description ?? ''}}</p>
                                        @endif

                                        <input type="text" name="trasaction_id"
                                               class="form-control form--control mt-2"
                                               placeholder="{{__('Transaction ID')}}">

                                        <input type="file" name="manual_payment_image"
                                               class="form-control form--control mt-2"
                                               placeholder="{{__('Transaction Attachment')}}" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Deposit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- Withdraw Request Modal with Real-time Validation -->
    <div class="modal fade" id="withdrawRequestModal" tabindex="-1" aria-hidden="true">
        <form action="{{ route('tenant.withdraw.submit') }}" method="post" id="withdrawForm" novalidate>
            @csrf
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title">
                            <i class="las la-wallet me-2"></i>
                            {{ __('Withdraw Request') }}
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal">
                            <span aria-hidden="true">×</span>
                        </button>

                    </div>

                    <div class="modal-body">
                        <!-- Available Balance Alert -->
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="las la-info-circle fs-4 me-2"></i>
                            <div>
                                <strong>{{ __('Available Balance:') }}</strong>
                                <span class="fs-5 ms-2" id="availableBalance">{{ float_amount_with_currency_symbol($balance->balance ?? 0) }}</span>
                            </div>
                        </div>

                        <!-- Withdraw Amount -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">
                                {{ __('Withdraw Amount') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                            <span class="input-group-text">
                                <i class="las la-dollar-sign"></i>
                            </span>
                                <input type="number"
                                       step="0.01"
                                       name="amount"
                                       id="withdrawAmount"
                                       class="form-control"
                                       data-required="1"
                                       placeholder="{{ __('Enter amount') }}"
                                       data-min="10"
                                       data-max="{{ $balance->balance ?? 0 }}"
                                       value="{{ old('amount') }}">
                            </div>
                            <div id="amountError" class="invalid-feedback d-block" style="display: none !important;"></div>
                            <div id="amountSuccess" class="valid-feedback d-block" style="display: none !important;"></div>
                            <small class="text-muted">
                                <i class="las la-info-circle me-1"></i>
                                Minimum: {{ float_amount_with_currency_symbol(10) }} |
                                Maximum: {{ float_amount_with_currency_symbol($balance->balance ?? 0) }}
                            </small>
                        </div>

                        <!-- Payment Gateway Selection -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">
                                {{ __('Select Payment Gateway') }}
                                <span class="text-danger">*</span>
                            </label>

                            @if($withdraw_gateways && $withdraw_gateways->count() > 0)
                                <div class="payment-gateway-list">
                                    <input type="hidden" name="selected_payment_gateway" id="selected_payment_gateway" value="{{ old('selected_payment_gateway') }}">

                                    <div class="row g-3">
                                        @foreach($withdraw_gateways as $gateway)
                                            <div class="col-md-6">
                                                <div class="gateway-card {{ old('selected_payment_gateway') == $gateway->gateway_id ? 'selected' : '' }}"
                                                     data-gateway-id="{{ $gateway->gateway_id }}"
                                                     onclick="selectGateway('{{ $gateway->gateway_id }}')">
                                                    <div class="d-flex align-items-center">
                                                        <div class="gateway-icon">
                                                            <i class="las la-wallet fs-4"></i>
                                                        </div>
                                                        <div class="gateway-info flex-grow-1">
                                                            <h6 class="mb-0 fw-bold">{{ $gateway->name }}</h6>
                                                            <small class="text-muted">
                                                                {{ count($gateway->fields ?? []) }} field(s) required
                                                            </small>
                                                        </div>
                                                        <div class="gateway-check">
                                                            <i class="las la-check-circle fs-3"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="gatewayError" class="invalid-feedback d-block mt-2" style="display: none !important;"></div>
                                </div>

                                <!-- Dynamic Gateway Fields Container -->
                                <div id="gatewayFieldsContainer" class="mt-4">
                                    @foreach($withdraw_gateways as $gateway)
                                        @if($gateway->fields && count($gateway->fields) > 0)
                                            <div class="gateway-specific-fields {{ old('selected_payment_gateway') == $gateway->gateway_id ? '' : 'd-none' }}"
                                                 data-gateway="{{ $gateway->gateway_id }}">

                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0 fw-bold">
                                                            <i class="las la-edit me-2"></i>
                                                            {{ $gateway->name }} - Account Details
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row g-3">
                                                            @foreach($gateway->fields as $field)
                                                                @php
                                                                    $fieldName = 'field_' . preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($field['label']));
                                                                    $isRequired = isset($field['required']) && $field['required'];
                                                                @endphp

                                                                <div class="col-md-{{ in_array($field['type'], ['textarea']) ? '12' : '6' }}">
                                                                    <div class="form-group">
                                                                        <label class="form-label">
                                                                            {{ $field['label'] }}
                                                                            @if($isRequired)
                                                                                <span class="text-danger">*</span>
                                                                            @endif
                                                                        </label>

                                                                        @if($field['type'] == 'textarea')
                                                                            <textarea
                                                                                name="{{ $fieldName }}"
                                                                                id="{{ $fieldName }}"
                                                                                class="form-control gateway-field"
                                                                                placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
                                                                                rows="3"
                                                                                data-label="{{ $field['label'] }}"
                                                                                data-field-type="{{ $field['type'] }}"
                                                                            data-required="{{ $isRequired ? '1' : '0' }}">{{ old($fieldName) }}</textarea>
                                                                        @else
                                                                            <input
                                                                                type="{{ $field['type'] }}"
                                                                                name="{{ $fieldName }}"
                                                                                id="{{ $fieldName }}"
                                                                                class="form-control gateway-field"
                                                                                placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
                                                                                value="{{ old($fieldName) }}"
                                                                                data-label="{{ $field['label'] }}"
                                                                                data-field-type="{{ $field['type'] }}"
                                                                                data-required="{{ $isRequired ? '1' : '0' }}">
                                                                        @endif

                                                                        <div class="invalid-feedback"></div>
                                                                        <div class="valid-feedback"></div>

                                                                        @error($fieldName)
                                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="las la-exclamation-triangle me-2"></i>
                                    {{ __('No payment gateways available. Please contact administrator.') }}
                                </div>
                            @endif
                        </div>

                        <!-- Additional Note (Optional) -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">
                                {{ __('Note') }}
                                <span class="text-muted">({{ __('Optional') }})</span>
                            </label>
                            <textarea
                                name="note"
                                id="withdrawNote"
                                class="form-control"
                                rows="3"
                                maxlength="1000"
                                placeholder="{{ __('Add any additional information...') }}">{{ old('note') }}</textarea>
                            <small class="text-muted float-end">
                                <span id="noteCounter">0</span>/1000 characters
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            <i class="las la-times me-1"></i>
                            {{ __('Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitWithdrawBtn" disabled>
                            <i class="las la-paper-plane me-1"></i>
                            {{ __('Submit Request') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Pay Commission Modal -->
    <div class="modal fade" id="payCommissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" id="payCommissionModalContent">
                <!-- Dynamic content loaded via AJAX -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/backend/js/sweetalert2.js') }}"></script>
    <x-wallet-payment-gateway-js/>
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                @if(session('error'))
                $('#withdrawRequestModal').modal('show');
                @endif
                @if(old('amount') || old('selected_payment_gateway'))
                $('#withdrawRequestModal').modal('show');
                @endif
            });
        })(jQuery);
    </script>
    <script>
        (function ($) {
            "use strict";

            $(document).ready(function () {
                // Pay Commission Button Click
                $(document).on('click', '.pay-commission-btn', function(e) {
                    e.preventDefault();
                    let commissionId = $(this).data('id');
                    let amount = $(this).data('amount');

                    $.ajax({
                        url: '{{ route("landlord.tenant.commission.payment.modal", ":id") }}'.replace(':id', commissionId),
                        type: 'GET',
                        success: function(response) {
                            $('#payCommissionModalContent').html(response.html);
                            $('#payCommissionModal').modal('show');

                            // Reinitialize payment gateway selection
                            initializePaymentGatewaySelection();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Failed to load payment form'
                            });
                        }
                    });
                });

                // Payment Gateway Selection
                function initializePaymentGatewaySelection() {
                    $('.payment-list li').on('click', function() {
                        let gateway = $(this).data('gateway');
                        $('.payment-list li').removeClass('selected');
                        $(this).addClass('selected');
                        $('input[name="payment_gateway"]').val(gateway);

                        // Show/hide manual payment fields
                        if (gateway === 'manual_payment') {
                            $('.manual_transaction_id').removeClass('d-none');
                        } else {
                            $('.manual_transaction_id').addClass('d-none');
                        }
                    });
                }

                $('.close-bars, .body-overlay').on('click', function () {
                    $('.dashboard-close, .dashboard-close-main, .body-overlay').removeClass('active');
                });

                $('.sidebar-icon').on('click', function () {
                    $('.dashboard-close, .dashboard-close-main, .body-overlay').addClass('active');
                });
            });

        })(jQuery);
    </script>
    <script>
        $('#commissionPaymentForm').on('submit', function(e){
            e.preventDefault();
            let form = $(this);
            let btn = form.find('button[type="submit"]');
            btn.prop('disabled', true).text('{{ __("Processing...") }}');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success(res){
                    if(res.status === 'success'){
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Success") }}',
                            text: res.msg,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(()=> location.reload(), 1600);
                    } else if(res.status === 'gateway'){
                        Swal.fire({
                            icon: 'info',
                            title: '{{ __("Redirecting") }}',
                            text: res.msg
                        });
                    }
                },
                error(){
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Something went wrong") }}'
                    });
                },
                complete(){
                    btn.prop('disabled', false).text('{{ __("Confirm Payment") }}');
                }
            });
        });
    </script>
    <script>
        (function() {
            'use strict';

            const minWithdraw = 10;
            const maxWithdraw = parseFloat('{{ $balance->balance ?? 0 }}');
            const currencySymbol = '{{ site_currency_symbol() }}';
            let isFormValid = false;

            // Validation Functions
            function validateAmount() {
                const amountInput = document.getElementById('withdrawAmount');
                const amount = parseFloat(amountInput.value);
                const errorDiv = document.getElementById('amountError');
                const successDiv = document.getElementById('amountSuccess');

                amountInput.classList.remove('is-valid', 'is-invalid');
                errorDiv.style.display = 'none';
                successDiv.style.display = 'none';

                if (!amountInput.value) {
                    amountInput.classList.add('is-invalid');
                    errorDiv.innerHTML = '<i class="las la-exclamation-circle"></i> Amount is required';
                    errorDiv.style.display = 'block';
                    return false;
                }

                if (isNaN(amount) || amount <= 0) {
                    amountInput.classList.add('is-invalid');
                    errorDiv.innerHTML = '<i class="las la-exclamation-circle"></i> Please enter a valid amount';
                    errorDiv.style.display = 'block';
                    return false;
                }

                if (amount < minWithdraw) {
                    amountInput.classList.add('is-invalid');
                    errorDiv.innerHTML = `<i class="las la-exclamation-circle"></i> Minimum withdraw amount is ${currencySymbol}${minWithdraw}`;
                    errorDiv.style.display = 'block';
                    return false;
                }

                if (amount > maxWithdraw) {
                    amountInput.classList.add('is-invalid');
                    errorDiv.innerHTML = `<i class="las la-exclamation-circle"></i> Insufficient balance. Maximum: ${currencySymbol}${maxWithdraw.toFixed(2)}`;
                    errorDiv.style.display = 'block';
                    return false;
                }

                amountInput.classList.add('is-valid');
                successDiv.innerHTML = `<i class="las la-check-circle"></i> Valid amount: ${currencySymbol}${amount.toFixed(2)}`;
                successDiv.style.display = 'block';
                return true;
            }

            function validateGateway() {
                const gateway = document.getElementById('selected_payment_gateway').value;
                const errorDiv = document.getElementById('gatewayError');
                errorDiv.style.display = 'none';

                if (!gateway) {
                    errorDiv.innerHTML = '<i class="las la-exclamation-circle"></i> Please select a payment gateway';
                    errorDiv.style.display = 'block';
                    return false;
                }
                return true;
            }

            function validateGatewayField(input) {
                const value = input.value.trim();
                const type = input.getAttribute('data-field-type');
                const label = input.getAttribute('data-label');
                const isRequired = input.hasAttribute('required');
                const feedbackDiv = input.parentElement.querySelector('.invalid-feedback');
                const successDiv = input.parentElement.querySelector('.valid-feedback');

                // Clear previous states
                input.classList.remove('is-valid', 'is-invalid');
                if (feedbackDiv) feedbackDiv.textContent = '';
                if (successDiv) successDiv.textContent = '';

                // Only validate if field is in visible gateway
                const parentFieldsDiv = input.closest('.gateway-specific-fields');
                if (parentFieldsDiv && parentFieldsDiv.classList.contains('d-none')) {
                    return true; // Don't validate hidden fields
                }

                if (isRequired && !value) {
                    input.classList.add('is-invalid');
                    if (feedbackDiv) feedbackDiv.innerHTML = `<i class="las la-exclamation-circle"></i> ${label} is required`;
                    return false;
                }

                if (value) {
                    // Type-specific validation
                    if (type === 'email') {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) {
                            input.classList.add('is-invalid');
                            if (feedbackDiv) feedbackDiv.innerHTML = '<i class="las la-exclamation-circle"></i> Please enter a valid email address';
                            return false;
                        }
                    } else if (type === 'tel' || type === 'number') {
                        if (isNaN(value)) {
                            input.classList.add('is-invalid');
                            if (feedbackDiv) feedbackDiv.innerHTML = '<i class="las la-exclamation-circle"></i> Please enter a valid number';
                            return false;
                        }
                        if (type === 'tel' && value.length < 10) {
                            input.classList.add('is-invalid');
                            if (feedbackDiv) feedbackDiv.innerHTML = '<i class="las la-exclamation-circle"></i> Please enter a valid phone number (min 10 digits)';
                            return false;
                        }
                    }

                    input.classList.add('is-valid');
                    if (successDiv) successDiv.innerHTML = '<i class="las la-check-circle"></i> Valid';
                    return true;
                }

                return !isRequired; // Optional field with no value is valid
            }

            function validateAllGatewayFields() {
                const selectedGateway = document.getElementById('selected_payment_gateway')?.value;
                if (!selectedGateway) return true; // No gateway selected, skip field validation

                const gatewayFields = document.querySelector(`.gateway-specific-fields[data-gateway="${selectedGateway}"]`);
                if (!gatewayFields || gatewayFields.classList.contains('d-none')) return true; // Hidden, skip validation

                const fields = gatewayFields.querySelectorAll('.gateway-field');
                let allValid = true;

                fields.forEach(field => {
                    if (!validateGatewayField(field)) {
                        allValid = false;
                    }
                });

                return allValid;
            }

            function validateForm() {
                const amountValid = validateAmount();
                const gatewayValid = validateGateway();
                const fieldsValid = validateAllGatewayFields();

                isFormValid = amountValid && gatewayValid && fieldsValid;

                const submitBtn = document.getElementById('submitWithdrawBtn');
                submitBtn.disabled = !isFormValid;

                if (isFormValid) {
                    submitBtn.classList.add('btn-success');
                    submitBtn.classList.remove('btn-primary');
                } else {
                    submitBtn.classList.remove('btn-success');
                    submitBtn.classList.add('btn-primary');
                }

                return isFormValid;
            }

            // Event Listeners
            document.addEventListener('DOMContentLoaded', function() {
                // Amount validation
                const amountInput = document.getElementById('withdrawAmount');
                if (amountInput) {
                    amountInput.addEventListener('input', validateForm);
                    amountInput.addEventListener('blur', validateAmount);
                }

                // Note character counter
                const noteInput = document.getElementById('withdrawNote');
                const noteCounter = document.getElementById('noteCounter');
                if (noteInput && noteCounter) {
                    noteInput.addEventListener('input', function() {
                        noteCounter.textContent = this.value.length;
                    });
                    noteCounter.textContent = noteInput.value.length;
                }

                // Gateway field validation - only for visible fields
                document.addEventListener('input', function(e) {
                    if (e.target.classList.contains('gateway-field')) {
                        // Check if field is visible
                        const parentFieldsDiv = e.target.closest('.gateway-specific-fields');
                        if (parentFieldsDiv && !parentFieldsDiv.classList.contains('d-none')) {
                            validateGatewayField(e.target);
                            validateForm();
                        }
                    }
                });

                document.addEventListener('blur', function(e) {
                    if (e.target.classList.contains('gateway-field')) {
                        // Check if field is visible
                        const parentFieldsDiv = e.target.closest('.gateway-specific-fields');
                        if (parentFieldsDiv && !parentFieldsDiv.classList.contains('d-none')) {
                            validateGatewayField(e.target);
                        }
                    }
                }, true);

                // Form submission
                const withdrawForm = document.getElementById('withdrawForm');
                if (withdrawForm) {
                    withdrawForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        if (!validateForm()) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please fix all errors before submitting'
                            });
                            return false;
                        }

                        const submitBtn = document.getElementById('submitWithdrawBtn');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="las la-spinner la-spin me-1"></i> Processing...';
                        }
                        this.submit();
                    });
                }
                // Initial validation
                setTimeout(() => validateForm(), 500);
            });

            window.validateWithdrawForm = validateForm;

        })();
    </script>
    <script>
        (function() {
            'use strict';

            console.log('Withdraw gateway handler initialized');

            // Global function to select gateway
            window.selectGateway = function(gatewayId) {
                console.log('Gateway selected:', gatewayId);

                // Update hidden input
                const hiddenInput = document.getElementById('selected_payment_gateway');
                if (hiddenInput) {
                    hiddenInput.value = gatewayId;
                }

                // Update UI - Remove all selected classes
                document.querySelectorAll('.gateway-card').forEach(card => {
                    card.classList.remove('selected');
                });

                // Add selected class to clicked card
                const selectedCard = document.querySelector(`[data-gateway-id="${gatewayId}"]`);
                if (selectedCard) {
                    selectedCard.classList.add('selected');
                }

                // Hide all gateway fields
                document.querySelectorAll('.gateway-specific-fields').forEach(field => {
                    field.classList.add('d-none');

                    // Remove required and clear validation
                    field.querySelectorAll('input, textarea, select').forEach(input => {
                        input.removeAttribute('required');
                        input.classList.remove('is-valid', 'is-invalid');
                        const feedback = input.parentElement.querySelector('.invalid-feedback');
                        const successFeedback = input.parentElement.querySelector('.valid-feedback');
                        if (feedback) feedback.textContent = '';
                        if (successFeedback) successFeedback.textContent = '';
                    });
                });

                // Show selected gateway fields
                const selectedFields = document.querySelector(`.gateway-specific-fields[data-gateway="${gatewayId}"]`);
                if (selectedFields) {
                    selectedFields.classList.remove('d-none');

                    // Add required back
                    selectedFields.querySelectorAll('.form-label').forEach(label => {
                        if (label.querySelector('.text-danger')) {
                            const formGroup = label.closest('.form-group');
                            const input = formGroup.querySelector('input, textarea, select');
                            if (input) {
                                input.setAttribute('required', 'required');
                            }
                        }
                    });

                    console.log('Fields shown for gateway:', gatewayId);
                }

                // Clear gateway error
                const gatewayError = document.getElementById('gatewayError');
                if (gatewayError) {
                    gatewayError.style.display = 'none';
                }

                // Trigger validation
                if (typeof window.validateWithdrawForm === 'function') {
                    setTimeout(() => window.validateWithdrawForm(), 100);
                }
            };

            // Initialize on modal shown
            $('#withdrawRequestModal').on('shown.bs.modal', function() {
                console.log('Withdraw modal opened');

                const hiddenInput = document.getElementById('selected_payment_gateway');
                if (hiddenInput && hiddenInput.value) {
                    setTimeout(() => selectGateway(hiddenInput.value), 200);
                }
            });

            // Success/Error messages
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session("success") }}',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                $('#withdrawRequestModal').modal('hide');
            });
            @endif

            @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session("error") }}',
                confirmButtonText: 'OK'
            });
            @endif

        })();
    </script>
@endsection
