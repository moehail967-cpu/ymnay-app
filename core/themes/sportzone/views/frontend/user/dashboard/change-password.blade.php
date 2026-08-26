@extends(include_theme_path('user.dashboard.user-master'))
@section('title') {{ __('Change Password') }} @endsection

@section('dashboard_content')

<div class="sz-dash-section-title">{{ __('Change Password') }}</div>

<div class="sz-dash-card">
    <form method="POST" action="{{ theme_user_password_change_url() }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-12">
                <div class="sz-dash-form-group">
                    <label class="sz-dash-form-label">{{ __('Current Password') }} <span style="color:var(--sz-red);">*</span></label>
                    <input type="password" name="current_password" class="sz-dash-form-input" placeholder="{{ __('••••••••') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="sz-dash-form-group">
                    <label class="sz-dash-form-label">{{ __('New Password') }} <span style="color:var(--sz-red);">*</span></label>
                    <input type="password" name="password" class="sz-dash-form-input" placeholder="{{ __('Min. 8 characters') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="sz-dash-form-group">
                    <label class="sz-dash-form-label">{{ __('Confirm New Password') }} <span style="color:var(--sz-red);">*</span></label>
                    <input type="password" name="password_confirmation" class="sz-dash-form-input" placeholder="{{ __('Repeat new password') }}" required>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="sz-dash-btn sz-dash-btn-red">
                    <i class="mdi mdi-lock-reset"></i> {{ __('Update Password') }}
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
