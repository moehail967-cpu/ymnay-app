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

<div class="d-flex gap-2 flex-wrap mb-4">
    @foreach([
        ['profile',  'la-user-edit',      __('Edit Profile')],
        ['address',  'la-map-marker-alt',  __('Address Book')],
        ['password', 'la-lock',            __('Change Password')],
    ] as [$key, $icon, $label])
    <button class="fn-account-tab fn-btn {{ $loop->first ? 'fn-btn-gold' : 'fn-btn-outline' }}"
            data-tab="{{ $key }}" type="button">
        <i class="las {{ $icon }}"></i> {{ $label }}
    </button>
    @endforeach
</div>

{{-- Profile Tab --}}
<div class="fn-account-panel" id="fn-tab-profile">
    <div class="fn-dash-card">
        <div class="fn-dash-card-header"><i class="las la-user-edit fn-icon-accent"></i> {{ __('Edit Profile') }}</div>
        <div class="fn-dash-card-body">
            <form class="profile-edit-form" action="#" enctype="multipart/form-data">
                @csrf
                <div class="fn-photo-row">
                    <x-fields.media-upload :name="'image'" :title="''" :id="$user_details->image"/>
                    <div>
                        <div class="fn-photo-caption">{{ __('Profile Photo') }}</div>
                        <div class="fn-photo-caption-sm">{{ __('Click the image to upload') }}</div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Full Name') }} <span class="fn-required">*</span></label>
                        <input class="fn-input" type="text" name="name" value="{{ $user_details->name }}" placeholder="{{ __('Your name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Email Address') }} <span class="fn-required">*</span></label>
                        <input class="fn-input" type="email" name="email" value="{{ $user_details->email }}" placeholder="{{ __('Your email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Phone Number') }}</label>
                        <input class="fn-input" id="phone" type="text" name="phone" value="{{ $user_details->mobile }}" placeholder="{{ __('Phone number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Company') }}</label>
                        <input class="fn-input" type="text" name="company" value="{{ $user_details->company }}" placeholder="{{ __('Company name') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="fn-label">{{ __('Country') }}</label>
                        <select class="fn-input fn-select" name="country">
                            <option value="">{{ __('Select country') }}</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}" @selected($country->id == $user_details->country)>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fn-label">{{ __('State') }}</label>
                        <input class="fn-input stateField" type="text" name="state"
                            value="@auth('web'){{ $user_details->state ?? '' }}@else{{ old('state') }}@endauth"
                            placeholder="{{ __('State / Province') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="fn-label">{{ __('City') }}</label>
                        <input class="fn-input cityField" type="text" name="city"
                            value="@auth('web'){{ $user_details->city ?? '' }}@else{{ old('city') }}@endauth"
                            placeholder="{{ __('City') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Postal Code') }}</label>
                        <input class="fn-input" type="text" name="postal_code" value="{{ $user_details->postal_code }}" placeholder="{{ __('Postal code') }}">
                    </div>
                    <div class="col-12">
                        <label class="fn-label">{{ __('Address') }}</label>
                        <textarea class="fn-input fn-textarea" name="address" rows="3">{{ $user_details->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="fn-btn fn-btn-gold profile-submit-btn">
                            <i class="las la-save"></i> {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Address Tab --}}
<div class="fn-account-panel d-none" id="fn-tab-address">
    <div class="fn-dash-card">
        <div class="fn-dash-card-header"><i class="las la-map-marker-alt fn-icon-accent"></i> {{ __('Address Book') }}</div>
        <div class="fn-dash-card-body">
            <form class="address_form" action="#" id="fn_address_form">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Full Name') }} <span class="fn-required">*</span></label>
                        <input class="fn-input" type="text" name="full_name" value="{{ $delivery?->full_name }}" placeholder="{{ __('Full name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Email') }} <span class="fn-required">*</span></label>
                        <input class="fn-input" type="email" name="email" value="{{ $delivery?->email }}" placeholder="{{ __('Email address') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Phone') }} <span class="fn-required">*</span></label>
                        <input class="fn-input" id="address_phone" type="text" name="phone" value="{{ $delivery?->phone }}" placeholder="{{ __('Phone number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fn-label">{{ __('Postal Code') }}</label>
                        <input class="fn-input" type="text" name="postal_code" value="{{ $delivery?->postal_code }}" placeholder="{{ __('Postal code') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="fn-label">{{ __('Country') }} <span class="fn-required">*</span></label>
                        <select class="fn-input fn-select countryField" name="country" id="fnAddressCountryField">
                            <option value="">{{ __('Select country') }}</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}" @selected($country->id == ($delivery->country_id ?? ''))>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fn-label">{{ __('State') }} <span class="fn-required">*</span></label>
                        <select class="fn-input fn-select stateField" name="state" id="fnAddressStateField">
                            <option value="">{{ __('Select state') }}</option>
                            @foreach($shipStates as $st)
                                <option value="{{ $st->id }}" @selected($st->id == ($delivery->state_id ?? ''))>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fn-label">{{ __('City') }}</label>
                        <input class="fn-input cityField" type="text" name="city" value="{{ old('city', $delivery?->city) }}" placeholder="{{ __('City / Town') }}">
                    </div>
                    <div class="col-12">
                        <label class="fn-label">{{ __('Address') }}</label>
                        <textarea class="fn-input fn-textarea" name="address" rows="3">{{ $delivery?->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="fn-btn fn-btn-gold address-submit-btn">
                            <i class="las la-save"></i> {{ __('Save Address') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Password Tab --}}
<div class="fn-account-panel d-none" id="fn-tab-password">
    <div class="fn-dash-card">
        <div class="fn-dash-card-header"><i class="las la-lock fn-icon-accent"></i> {{ __('Change Password') }}</div>
        <div class="fn-dash-card-body">
            <form class="change_password_form" action="#">
                @csrf
                <div class="row g-3 fn-pw-form">
                    <div class="col-12">
                        <label class="fn-label">{{ __('Current Password') }} <span class="fn-required">*</span></label>
                        <input class="fn-input" type="password" name="old_password" placeholder="{{ __('Enter current password') }}">
                    </div>
                    <div class="col-12">
                        <label class="fn-label">{{ __('New Password') }} <span class="fn-required">*</span></label>
                        <input class="fn-input" type="password" name="password" placeholder="{{ __('Enter new password') }}">
                    </div>
                    <div class="col-12">
                        <label class="fn-label">{{ __('Confirm New Password') }} <span class="fn-required">*</span></label>
                        <input class="fn-input" type="password" name="password_confirmation" placeholder="{{ __('Confirm new password') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="fn-btn fn-btn-gold save-password-btn">
                            <i class="las la-lock"></i> {{ __('Update Password') }}
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
    function fnTab(key) {
        $('.fn-account-panel').addClass('d-none');
        $('.fn-account-tab').removeClass('fn-btn-gold').addClass('fn-btn-outline');
        $('#fn-tab-' + key).removeClass('d-none');
        $('.fn-account-tab[data-tab="' + key + '"]').removeClass('fn-btn-outline').addClass('fn-btn-gold');
    }
    fnTab('profile');
    $(document).on('click', '.fn-account-tab', function () { fnTab($(this).data('tab')); });

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

    $(document).on('change', '#fnAddressCountryField', function () {
        $.post('{{ theme_state_search_url() }}', { _token: '{{ csrf_token() }}', country: $(this).val() }, function (data) {
            var sf = $('#fnAddressStateField').empty().append('<option value="">{{ __("Select a state") }}</option>');
            $.each(data.states||[], function (i, v) { sf.append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });
});
</script>
@endsection
