
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

@section('section')
        <div>
            <h2 class="dashboards-title">{{ __('Withdraw History') }}</h2>
            <div class="notice-board">
            </div>
        </div>
        {{-- withdraw Log Table --}}
        <table class="custom--table commission-table">
            <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Method') }}</th>
                <th>{{ __('Date') }}</th>
            </tr>
            </thead>
            <tbody class="">
            @forelse($withdraw_request_logs as $log)
                {{-- @dd($log) --}}
                <tr>
                    <td data-label="{{ __('ID') }}">{{ $log->id }}</td>
                    <td data-label="{{ __('Amount') }}">{{ float_amount_with_currency_symbol($log->amount) }}</td>
                    <td data-label="{{ __('Status') }}">
                            <span class="badge bg-{{
                                $log->status == 'complete' ? 'success' :
                                ($log->status == 'cancel' ? 'danger' : 'warning')
                            }}">
                                {{ ucfirst($log->status) }}
                            </span>
                    </td>
                    <td data-label="{{ __('Method') }}">{{ $log->payment_method ?? 'N/A' }}</td>
                    <td data-label="{{ __('Date') }}">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center mt-2">{{ __('No commission payment logs found') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="blog-pagination margin-top-55">
            <div class="custom-pagination mt-4 mt-lg-5">
                {!! $withdraw_request_logs->links() !!}
                {{-- Add pagination for commission logs if needed --}}
            </div>
        </div>

@endsection
