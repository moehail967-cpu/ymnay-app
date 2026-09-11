@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Change Password') }} @endsection
@section('dash-title') {{ __('Change Password') }} @endsection

@section('dashboard-content')
<div class="pf-dash-card">
    <div class="pf-dash-card-title"><i class="las la-lock"></i> {{ __('Change Password') }}</div>
    <form action="{{ theme_user_password_change_url() }}" method="post">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="pf-label">{{ __('Current Password') }} <span class="pf-required">*</span></label>
                <input type="password" name="current_password" class="pf-input" placeholder="{{ __('Enter current password') }}">
            </div>
            <div class="col-md-6">
                <label class="pf-label">{{ __('New Password') }} <span class="pf-required">*</span></label>
                <input type="password" name="new_password" class="pf-input" placeholder="{{ __('Enter new password') }}">
            </div>
            <div class="col-md-6">
                <label class="pf-label">{{ __('Confirm Password') }} <span class="pf-required">*</span></label>
                <input type="password" name="new_password_confirmation" class="pf-input" placeholder="{{ __('Confirm new password') }}">
            </div>
            <div class="col-12">
                <button type="submit" id="change_pwd_btn" class="pf-btn pf-btn-teal">
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
