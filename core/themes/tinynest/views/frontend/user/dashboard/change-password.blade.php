@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Change Password') }} @endsection

@section('section')

<div class="tn-dash-section-title">
    <i class="mdi mdi-lock-reset" style="color:var(--tn-rose);"></i> {{ __('Change Password') }}
</div>

<div class="tn-dash-box" style="max-width:540px;padding:28px;">
    <form action="{{ theme_user_password_change_url() }}" method="POST" class="change_password_form">
        @csrf
        <div class="mb-3">
            <label class="tn-label">{{ __('Current Password') }} <span class="tn-required">*</span></label>
            <input type="password" name="old_password" class="tn-input" placeholder="{{ __('Current password') }}">
        </div>
        <div class="mb-3">
            <label class="tn-label">{{ __('New Password') }} <span class="tn-required">*</span></label>
            <input type="password" name="password" class="tn-input" placeholder="{{ __('New password') }}">
        </div>
        <div class="mb-4">
            <label class="tn-label">{{ __('Confirm New Password') }} <span class="tn-required">*</span></label>
            <input type="password" name="password_confirmation" class="tn-input" placeholder="{{ __('Confirm new password') }}">
        </div>
        <button type="submit" class="tn-btn tn-btn-rose">
            <i class="mdi mdi-lock-check-outline"></i> {{ __('Update Password') }}
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
            } else { toastr.error(data.msg); }
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
