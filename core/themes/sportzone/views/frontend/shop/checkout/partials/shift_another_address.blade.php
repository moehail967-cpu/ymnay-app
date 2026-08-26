<div style="margin-top:16px;padding:20px;background:var(--sz-bg);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);">
    <div style="font-family:var(--sz-font-head);font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-dark);margin-bottom:16px;">
        <i class="mdi mdi-map-marker-outline" style="color:var(--sz-red);"></i> {{ __('Ship to a Different Address') }}
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('First Name') }}</label>
            <input type="text" name="ship_first_name" class="sz-checkout-input" placeholder="{{ __('Jane') }}" value="{{ old('ship_first_name') }}">
        </div>
        <div class="col-md-6">
            <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Last Name') }}</label>
            <input type="text" name="ship_last_name" class="sz-checkout-input" placeholder="{{ __('Smith') }}" value="{{ old('ship_last_name') }}">
        </div>
        <div class="col-md-6">
            <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Email') }}</label>
            <input type="email" name="ship_email" class="sz-checkout-input" placeholder="{{ __('jane@example.com') }}" value="{{ old('ship_email') }}">
        </div>
        <div class="col-md-6">
            <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Phone') }}</label>
            <input type="tel" name="ship_phone" class="sz-checkout-input" placeholder="{{ __('01711000000') }}" value="{{ old('ship_phone') }}">
        </div>
        <div class="col-12">
            <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Address') }}</label>
            <input type="text" name="ship_address" class="sz-checkout-input" placeholder="{{ __('123 Main Street') }}" value="{{ old('ship_address') }}">
        </div>
        <div class="col-md-4">
            <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Country') }}</label>
            <select name="ship_country" class="sz-checkout-input">
                <option value="">{{ __('Select Country') }}</option>
                @foreach(\App\Models\Country::all() as $country)
                    <option value="{{ $country->id }}" {{ old('ship_country') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('State') }}</label>
            <input type="text" name="ship_state" class="sz-checkout-input stateField" placeholder="{{ __('State / Province') }}" value="{{ old('ship_state') }}">
        </div>
        <div class="col-md-4">
            <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('City') }}</label>
            <input type="text" name="ship_city" class="sz-checkout-input cityField" placeholder="{{ __('City') }}" value="{{ old('ship_city') }}">
        </div>
        <div class="col-md-6">
            <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Zip / Postal Code') }}</label>
            <input type="text" name="ship_zip_code" class="sz-checkout-input" placeholder="{{ __('10001') }}" value="{{ old('ship_zip_code') }}">
        </div>
    </div>
</div>
