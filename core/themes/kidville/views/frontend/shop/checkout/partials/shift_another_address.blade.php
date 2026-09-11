@php
    $inp = 'width:100%;padding:10px 14px;border:2px solid var(--kv-border);border-radius:var(--kv-radius-sm);font-size:14px;outline:none;background:#fff;transition:border-color .2s;color:var(--kv-dark);';
    $lbl = 'font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--kv-muted);margin-bottom:6px;display:block;';
@endphp

<div style="margin-top:20px;padding-top:20px;border-top:2px dashed var(--kv-border);">
    <a href="javascript:void(0)" class="create-accounts shift-another-address"
       style="font-size:13px;color:var(--kv-blue);font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
        <i class="las la-map-marker-alt"></i> {{ __('Ship to a Different Address') }}
    </a>

    <div class="checkout-address-form-wrapper" style="display:none;margin-top:20px;">
        <div style="background:var(--kv-light);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:20px;">
            <div style="font-size:13px;font-weight:800;color:var(--kv-red);margin-bottom:16px;">
                {{ __('Alternate Address') }}
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Full Name') }}</label>
                    <input type="text" name="shift_name" placeholder="{{ __('Full Name') }}" style="{{ $inp }}"
                           onfocus="this.style.borderColor='var(--kv-red)'" onblur="this.style.borderColor='var(--kv-border)'">
                </div>
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Mobile Number') }}</label>
                    <input type="tel" name="shift_phone" placeholder="{{ __('Mobile Number') }}" style="{{ $inp }}"
                           onfocus="this.style.borderColor='var(--kv-red)'" onblur="this.style.borderColor='var(--kv-border)'">
                </div>
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Email Address') }}</label>
                    <input type="email" name="shift_email" placeholder="{{ __('Email') }}" style="{{ $inp }}"
                           onfocus="this.style.borderColor='var(--kv-red)'" onblur="this.style.borderColor='var(--kv-border)'">
                </div>
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Country') }}</label>
                    <select name="shift_country" class="shift-another-country" style="{{ $inp }}cursor:pointer;" id="shift_country">
                        <option value="" selected disabled>{{ __('Select a country') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('State') }}</label>
                    <select name="shift_state" class="shift-another-state stateField form--control" style="{{ $inp }}cursor:pointer;" id="shift_state"></select>
                </div>
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('City / Town') }}</label>
                    <select name="shift_city" class="shift-another-city cityField form--control" style="{{ $inp }}cursor:pointer;" id="shift_city"></select>
                </div>
                <div class="col-12">
                    <label style="{{ $lbl }}">{{ __('Address') }}</label>
                    <textarea name="shift_address" rows="2" placeholder="{{ __('Type Address') }}"
                              style="{{ $inp }}height:70px;resize:vertical;"
                              onfocus="this.style.borderColor='var(--kv-red)'" onblur="this.style.borderColor='var(--kv-border)'"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
