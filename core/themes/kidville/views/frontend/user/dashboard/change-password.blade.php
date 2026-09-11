@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Change Password') }} @endsection

@section('section')

<div style="font-size:13px;font-weight:800;color:var(--kv-red);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="las la-key"></i> {{ __('Change Password') }}
</div>

<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:28px;max-width:480px;box-shadow:var(--kv-shadow);">
    <form class="change_password_form">
        @csrf
        <div class="kv-form-group">
            <label class="kv-label">{{ __('Current Password') }} <span class="kv-required">*</span></label>
            <input type="password" name="current_password" class="kv-input" placeholder="{{ __('Current password') }}">
        </div>
        <div class="kv-form-group">
            <label class="kv-label">{{ __('New Password') }} <span class="kv-required">*</span></label>
            <input type="password" name="new_password" class="kv-input" placeholder="{{ __('New password') }}">
        </div>
        <div class="kv-form-group">
            <label class="kv-label">{{ __('Confirm New Password') }} <span class="kv-required">*</span></label>
            <input type="password" name="new_password_confirmation" class="kv-input" placeholder="{{ __('Confirm new password') }}">
        </div>
        <button type="submit" class="kv-btn kv-btn-red" style="margin-top:8px;">
            <i class="las la-lock"></i> {{ __('Update Password') }}
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
            $.each(r.errors, function(k,v){ toastr.error(v); });
        }
    });
});
</script>
@endsection
