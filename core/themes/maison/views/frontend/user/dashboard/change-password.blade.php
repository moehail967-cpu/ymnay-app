@extends(include_theme_path('user.user-master'))

@section('title') {{ __('Change Password') }} @endsection

@section('dashboard_content')

<div class="ms-dash-section-title">
    <i class="mdi mdi-lock-reset" style="margin-right:6px;color:var(--ms-olive);"></i>
    {{ __('Change Password') }}
</div>

<div class="ms-dash-card" style="max-width:480px;">
    <form class="change_password_form">
        @csrf
        <div class="ms-form-group">
            <label class="ms-form-label">{{ __('Current Password') }}</label>
            <input type="password" name="current_password" class="ms-form-input"
                   placeholder="{{ __('Current password') }}" required>
        </div>
        <div class="ms-form-group">
            <label class="ms-form-label">{{ __('New Password') }}</label>
            <input type="password" name="new_password" class="ms-form-input"
                   placeholder="{{ __('New password') }}" required>
        </div>
        <div class="ms-form-group">
            <label class="ms-form-label">{{ __('Confirm New Password') }}</label>
            <input type="password" name="new_password_confirmation" class="ms-form-input"
                   placeholder="{{ __('Confirm new password') }}" required>
        </div>
        <button type="submit" class="ms-btn-dark" style="margin-top:8px;">
            <i class="mdi mdi-lock-check-outline" style="margin-right:6px;"></i>
            {{ __('Update Password') }}
        </button>
    </form>
</div>

@endsection

@section('dashboard-scripts')
<script>
$(document).on('submit', '.change_password_form', function(e){
    e.preventDefault();
    $.ajax({
        url: '{{ theme_user_password_change_url() }}',
        type: 'POST', processData: false, contentType: false,
        data: new FormData(e.target),
        beforeSend: function(){ $('.loader').show(); },
        success: function(data){
            $('.loader').hide();
            if(data.type === 'success'){
                toastr.success(data.msg);
                toastr.warning('{{ __("Logging you out for security…") }}');
                setTimeout(function(){ location.href = data.url; }, 3000);
            } else {
                toastr.error(data.msg);
            }
        },
        error: function(data){
            $('.loader').hide();
            var r = JSON.parse(data.responseText);
            $.each(r.errors, function(k, v){ toastr.error(v); });
        }
    });
});
</script>
@endsection
