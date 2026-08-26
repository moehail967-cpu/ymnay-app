@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Register') }} @endsection
@section('page-title') {{ __('Register') }} @endsection

@section('content')
<div class="fn-page-banner">
    <div class="container">
        <h1>{{ __('Create Account') }}</h1>
        <div class="fn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="mx-auto fn-auth-max-wrap">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}
        <div class="fn-auth-card">
            <h2 class="fn-auth-title">{{ __('Create Your Account') }}</h2>
            <p class="fn-auth-sub">{{ __('Already have an account?') }} <a href="{{ route('tenant.user.login') }}" class="fn-auth-switch">{{ __('Sign In') }}</a></p>

            <form action="{{ theme_register_store_url() }}" method="post" id="register_form">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Full Name') }} <span class="fn-required">*</span></label>
                        <input type="text" name="name" class="fn-input" placeholder="{{ __('Jane Smith') }}" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Username') }} <span class="fn-required">*</span></label>
                        <input type="text" name="username" class="fn-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Email Address') }} <span class="fn-required">*</span></label>
                        <input type="email" name="email" class="fn-input" placeholder="{{ __('your@email.com') }}" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Password') }} <span class="fn-required">*</span></label>
                        <div class="fn-input-wrap">
                            <input type="password" name="password" id="fn_reg_pwd" class="fn-input" placeholder="{{ __('Min. 8 characters') }}" required>
                            <button type="button" class="fn-input-eye" onclick="fnTogglePwd('fn_reg_pwd',this)"><i class="las la-eye"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Confirm Password') }} <span class="fn-required">*</span></label>
                        <input type="password" name="password_confirmation" class="fn-input" placeholder="{{ __('Repeat your password') }}" required>
                    </div>

                    {{-- Address --}}
                    <div class="col-12"><hr class="fn-divider"><p class="fn-section-label">{{ __('Delivery Address') }} <span class="fn-muted">({{ __('optional') }})</span></p></div>
                    
                    
                    
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Zip / Postal Code') }}</label>
                        <input type="text" name="zipcode" class="fn-input" placeholder="{{ __('Zip code') }}" value="{{ old('zipcode') }}">
                    </div>
                    <div class="col-12">
                        <label class="fn-label">{{ __('Address') }}</label>
                        <textarea name="address" class="fn-input fn-textarea" rows="2" placeholder="{{ __('Street address') }}">{{ old('address') }}</textarea>
                    </div>
                </div>

                <div class="fn-terms-check mt-3">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">{{ __('I agree to the') }} <a href="#">{{ __('Terms of Service') }}</a> &amp; <a href="#">{{ __('Privacy Policy') }}</a></label>
                </div>

                <button type="submit" id="register" class="fn-btn fn-btn-gold w-100 fn-auth-submit mt-3">
                    <i class="las la-user-plus"></i> {{ __('Create Account') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_generate_password_js() !!}
<script>
function fnTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}

$(function () {
    // State AJAX
    $(document).on('keyup', '#register_state', function () {
        var q = $(this).val(), country = $('#register_country').val();
        if (!q) { $('#register_state_dropdown').empty().hide(); return; }
        $.get('{{ theme_state_search_url() }}', { search: q, country: country }, function (data) {
            var $ul = $('#register_state_dropdown').empty();
            if (data.length) {
                $.each(data, function (i, s) {
                    $ul.append($('<li>').text(s.name).on('click', function () {
                        $('#register_state').val(s.name);
                        $ul.empty().hide();
                    }));
                });
                $ul.show();
            } else { $ul.hide(); }
        });
    });

    // City AJAX
    $(document).on('keyup', '#register_city', function () {
        var q = $(this).val(), state = $('#register_state').val();
        if (!q) { $('#register_city_dropdown').empty().hide(); return; }
        $.get('{{ theme_city_search_url() }}', { search: q, state: state }, function (data) {
            var $ul = $('#register_city_dropdown').empty();
            if (data.length) {
                $.each(data, function (i, c) {
                    $ul.append($('<li>').text(c.name).on('click', function () {
                        $('#register_city').val(c.name);
                        $ul.empty().hide();
                    }));
                });
                $ul.show();
            } else { $ul.hide(); }
        });
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#register_state_wrap').length) $('#register_state_dropdown').empty().hide();
        if (!$(e.target).closest('#register_city_wrap').length) $('#register_city_dropdown').empty().hide();
    });

    // Submit loading state
    $('#register_form').on('submit', function () {
        var btn = $('#register');
        btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}').css({'opacity':'0.7','pointer-events':'none'});
    });
});
</script>
@endsection
