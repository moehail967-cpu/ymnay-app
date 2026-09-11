@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Change Password') }} @endsection

@section('section')

<div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="las la-key"></i> {{ __('Change Password') }}
</div>

<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:28px;max-width:480px;box-shadow:var(--gc-shadow);">
    <form class="change_password_form">
        @csrf
        @php
            $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:14px;font-family:Georgia,serif;outline:none;background:#fff;transition:border-color .2s;color:var(--gc-dark);';
            $lbl = 'font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-muted);display:block;margin-bottom:6px;';
        @endphp
        <div style="margin-bottom:16px;">
            <label style="{{ $lbl }}">{{ __('Current Password') }} *</label>
            <input type="password" name="current_password" style="{{ $inp }}" placeholder="{{ __('Current password') }}"
                   onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
        </div>
        <div style="margin-bottom:16px;">
            <label style="{{ $lbl }}">{{ __('New Password') }} *</label>
            <input type="password" name="new_password" style="{{ $inp }}" placeholder="{{ __('New password') }}"
                   onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
        </div>
        <div style="margin-bottom:24px;">
            <label style="{{ $lbl }}">{{ __('Confirm New Password') }} *</label>
            <input type="password" name="new_password_confirmation" style="{{ $inp }}" placeholder="{{ __('Confirm new password') }}"
                   onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
        </div>
        <button type="submit" class="gc-btn gc-btn-primary">
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
