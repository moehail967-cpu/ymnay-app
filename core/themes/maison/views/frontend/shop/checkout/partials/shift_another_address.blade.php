{{-- Maison: ship to a different address --}}
<div style="margin-top:20px;">
    <button type="button" class="shift-another-address"
            style="display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--ms-charcoal);background:transparent;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:10px 18px;cursor:pointer;transition:all .2s;"
            onmouseover="this.style.borderColor='var(--ms-linen)';this.style.color='var(--ms-linen-d)'"
            onmouseout="this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-charcoal)'">
        <i class="mdi mdi-map-marker-plus-outline"></i>
        {{ __('Ship to a Different Address?') }}
        <i class="mdi mdi-chevron-down"></i>
    </button>

    <div class="shift-address-form" style="display:none;margin-top:16px;padding:20px;background:var(--ms-warm);border:1px solid var(--ms-border);border-radius:var(--ms-radius);">
        <div style="font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--ms-border);">
            {{ __('Alternate Shipping Address') }}
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="ms-form-group">
                    <label class="ms-form-label">{{ __('Full Name') }}</label>
                    <input type="text" name="shift_name" class="ms-form-input" placeholder="{{ __('Your name') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="ms-form-group">
                    <label class="ms-form-label">{{ __('Phone') }}</label>
                    <input type="text" name="shift_phone" class="ms-form-input" placeholder="{{ __('Phone number') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="ms-form-group">
                    <label class="ms-form-label">{{ __('Email') }}</label>
                    <input type="email" name="shift_email" class="ms-form-input" placeholder="{{ __('Email address') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="ms-form-group">
                    <label class="ms-form-label">{{ __('Postal / ZIP Code') }}</label>
                    <input type="text" name="shift_zip_code" class="ms-form-input" placeholder="{{ __('ZIP code') }}">
                </div>
            </div>
            <div class="col-12">
                <div class="ms-form-group">
                    <label class="ms-form-label">{{ __('Country') }}</label>
                    <select name="shift_country" class="ms-form-input shift-another-country" style="appearance:auto;">
                        <option value="">{{ __('Select Country') }}</option>
                        @foreach(\App\Models\Country::all() as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="shift_country" class="shift-another-country">
                </div>
            </div>
            <div class="col-md-6">
                <div class="ms-form-group">
                    <label class="ms-form-label">{{ __('State / Province') }}</label>
                    <input type="text" name="shift_state" class="ms-form-input shift-another-state" placeholder="{{ __('State or province') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="ms-form-group">
                    <label class="ms-form-label">{{ __('City') }}</label>
                    <input type="text" name="shift_city" class="ms-form-input shift-another-city" placeholder="{{ __('City') }}">
                </div>
            </div>
            <div class="col-12">
                <div class="ms-form-group">
                    <label class="ms-form-label">{{ __('Full Address') }}</label>
                    <textarea name="shift_address" class="ms-form-input" rows="3" placeholder="{{ __('Street address, apartment, unit, etc.') }}" style="resize:vertical;height:auto;"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).on('click', '.shift-another-address', function(){
    var icon = $(this).find('.mdi-chevron-down, .mdi-chevron-up');
    $('.shift-address-form').slideToggle(250, function(){
        icon.toggleClass('mdi-chevron-down mdi-chevron-up');
    });
});

$(document).on('change', '.shift-another-country', function(){
    var countryId = $(this).val();
    if(!countryId) return;
    $.post('{{ theme_state_search_url() }}', { _token: '{{ csrf_token() }}', country_id: countryId }, function(data){
        // populate state if needed — basic text input here
    });
});
</script>
@endpush
