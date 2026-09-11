@extends(theme_path('frontend.user.dashboard.user-master'))
@section('title') {{ __('Manage Account') }} @endsection
@section('dash-title') {{ __('Manage Account') }} @endsection

@section('style')
<x-media-upload.css/>
<style>
/* ── Override admin media-upload component for Electro profile photo ── */
.mc-profile-photo-wrap .form-group { margin: 0; }
.mc-profile-photo-wrap label[for="image"] { display: none; }
.mc-profile-photo-wrap .media-upload-btn-wrapper { display: flex; align-items: center; gap: 0; }
.mc-profile-photo-wrap .img-wrap { position: relative; width: 72px; height: 72px; flex-shrink: 0; }
.mc-profile-photo-wrap .attachment-preview,
.mc-profile-photo-wrap .attachment-preview .thumbnail,
.mc-profile-photo-wrap .attachment-preview .thumbnail .centered { width: 72px; height: 72px; border-radius: 50%; overflow: hidden; }
.mc-profile-photo-wrap .attachment-preview img.user-thumb { width: 72px; height: 72px; object-fit: cover; border-radius: 50%; }
.mc-profile-photo-wrap .rmv-span { position: absolute; top: 0; right: 0; width: 20px; height: 20px; background: #1A85ED; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 10px; z-index: 1; }
.mc-profile-photo-wrap br { display: none; }
.mc-profile-photo-wrap .btn.btn-info.media_upload_form_btn { background: transparent; border: none; color: #1A85ED; font-size: 13px; font-weight: 600; padding: 0 0 0 14px; box-shadow: none; }
.mc-profile-photo-wrap .btn.btn-info.media_upload_form_btn:hover { background: transparent; color: #1266B8; text-decoration: underline; }
/* Placeholder when no image selected */
.mc-profile-photo-wrap .img-wrap:not(:has(.attachment-preview img))::before { content: attr(data-initial); width: 72px; height: 72px; border-radius: 50%; background: #1A85ED; color: #fff; font-size: 26px; font-weight: 800; display: flex; align-items: center; justify-content: center; }
</style>
@endsection

@section('dashboard-content')

@php
    $delivery   = $user_details?->delivery_address;
    $shipStates = \Modules\CountryManage\Entities\State::where([
        'status'     => 'publish',
        'country_id' => $delivery->country_id ?? '',
    ])->get();
@endphp

{{-- Tab buttons --}}
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px;">
    @foreach([
        ['profile',  'las la-user-edit',  __('Edit Profile')],
        ['address',  'las la-map-marker', __('Address Book')],
        ['password', 'las la-lock',       __('Change Password')],
    ] as [$key, $icon, $label])
    <button class="mc-account-tab mc-btn {{ $loop->first ? 'mc-btn-primary' : 'mc-btn-outline' }}"
            data-tab="{{ $key }}" type="button">
        <i class="{{ $icon }}"></i> {{ $label }}
    </button>
    @endforeach
</div>

{{-- Profile Tab --}}
<div class="mc-account-panel" id="mc-tab-profile">
    <div class="mc-dash-card">
        <div class="mc-dash-card-title"><i class="las la-user-edit"></i> {{ __('Edit Profile') }}</div>
        <form class="profile-edit-form" action="#" enctype="multipart/form-data">
            @csrf
            <div style="display:flex;align-items:center;gap:16px;padding:14px 16px;background:#EAF2F8;border:1px solid #EAF2F8;border-radius:6px;margin-bottom:20px;">
                <div class="mc-profile-photo-wrap">
                    <x-fields.media-upload :name="'image'" :title="''" :id="$user_details->image"/>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#1a1a1a;margin-bottom:4px;">{{ __('Profile Photo') }}</div>
                    <div style="font-size:12px;color:#888;">{{ __('Click photo or the button to change') }}</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="mc-form-label">{{ __('Full Name') }} <span class="mc-form-required">*</span></label>
                    <input class="mc-form-input" type="text" name="name" value="{{ $user_details->name }}" placeholder="{{ __('Your name') }}">
                </div>
                <div class="col-md-6">
                    <label class="mc-form-label">{{ __('Email Address') }} <span class="mc-form-required">*</span></label>
                    <input class="mc-form-input" type="email" name="email" value="{{ $user_details->email }}" placeholder="{{ __('Your email') }}">
                </div>
                <div class="col-md-6">
                    <label class="mc-form-label">{{ __('Phone Number') }}</label>
                    <input class="mc-form-input" id="phone" type="text" name="phone" value="{{ $user_details->mobile }}" placeholder="{{ __('Phone number') }}">
                </div>
                <div class="col-md-6">
                    <label class="mc-form-label">{{ __('Company') }}</label>
                    <input class="mc-form-input" type="text" name="company" value="{{ $user_details->company }}" placeholder="{{ __('Company name') }}">
                </div>
                <div class="col-md-4">
                    <label class="mc-form-label">{{ __('Country') }}</label>
                    <select class="mc-form-select" name="country">
                        <option value="">{{ __('Select country') }}</option>
                        @foreach($countries ?? [] as $country)
                            <option value="{{ $country->id }}" @selected($country->id == $user_details->country)>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="mc-form-label">{{ __('State') }}</label>
                    <input class="mc-form-input stateField" type="text" name="state"
                        value="@auth('web'){{ $user_details->state ?? '' }}@else{{ old('state') }}@endauth"
                        placeholder="{{ __('State / Province') }}">
                </div>
                <div class="col-md-4">
                    <label class="mc-form-label">{{ __('City') }}</label>
                    <input class="mc-form-input cityField" type="text" name="city"
                        value="@auth('web'){{ $user_details->city ?? '' }}@else{{ old('city') }}@endauth"
                        placeholder="{{ __('City') }}">
                </div>
                <div class="col-md-6">
                    <label class="mc-form-label">{{ __('Postal Code') }}</label>
                    <input class="mc-form-input" type="text" name="postal_code" value="{{ $user_details->postal_code }}" placeholder="{{ __('Postal code') }}">
                </div>
                <div class="col-12">
                    <label class="mc-form-label">{{ __('Address') }}</label>
                    <textarea class="mc-form-input mc-form-textarea" name="address" rows="3">{{ $user_details->address }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="mc-btn mc-btn-primary profile-submit-btn">
                        <i class="las la-save"></i> {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Address Tab --}}
<div class="mc-account-panel" id="mc-tab-address" style="display:none;">
    <div class="mc-dash-card">
        <div class="mc-dash-card-title"><i class="las la-map-marker"></i> {{ __('Address Book') }}</div>
        <form class="address_form" action="#" id="el_address_form">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="mc-form-label">{{ __('Full Name') }} <span class="mc-form-required">*</span></label>
                    <input class="mc-form-input" type="text" name="full_name" value="{{ $delivery?->full_name }}" placeholder="{{ __('Full name') }}">
                </div>
                <div class="col-md-6">
                    <label class="mc-form-label">{{ __('Email') }} <span class="mc-form-required">*</span></label>
                    <input class="mc-form-input" type="email" name="email" value="{{ $delivery?->email }}" placeholder="{{ __('Email address') }}">
                </div>
                <div class="col-md-6">
                    <label class="mc-form-label">{{ __('Phone') }} <span class="mc-form-required">*</span></label>
                    <input class="mc-form-input" id="address_phone" type="text" name="phone" value="{{ $delivery?->phone }}" placeholder="{{ __('Phone number') }}">
                </div>
                <div class="col-md-6">
                    <label class="mc-form-label">{{ __('Postal Code') }}</label>
                    <input class="mc-form-input" type="text" name="postal_code" value="{{ $delivery?->postal_code }}" placeholder="{{ __('Postal code') }}">
                </div>
                <div class="col-md-4">
                    <label class="mc-form-label">{{ __('Country') }} <span class="mc-form-required">*</span></label>
                    <select class="mc-form-select countryField" name="country" id="elAddressCountryField">
                        <option value="">{{ __('Select country') }}</option>
                        @foreach($countries ?? [] as $country)
                            <option value="{{ $country->id }}" @selected($country->id == ($delivery->country_id ?? ''))>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="mc-form-label">{{ __('State') }} <span class="mc-form-required">*</span></label>
                    <select class="mc-form-select stateField" name="state" id="elAddressStateField">
                        <option value="">{{ __('Select state') }}</option>
                        @foreach($shipStates as $st)
                            <option value="{{ $st->id }}" @selected($st->id == ($delivery->state_id ?? ''))>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="mc-form-label">{{ __('City') }}</label>
                    <input class="mc-form-input cityField" type="text" name="city" value="{{ old('city', $delivery?->city) }}" placeholder="{{ __('City / Town') }}">
                </div>
                <div class="col-12">
                    <label class="mc-form-label">{{ __('Address') }}</label>
                    <textarea class="mc-form-input mc-form-textarea" name="address" rows="3">{{ $delivery?->address }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="mc-btn mc-btn-primary address-submit-btn">
                        <i class="las la-save"></i> {{ __('Save Address') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Password Tab --}}
<div class="mc-account-panel" id="mc-tab-password" style="display:none;">
    <div class="mc-dash-card">
        <div class="mc-dash-card-title"><i class="las la-lock"></i> {{ __('Change Password') }}</div>
        <form class="change_password_form" action="#">
            @csrf
            <div class="row g-3" style="max-width:480px;">
                <div class="col-12">
                    <label class="mc-form-label">{{ __('Current Password') }} <span class="mc-form-required">*</span></label>
                    <input class="mc-form-input" type="password" name="old_password" placeholder="{{ __('Enter current password') }}">
                </div>
                <div class="col-12">
                    <label class="mc-form-label">{{ __('New Password') }} <span class="mc-form-required">*</span></label>
                    <input class="mc-form-input" type="password" name="password" placeholder="{{ __('Enter new password') }}">
                </div>
                <div class="col-12">
                    <label class="mc-form-label">{{ __('Confirm New Password') }} <span class="mc-form-required">*</span></label>
                    <input class="mc-form-input" type="password" name="password_confirmation" placeholder="{{ __('Confirm new password') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="mc-btn mc-btn-primary save-password-btn">
                        <i class="las la-lock"></i> {{ __('Update Password') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<x-media-upload.markup/>

@endsection

@section('dashboard-scripts')
<x-custom-js.phone-number-config selector="#phone" key="1"/>
<x-custom-js.phone-number-config selector="#address_phone" key="2"/>
<script>
$(function () {
    function elTab(key) {
        $('.mc-account-panel').hide();
        $('.mc-account-tab').removeClass('mc-btn-primary').addClass('mc-btn-outline');
        $('#mc-tab-' + key).show();
        $('.mc-account-tab[data-tab="' + key + '"]').removeClass('mc-btn-outline').addClass('mc-btn-primary');
    }
    elTab('profile');
    $(document).on('click', '.mc-account-tab', function () { elTab($(this).data('tab')); });

    setTimeout(function () {
        $('#phone').val('{{ $user_details->mobile ?? "" }}');
        $('#address_phone').val('{{ $user_details?->user_delivery_address?->phone ?? "" }}');
    }, 500);

    $(document).on('click', '.attachment-preview .user-thumb', function () { $('.media_upload_form_btn').trigger('click'); });

    $(document).on('submit', 'form.profile-edit-form', function (e) {
        e.preventDefault();
        $.ajax({ url: '{{ theme_user_profile_update_url() }}', type: 'POST', processData: false, contentType: false, data: new FormData(e.target),
            beforeSend: function () { $('.loader').show(); },
            success: function (d) { $('.loader').hide(); if (d.msg) toastr.success(d.msg); },
            error: function (xhr) { $('.loader').hide(); $.each((xhr.responseJSON||{}).errors||{}, function (k, v) { toastr.error(v); }); }
        });
    });

    $(document).on('click', '.address-submit-btn', function (e) {
        e.preventDefault();
        $.ajax({ type: 'POST', url: '{{ theme_user_address_update_url() }}',
            data: { _token: '{{ csrf_token() }}', name: $('.address_form input[name=full_name]').val(), email: $('.address_form input[name=email]').val(), phone: $('.address_form input[name=phone]').val(), country: $('.address_form select[name=country]').val(), state: $('.address_form select[name=state]').val(), city: $('.address_form input[name=city]').val(), postal_code: $('.address_form input[name=postal_code]').val(), address: $('.address_form textarea[name=address]').val() },
            beforeSend: function () { $('.loader').show(); },
            success: function (d) { $('.loader').hide(); if (d.msg) toastr.success(d.msg); },
            error: function (xhr) { $('.loader').hide(); $.each((xhr.responseJSON||{}).errors||{}, function (k, v) { toastr.error(v); }); }
        });
    });

    $(document).on('submit', '.change_password_form', function (e) {
        e.preventDefault();
        $.ajax({ url: '{{ theme_user_password_change_url() }}', type: 'POST', processData: false, contentType: false, data: new FormData(e.target),
            beforeSend: function () { $('.loader').show(); },
            success: function (d) { $('.loader').hide(); if (d.type === 'success') { toastr.success(d.msg); setTimeout(function () { location.href = d.url; }, 3000); } else { toastr.error(d.msg); } },
            error: function (xhr) { $('.loader').hide(); $.each((xhr.responseJSON||{}).errors||{}, function (k, v) { toastr.error(v); }); }
        });
    });

    $(document).on('change', '#elAddressCountryField', function () {
        $.post('{{ theme_state_search_url() }}', { _token: '{{ csrf_token() }}', country: $(this).val() }, function (data) {
            var sf = $('#elAddressStateField').empty().append('<option value="">{{ __("Select a state") }}</option>');
            $.each(data.states||[], function (i, v) { sf.append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });
});
</script>
@endsection
