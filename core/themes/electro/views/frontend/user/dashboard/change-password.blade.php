@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Change Password') }} @endsection
@section('dash-title') {{ __('Change Password') }} @endsection

@section('dashboard-content')
<div class="el-dash-card">
    <div class="el-dash-card-title"><i class="las la-lock"></i> {{ __('Change Password') }}</div>
    <form action="{{ theme_user_password_change_url() }}" method="post">
        @csrf
        <div class="row g-3" style="max-width:500px;">
            <div class="col-12">
                <label class="el-form-label">{{ __('Current Password') }} <span class="el-form-required">*</span></label>
                <input type="password" name="current_password" class="el-form-input" placeholder="{{ __('Enter current password') }}">
            </div>
            <div class="col-12">
                <label class="el-form-label">{{ __('New Password') }} <span class="el-form-required">*</span></label>
                <input type="password" name="new_password" class="el-form-input" placeholder="{{ __('Enter new password') }}">
            </div>
            <div class="col-12">
                <label class="el-form-label">{{ __('Confirm Password') }} <span class="el-form-required">*</span></label>
                <input type="password" name="new_password_confirmation" class="el-form-input" placeholder="{{ __('Confirm new password') }}">
            </div>
            <div class="col-12">
                <button type="submit" id="change_pwd_btn" class="el-btn el-btn-primary">
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
