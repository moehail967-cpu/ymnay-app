@php
    $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:14px;font-family:Georgia,serif;outline:none;background:#fff;transition:border-color .2s;color:var(--gc-dark);';
    $lbl = 'font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:6px;display:block;';
@endphp

<div style="margin-top:20px;padding-top:20px;border-top:1px dashed var(--gc-border);">
    <a href="javascript:void(0)" class="create-accounts shift-another-address"
       style="font-size:13px;color:var(--gc-rose);font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-style:italic;">
        <i class="las la-map-marker-alt"></i> {{ __('Ship to a Different Address') }}
    </a>

    <div class="checkout-address-form-wrapper" style="display:none;margin-top:20px;">
        <div style="background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;">
            <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:16px;">
                {{ __('Alternate Address') }}
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Full Name') }}</label>
                    <input type="text" name="shift_name" placeholder="{{ __('Full Name') }}" style="{{ $inp }}"
                           onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                </div>
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Mobile Number') }}</label>
                    <input type="tel" name="shift_phone" placeholder="{{ __('Mobile Number') }}" style="{{ $inp }}"
                           onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                </div>
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Email Address') }}</label>
                    <input type="email" name="shift_email" placeholder="{{ __('Email') }}" style="{{ $inp }}"
                           onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
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
                              onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
