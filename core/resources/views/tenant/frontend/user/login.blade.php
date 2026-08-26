@extends('tenant.frontend.frontend-page-master')

@section('title')
    {{__('User Login')}}
@endsection

@section('page-title')
    {{__('User Login')}}
@endsection

@section('style')
<style>
.social-login-divider{display:flex;align-items:center;margin:18px 0;gap:10px;color:#999;font-size:13px}
.social-login-divider::before,.social-login-divider::after{content:'';flex:1;height:1px;background:#e5e5e5}
.btn-google-login{display:flex;align-items:center;justify-content:center;width:100%;padding:10px 16px;border:1px solid #dadce0;border-radius:6px;background:#fff;color:#3c4043;font-size:14px;font-weight:500;text-decoration:none;transition:background .15s,box-shadow .15s}
.btn-google-login:hover{background:#f8f9fa;box-shadow:0 1px 4px rgba(0,0,0,.1);color:#3c4043;text-decoration:none}
</style>
@endsection
@section('content')
    <!-- sign-in area start -->
    <div class="sign-in-area-wrapper" data-padding-top="50" data-padding-bottom="50">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="sign-in register">
                        <h4 class="title">{{__('Sign In')}}</h4>
                        <div class="form-wrapper">
                            {!! theme_error_msg() !!}
                            {!! theme_flash_msg() !!}
                            <form action="" method="post" enctype="multipart/form-data" class="account-form" id="login_form_order_page">
                                <div class="error-wrap"></div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{__('Email or Username')}}<x-fields.mandatory-indicator/></label>
                                    <input type="text" name="username" class="form-control" id="exampleInputEmail1" placeholder="{{__('Type Your Email or Username')}}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{__('Password')}}<x-fields.mandatory-indicator/></label>
                                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="{{__('Password')}}">
                                </div>

                                <div class="form-group form-check">
                                    <div class="box-wrap">
                                        <div class="left">
                                            <input type="checkbox" name="remember" class="form-check-input" id="exampleCheck1">
                                            <label class="form-check-label" for="exampleCheck1">{{__('Remember Me')}}</label>
                                        </div>
                                        <div class="right">
                                            <a href="{{route('tenant.user.forget.password')}}">{{__('Forgot Password?')}}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-wrapper text-center d-flex justify-content-between gap-5">
                                    <button type="submit" id="login_btn" class="btn-default rounded-btn w-100">{{__('Login')}}</button>

                                    @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                                        <a href="{{route('tenant.user.login.otp')}}" id="otp_login_btn" class="btn-default rounded-btn w-100">{{__('Login with OTP')}}</a>
                                    @endif
                                </div>

                            </form>
                            <p class="info">{{__("Do not have an account?")}} <a href="{{route('tenant.user.register')}}" class="active">{{__('Sign up')}}</a></p>
                            @include('socialauth::partials.google-button')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- sign-in area end -->
@endsection
@section('scripts')
   {!! theme_ajax_login_js() !!}
@endsection
