<div style="margin-top:24px;border-top:1px solid var(--vl-border);padding-top:20px;">
    <a href="javascript:void(0)" class="shift-another-address" style="font-size:11px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);text-decoration:none;">
        + {{ __('Ship to a different address') }}
    </a>
    <div class="shift-address-form" style="display:none;margin-top:20px;">
        {{-- shift address fields using same input style --}}
        <div class="row g-3">
            <div class="col-12">
                <label style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);display:block;margin-bottom:8px;">{{ __('Country') }}</label>
                <select name="shift_country" class="shift-another-country" style="width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;">
                    <option value="" selected disabled>{{ __('Select Country') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);display:block;margin-bottom:8px;">{{ __('State') }}</label>
                <select name="shift_state" class="shift-another-state" style="width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;">
                    <option value="">{{ __('Select State') }}</option>
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);display:block;margin-bottom:8px;">{{ __('City') }}</label>
                <input type="text" name="shift_city" class="shift-another-city" placeholder="{{ __('City') }}" style="width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;">
            </div>
            <div class="col-12">
                <label style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);display:block;margin-bottom:8px;">{{ __('Address') }}</label>
                <textarea name="shift_address" rows="3" placeholder="{{ __('Address') }}" style="width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;resize:vertical;"></textarea>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.shift-another-address', function () {
    var form = $(this).next('.shift-address-form');
    form.slideToggle(200);
    $(this).text(form.is(':visible') ? '- {{ __("Ship to a different address") }}' : '+ {{ __("Ship to a different address") }}');
});

$(document).on('change', '.shift-another-country', function () {
    var countryId = $(this).val();
    $.ajax({
        url: '{{ theme_checkout_state_url() }}',
        type: 'POST',
        data: { _token: '{{ theme_csrf() }}', country_id: countryId },
        success: function (data) {
            var stateSelect = $('.shift-another-state');
            stateSelect.empty().append('<option value="">{{ __("Select State") }}</option>');
            if (data.states) {
                $.each(data.states, function (k, s) {
                    stateSelect.append('<option value="' + s.id + '">' + s.name + '</option>');
                });
            }
        }
    });
});
</script>
