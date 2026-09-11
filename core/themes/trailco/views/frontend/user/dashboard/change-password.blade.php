@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Change Password') }} @endsection
@section('page-title') {{ __('Change Password') }} @endsection

@section('dashboard-content')

{!! theme_error_msg() !!}
{!! theme_flash_msg() !!}

@php
$inputStyle = "width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;font-family:inherit;outline:none;background:#fff;color:var(--tr-bark);";
$labelStyle = "font-size:12px;font-weight:700;color:var(--tr-stone);display:block;margin-bottom:6px;";
@endphp

<div class="tr-dash-card">
    <div class="tr-dash-card-header">
        <span><i class="mdi mdi-lock-reset" style="margin-right:6px;"></i>{{ __('Change Password') }}</span>
    </div>
    <div class="tr-dash-card-body">
        <form id="tr_change_password_form" action="{{ theme_user_password_change_url() }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-8 col-lg-6">
                    <div style="margin-bottom:16px;">
                        <label style="{{ $labelStyle }}">
                            {{ __('Current Password') }} <span style="color:var(--tr-terra);">*</span>
                        </label>
                        <input type="password" name="current_password" required autocomplete="current-password"
                               style="{{ $inputStyle }}"
                               onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="{{ $labelStyle }}">
                            {{ __('New Password') }} <span style="color:var(--tr-terra);">*</span>
                        </label>
                        <input type="password" name="new_password" required autocomplete="new-password"
                               style="{{ $inputStyle }}"
                               onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                    </div>

                    <div style="margin-bottom:24px;">
                        <label style="{{ $labelStyle }}">
                            {{ __('Confirm New Password') }} <span style="color:var(--tr-terra);">*</span>
                        </label>
                        <input type="password" name="confirm_password" required autocomplete="new-password"
                               style="{{ $inputStyle }}"
                               onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                    </div>

                    <div style="border-top:1px solid var(--tr-border);padding-top:18px;display:flex;gap:12px;align-items:center;">
                        <button type="submit" class="tr-btn tr-btn-terra">
                            <i class="mdi mdi-lock-check-outline"></i> {{ __('Update Password') }}
                        </button>
                        <a href="{{ theme_user_manage_account_url() }}"
                           class="tr-btn tr-btn-outline">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>

                <div class="col-md-4 col-lg-6">
                    <div style="background:var(--tr-cream);border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:18px;">
                        <h6 style="font-size:13px;font-weight:700;color:var(--tr-bark);margin-bottom:12px;">
                            <i class="mdi mdi-shield-check-outline" style="color:var(--tr-olive);margin-right:6px;"></i>
                            {{ __('Password Tips') }}
                        </h6>
                        <ul style="list-style:none;padding:0;margin:0;font-size:12px;color:var(--tr-stone);line-height:2;">
                            <li><i class="mdi mdi-check" style="color:var(--tr-olive);margin-right:6px;"></i>{{ __('At least 8 characters long') }}</li>
                            <li><i class="mdi mdi-check" style="color:var(--tr-olive);margin-right:6px;"></i>{{ __('Mix of uppercase & lowercase') }}</li>
                            <li><i class="mdi mdi-check" style="color:var(--tr-olive);margin-right:6px;"></i>{{ __('Include numbers & symbols') }}</li>
                            <li><i class="mdi mdi-check" style="color:var(--tr-olive);margin-right:6px;"></i>{{ __('Never share your password') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('dashboard-scripts')
<script>
$(function () {
    $(document).on('submit', '#tr_change_password_form', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (d) {
                d.type === 'success' ? toastr.success(d.msg) : toastr.error(d.msg);
            },
            error: function (xhr) {
                $.each(xhr.responseJSON?.errors ?? {}, function (k, v) { toastr.error(v[0] ?? v); });
            }
        });
    });
});
</script>
@endsection
