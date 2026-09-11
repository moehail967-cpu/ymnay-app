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

{{-- Tab Nav --}}
<div class="fp-dash-card" style="margin-bottom:20px;">
    <div class="fp-dash-card-body" style="padding:0;">
        <div style="display:flex;border-bottom:1px solid var(--fp-border);overflow-x:auto;">
            @foreach([
                ['profile',  'mdi-account-outline',   __('Profile')],
                ['address',  'mdi-map-marker-outline', __('Address')],
                ['password', 'mdi-lock-outline',       __('Password')],
            ] as [$key, $icon, $label])
            <button class="fp-account-tab-btn" data-tab="{{ $key }}" type="button"
                    style="padding:13px 20px;background:none;border:none;border-bottom:3px solid transparent;font-family:var(--fp-font-head);font-size:13px;font-weight:700;color:var(--fp-muted);text-transform:uppercase;letter-spacing:1px;cursor:pointer;white-space:nowrap;transition:all .2s;display:flex;align-items:center;gap:8px;">
                <i class="mdi {{ $icon }}"></i> {{ $label }}
            </button>
            @endforeach
        </div>
    </div>
</div>

{{-- ===== Profile Tab ===== --}}
<div class="fp-account-panel" id="fp-tab-profile">
    <div class="fp-dash-card">
        <div class="fp-dash-card-header">{{ __('Edit Profile') }}</div>
        <div class="fp-dash-card-body">
            <form class="profile-edit-form" action="#" enctype="multipart/form-data">
                @csrf

                {{-- Avatar --}}
                <div style="display:flex;align-items:center;gap:18px;padding:14px 16px;background:var(--fp-mid);border:1px solid var(--fp-border);border-radius:var(--fp-radius);margin-bottom:22px;">
                    <x-fields.media-upload :name="'image'" :title="''" :id="$user_details->image"/>
                    <div>
                        <div style="font-family:var(--fp-font-head);font-size:13px;font-weight:700;color:var(--fp-text);letter-spacing:1px;text-transform:uppercase;margin-bottom:4px;">{{ __('Profile Photo') }}</div>
                        <div style="font-size:12px;color:var(--fp-muted);">{{ __('Click the image to upload a new photo') }}</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fp-dash-label">{{ __('Full Name') }} <span style="color:var(--fp-green);">*</span></label>
                        <input class="fp-dash-input" type="text" name="name" value="{{ $user_details->name }}" placeholder="{{ __('Your name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fp-dash-label">{{ __('Email Address') }} <span style="color:var(--fp-green);">*</span></label>
                        <input class="fp-dash-input" type="email" name="email" value="{{ $user_details->email }}" placeholder="{{ __('Your email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fp-dash-label">{{ __('Phone Number') }}</label>
                        <input class="fp-dash-input" id="phone" type="text" name="phone" value="{{ $user_details->mobile }}" placeholder="{{ __('Phone number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fp-dash-label">{{ __('Company') }}</label>
                        <input class="fp-dash-input" type="text" name="company" value="{{ $user_details->company }}" placeholder="{{ __('Company name') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="fp-dash-label">{{ __('Country') }}</label>
                        <select class="fp-dash-input" name="country" id="profileCountryField" style="cursor:pointer;">
                            <option value="">{{ __('Select country') }}</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}" @selected($country->id == $user_details->country)>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fp-dash-label">{{ __('State') }}</label>
                        <input class="fp-dash-input stateField" type="text" name="state" id="profileState"
                            value="@auth('web'){{ $user_details->state ?? '' }}@else{{ old('state') }}@endauth"
                            placeholder="{{ __('State / Province') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="fp-dash-label">{{ __('City') }}</label>
                        <input class="fp-dash-input cityField" type="text" name="city"
                            value="@auth('web'){{ $user_details->city ?? '' }}@else{{ old('city') }}@endauth"
                            placeholder="{{ __('City') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fp-dash-label">{{ __('Postal Code') }}</label>
                        <input class="fp-dash-input" type="text" name="postal_code" value="{{ $user_details->postal_code }}" placeholder="{{ __('Postal code') }}">
                    </div>
                    <div class="col-12">
                        <label class="fp-dash-label">{{ __('Address') }}</label>
                        <textarea class="fp-dash-input" name="address" rows="3" style="resize:vertical;">{{ $user_details->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="fp-btn fp-btn-primary profile-submit-btn">
                            <i class="mdi mdi-content-save-outline"></i> {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Address Tab ===== --}}
<div class="fp-account-panel" id="fp-tab-address" style="display:none;">
    <div class="fp-dash-card">
        <div class="fp-dash-card-header">{{ __('Address Book') }}</div>
        <div class="fp-dash-card-body">
            <form class="address_form" action="#" id="fp_address_form">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fp-dash-label">{{ __('Full Name') }} <span style="color:var(--fp-green);">*</span></label>
                        <input class="fp-dash-input" type="text" name="full_name" value="{{ $delivery?->full_name }}" placeholder="{{ __('Full name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fp-dash-label">{{ __('Email') }} <span style="color:var(--fp-green);">*</span></label>
                        <input class="fp-dash-input" type="email" name="email" value="{{ $delivery?->email }}" placeholder="{{ __('Email address') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fp-dash-label">{{ __('Phone') }} <span style="color:var(--fp-green);">*</span></label>
                        <input class="fp-dash-input" id="address_phone" type="text" name="phone" value="{{ $delivery?->phone }}" placeholder="{{ __('Phone number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="fp-dash-label">{{ __('Postal Code') }}</label>
                        <input class="fp-dash-input" type="text" name="postal_code" value="{{ $delivery?->postal_code }}" placeholder="{{ __('Postal code') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="fp-dash-label">{{ __('Country') }} <span style="color:var(--fp-green);">*</span></label>
                        <select class="fp-dash-input countryField" name="country" id="addressCountryField" style="cursor:pointer;">
                            <option value="">{{ __('Select country') }}</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}" @selected($country->id == ($delivery->country_id ?? ''))>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fp-dash-label">{{ __('State') }} <span style="color:var(--fp-green);">*</span></label>
                        <select class="fp-dash-input stateField" name="state" id="addressStateField" style="cursor:pointer;">
                            <option value="">{{ __('Select state') }}</option>
                            @foreach($shipStates as $st)
                                <option value="{{ $st->id }}" @selected($st->id == ($delivery->state_id ?? ''))>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fp-dash-label">{{ __('City') }}</label>
                        <input class="fp-dash-input cityField" type="text" name="city" value="{{ old('city', $delivery?->city) }}" placeholder="{{ __('City / Town') }}">
                    </div>
                    <div class="col-12">
                        <label class="fp-dash-label">{{ __('Address') }}</label>
                        <textarea class="fp-dash-input" name="address" rows="3" style="resize:vertical;">{{ $delivery?->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="fp-btn fp-btn-primary address-submit-btn">
                            <i class="mdi mdi-content-save-outline"></i> {{ __('Save Address') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Password Tab ===== --}}
<div class="fp-account-panel" id="fp-tab-password" style="display:none;">
    <div class="fp-dash-card">
        <div class="fp-dash-card-header">{{ __('Change Password') }}</div>
        <div class="fp-dash-card-body">
            <form class="change_password_form" action="#">
                @csrf
                <div class="row g-3" style="max-width:480px;">
                    <div class="col-12">
                        <label class="fp-dash-label">{{ __('Current Password') }} <span style="color:var(--fp-green);">*</span></label>
                        <input class="fp-dash-input" type="password" name="old_password" placeholder="{{ __('Enter current password') }}">
                    </div>
                    <div class="col-12">
                        <label class="fp-dash-label">{{ __('New Password') }} <span style="color:var(--fp-green);">*</span></label>
                        <input class="fp-dash-input" type="password" name="password" placeholder="{{ __('Enter new password') }}">
                    </div>
                    <div class="col-12">
                        <label class="fp-dash-label">{{ __('Confirm New Password') }} <span style="color:var(--fp-green);">*</span></label>
                        <input class="fp-dash-input" type="password" name="password_confirmation" placeholder="{{ __('Confirm new password') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="fp-btn fp-btn-primary save-password-btn">
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
    function fpTab(key) {
        $('.fp-account-panel').hide();
        $('.fp-account-tab-btn').css({'color':'var(--fp-muted)','border-bottom-color':'transparent'});
        $('#fp-tab-' + key).show();
        $('.fp-account-tab-btn[data-tab="' + key + '"]').css({'color':'var(--fp-green)','border-bottom-color':'var(--fp-green)'});
    }
    fpTab('profile');
    $(document).on('click', '.fp-account-tab-btn', function () { fpTab($(this).data('tab')); });

    setTimeout(function () {
        $('#phone').val('{{ $user_details->mobile ?? "" }}');
        $('#address_phone').val('{{ $user_details?->user_delivery_address?->phone ?? "" }}');
    }, 500);

    $(document).on('click', '.attachment-preview .user-thumb', function () { $('.media_upload_form_btn').trigger('click'); });

    // Profile update
    $(document).on('submit', 'form.profile-edit-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ theme_user_profile_update_url() }}', type: 'POST',
            processData: false, contentType: false, data: new FormData(e.target),
            beforeSend: function () { $('.loader').show(); },
            success: function (d) { $('.loader').hide(); if (d.msg) toastr.success(d.msg); },
            error: function (xhr) { $('.loader').hide(); $.each((xhr.responseJSON||{}).errors||{}, function (k, v) { toastr.error(v); }); }
        });
    });

    // Address update
    $(document).on('click', '.address-submit-btn', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST', url: '{{ theme_user_address_update_url() }}',
            data: {
                _token:      '{{ csrf_token() }}',
                name:        $('.address_form input[name=full_name]').val(),
                email:       $('.address_form input[name=email]').val(),
                phone:       $('.address_form input[name=phone]').val(),
                country:     $('.address_form select[name=country]').val(),
                state:       $('.address_form select[name=state]').val(),
                city:        $('.address_form input[name=city]').val(),
                postal_code: $('.address_form input[name=postal_code]').val(),
                address:     $('.address_form textarea[name=address]').val(),
            },
            beforeSend: function () { $('.loader').show(); },
            success: function (d) { $('.loader').hide(); if (d.msg) toastr.success(d.msg); },
            error: function (xhr) { $('.loader').hide(); $.each((xhr.responseJSON||{}).errors||{}, function (k, v) { toastr.error(v); }); }
        });
    });

    // Password change
    $(document).on('submit', '.change_password_form', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ theme_user_password_change_url() }}', type: 'POST',
            processData: false, contentType: false, data: new FormData(e.target),
            beforeSend: function () { $('.loader').show(); },
            success: function (d) {
                $('.loader').hide();
                if (d.type === 'success') {
                    toastr.success(d.msg);
                    toastr.warning('{{ __("We\'re logging you out for security…") }}');
                    setTimeout(function () { location.href = d.url; }, 3000);
                } else { toastr.error(d.msg); }
            },
            error: function (xhr) { $('.loader').hide(); $.each((xhr.responseJSON||{}).errors||{}, function (k, v) { toastr.error(v); }); }
        });
    });

    // Country → state (address form)
    $(document).on('change', '#addressCountryField', function () {
        $.post('{{ theme_state_search_url() }}', { _token: '{{ csrf_token() }}', country: $(this).val() }, function (data) {
            var sf = $('#addressStateField').empty().append('<option value="">{{ __("Select state") }}</option>');
            $.each(data.states||[], function (i, v) { sf.append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });
});
</script>
@endsection
