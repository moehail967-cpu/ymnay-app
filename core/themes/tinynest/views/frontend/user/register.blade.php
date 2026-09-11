@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Register') }} @endsection
@section('page-title') {{ __('Register') }} @endsection

@section('content')
<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{{ __('Create Account') }}</h1>
        <div class="tn-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:48px 16px 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="tn-auth-card">
                <div class="tn-auth-icon">🌿</div>
                <h2 class="tn-auth-title">{{ __('Join TinyNest') }}</h2>
                <p class="tn-auth-sub">{{ __('Create an account for a cuter shopping experience.') }}</p>

                {!! theme_flash_msg() !!}
                {!! theme_error_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="POST" id="tn_register_form">
                    {!! theme_csrf_field() !!}
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="tn-label">{{ __('Full Name') }} *</label>
                            <input type="text" name="name" class="tn-input" value="{{ old('name') }}" placeholder="{{ __('Your full name') }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="tn-label">{{ __('Username') }} *</label>
                            <input type="text" name="username" class="tn-input" value="{{ old('username') }}" placeholder="{{ __('Choose a username') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="tn-label">{{ __('Email Address') }} *</label>
                            <input type="email" name="email" class="tn-input" value="{{ old('email') }}"
                                   placeholder="{{ __('you@example.com') }}" required>
                        </div>



                        <div class="col-12">
                            <label class="tn-label">{{ __('Password') }} *</label>
                            <div class="tn-input-wrap">
                                <input type="password" name="password" id="tn_reg_pwd" class="tn-input"
                                       placeholder="{{ __('Min 8 characters') }}" required>
                                <button type="button" class="tn-pwd-toggle" onclick="tnTogglePwd2()">
                                    <i class="las la-eye" id="tn_reg_pwd_icon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="tn-label">{{ __('Confirm Password') }} *</label>
                            <input type="password" name="password_confirmation" class="tn-input"
                                   placeholder="{{ __('Repeat password') }}" required>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="tn-btn tn-btn-primary w-100"
                                    style="justify-content:center;border-radius:12px;font-size:15px;" id="tn_reg_submit">
                                {{ __('Create Account') }}
                            </button>
                        </div>
                    </div>
                </form>

                <p class="text-center mt-4" style="font-size:14px;color:var(--tn-muted);">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--tn-purple);font-weight:700;">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function tnTogglePwd2() {
    var inp  = document.getElementById('tn_reg_pwd');
    var icon = document.getElementById('tn_reg_pwd_icon');
    if (inp.type === 'password') { inp.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { inp.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
{!! theme_generate_password_js('#tn_gen_pwd_btn', '#tn_reg_pwd') !!}
<script>
(function ($) {
    // show loading state on form submit
    $('form').on('submit', function () {
        var $btn = $('#tn_reg_submit');
        $btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
        $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
    });

    $('#tn_reg_country').on('change', function () {
        var country = $(this).val();
        if (!country) return;
        $.post('{{ theme_checkout_state_url() }}', { _token: '{{ theme_csrf() }}', country_id: country }, function (res) {
            var $state = $('#tn_reg_state').empty().append('<option value="">{{ __("Select State") }}</option>');
            if (res.states) {
                $.each(res.states, function (i, s) {
                    $state.append('<option value="' + s.id + '">' + s.name + '</option>');
                });
            }
        });
    });
})(jQuery);
</script>
@endsection
