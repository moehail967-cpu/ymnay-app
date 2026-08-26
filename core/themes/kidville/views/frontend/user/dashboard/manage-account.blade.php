@extends('theme::frontend.user.dashboard.user-master')
@section('title') {{ __('Manage Account') }} @endsection
@section('section')

@php
    $delivery   = $user_details?->delivery_address;
    $shipStates = \Modules\CountryManage\Entities\State::where([
        'status'     => 'publish',
        'country_id' => $delivery->country_id ?? '',
    ])->get();
@endphp

<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
    @foreach([
        ['profile',  'mdi-account-edit-outline', __('Edit Profile')],
        ['address',  'mdi-map-marker-outline',   __('Address Book')],
        ['password', 'mdi-lock-outline',          __('Change Password')],
    ] as [$key, $icon, $label])
    <button class="kv-account-tab kv-btn {{ $loop->first ? 'kv-btn-red' : 'kv-btn-outline' }}"
            data-tab="{{ $key }}" type="button" style="display:inline-flex;align-items:center;gap:6px;">
        <i class="mdi {{ $icon }}"></i> {{ $label }}
    </button>
    @endforeach
</div>

{{-- ===== Profile Tab ===== --}}
<div class="kv-account-panel" id="kv-tab-profile">
    <div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);overflow:hidden;margin-bottom:20px;">
        <div style="padding:14px 20px;border-bottom:2px solid var(--kv-border);font-weight:800;color:var(--kv-dark);display:flex;align-items:center;gap:8px;">
            <i class="mdi mdi-account-edit-outline" style="color:var(--main-color-one);font-size:18px;"></i> {{ __('Edit Profile') }}
        </div>
        <div style="padding:24px;">
            <form class="profile-edit-form" action="#" enctype="multipart/form-data">
                @csrf
                <div style="display:flex;align-items:center;gap:16px;padding:14px 16px;background:var(--kv-bg,#FFFEF8);border:2px solid var(--kv-border);border-radius:var(--kv-radius);margin-bottom:20px;">
                    <x-fields.media-upload :name="'image'" :title="''" :id="$user_details->image"/>
                    <div>
                        <div style="font-size:14px;font-weight:800;color:var(--kv-dark);margin-bottom:4px;">{{ __('Profile Photo') }}</div>
                        <div style="font-size:12px;color:var(--kv-muted,#888);">{{ __('Click the image to upload') }}</div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="info-title">{{ __('Full Name') }} <span style="color:var(--main-color-one);">*</span></label>
                        <input class="kv-input" type="text" name="name" value="{{ $user_details->name }}" placeholder="{{ __('Your name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="info-title">{{ __('Email Address') }} <span style="color:var(--main-color-one);">*</span></label>
                        <input class="kv-input" type="email" name="email" value="{{ $user_details->email }}" placeholder="{{ __('Your email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="info-title">{{ __('Phone Number') }}</label>
                        <input class="kv-input" id="phone" type="text" name="phone" value="{{ $user_details->mobile }}" placeholder="{{ __('Phone number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="info-title">{{ __('Company') }}</label>
                        <input class="kv-input" type="text" name="company" value="{{ $user_details->company }}" placeholder="{{ __('Company name') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="info-title">{{ __('Country') }}</label>
                        <select class="kv-input" name="country" style="cursor:pointer;">
                            <option value="">{{ __('Select country') }}</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}" @selected($country->id == $user_details->country)>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="info-title">{{ __('State') }}</label>
                        <input class="kv-input stateField" type="text" name="state"
                            value="@auth('web'){{ $user_details->state ?? '' }}@else{{ old('state') }}@endauth"
                            placeholder="{{ __('State / Province') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="info-title">{{ __('City') }}</label>
                        <input class="kv-input cityField" type="text" name="city"
                            value="@auth('web'){{ $user_details->city ?? '' }}@else{{ old('city') }}@endauth"
                            placeholder="{{ __('City') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="info-title">{{ __('Postal Code') }}</label>
                        <input class="kv-input" type="text" name="postal_code" value="{{ $user_details->postal_code }}" placeholder="{{ __('Postal code') }}">
                    </div>
                    <div class="col-12">
                        <label class="info-title">{{ __('Address') }}</label>
                        <textarea class="kv-input" name="address" rows="3" style="resize:vertical;height:auto;">{{ $user_details->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="kv-btn kv-btn-red profile-submit-btn">
                            <i class="mdi mdi-content-save-outline"></i> {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Address Tab ===== --}}
<div class="kv-account-panel" id="kv-tab-address" style="display:none;">
    <div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);overflow:hidden;margin-bottom:20px;">
        <div style="padding:14px 20px;border-bottom:2px solid var(--kv-border);font-weight:800;color:var(--kv-dark);display:flex;align-items:center;gap:8px;">
            <i class="mdi mdi-map-marker-outline" style="color:var(--main-color-one);font-size:18px;"></i> {{ __('Address Book') }}
        </div>
        <div style="padding:24px;">
            <form class="address_form" action="#" id="kv_address_form">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="info-title">{{ __('Full Name') }} <span style="color:var(--main-color-one);">*</span></label>
                        <input class="kv-input" type="text" name="full_name" value="{{ $delivery?->full_name }}" placeholder="{{ __('Full name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="info-title">{{ __('Email') }} <span style="color:var(--main-color-one);">*</span></label>
                        <input class="kv-input" type="email" name="email" value="{{ $delivery?->email }}" placeholder="{{ __('Email address') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="info-title">{{ __('Phone') }} <span style="color:var(--main-color-one);">*</span></label>
                        <input class="kv-input" id="address_phone" type="text" name="phone" value="{{ $delivery?->phone }}" placeholder="{{ __('Phone number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="info-title">{{ __('Postal Code') }}</label>
                        <input class="kv-input" type="text" name="postal_code" value="{{ $delivery?->postal_code }}" placeholder="{{ __('Postal code') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="info-title">{{ __('Country') }} <span style="color:var(--main-color-one);">*</span></label>
                        <select class="kv-input countryField" name="country" id="kvAddressCountryField" style="cursor:pointer;">
                            <option value="">{{ __('Select country') }}</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}" @selected($country->id == ($delivery->country_id ?? ''))>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="info-title">{{ __('State') }} <span style="color:var(--main-color-one);">*</span></label>
                        <select class="kv-input stateField" name="state" id="kvAddressStateField" style="cursor:pointer;">
                            <option value="">{{ __('Select state') }}</option>
                            @foreach($shipStates as $st)
                                <option value="{{ $st->id }}" @selected($st->id == ($delivery->state_id ?? ''))>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="info-title">{{ __('City') }}</label>
                        <input class="kv-input cityField" type="text" name="city" value="{{ old('city', $delivery?->city) }}" placeholder="{{ __('City / Town') }}">
                    </div>
                    <div class="col-12">
                        <label class="info-title">{{ __('Address') }}</label>
                        <textarea class="kv-input" name="address" rows="3" style="resize:vertical;height:auto;">{{ $delivery?->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="kv-btn kv-btn-red address-submit-btn">
                            <i class="mdi mdi-content-save-outline"></i> {{ __('Save Address') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Password Tab ===== --}}
<div class="kv-account-panel" id="kv-tab-password" style="display:none;">
    <div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);overflow:hidden;margin-bottom:20px;">
        <div style="padding:14px 20px;border-bottom:2px solid var(--kv-border);font-weight:800;color:var(--kv-dark);display:flex;align-items:center;gap:8px;">
            <i class="mdi mdi-lock-outline" style="color:var(--main-color-one);font-size:18px;"></i> {{ __('Change Password') }}
        </div>
        <div style="padding:24px;">
            <form class="change_password_form" action="#">
                @csrf
                <div class="row g-3" style="max-width:480px;">
                    <div class="col-12">
                        <label class="info-title">{{ __('Current Password') }} <span style="color:var(--main-color-one);">*</span></label>
                        <input class="kv-input" type="password" name="old_password" placeholder="{{ __('Enter current password') }}">
                    </div>
                    <div class="col-12">
                        <label class="info-title">{{ __('New Password') }} <span style="color:var(--main-color-one);">*</span></label>
                        <input class="kv-input" type="password" name="password" placeholder="{{ __('Enter new password') }}">
                    </div>
                    <div class="col-12">
                        <label class="info-title">{{ __('Confirm New Password') }} <span style="color:var(--main-color-one);">*</span></label>
                        <input class="kv-input" type="password" name="password_confirmation" placeholder="{{ __('Confirm new password') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="kv-btn kv-btn-red save-password-btn">
                            <i class="mdi mdi-lock-check-outline"></i> {{ __('Update Password') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
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
    function kvTab(key) {
        $('.kv-account-panel').hide();
        $('.kv-account-tab').removeClass('kv-btn-red').addClass('kv-btn-outline');
        $('#kv-tab-' + key).show();
        $('.kv-account-tab[data-tab="' + key + '"]').removeClass('kv-btn-outline').addClass('kv-btn-red');
    }
    kvTab('profile');
    $(document).on('click', '.kv-account-tab', function () { kvTab($(this).data('tab')); });

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

    $(document).on('change', '#kvAddressCountryField', function () {
        $.post('{{ theme_state_search_url() }}', { _token: '{{ csrf_token() }}', country: $(this).val() }, function (data) {
            var sf = $('#kvAddressStateField').empty().append('<option value="">{{ __("Select a state") }}</option>');
            $.each(data.states||[], function (i, v) { sf.append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });
});
</script>
@endsection
