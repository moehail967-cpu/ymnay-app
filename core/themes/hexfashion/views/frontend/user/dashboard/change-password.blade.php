@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Change Password') }} @endsection
@section('dash-title') {{ __('Change Password') }} @endsection

@section('dashboard-content')
<div class="hf-dash-card">
    <div class="hf-dash-card-title"><i class="las la-lock"></i> {{ __('Change Password') }}</div>
    <form action="{{ theme_user_password_change_url() }}" method="post">
        @csrf
        <div class="row g-3" style="max-width:500px;">
            <div class="col-12">
                <label class="hf-form-label">{{ __('Current Password') }} <span class="hf-form-required">*</span></label>
                <input type="password" name="current_password" class="hf-form-input" placeholder="{{ __('Enter current password') }}">
            </div>
            <div class="col-12">
                <label class="hf-form-label">{{ __('New Password') }} <span class="hf-form-required">*</span></label>
                <input type="password" name="new_password" class="hf-form-input" placeholder="{{ __('Enter new password') }}">
            </div>
            <div class="col-12">
                <label class="hf-form-label">{{ __('Confirm Password') }} <span class="hf-form-required">*</span></label>
                <input type="password" name="new_password_confirmation" class="hf-form-input" placeholder="{{ __('Confirm new password') }}">
            </div>
            <div class="col-12">
                <button type="submit" id="change_pwd_btn" class="hf-btn hf-btn-primary">
                    <i class="las la-lock"></i> {{ __('Update Password') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
{!! theme_btn_loading_js('change_pwd_btn', __('Updating…')) !!}
@endsection
