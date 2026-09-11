@php
    $classOption = $classOption ?? new stdClass();
@endphp

<tr class="border-b border-main hover:bg-muted transition-colors">
    <td class="px-3 py-2.5">
        <input value="{{ $classOption->tax ?? "" }}" type="checkbox" class="tax-option-row-check w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"/>
    </td>
    <td class="px-3 py-2.5">
        <input value="{{ $classOption->tax_name ?? "" }}" type="text" name="tax_name[]"
               class="w-full bg-secondary border border-main rounded-lg px-3 py-1.5 text-sm text-dark outline-none focus:border-primary transition" required>
    </td>
    <td class="px-3 py-2.5">
        <select name="country_id[]" id="country_id" class="w-full bg-secondary border border-main rounded-lg px-3 py-1.5 text-sm text-dark outline-none focus:border-primary transition">
            <option value="">{{ __("Select Country") }}</option>
            @foreach($countries as $country)
                <option @if(!empty($classOption))
                            {{ $country->id == ($classOption->country_id ?? '') ? "selected" : "" }}
                        @endif value="{{ $country->id }}">{{ $country->name }}</option>
            @endforeach
        </select>
    </td>
    <td class="px-3 py-2.5">
        <select name="state_id[]" id="state_id" class="w-full bg-secondary border border-main rounded-lg px-3 py-1.5 text-sm text-dark outline-none focus:border-primary transition">
            <option value="">{{ __("Select State") }}</option>
            @foreach($classOption?->states ?? [] as $state)
                <option {{ $state->id == $classOption->state_id ? "selected" : "" }} value="{{ $state->id ?? "" }}">{{ $state->name }}</option>
            @endforeach
        </select>
    </td>
    <td class="px-3 py-2.5">
        <select name="city_id[]" id="city_id" class="w-full bg-secondary border border-main rounded-lg px-3 py-1.5 text-sm text-dark outline-none focus:border-primary transition">
            <option value="">{{ __("Select City") }}</option>
            @foreach($classOption?->cities ?? [] as $city)
                <option {{ $city->id == $classOption->city_id ? "selected" : "" }} value="{{ $city->id ?? "" }}">{{ $city->name }}</option>
            @endforeach
        </select>
    </td>
    <td class="px-3 py-2.5">
        <input value="{{ $classOption->postal_code ?? "" }}" type="text" name="postal_code[]"
               class="w-full bg-secondary border border-main rounded-lg px-3 py-1.5 text-sm text-dark outline-none focus:border-primary transition">
    </td>
    <td class="px-3 py-2.5">
        <input value="{{ $classOption->rate ?? '0.00' }}" type="number" name="rate[]" step="0.01"
               class="w-full bg-secondary border border-main rounded-lg px-3 py-1.5 text-sm text-dark outline-none focus:border-primary transition" required>
    </td>
    <td class="px-3 py-2.5 hidden">
        <input {{ ($classOption->is_compound ?? "") == 1 ? "checked" : "" }} type="checkbox" name="is_compound[]" value="1"
               class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary"/>
    </td>
    <td class="px-3 py-2.5 text-center">
        <input {{ ($classOption->is_shipping ?? "") == 1 ? "checked" : "" }} type="checkbox" name="is_shipping[]" value="1"
               class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"/>
    </td>
    <td class="px-3 py-2.5">
        <input value="{{ $classOption->priority ?? "" }}" type="number" name="priority[]"
               class="w-full bg-secondary border border-main rounded-lg px-3 py-1.5 text-sm text-dark outline-none focus:border-primary transition" required>
    </td>
</tr>
