@extends(include_theme_path('user.user-master'))

@section('title') {{ __('Change Password') }} @endsection
@section('dash-title') {{ __('Change Password') }} @endsection

@section('dashboard-content')
<div class="ph-dash-card">
    <div class="ph-dash-card-title"><i class="las la-lock"></i> {{ __('Change Password') }}</div>
    <form action="{{ theme_user_password_change_url() }}" method="post">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="ph-label">{{ __('Current Password') }} <span class="ph-required">*</span></label>
                <input type="password" name="current_password" class="ph-input" placeholder="{{ __('Enter current password') }}">
            </div>
            <div class="col-md-6">
                <label class="ph-label">{{ __('New Password') }} <span class="ph-required">*</span></label>
                <input type="password" name="new_password" class="ph-input" placeholder="{{ __('Enter new password') }}">
            </div>
            <div class="col-md-6">
                <label class="ph-label">{{ __('Confirm Password') }} <span class="ph-required">*</span></label>
                <input type="password" name="new_password_confirmation" class="ph-input" placeholder="{{ __('Confirm new password') }}">
            </div>
            <div class="col-12">
                <button type="submit" id="change_pwd_btn" class="ph-btn ph-btn-terra">
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
