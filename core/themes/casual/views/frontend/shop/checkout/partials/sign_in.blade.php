<span class="cs-checkout-login-hint">
    <i class="las la-exclamation-circle"></i> {{ __('Returning Customer?') }}
    <a class="click-open-form cs-checkout-login-link" href="javascript:void(0)">{{ __('Click here to Login') }}</a>
</span>

<div class="checkout-form-open">
    <div class="cs-checkout-signin-box">
        <h5 class="cs-checkout-signin-title">{{ __('Sign In') }}</h5>
        <div id="login_form_order_page">
            @csrf
            <div class="error-wrap cs-form-error"></div>
            <div class="cs-form-group">
                <input type="text" name="username" class="cs-dash-input"
                       placeholder="{{ __('Username') }}">
            </div>
            <div class="cs-form-group">
                <input type="password" name="password" class="cs-dash-input"
                       placeholder="{{ __('Password') }}">
            </div>
            <div class="cs-form-group">
                <button class="cs-checkout-btn" id="login_btn" type="submit">{{ __('Login') }}</button>
            </div>
            <div class="cs-checkout-signin-links">
                <label class="cs-checkout-remember">
                    <input type="checkbox" name="remember"> {{ __('Remember Me') }}
                </label>
                <div class="cs-checkout-signin-extras">
                    <a href="{{ theme_register_url() }}">{{ __('Create new account?') }}</a>
                    <a href="{{ theme_forget_password_url() }}">{{ __('Forgot Password?') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
