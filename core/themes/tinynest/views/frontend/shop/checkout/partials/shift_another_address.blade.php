{{-- TinyNest: Alternate delivery address --}}
<div style="margin-top:20px;padding-top:20px;border-top:2px dashed var(--tn-border);">
    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:700;color:var(--tn-dark);">
        <input type="checkbox" id="tn_shift_address_check" style="accent-color:var(--tn-purple);width:16px;height:16px;">
        {{ __('Deliver to a different address') }}
    </label>

    <div id="tn_alt_address_form" style="display:none;margin-top:20px;">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="tn-label">{{ __('Full Name') }}</label>
                <input type="text" name="shipping_name" class="tn-input" placeholder="{{ __('Full Name') }}">
            </div>
            <div class="col-md-6">
                <label class="tn-label">{{ __('Phone') }}</label>
                <input type="tel" name="shipping_phone" class="tn-input" placeholder="{{ __('Phone') }}">
            </div>
            <div class="col-md-6">
                <label class="tn-label">{{ __('Email') }}</label>
                <input type="email" name="shipping_email" class="tn-input" placeholder="{{ __('Email') }}">
            </div>
            <div class="col-md-6">
                <label class="tn-label">{{ __('ZIP / Postal Code') }}</label>
                <input type="text" name="shipping_postal_code" class="tn-input" placeholder="{{ __('ZIP / Postal Code') }}">
            </div>
            <div class="col-md-6">
                <label class="tn-label">{{ __('Country') }}</label>
                <select name="shipping_country" class="tn-input">
                    <option value="">{{ __('Select Country') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="tn-label">{{ __('State') }}</label>
                <input type="text" name="shipping_state" class="tn-input" placeholder="{{ __('State / Province') }}">
            </div>
            <div class="col-md-6">
                <label class="tn-label">{{ __('City') }}</label>
                <input type="text" name="shipping_city" class="tn-input" placeholder="{{ __('City') }}">
            </div>
            <div class="col-12">
                <label class="tn-label">{{ __('Address') }}</label>
                <textarea name="shipping_address" rows="2" class="tn-input tn-input-textarea"
                          placeholder="{{ __('Street address') }}"></textarea>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).on('change', '#tn_shift_address_check', function () {
    var checked = $(this).is(':checked');
    $('#tn_alt_address_form').slideToggle(250);
    $('input[name=shift_another_address]').val(checked ? 1 : '');
});
</script>
@endpush
