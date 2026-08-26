<div class="single-input mt-4 position-relative">
    <label class="label-title mb-3">State</label>
    <input type="text" class="form--control stateField live-state-input"
           name="state" id="state"
           value="{{ old('state', $billing_info->state->name ?? $account_info->state->name ?? '') }}"
    >
</div>

<div class="single-input mt-4 position-relative">
    <label class="label-title mb-3">City/Town</label>
    <input type="text" class="form--control live-city-input"
           name="city"
           value="{{ old('city', $billing_info->city ?? $account_info->city ?? '') }}"
    >
</div>


