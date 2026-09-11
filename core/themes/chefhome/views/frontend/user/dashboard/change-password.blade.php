@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Change Password') }} @endsection

@section('section')

<div style="font-weight:800;color:var(--ch-dark);margin-bottom:20px;font-size:15px;">
    <i class="las la-lock" style="color:var(--ch-red);"></i> {{ __('Change Password') }}
</div>

<div class="ch-dash-card" style="max-width:480px;">
    <div class="ch-dash-card-body">
        <form class="change_password_form">
            @csrf
            <div style="margin-bottom:16px;">
                <label class="ch-dash-label">{{ __('Current Password') }} *</label>
                <input type="password" name="current_password" class="ch-dash-input" placeholder="{{ __('Current password') }}">
            </div>
            <div style="margin-bottom:16px;">
                <label class="ch-dash-label">{{ __('New Password') }} *</label>
                <input type="password" name="new_password" class="ch-dash-input" placeholder="{{ __('New password') }}">
            </div>
            <div style="margin-bottom:24px;">
                <label class="ch-dash-label">{{ __('Confirm New Password') }} *</label>
                <input type="password" name="new_password_confirmation" class="ch-dash-input" placeholder="{{ __('Confirm new password') }}">
            </div>
            <button type="submit" class="ch-btn ch-btn-primary" style="padding:12px 28px;">
                <i class="las la-check-circle"></i> {{ __('Update Password') }}
            </button>
        </form>
    </div>
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
