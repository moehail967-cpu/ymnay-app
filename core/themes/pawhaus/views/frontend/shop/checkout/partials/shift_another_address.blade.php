{{-- PawHaus: Alternate delivery address --}}
<div style="margin-top:20px;padding-top:20px;border-top:2px dashed var(--ph-border);">
    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:700;color:var(--ph-dark);">
        <input type="checkbox" id="ph_shift_address_check" style="accent-color:var(--ph-terra);width:16px;height:16px;">
        {{ __('Deliver to a different address') }}
    </label>

    <div id="ph_alt_address_form" style="display:none;margin-top:20px;">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="ph-label">{{ __('Full Name') }}</label>
                <input type="text" name="shipping_name" class="ph-input stateField" placeholder="{{ __('Full Name') }}">
            </div>
            <div class="col-md-6">
                <label class="ph-label">{{ __('Phone') }}</label>
                <input type="tel" name="shipping_phone" class="ph-input" placeholder="{{ __('Phone') }}">
            </div>
            <div class="col-md-6">
                <label class="ph-label">{{ __('Email') }}</label>
                <input type="email" name="shipping_email" class="ph-input" placeholder="{{ __('Email') }}">
            </div>
            <div class="col-md-6">
                <label class="ph-label">{{ __('ZIP / PIN Code') }}</label>
                <input type="text" name="shipping_postal_code" class="ph-input" placeholder="{{ __('ZIP / PIN Code') }}">
            </div>
            <div class="col-md-6">
                <label class="ph-label">{{ __('Country') }}</label>
                <select name="shipping_country" class="ph-input">
                    <option value="">{{ __('Select Country') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="ph-label">{{ __('State') }}</label>
                <input type="text" name="shipping_state" class="ph-input stateField" placeholder="{{ __('State / Province') }}">
            </div>
            <div class="col-md-6">
                <label class="ph-label">{{ __('City') }}</label>
                <input type="text" name="shipping_city" class="ph-input cityField" placeholder="{{ __('City') }}">
            </div>
            <div class="col-12">
                <label class="ph-label">{{ __('Address') }}</label>
                <textarea name="shipping_address" class="ph-input" rows="2" style="height:auto;resize:vertical;" placeholder="{{ __('Street address') }}"></textarea>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).on('change', '#ph_shift_address_check', function(){
    var checked = $(this).is(':checked');
    $('#ph_alt_address_form').slideToggle(250);
    $('input[name=shift_another_address]').val(checked ? 1 : '');
});
</script>
@endpush
