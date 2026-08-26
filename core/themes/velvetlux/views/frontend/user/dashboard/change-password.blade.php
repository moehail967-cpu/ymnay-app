@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Change Password') }} @endsection
@section('page-title') {{ __('Change Password') }} @endsection

@section('dashboard-content')
<div class="vl-dash-card">
    <div class="vl-dash-card-header">{{ __('Change Password') }}</div>
    <div class="vl-dash-card-body">
        <form id="user_password_change_form">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="vl-dash-label">{{ __('Current Password') }}</label>
                    <input type="password" name="current_password" class="vl-dash-input" placeholder="••••••••">
                </div>
                <div class="col-md-6">
                    <label class="vl-dash-label">{{ __('New Password') }}</label>
                    <input type="password" name="new_password" class="vl-dash-input" placeholder="••••••••">
                </div>
                <div class="col-md-6">
                    <label class="vl-dash-label">{{ __('Confirm New Password') }}</label>
                    <input type="password" name="new_password_confirmation" class="vl-dash-input" placeholder="••••••••">
                </div>
                <div class="col-12">
                    <button type="submit" class="vl-btn vl-btn-primary" style="font-size:10px;letter-spacing:3px;">
                        <i class="mdi mdi-lock-reset"></i> {{ __('Update Password') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('dashboard-scripts')
<script>
$(document).on('submit', '#user_password_change_form', function (e) {
    e.preventDefault();
    $.ajax({
        url: '{{ theme_user_password_change_url() }}',
        type: 'POST',
        data: $(this).serialize(),
        success: function (d) { d.type === 'success' ? toastr.success(d.msg) : toastr.error(d.msg); },
        error: function (xhr) { $.each(xhr.responseJSON?.errors ?? {}, (k, v) => toastr.error(v)); }
    });
});
</script>
@endsection
