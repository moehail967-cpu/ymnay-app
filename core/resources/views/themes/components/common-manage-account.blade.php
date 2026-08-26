@php
    $delivery = $user_details?->delivery_address;
    $shipping_states = \Modules\CountryManage\Entities\State::where([
        'status'     => 'publish',
        'country_id' => $delivery->country_id ?? '',
    ])->get();
@endphp

<style>
/* ===== Manage Account — shared component ===== */
.ma-tabs{display:flex;border-bottom:2px solid #f0f0f0;margin-bottom:28px;overflow-x:auto;gap:0}
.ma-tab-btn{padding:12px 22px;background:none;border:none;border-bottom:3px solid transparent;margin-bottom:-2px;font-size:13px;font-weight:700;color:#6b7280;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:7px;transition:color .2s,border-color .2s;letter-spacing:.3px}
.ma-tab-btn.ma-active{color:var(--main-color-one,#333);border-bottom-color:var(--main-color-one,#333)}
.ma-tab-btn i{font-size:16px}
.ma-panel{display:none}
.ma-panel.ma-active{display:block}
.ma-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;margin-bottom:20px}
.ma-card-head{padding:15px 22px;border-bottom:1px solid #f3f4f6;font-size:15px;font-weight:700;color:#111827;display:flex;align-items:center;gap:8px}
.ma-card-head i{color:var(--main-color-one,#333);font-size:18px}
.ma-card-body{padding:24px}
.ma-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.ma-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}
@media(max-width:640px){.ma-grid-2,.ma-grid-3{grid-template-columns:1fr}}
.ma-field{display:flex;flex-direction:column;gap:5px;margin-bottom:16px}
.ma-field:last-child{margin-bottom:0}
.ma-label{font-size:13px;font-weight:600;color:#374151}
.ma-req{color:#ef4444}
.ma-input{width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:10px 14px;font-size:14px;color:#111827;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s;font-family:inherit}
.ma-input:focus{border-color:var(--main-color-one,#333);box-shadow:0 0 0 3px color-mix(in srgb,var(--main-color-one,#333) 12%,transparent)}
select.ma-input{cursor:pointer}
textarea.ma-input{resize:vertical;min-height:90px}
.ma-btn{display:inline-flex;align-items:center;gap:7px;padding:11px 28px;border-radius:8px;font-size:14px;font-weight:700;background:var(--main-color-one,#333);color:#fff;border:none;cursor:pointer;transition:opacity .2s;margin-top:4px}
.ma-btn:hover{opacity:.88}
.ma-btn i{font-size:17px}
/* Photo row */
.ma-photo-row{display:flex;align-items:center;gap:20px;margin-bottom:24px;padding:18px 20px;background:#f9fafb;border-radius:10px;border:1px solid #e5e7eb}
.ma-photo-meta h6{font-size:14px;font-weight:700;color:#111827;margin:0 0 4px}
.ma-photo-meta p{font-size:12px;color:#6b7280;margin:0}
/* Info pairs (read-only overview) */
.ma-pair{display:flex;padding:9px 0;border-bottom:1px solid #f3f4f6;font-size:14px}
.ma-pair:last-child{border-bottom:none}
.ma-pair-label{flex:0 0 130px;font-weight:600;color:#6b7280}
.ma-pair-value{flex:1;color:#111827}
</style>

<div>
    {{-- ====== Tab Navigation ====== --}}
    <div class="ma-tabs">
        @foreach([
            ['profile',  'mdi-account-edit-outline', __('Edit Profile')],
            ['address',  'mdi-map-marker-outline',   __('Address Book')],
            ['password', 'mdi-lock-outline',          __('Change Password')],
        ] as [$key, $icon, $label])
        <button class="ma-tab-btn {{ $loop->first ? 'ma-active' : '' }}" data-ma-tab="{{ $key }}" type="button">
            <i class="mdi {{ $icon }}"></i> {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ====== Profile Tab ====== --}}
    <div class="ma-panel ma-active" id="ma-tab-profile">
        <form class="profile-edit-form" id="user_profile_update_form" action="#" enctype="multipart/form-data">
            @csrf

            {{-- Photo --}}
            <div class="ma-photo-row">
                <x-fields.media-upload :name="'image'" :title="''" :id="$user_details->image"/>
                <div class="ma-photo-meta">
                    <h6>{{ __('Profile Photo') }}</h6>
                    <p>{{ __('Click the image to upload a new profile picture') }}</p>
                </div>
            </div>

            {{-- Personal --}}
            <div class="ma-card">
                <div class="ma-card-head"><i class="mdi mdi-account-outline"></i> {{ __('Personal Information') }}</div>
                <div class="ma-card-body">
                    <div class="ma-grid-2">
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Full Name') }} <span class="ma-req">*</span></label>
                            <input class="ma-input" type="text" name="name" value="{{ $user_details->name }}" placeholder="{{ __('Your name') }}">
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Email Address') }} <span class="ma-req">*</span></label>
                            <input class="ma-input" type="email" name="email" value="{{ $user_details->email }}" placeholder="{{ __('Your email') }}">
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Phone Number') }}</label>
                            <input class="ma-input" id="phone" type="text" name="phone" value="{{ $user_details->mobile }}" placeholder="{{ __('Phone number') }}">
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Company') }}</label>
                            <input class="ma-input" type="text" name="company" value="{{ $user_details->company }}" placeholder="{{ __('Company name') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="ma-card">
                <div class="ma-card-head"><i class="mdi mdi-map-outline"></i> {{ __('Address Details') }}</div>
                <div class="ma-card-body">
                    <div class="ma-grid-3">
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Country') }}</label>
                            <select class="ma-input" name="country" id="profileCountryField">
                                <option value="">{{ __('Select country') }}</option>
                                @foreach($countries ?? [] as $country)
                                    <option value="{{ $country->id }}" @selected($country->id == $user_details->country)>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('State') }}</label>
                            <input class="ma-input stateField" type="text" name="state" id="profileState"
                                value="@auth('web'){{ $user_details->state ?? '' }}@else{{ old('state') }}@endauth"
                                placeholder="{{ __('State / Province') }}">
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('City') }}</label>
                            <input class="ma-input cityField" type="text" name="city"
                                value="@auth('web'){{ $user_details->city ?? '' }}@else{{ old('city') }}@endauth"
                                placeholder="{{ __('City') }}">
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Postal Code') }}</label>
                            <input class="ma-input" type="text" name="postal_code" value="{{ $user_details->postal_code }}" placeholder="{{ __('Postal code') }}">
                        </div>
                    </div>
                    <div class="ma-field" style="margin-top:4px;">
                        <label class="ma-label">{{ __('Address') }}</label>
                        <textarea class="ma-input" name="address" rows="3">{{ $user_details->address }}</textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="ma-btn profile-submit-btn">
                <i class="mdi mdi-content-save-outline"></i> {{ __('Save Changes') }}
            </button>
        </form>
    </div>

    {{-- ====== Address Tab ====== --}}
    <div class="ma-panel" id="ma-tab-address">
        <div class="ma-card">
            <div class="ma-card-head"><i class="mdi mdi-map-marker-outline"></i> {{ __('Billing / Delivery Address') }}</div>
            <div class="ma-card-body">
                <form class="address_form" id="user_address_update_form" action="#">
                    @csrf
                    <div class="ma-grid-2">
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Full Name') }} <span class="ma-req">*</span></label>
                            <input class="ma-input" type="text" name="full_name" value="{{ $delivery?->full_name }}" placeholder="{{ __('Full name') }}">
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Email') }} <span class="ma-req">*</span></label>
                            <input class="ma-input" type="email" name="email" value="{{ $delivery?->email }}" placeholder="{{ __('Email address') }}">
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Phone') }} <span class="ma-req">*</span></label>
                            <input class="ma-input" id="address_phone" type="text" name="phone" value="{{ $delivery?->phone }}" placeholder="{{ __('Phone number') }}">
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Postal Code') }} <span class="ma-req">*</span></label>
                            <input class="ma-input" type="text" name="postal_code" value="{{ $delivery?->postal_code }}" placeholder="{{ __('Postal code') }}">
                        </div>
                    </div>
                    <div class="ma-grid-3" style="margin-bottom:16px;">
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Country') }} <span class="ma-req">*</span></label>
                            <select class="ma-input countryField" name="country" id="addressCountryField">
                                <option value="">{{ __('Select country') }}</option>
                                @foreach($countries ?? [] as $country)
                                    <option value="{{ $country->id }}" @selected($country->id == ($delivery->country_id ?? ''))>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('State') }} <span class="ma-req">*</span></label>
                            <select class="ma-input stateField" name="state" id="addressStateField">
                                <option value="">{{ __('Select state') }}</option>
                                @foreach($shipping_states as $st)
                                    <option value="{{ $st->id }}" @selected($st->id == ($delivery->state_id ?? ''))>{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('City') }} <span class="ma-req">*</span></label>
                            <input class="ma-input cityField" type="text" name="city" value="{{ old('city', $delivery?->city) }}" placeholder="{{ __('City / Town') }}">
                        </div>
                    </div>
                    <div class="ma-field">
                        <label class="ma-label">{{ __('Address') }} <span class="ma-req">*</span></label>
                        <textarea class="ma-input" name="address" rows="3">{{ $delivery?->address }}</textarea>
                    </div>
                    <button type="submit" class="ma-btn address-submit-btn">
                        <i class="mdi mdi-content-save-outline"></i> {{ __('Save Address') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ====== Password Tab ====== --}}
    <div class="ma-panel" id="ma-tab-password">
        <div class="ma-card">
            <div class="ma-card-head"><i class="mdi mdi-lock-outline"></i> {{ __('Change Password') }}</div>
            <div class="ma-card-body">
                <form class="change_password_form" id="user_password_change_form" action="#">
                    @csrf
                    <div style="max-width:460px;">
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Current Password') }} <span class="ma-req">*</span></label>
                            <input class="ma-input" type="password" name="old_password" placeholder="{{ __('Enter current password') }}">
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('New Password') }} <span class="ma-req">*</span></label>
                            <input class="ma-input" type="password" name="password" placeholder="{{ __('Enter new password') }}">
                        </div>
                        <div class="ma-field">
                            <label class="ma-label">{{ __('Confirm New Password') }} <span class="ma-req">*</span></label>
                            <input class="ma-input" type="password" name="password_confirmation" placeholder="{{ __('Confirm new password') }}">
                        </div>
                        <button type="submit" class="ma-btn save-password-btn">
                            <i class="mdi mdi-lock-check-outline"></i> {{ __('Update Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<x-media-upload.markup/>

<script>
$(function () {
    // ---- Tab switching ----
    function maSwitchTab(key) {
        $('.ma-tab-btn').removeClass('ma-active');
        $('.ma-panel').removeClass('ma-active');
        $('.ma-tab-btn[data-ma-tab="' + key + '"]').addClass('ma-active');
        $('#ma-tab-' + key).addClass('ma-active');
    }
    $(document).on('click', '.ma-tab-btn', function () {
        maSwitchTab($(this).data('ma-tab'));
    });

    // ---- Media upload trigger ----
    $(document).on('click', '.attachment-preview .user-thumb', function () {
        $('.media_upload_form_btn').trigger('click');
    });

    // ---- Profile update ----
    $(document).on('submit', '#user_profile_update_form, form.profile-edit-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ theme_user_profile_update_url() }}',
            type: 'POST', processData: false, contentType: false,
            data: new FormData(e.target),
            beforeSend: function () { $('.loader').show(); },
            success: function (d) {
                $('.loader').hide();
                if (d.msg) toastr.success(d.msg);
            },
            error: function (xhr) {
                $('.loader').hide();
                $.each((xhr.responseJSON || {}).errors || {}, function (k, v) { toastr.error(v); });
            }
        });
    });

    // ---- Address update ----
    $(document).on('submit', '#user_address_update_form, form.address_form', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '{{ theme_user_address_update_url() }}',
            data: $(this).serialize(),
            beforeSend: function () { $('.loader').show(); },
            success: function (d) {
                $('.loader').hide();
                if (d.msg) toastr.success(d.msg);
            },
            error: function (xhr) {
                $('.loader').hide();
                $.each((xhr.responseJSON || {}).errors || {}, function (k, v) { toastr.error(v); });
            }
        });
    });

    // ---- Password change ----
    $(document).on('submit', '#user_password_change_form, .change_password_form', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ theme_user_password_change_url() }}',
            type: 'POST', processData: false, contentType: false,
            data: new FormData(e.target),
            beforeSend: function () { $('.loader').show(); },
            success: function (d) {
                $('.loader').hide();
                if (d.type === 'success') {
                    toastr.success(d.msg);
                    toastr.warning('{{ __("We\\'re logging you out for security and redirecting to login…") }}');
                    setTimeout(function () { location.href = d.url; }, 3000);
                } else {
                    toastr.error(d.msg);
                }
            },
            error: function (xhr) {
                $('.loader').hide();
                $.each((xhr.responseJSON || {}).errors || {}, function (k, v) { toastr.error(v); });
            }
        });
    });

    // ---- Country → state (profile form) ----
    $(document).on('change', '#profileCountryField', function () {
        $.post('{{ theme_state_search_url() }}',
            { _token: '{{ csrf_token() }}', country: $(this).val() },
            function (data) {
                $('#profileState').val('');
            }
        );
    });

    // ---- Country → state (address form) ----
    $(document).on('change', '#addressCountryField', function () {
        $.post('{{ theme_state_search_url() }}',
            { _token: '{{ csrf_token() }}', country: $(this).val() },
            function (data) {
                var sf = $('#addressStateField').empty().append('<option value="">{{ __("Select state") }}</option>');
                $.each(data.states || [], function (i, v) {
                    sf.append('<option value="' + v.id + '">' + v.name + '</option>');
                });
            }
        );
    });
});
</script>
