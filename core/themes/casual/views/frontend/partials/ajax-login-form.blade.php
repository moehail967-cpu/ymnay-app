@php $title = $title ?? __('Login to continue'); @endphp

<div class="cs-ajax-login-wrap">
    @if($title)
    <p class="cs-ajax-login-notice">{{ $title }}</p>
    @endif
    <form action="{{ theme_ajax_login_url() }}" method="post" class="account-form" id="login_form_order_page">
        @csrf
        <div class="error-wrap mb-3"></div>
        <div class="mb-3">
            <label class="cs-dash-label">{{ __('Username or Email') }}</label>
            <input type="text" name="username" class="cs-dash-input" placeholder="{{ __('your@email.com') }}">
        </div>
        <div class="mb-3">
            <label class="cs-dash-label">{{ __('Password') }}</label>
            <input type="password" name="password" class="cs-dash-input" placeholder="••••••••">
        </div>
        <div class="cs-ajax-login-row mb-3">
            <label class="cs-ajax-login-remember">
                <input type="checkbox" name="remember"> {{ __('Remember Me') }}
            </label>
            <a href="{{ theme_forget_password_url() }}" class="cs-ajax-login-forgot">{{ __('Forgot Password?') }}</a>
        </div>
        <button type="submit" id="login_btn" class="cs-dash-submit-btn w-100">
            <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
        </button>
        <p class="cs-auth-switch-note mt-3">
            {{ __("Don't have an account?") }}
            <a href="{{ theme_register_url() }}" class="cs-auth-switch-link">{{ __('Create one') }}</a>
        </p>
    </form>
</div>
