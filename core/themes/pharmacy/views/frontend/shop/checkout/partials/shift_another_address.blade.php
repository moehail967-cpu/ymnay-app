{{-- Pharmacy: Alternate delivery address --}}
<div style="margin-top:20px;padding-top:20px;border-top:2px dashed var(--pf-border);">
    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:600;color:var(--pf-dark);">
        <input type="checkbox" id="pf_shift_address_check" style="accent-color:var(--pf-teal);width:16px;height:16px;">
        {{ __('Deliver to a different address') }}
    </label>

    <div id="pf_alt_address_form" style="display:none;margin-top:20px;">
        <div class="row g-3">
            <div class="col-md-6">
                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">{{ __('Full Name') }}</label>
                <input type="text" name="shipping_name" placeholder="{{ __('Full Name') }}"
                       style="width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;"
                       class="stateField"
                       onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
            </div>
            <div class="col-md-6">
                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">{{ __('Phone') }}</label>
                <input type="tel" name="shipping_phone" placeholder="{{ __('Phone') }}"
                       style="width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;"
                       onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
            </div>
            <div class="col-md-6">
                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">{{ __('Email') }}</label>
                <input type="email" name="shipping_email" placeholder="{{ __('Email') }}"
                       style="width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;"
                       onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
            </div>
            <div class="col-md-6">
                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">{{ __('ZIP / PIN Code') }}</label>
                <input type="text" name="shipping_postal_code" placeholder="{{ __('ZIP / PIN Code') }}"
                       style="width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;"
                       onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
            </div>
            <div class="col-md-6">
                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">{{ __('Country') }}</label>
                <select name="shipping_country"
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;background:#fff;appearance:auto;">
                    <option value="">{{ __('Select Country') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">{{ __('State') }}</label>
                <input type="text" name="shipping_state" placeholder="{{ __('State / Province') }}"
                       style="width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;"
                       class="stateField"
                       onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
            </div>
            <div class="col-md-6">
                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">{{ __('City') }}</label>
                <input type="text" name="shipping_city" placeholder="{{ __('City') }}"
                       style="width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;"
                       class="cityField"
                       onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
            </div>
            <div class="col-12">
                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">{{ __('Address') }}</label>
                <textarea name="shipping_address" rows="2"
                          placeholder="{{ __('Street address') }}"
                          style="width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;resize:vertical;"
                          onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'"></textarea>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).on('change', '#pf_shift_address_check', function(){
    var checked = $(this).is(':checked');
    $('#pf_alt_address_form').slideToggle(250);
    $('input[name=shift_another_address]').val(checked ? 1 : '');
});
</script>
@endpush
