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
        ['profile',  'las la-user-edit',    __('Edit Profile')],
        ['address',  'las la-map-marker',   __('Address Book')],
        ['password', 'las la-lock',          __('Change Password')],
    ] as [$key, $icon, $label])
    <button class="gl-account-tab" data-tab="{{ $key }}" type="button"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:none;border:1px solid var(--gl-border);border-radius:var(--gl-radius);font-size:13px;font-weight:600;color:var(--gl-muted);cursor:pointer;transition:all .2s;">
        <i class="{{ $icon }}"></i> {{ $label }}
    </button>
    @endforeach
</div>

{{-- ===== Profile Tab ===== --}}
<div class="gl-account-panel" id="gl-tab-profile">
    <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;margin-bottom:20px;">
        <div style="padding:14px 20px;border-bottom:1px solid var(--gl-border);font-weight:700;color:var(--gl-dark);display:flex;align-items:center;gap:8px;">
            <i class="las la-user-edit" style="color:var(--gl-gold);font-size:17px;"></i> {{ __('Edit Profile') }}
        </div>
        <div style="padding:24px;">
            <form class="profile-edit-form" action="#" enctype="multipart/form-data">
                @csrf
                <div style="display:flex;align-items:center;gap:16px;padding:14px 16px;background:var(--gl-bg,#f8f8f8);border:1px solid var(--gl-border);border-radius:var(--gl-radius);margin-bottom:20px;">
                    <x-fields.media-upload :name="'image'" :title="''" :id="$user_details->image"/>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:var(--gl-dark);margin-bottom:4px;">{{ __('Profile Photo') }}</div>
                        <div style="font-size:12px;color:var(--gl-muted);">{{ __('Click the image to upload') }}</div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="label-title">{{ __('Full Name') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input class="form--control" type="text" name="name" value="{{ $user_details->name }}" placeholder="{{ __('Your name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="label-title">{{ __('Email Address') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input class="form--control" type="email" name="email" value="{{ $user_details->email }}" placeholder="{{ __('Your email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="label-title">{{ __('Phone Number') }}</label>
                        <input class="form--control" id="phone" type="text" name="phone" value="{{ $user_details->mobile }}" placeholder="{{ __('Phone number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="label-title">{{ __('Company') }}</label>
                        <input class="form--control" type="text" name="company" value="{{ $user_details->company }}" placeholder="{{ __('Company name') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="label-title">{{ __('Country') }}</label>
                        <select class="form--control" name="country" style="cursor:pointer;">
                            <option value="">{{ __('Select country') }}</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}" @selected($country->id == $user_details->country)>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="label-title">{{ __('State') }}</label>
                        <input class="form--control stateField" type="text" name="state"
                            value="@auth('web'){{ $user_details->state ?? '' }}@else{{ old('state') }}@endauth"
                            placeholder="{{ __('State / Province') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="label-title">{{ __('City') }}</label>
                        <input class="form--control cityField" type="text" name="city"
                            value="@auth('web'){{ $user_details->city ?? '' }}@else{{ old('city') }}@endauth"
                            placeholder="{{ __('City') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="label-title">{{ __('Postal Code') }}</label>
                        <input class="form--control" type="text" name="postal_code" value="{{ $user_details->postal_code }}" placeholder="{{ __('Postal code') }}">
                    </div>
                    <div class="col-12">
                        <label class="label-title">{{ __('Address') }}</label>
                        <textarea class="form--control form--message" name="address" rows="3" style="resize:vertical;">{{ $user_details->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="profile-submit-btn"
                                style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;">
                            <i class="las la-save"></i> {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Address Tab ===== --}}
<div class="gl-account-panel" id="gl-tab-address" style="display:none;">
    <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;margin-bottom:20px;">
        <div style="padding:14px 20px;border-bottom:1px solid var(--gl-border);font-weight:700;color:var(--gl-dark);display:flex;align-items:center;gap:8px;">
            <i class="las la-map-marker" style="color:var(--gl-gold);font-size:17px;"></i> {{ __('Address Book') }}
        </div>
        <div style="padding:24px;">
            <form class="address_form" action="#" id="gl_address_form">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="label-title">{{ __('Full Name') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input class="form--control" type="text" name="full_name" value="{{ $delivery?->full_name }}" placeholder="{{ __('Full name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="label-title">{{ __('Email') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input class="form--control" type="email" name="email" value="{{ $delivery?->email }}" placeholder="{{ __('Email address') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="label-title">{{ __('Phone') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input class="form--control" id="address_phone" type="text" name="phone" value="{{ $delivery?->phone }}" placeholder="{{ __('Phone number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="label-title">{{ __('Postal Code') }}</label>
                        <input class="form--control" type="text" name="postal_code" value="{{ $delivery?->postal_code }}" placeholder="{{ __('Postal code') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="label-title">{{ __('Country') }} <span style="color:var(--gl-gold);">*</span></label>
                        <select class="form--control countryField" name="country" id="glAddressCountryField" style="cursor:pointer;">
                            <option value="">{{ __('Select country') }}</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->id }}" @selected($country->id == ($delivery->country_id ?? ''))>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="label-title">{{ __('State') }} <span style="color:var(--gl-gold);">*</span></label>
                        <select class="form--control stateField" name="state" id="glAddressStateField" style="cursor:pointer;">
                            <option value="">{{ __('Select state') }}</option>
                            @foreach($shipStates as $st)
                                <option value="{{ $st->id }}" @selected($st->id == ($delivery->state_id ?? ''))>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="label-title">{{ __('City') }}</label>
                        <input class="form--control cityField" type="text" name="city" value="{{ old('city', $delivery?->city) }}" placeholder="{{ __('City / Town') }}">
                    </div>
                    <div class="col-12">
                        <label class="label-title">{{ __('Address') }}</label>
                        <textarea class="form--control form--message" name="address" rows="3" style="resize:vertical;">{{ $delivery?->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="address-submit-btn"
                                style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;">
                            <i class="las la-save"></i> {{ __('Save Address') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Password Tab ===== --}}
<div class="gl-account-panel" id="gl-tab-password" style="display:none;">
    <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;margin-bottom:20px;">
        <div style="padding:14px 20px;border-bottom:1px solid var(--gl-border);font-weight:700;color:var(--gl-dark);display:flex;align-items:center;gap:8px;">
            <i class="las la-lock" style="color:var(--gl-gold);font-size:17px;"></i> {{ __('Change Password') }}
        </div>
        <div style="padding:24px;">
            <form class="change_password_form" action="#">
                @csrf
                <div class="row g-3" style="max-width:480px;">
                    <div class="col-12">
                        <label class="label-title">{{ __('Current Password') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input class="form--control" type="password" name="old_password" placeholder="{{ __('Enter current password') }}">
                    </div>
                    <div class="col-12">
                        <label class="label-title">{{ __('New Password') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input class="form--control" type="password" name="password" placeholder="{{ __('Enter new password') }}">
                    </div>
                    <div class="col-12">
                        <label class="label-title">{{ __('Confirm New Password') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input class="form--control" type="password" name="password_confirmation" placeholder="{{ __('Confirm new password') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="save-password-btn"
                                style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:13px;font-weight:700;cursor:pointer;transition:background .2s;">
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
    function glTab(key) {
        $('.gl-account-panel').hide();
        $('.gl-account-tab').css({'color':'var(--gl-muted)','background':'none','border-color':'var(--gl-border)'});
        $('#gl-tab-' + key).show();
        $('.gl-account-tab[data-tab="' + key + '"]').css({'color':'var(--gl-gold)','background':'rgba(180,140,80,.07)','border-color':'var(--gl-gold)'});
    }
    glTab('profile');
    $(document).on('click', '.gl-account-tab', function () { glTab($(this).data('tab')); });

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

    $(document).on('change', '#glAddressCountryField', function () {
        $.post('{{ theme_state_search_url() }}', { _token: '{{ csrf_token() }}', country: $(this).val() }, function (data) {
            var sf = $('#glAddressStateField').empty().append('<option value="">{{ __("Select a state") }}</option>');
            $.each(data.states||[], function (i, v) { sf.append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });
});
</script>
@endsection
