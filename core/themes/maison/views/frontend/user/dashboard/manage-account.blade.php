@extends(include_theme_path('user.user-master'))
@section('title') {{ __('Manage Account') }} @endsection
@section('dashboard_content')

@php
    $delivery   = $user_details?->delivery_address;
    $shipStates = \Modules\CountryManage\Entities\State::where([
        'status'     => 'publish',
        'country_id' => $delivery->country_id ?? '',
    ])->get();
@endphp

<div style="display:flex;gap:0;border-bottom:1px solid var(--ms-border,#e8ddd0);margin-bottom:24px;overflow-x:auto;">
    @foreach([
        ['profile',  'las la-user-edit',  __('Profile')],
        ['address',  'las la-map-marker', __('Address')],
        ['password', 'las la-lock',       __('Password')],
    ] as [$key, $icon, $label])
    <button class="ms-account-tab" data-tab="{{ $key }}" type="button"
            style="padding:12px 22px;background:none;border:none;border-bottom:2px solid transparent;font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--ms-muted,#999);cursor:pointer;white-space:nowrap;transition:all .2s;display:flex;align-items:center;gap:7px;">
        <i class="{{ $icon }}" style="font-size:15px;"></i> {{ $label }}
    </button>
    @endforeach
</div>

{{-- ===== Profile Tab ===== --}}
<div class="ms-account-panel" id="ms-tab-profile">
    <div class="ms-dash-card">
        <div class="ms-dash-section-title">{{ __('Edit Profile') }}</div>
        <form class="profile-edit-form" action="#" enctype="multipart/form-data">
            @csrf
            <div style="display:flex;align-items:center;gap:16px;padding:14px 16px;background:var(--ms-linen,#f5ede3);border:1px solid var(--ms-border,#e8ddd0);border-radius:var(--ms-radius,4px);margin-bottom:20px;">
                <x-fields.media-upload :name="'image'" :title="''" :id="$user_details->image"/>
                <div>
                    <div style="font-size:13px;font-weight:700;color:var(--ms-dark,#1a1a1a);margin-bottom:4px;">{{ __('Profile Photo') }}</div>
                    <div style="font-size:12px;color:var(--ms-muted,#999);">{{ __('Click the image to upload') }}</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="ms-form-label">{{ __('Full Name') }} <span style="color:var(--ms-accent,#c4a882);">*</span></label>
                    <input class="ms-form-input" type="text" name="name" value="{{ $user_details->name }}" placeholder="{{ __('Your name') }}">
                </div>
                <div class="col-md-6">
                    <label class="ms-form-label">{{ __('Email Address') }} <span style="color:var(--ms-accent,#c4a882);">*</span></label>
                    <input class="ms-form-input" type="email" name="email" value="{{ $user_details->email }}" placeholder="{{ __('Your email') }}">
                </div>
                <div class="col-md-6">
                    <label class="ms-form-label">{{ __('Phone Number') }}</label>
                    <input class="ms-form-input" id="phone" type="text" name="phone" value="{{ $user_details->mobile }}" placeholder="{{ __('Phone number') }}">
                </div>
                <div class="col-md-6">
                    <label class="ms-form-label">{{ __('Company') }}</label>
                    <input class="ms-form-input" type="text" name="company" value="{{ $user_details->company }}" placeholder="{{ __('Company name') }}">
                </div>
                <div class="col-md-4">
                    <label class="ms-form-label">{{ __('Country') }}</label>
                    <select class="ms-form-input" name="country" style="cursor:pointer;">
                        <option value="">{{ __('Select country') }}</option>
                        @foreach($countries ?? [] as $country)
                            <option value="{{ $country->id }}" @selected($country->id == $user_details->country)>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="ms-form-label">{{ __('State') }}</label>
                    <input class="ms-form-input stateField" type="text" name="state"
                        value="@auth('web'){{ $user_details->state ?? '' }}@else{{ old('state') }}@endauth"
                        placeholder="{{ __('State / Province') }}">
                </div>
                <div class="col-md-4">
                    <label class="ms-form-label">{{ __('City') }}</label>
                    <input class="ms-form-input cityField" type="text" name="city"
                        value="@auth('web'){{ $user_details->city ?? '' }}@else{{ old('city') }}@endauth"
                        placeholder="{{ __('City') }}">
                </div>
                <div class="col-md-6">
                    <label class="ms-form-label">{{ __('Postal Code') }}</label>
                    <input class="ms-form-input" type="text" name="postal_code" value="{{ $user_details->postal_code }}" placeholder="{{ __('Postal code') }}">
                </div>
                <div class="col-12">
                    <label class="ms-form-label">{{ __('Address') }}</label>
                    <textarea class="ms-form-input" name="address" rows="3" style="resize:vertical;height:auto;">{{ $user_details->address }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="profile-submit-btn"
                            style="display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:var(--ms-dark,#1a1a1a);color:#fff;border:none;border-radius:var(--ms-radius,4px);font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;">
                        <i class="las la-save"></i> {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== Address Tab ===== --}}
<div class="ms-account-panel" id="ms-tab-address" style="display:none;">
    <div class="ms-dash-card">
        <div class="ms-dash-section-title">{{ __('Address Book') }}</div>
        <form class="address_form" action="#" id="ms_address_form">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="ms-form-label">{{ __('Full Name') }} <span style="color:var(--ms-accent,#c4a882);">*</span></label>
                    <input class="ms-form-input" type="text" name="full_name" value="{{ $delivery?->full_name }}" placeholder="{{ __('Full name') }}">
                </div>
                <div class="col-md-6">
                    <label class="ms-form-label">{{ __('Email') }} <span style="color:var(--ms-accent,#c4a882);">*</span></label>
                    <input class="ms-form-input" type="email" name="email" value="{{ $delivery?->email }}" placeholder="{{ __('Email address') }}">
                </div>
                <div class="col-md-6">
                    <label class="ms-form-label">{{ __('Phone') }} <span style="color:var(--ms-accent,#c4a882);">*</span></label>
                    <input class="ms-form-input" id="address_phone" type="text" name="phone" value="{{ $delivery?->phone }}" placeholder="{{ __('Phone number') }}">
                </div>
                <div class="col-md-6">
                    <label class="ms-form-label">{{ __('Postal Code') }}</label>
                    <input class="ms-form-input" type="text" name="postal_code" value="{{ $delivery?->postal_code }}" placeholder="{{ __('Postal code') }}">
                </div>
                <div class="col-md-4">
                    <label class="ms-form-label">{{ __('Country') }} <span style="color:var(--ms-accent,#c4a882);">*</span></label>
                    <select class="ms-form-input countryField" name="country" id="msAddressCountryField" style="cursor:pointer;">
                        <option value="">{{ __('Select country') }}</option>
                        @foreach($countries ?? [] as $country)
                            <option value="{{ $country->id }}" @selected($country->id == ($delivery->country_id ?? ''))>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="ms-form-label">{{ __('State') }} <span style="color:var(--ms-accent,#c4a882);">*</span></label>
                    <select class="ms-form-input stateField" name="state" id="msAddressStateField" style="cursor:pointer;">
                        <option value="">{{ __('Select state') }}</option>
                        @foreach($shipStates as $st)
                            <option value="{{ $st->id }}" @selected($st->id == ($delivery->state_id ?? ''))>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="ms-form-label">{{ __('City') }}</label>
                    <input class="ms-form-input cityField" type="text" name="city" value="{{ old('city', $delivery?->city) }}" placeholder="{{ __('City / Town') }}">
                </div>
                <div class="col-12">
                    <label class="ms-form-label">{{ __('Address') }}</label>
                    <textarea class="ms-form-input" name="address" rows="3" style="resize:vertical;height:auto;">{{ $delivery?->address }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="address-submit-btn"
                            style="display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:var(--ms-dark,#1a1a1a);color:#fff;border:none;border-radius:var(--ms-radius,4px);font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;">
                        <i class="las la-save"></i> {{ __('Save Address') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== Password Tab ===== --}}
<div class="ms-account-panel" id="ms-tab-password" style="display:none;">
    <div class="ms-dash-card">
        <div class="ms-dash-section-title">{{ __('Change Password') }}</div>
        <form class="change_password_form" action="#">
            @csrf
            <div class="row g-3" style="max-width:480px;">
                <div class="col-12">
                    <label class="ms-form-label">{{ __('Current Password') }} <span style="color:var(--ms-accent,#c4a882);">*</span></label>
                    <input class="ms-form-input" type="password" name="old_password" placeholder="{{ __('Enter current password') }}">
                </div>
                <div class="col-12">
                    <label class="ms-form-label">{{ __('New Password') }} <span style="color:var(--ms-accent,#c4a882);">*</span></label>
                    <input class="ms-form-input" type="password" name="password" placeholder="{{ __('Enter new password') }}">
                </div>
                <div class="col-12">
                    <label class="ms-form-label">{{ __('Confirm New Password') }} <span style="color:var(--ms-accent,#c4a882);">*</span></label>
                    <input class="ms-form-input" type="password" name="password_confirmation" placeholder="{{ __('Confirm new password') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="save-password-btn"
                            style="display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:var(--ms-dark,#1a1a1a);color:#fff;border:none;border-radius:var(--ms-radius,4px);font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;">
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
<x-media-upload.js/>
<x-custom-js.phone-number-config selector="#phone" key="1"/>
<x-custom-js.phone-number-config selector="#address_phone" key="2"/>
<script>
$(function () {
    function msTab(key) {
        $('.ms-account-panel').hide();
        $('.ms-account-tab').css({'color':'var(--ms-muted,#999)','border-bottom-color':'transparent'});
        $('#ms-tab-' + key).show();
        $('.ms-account-tab[data-tab="' + key + '"]').css({'color':'var(--ms-dark,#1a1a1a)','border-bottom-color':'var(--ms-dark,#1a1a1a)'});
    }
    msTab('profile');
    $(document).on('click', '.ms-account-tab', function () { msTab($(this).data('tab')); });

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

    $(document).on('change', '#msAddressCountryField', function () {
        $.post('{{ theme_state_search_url() }}', { _token: '{{ csrf_token() }}', country: $(this).val() }, function (data) {
            var sf = $('#msAddressStateField').empty().append('<option value="">{{ __("Select a state") }}</option>');
            $.each(data.states||[], function (i, v) { sf.append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });
});
</script>
@endsection
