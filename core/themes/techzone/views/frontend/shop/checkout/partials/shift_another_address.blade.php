@php $inp = 'width:100%;padding:10px 14px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:14px;font-family:var(--tz-font);outline:none;'; $lbl = 'font-size:12px;font-weight:600;color:var(--tz-muted);display:block;margin-bottom:6px;'; @endphp

<div style="margin-top:16px;border-top:1px solid var(--tz-border);padding-top:16px;">
    <button type="button" class="shift-another-address"
            style="background:transparent;color:var(--tz-blue);border:1px solid var(--tz-blue);padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:12px;font-weight:600;cursor:pointer;font-family:var(--tz-font);display:flex;align-items:center;gap:6px;transition:all .2s;"
            onmouseover="this.style.background='var(--tz-blue)';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='var(--tz-blue)'">
        <i class="mdi mdi-map-marker-plus-outline"></i> {{ __('Ship to a Different Address') }}
    </button>

    <div class="shift-address-form" style="display:none;margin-top:16px;">
        <div class="row g-3">
            <div class="col-md-6">
                <label style="{{ $lbl }}">{{ __('Full Name') }}</label>
                <input type="text" name="shift_name" class="tz-checkout-input" style="{{ $inp }}" placeholder="{{ __('Full Name') }}">
            </div>
            <div class="col-md-6">
                <label style="{{ $lbl }}">{{ __('Phone') }}</label>
                <input type="tel" name="shift_phone" class="tz-checkout-input" style="{{ $inp }}" placeholder="{{ __('Phone Number') }}">
            </div>
            <div class="col-12">
                <label style="{{ $lbl }}">{{ __('Country') }}</label>
                <select name="shift_country" class="shift-another-country tz-checkout-input" style="{{ $inp }}cursor:pointer;">
                    <option value="">{{ __('Select Country') }}</option>
                    @foreach($countries ?? [] as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 position-relative">
                <label style="{{ $lbl }}">{{ __('State') }}</label>
                <select name="shift_state" class="shift-another-state tz-checkout-input" style="{{ $inp }}cursor:pointer;">
                    <option value="">{{ __('Select State') }}</option>
                </select>
            </div>
            <div class="col-md-6 position-relative">
                <label style="{{ $lbl }}">{{ __('City') }}</label>
                <input type="text" name="shift_city" class="stateField live-state-input tz-checkout-input" style="{{ $inp }}" placeholder="{{ __('City') }}">
                <div class="live-dropdown state-dropdown"></div>
            </div>
            <div class="col-md-6">
                <label style="{{ $lbl }}">{{ __('Postal Code') }}</label>
                <input type="text" name="shift_postal_code" class="tz-checkout-input" style="{{ $inp }}" placeholder="{{ __('Postal Code') }}">
            </div>
            <div class="col-12">
                <label style="{{ $lbl }}">{{ __('Address') }}</label>
                <textarea name="shift_address" class="tz-checkout-input" style="{{ $inp }}height:70px;resize:vertical;" rows="2" placeholder="{{ __('Full Address') }}"></textarea>
            </div>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
$(function(){
    $(document).on('click','.shift-another-address',function(){
        var form=$('.shift-address-form');
        if(form.is(':hidden')){ form.slideDown(200); $(this).html('<i class="mdi mdi-close"></i> {{ __("Cancel") }}').css({'color':'var(--tz-muted)','borderColor':'var(--tz-border)'}); $('.shift_another_address').val('on'); }
        else{ form.slideUp(200); $(this).html('<i class="mdi mdi-map-marker-plus-outline"></i> {{ __("Ship to a Different Address") }}').css({'color':'var(--tz-blue)','borderColor':'var(--tz-blue)'}); $('.shift_another_address').val(''); }
    });
});
</script>
@endsection
