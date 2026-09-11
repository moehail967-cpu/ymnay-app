@extends(theme_path('frontend.user.dashboard.user-master'))
@section('title') {{ __('Manage Account') }} @endsection
@section('page-title') {{ __('Manage Account') }} @endsection
@section('dashboard-content')

@php
    $delivery   = $user_details?->delivery_address;
    $shipStates = \Modules\CountryManage\Entities\State::where([
        'status'     => 'publish',
        'country_id' => $delivery->country_id ?? '',
    ])->get();
@endphp

<div class="vl-dash-card" style="margin-bottom:20px;overflow:hidden;padding:0;">
    <div style="display:flex;border-bottom:1px solid var(--vl-border,#e8ddd0);overflow-x:auto;">
        @foreach([
            ['profile',  'las la-user-edit',  __('Profile')],
            ['address',  'las la-map-marker', __('Address')],
            ['password', 'las la-lock',       __('Password')],
        ] as [$key, $icon, $label])
        <button class="vl-account-tab" data-tab="{{ $key }}" type="button"
                style="padding:13px 22px;background:none;border:none;border-bottom:3px solid transparent;font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--vl-muted,#999);cursor:pointer;white-space:nowrap;transition:all .2s;display:flex;align-items:center;gap:7px;">
            <i class="{{ $icon }}" style="font-size:15px;"></i> {{ $label }}
        </button>
        @endforeach
    </div>
</div>

{{-- ===== Profile Tab ===== --}}
<div class="vl-account-panel" id="vl-tab-profile">
    <div class="vl-dash-card">
        <div class="vl-dash-card-header">{{ __('Edit Profile') }}</div>
        <div class="vl-dash-card-body">
            <form class="profile-edit-form" action="#" enctype="multipart/form-data">
                @csrf
                <div style="display:flex;align-items:center;gap:16px;padding:14px 16px;background:var(--vl-cream,#fdf8f3);border:1px solid var(--vl-border,#e8ddd0);border-radius:var(--vl-radius,4px);margin-bottom:20px;">
                    <x-fields.media-upload :name="'image'" :title="''" :id="$user_details->image"/>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:var(--vl-dark,#1a1a1a);margin-bottom:4px;">{{ __('Profile Photo') }}</div>
                        <div style="font-size:12px;color:var(--vl-muted,#999);">{{ __('Click the image to upload') }}</div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 single-input">
                        <label class="label-title">{{ __('Full Name') }} <span style="color:var(--vl-champagne,#c4a882);">*</span></label>
                        <input class="form--control" type="text" name="name" value="{{ $user_details->name }}" placeholder="{{ __('Your name') }}">
                    </div>
                    <div class="col-md-6 single-input">
                        <label class="label-title">{{ __('Email Address') }} <span style="color:var(--vl-champagne,#c4a882);">*</span></label>
                        <input class="form--control" type="email" name="email" value="{{ $user_details->email }}" placeholder="{{ __('Your email') }}">
                    </div>
                    <div class="col-md-6 single-input">
                        <label class="label-title">{{ __('Phone Number') }}</label>
                        <input class="form--control" id="phone" type="text" name="phone" value="{{ $user_details->mobile }}" placeholder="{{ __('Phone number') }}">
                    </div>
                    <div class="col-md-6 single-input">
                        <label class="label-title">{{ __('Company') }}</label>
                        <input class="form--control" type="text" name="company" value="{{ $user_details->company }}" placeholder="{{ __('Company name') }}">
                    </div>
                    <div class="col-md-4 single-input">
                        <label class="label-title">{{ __('Country') }}</label>
                        <select class="form--control" name="country" style="cursor:pointer;">
                            <option value="">{{ __('Select country') }}</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}" @selected($country->id == $user_details->country)>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 single-input">
                        <label class="label-title">{{ __('State') }}</label>
                        <input class="form--control stateField" type="text" name="state"
                            value="@auth('web'){{ $user_details->state ?? '' }}@else{{ old('state') }}@endauth"
                            placeholder="{{ __('State / Province') }}">
                    </div>
                    <div class="col-md-4 single-input">
                        <label class="label-title">{{ __('City') }}</label>
                        <input class="form--control cityField" type="text" name="city"
                            value="@auth('web'){{ $user_details->city ?? '' }}@else{{ old('city') }}@endauth"
                            placeholder="{{ __('City') }}">
                    </div>
                    <div class="col-md-6 single-input">
                        <label class="label-title">{{ __('Postal Code') }}</label>
                        <input class="form--control" type="text" name="postal_code" value="{{ $user_details->postal_code }}" placeholder="{{ __('Postal code') }}">
                    </div>
                    <div class="col-12 single-input">
                        <label class="label-title">{{ __('Address') }}</label>
                        <textarea class="form--control" name="address" rows="3" style="resize:vertical;height:auto;">{{ $user_details->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="profile-submit-btn"
                                style="display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:var(--vl-dark,#1a1a1a);color:#fff;border:none;border-radius:var(--vl-radius,4px);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;">
                            <i class="las la-save"></i> {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Address Tab ===== --}}
<div class="vl-account-panel" id="vl-tab-address" style="display:none;">
    <div class="vl-dash-card">
        <div class="vl-dash-card-header">{{ __('Address Book') }}</div>
        <div class="vl-dash-card-body">
            <form class="address_form" action="#" id="vl_address_form">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6 single-input">
                        <label class="label-title">{{ __('Full Name') }} <span style="color:var(--vl-champagne,#c4a882);">*</span></label>
                        <input class="form--control" type="text" name="full_name" value="{{ $delivery?->full_name }}" placeholder="{{ __('Full name') }}">
                    </div>
                    <div class="col-md-6 single-input">
                        <label class="label-title">{{ __('Email') }} <span style="color:var(--vl-champagne,#c4a882);">*</span></label>
                        <input class="form--control" type="email" name="email" value="{{ $delivery?->email }}" placeholder="{{ __('Email address') }}">
                    </div>
                    <div class="col-md-6 single-input">
                        <label class="label-title">{{ __('Phone') }} <span style="color:var(--vl-champagne,#c4a882);">*</span></label>
                        <input class="form--control" id="address_phone" type="text" name="phone" value="{{ $delivery?->phone }}" placeholder="{{ __('Phone number') }}">
                    </div>
                    <div class="col-md-6 single-input">
                        <label class="label-title">{{ __('Postal Code') }}</label>
                        <input class="form--control" type="text" name="postal_code" value="{{ $delivery?->postal_code }}" placeholder="{{ __('Postal code') }}">
                    </div>
                    <div class="col-md-4 single-input">
                        <label class="label-title">{{ __('Country') }} <span style="color:var(--vl-champagne,#c4a882);">*</span></label>
                        <select class="form--control countryField" name="country" id="vlAddressCountryField" style="cursor:pointer;">
                            <option value="">{{ __('Select country') }}</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}" @selected($country->id == ($delivery->country_id ?? ''))>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 single-input">
                        <label class="label-title">{{ __('State') }} <span style="color:var(--vl-champagne,#c4a882);">*</span></label>
                        <select class="form--control stateField" name="state" id="vlAddressStateField" style="cursor:pointer;">
                            <option value="">{{ __('Select state') }}</option>
                            @foreach($shipStates as $st)
                                <option value="{{ $st->id }}" @selected($st->id == ($delivery->state_id ?? ''))>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 single-input">
                        <label class="label-title">{{ __('City') }}</label>
                        <input class="form--control cityField" type="text" name="city" value="{{ old('city', $delivery?->city) }}" placeholder="{{ __('City / Town') }}">
                    </div>
                    <div class="col-12 single-input">
                        <label class="label-title">{{ __('Address') }}</label>
                        <textarea class="form--control" name="address" rows="3" style="resize:vertical;height:auto;">{{ $delivery?->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="address-submit-btn"
                                style="display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:var(--vl-dark,#1a1a1a);color:#fff;border:none;border-radius:var(--vl-radius,4px);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;">
                            <i class="las la-save"></i> {{ __('Save Address') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Password Tab ===== --}}
<div class="vl-account-panel" id="vl-tab-password" style="display:none;">
    <div class="vl-dash-card">
        <div class="vl-dash-card-header">{{ __('Change Password') }}</div>
        <div class="vl-dash-card-body">
            <form class="change_password_form" action="#">
                @csrf
                <div class="row g-3" style="max-width:480px;">
                    <div class="col-12 single-input">
                        <label class="label-title">{{ __('Current Password') }} <span style="color:var(--vl-champagne,#c4a882);">*</span></label>
                        <input class="form--control" type="password" name="old_password" placeholder="{{ __('Enter current password') }}">
                    </div>
                    <div class="col-12 single-input">
                        <label class="label-title">{{ __('New Password') }} <span style="color:var(--vl-champagne,#c4a882);">*</span></label>
                        <input class="form--control" type="password" name="password" placeholder="{{ __('Enter new password') }}">
                    </div>
                    <div class="col-12 single-input">
                        <label class="label-title">{{ __('Confirm New Password') }} <span style="color:var(--vl-champagne,#c4a882);">*</span></label>
                        <input class="form--control" type="password" name="password_confirmation" placeholder="{{ __('Confirm new password') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="save-password-btn"
                                style="display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:var(--vl-dark,#1a1a1a);color:#fff;border:none;border-radius:var(--vl-radius,4px);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;">
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
    function vlTab(key) {
        $('.vl-account-panel').hide();
        $('.vl-account-tab').css({'color':'var(--vl-muted,#999)','border-bottom-color':'transparent'});
        $('#vl-tab-' + key).show();
        $('.vl-account-tab[data-tab="' + key + '"]').css({'color':'var(--vl-champagne,#c4a882)','border-bottom-color':'var(--vl-champagne,#c4a882)'});
    }
    vlTab('profile');
    $(document).on('click', '.vl-account-tab', function () { vlTab($(this).data('tab')); });

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

    $(document).on('change', '#vlAddressCountryField', function () {
        $.post('{{ theme_state_search_url() }}', { _token: '{{ csrf_token() }}', country: $(this).val() }, function (data) {
            var sf = $('#vlAddressStateField').empty().append('<option value="">{{ __("Select a state") }}</option>');
            $.each(data.states||[], function (i, v) { sf.append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });
});
</script>
@endsection
