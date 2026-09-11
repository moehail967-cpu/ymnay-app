@extends('tenant.frontend.frontend-page-master')

@section('title')
    @php $title = $order_details->title ?? ''; @endphp
    {{ sprintf('Order For: %s', $title) }}
@endsection

@section('page-title') {{ sprintf('Order For: %s', $title) }} @endsection

@section('content')
<div class="cs-result-page">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cs-dash-box">
                    <div class="cs-dash-box-head">
                        <i class="las la-cube"></i>
                        {{ get_static_option('order_page_form_title') ?? __('Order Package') }}
                    </div>
                    <div class="cs-dash-box-body">
                        {!! theme_flash_msg() !!}
                        {!! theme_error_msg() !!}

                        @if(!empty(get_static_option('guest_order_system_status')))

                            {{-- Guest / Login section --}}
                            @if(!auth()->guard('web')->check())
                            <div class="cs-pkg-login-wrap mb-4">
                                <div class="cs-pkg-guest-toggle mb-3">
                                    <label class="cs-pkg-guest-label d-flex align-items-center gap-2">
                                        <input type="checkbox" id="guest_logout" name="checkout_type">
                                        {{ __('Continue as Guest') }}
                                    </label>
                                </div>
                                <div class="cs-ajax-login-section" id="cs-pkg-login-section">
                                    @include('tenant.frontend.partials.ajax-login-form', ['title' => __('Sign in to order')])
                                </div>
                                <button class="cs-dash-submit-btn next-step-button mt-3" style="display:none;" type="button">
                                    {{ __('Next Step') }}
                                </button>
                            </div>
                            @else
                            <div class="cs-pkg-logged-in mb-3">
                                <i class="las la-check-circle cs-icon-success"></i> {{ __('Logged in as') }} <strong>{{ auth()->user()->name }}</strong>
                            </div>
                            @endif

                            {{-- Order form --}}
                            <div id="cs-pkg-order-form" @if(!auth()->guard('web')->check()) style="display:none;" @endif>
                                <form action="{{ theme_payment_form_url() }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @php
                                        $name  = auth()->check() ? auth()->guard('web')->user()->name  : '';
                                        $email = auth()->check() ? auth()->guard('web')->user()->email : '';
                                    @endphp
                                    <input type="hidden" name="package_id" value="{{ $order_details->id }}">
                                    <input type="hidden" name="package" value="{{ $order_details->id }}">
                                    <input type="hidden" name="payment_gateway" class="payment_gateway_passing_clicking_name">
                                    <input type="hidden" name="pkg_user_name" id="pkg_user_name" value="">
                                    <input type="hidden" name="pkg_user_email" id="pkg_user_email" value="">

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="cs-dash-label">{{ __('Name') }}</label>
                                            <input type="text" id="order_name" name="name" value="{{ $name }}" class="cs-dash-input" placeholder="{{ __('Your name') }}" @if(auth()->guard('web')->check()) readonly @endif required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="cs-dash-label">{{ __('Email') }}</label>
                                            <input type="email" id="order_email" name="email" value="{{ $email }}" class="cs-dash-input" placeholder="{{ __('Your email') }}" @if(auth()->guard('web')->check()) readonly @endif required>
                                        </div>
                                    </div>

                                    {!! \App\Helpers\FormBuilderCustom::render_form(get_static_option('order_form'), null, null, 'btn-default') !!}
                                    {!! render_payment_gateway_for_form() !!}

                                    <div class="mb-3 d-none manual_transaction_id">
                                        <label class="cs-dash-label">{{ __('Transaction ID') }}</label>
                                        <input type="text" name="trasaction_id" class="cs-dash-input" placeholder="{{ __('Transaction ID') }}">
                                    </div>

                                    <button class="cs-dash-submit-btn" type="submit" id="order_pkg_btn">
                                        <i class="las la-shopping-cart"></i> {{ __('Order Package') }}
                                    </button>
                                </form>
                            </div>

                        @else

                            {{-- Guest order not enabled — must login --}}
                            @if(!auth()->guard('web')->check())
                            <div class="mb-4">
                                @include('tenant.frontend.partials.ajax-login-form', ['title' => __('You must login or create an account to order your package!')])
                            </div>
                            @else
                            <div class="cs-pkg-logged-in mb-3">
                                <i class="las la-check-circle cs-icon-success"></i> {{ __('Logged in as') }} <strong>{{ auth()->user()->name }}</strong>
                            </div>
                            @endif

                            @if(auth()->guard('web')->check())
                            <form action="{{ theme_payment_form_url() }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                    $name  = auth()->guard('web')->user()->name;
                                    $email = auth()->guard('web')->user()->email;
                                @endphp
                                <input type="hidden" name="package_id" value="{{ $order_details->id }}">
                                <input type="hidden" name="package" value="{{ $order_details->id }}">
                                <input type="hidden" name="payment_gateway" class="payment_gateway_passing_clicking_name">

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="cs-dash-label">{{ __('Name') }}</label>
                                        <input type="text" name="name" value="{{ $name }}" class="cs-dash-input" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="cs-dash-label">{{ __('Email') }}</label>
                                        <input type="email" name="email" value="{{ $email }}" class="cs-dash-input" readonly>
                                    </div>
                                </div>

                                {!! \App\Helpers\FormBuilderCustom::render_form(get_static_option('order_form'), null, null, 'btn-default') !!}
                                {!! render_payment_gateway_for_form() !!}

                                <div class="mb-3 d-none manual_transaction_id">
                                    <label class="cs-dash-label">{{ __('Transaction ID') }}</label>
                                    <input type="text" name="trasaction_id" class="cs-dash-input" placeholder="{{ __('Transaction ID') }}">
                                </div>

                                <button class="cs-dash-submit-btn" type="submit">
                                    <i class="las la-shopping-cart"></i> {{ __('Order Package') }}
                                </button>
                            </form>
                            @endif

                        @endif
                    </div>
                </div>
            </div>

            {{-- Package summary --}}
            <div class="col-lg-4">
                <div class="cs-dash-box">
                    <div class="cs-dash-box-head">
                        <i class="las la-tag"></i> {{ $order_details->title }}
                    </div>
                    <div class="cs-dash-box-body">
                        <div class="cs-dash-total-value mb-3">
                            {{ amount_with_currency_symbol($order_details->price) }}
                            <small class="cs-price-period">/ {{ $order_details->type }}</small>
                        </div>
                        <ul class="cs-pkg-features">
                            @foreach(explode("\n", $order_details->features) as $item)
                            <li><i class="las la-check-circle"></i> {{ trim($item) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';
    $(document).ready(function () {

        $(document).on('click', '#order_pkg_btn', function () {
            sessionStorage.pkg_user_name  = $('#order_name').val();
            sessionStorage.pkg_user_email = $('#order_email').val();
        });

        // Guest toggle
        $(document).on('change', '#guest_logout', function () {
            if ($(this).is(':checked')) {
                $('#cs-pkg-login-section').hide();
                $('.next-step-button').show();
            } else {
                $('#cs-pkg-login-section').show();
                $('.next-step-button').hide();
                $('#cs-pkg-order-form').hide();
            }
        });

        $(document).on('click', '.next-step-button', function () {
            $('#cs-pkg-order-form').show();
            $(this).hide();
        });

        // Ajax login
        $(document).on('click', '#login_btn', function (e) {
            e.preventDefault();
            var btn  = $(this);
            var form = $('#login_form_order_page');
            btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}').prop('disabled', true);
            $.ajax({
                type: 'POST',
                url: '{{ theme_ajax_login_url() }}',
                data: {
                    _token:   '{{ csrf_token() }}',
                    username: form.find('[name=username]').val(),
                    password: form.find('[name=password]').val(),
                    remember: form.find('[name=remember]').is(':checked') ? 1 : 0,
                },
                success: function (data) {
                    if (data.status === 'invalid') {
                        btn.html('<i class="las la-sign-in-alt"></i> {{ __("Sign In") }}').prop('disabled', false);
                        form.find('.error-wrap').html('<div class="alert alert-danger">' + data.msg + '</div>');
                    } else {
                        btn.html('{{ __("Redirecting…") }}');
                        location.reload();
                    }
                },
                error: function (xhr) {
                    btn.html('<i class="las la-sign-in-alt"></i> {{ __("Sign In") }}').prop('disabled', false);
                    var errors = xhr.responseJSON?.errors || {};
                    var list = '<ul class="alert alert-danger">';
                    $.each(errors, function (k, v) { list += '<li>' + v + '</li>'; });
                    form.find('.error-wrap').html(list + '</ul>');
                }
            });
        });

        // Gateway selection
        var defaultGateway = $('#site_global_payment_gateway').val();
        $('.payment-gateway-wrapper ul li[data-gateway="' + defaultGateway + '"]').addClass('selected');

        $(document).on('click', '.payment-gateway-wrapper > ul > li', function (e) {
            e.preventDefault();
            var gateway = $(this).data('gateway');
            if (gateway === 'manual_payment') {
                $('.manual_transaction_id').removeClass('d-none');
            } else {
                $('.manual_transaction_id').addClass('d-none');
            }
            $(this).addClass('selected').siblings().removeClass('selected');
            $('.payment_gateway_passing_clicking_name').val(gateway);
        });

    });
})(jQuery);
</script>
@endsection
