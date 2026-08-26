@extends(theme_view('frontend.user.user-master'))
@section('dash-title') {{ __('Change Password') }} @endsection

@section('dash-content')
{!! theme_error_msg() !!}
{!! theme_flash_msg() !!}

<div class="lg-dash-card">
    <div class="lg-dash-card-title">{{ __('Change Password') }}</div>
    <form action="{{ theme_user_password_change_url() }}" method="POST" style="max-width:480px;">
        @csrf
        <div class="mb-3">
            <label class="lg-dash-label">{{ __('Current Password') }}</label>
            <input type="password" name="current_password" class="lg-dash-input" placeholder="{{ __('Current password') }}">
        </div>
        <div class="mb-3">
            <label class="lg-dash-label">{{ __('New Password') }}</label>
            <input type="password" name="new_password" class="lg-dash-input" placeholder="{{ __('New password') }}">
        </div>
        <div class="mb-4">
            <label class="lg-dash-label">{{ __('Confirm New Password') }}</label>
            <input type="password" name="new_password_confirmation" class="lg-dash-input" placeholder="{{ __('Confirm new password') }}">
        </div>
        <button type="submit" class="lg-dash-btn lg-dash-btn-gold">
            <i class="las la-lock"></i> {{ __('Update Password') }}
        </button>
    </form>
</div>
@endsection
