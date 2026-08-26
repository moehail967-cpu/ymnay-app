@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Change Password') }} @endsection

@section('dashboard_content')

<div style="font-size:18px;font-weight:700;color:#fff;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="mdi mdi-lock-outline" style="color:var(--tz-blue);"></i> {{ __('Change Password') }}
</div>

<div class="tz-dash-card tz-dash-card-body" style="max-width:520px;">
    <form method="POST" action="{{ theme_user_password_change_url() }}">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="tz-dash-label">{{ __('Current Password') }} <span style="color:#ef4444;">*</span></label>
                <input type="password" name="current_password" class="tz-dash-input" placeholder="••••••••" required>
            </div>
            <div class="col-md-6">
                <label class="tz-dash-label">{{ __('New Password') }} <span style="color:#ef4444;">*</span></label>
                <input type="password" name="password" class="tz-dash-input" placeholder="{{ __('Min. 8 characters') }}" required>
            </div>
            <div class="col-md-6">
                <label class="tz-dash-label">{{ __('Confirm New Password') }} <span style="color:#ef4444;">*</span></label>
                <input type="password" name="password_confirmation" class="tz-dash-input" placeholder="{{ __('Repeat new password') }}" required>
            </div>
            <div class="col-12">
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:8px;background:var(--tz-blue);color:#fff;border:0;padding:11px 22px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:background .2s;"
                        onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                    <i class="mdi mdi-lock-reset"></i> {{ __('Update Password') }}
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
