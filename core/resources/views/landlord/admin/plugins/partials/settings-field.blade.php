@php
    $type        = $field['type'] ?? 'text';
    $key         = $field['key'];
    $label       = $field['label'] ?? ucwords(str_replace('_', ' ', $key));
    $description = $field['description'] ?? null;
    $required    = !empty($field['required']);
    $value       = $current[$key] ?? ($field['default'] ?? '');
    $options     = $field['options'] ?? [];
@endphp

<div class="bg-surface rounded-xl border border-main p-5">
    <label class="block text-sm font-semibold text-dark mb-1">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
    @if($description)
        <p class="text-xs text-muted mb-3">{{ $description }}</p>
    @endif

    @switch($type)

        @case('text')
        @case('url')
        @case('email')
            <input type="{{ $type }}"
                   name="{{ $key }}"
                   value="{{ $value }}"
                   {{ $required ? 'required' : '' }}
                   class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary focus:outline-none focus:border-primary">
            @break

        @case('number')
            <input type="number"
                   name="{{ $key }}"
                   value="{{ $value }}"
                   {{ isset($field['min']) ? 'min="'.$field['min'].'"' : '' }}
                   {{ isset($field['max']) ? 'max="'.$field['max'].'"' : '' }}
                   {{ $required ? 'required' : '' }}
                   class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary focus:outline-none focus:border-primary">
            @break

        @case('textarea')
            <textarea name="{{ $key }}"
                      rows="{{ $field['rows'] ?? 4 }}"
                      {{ $required ? 'required' : '' }}
                      class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary focus:outline-none focus:border-primary resize-y">{{ $value }}</textarea>
            @break

        @case('code')
            <textarea name="{{ $key }}"
                      rows="{{ $field['rows'] ?? 6 }}"
                      class="w-full px-3 py-2.5 text-xs border border-main rounded-lg bg-secondary focus:outline-none focus:border-primary resize-y font-mono">{{ $value }}</textarea>
            @break

        @case('select')
            <select name="{{ $key }}"
                    class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary focus:outline-none focus:border-primary">
                @foreach($options as $optVal => $optLabel)
                    <option value="{{ $optVal }}" {{ $value == $optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                @endforeach
            </select>
            @break

        @case('multiselect')
            @php $selectedVals = is_array($value) ? $value : json_decode($value ?? '[]', true); @endphp
            <select name="{{ $key }}[]"
                    multiple
                    class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary focus:outline-none focus:border-primary min-h-[120px]">
                @foreach($options as $optVal => $optLabel)
                    <option value="{{ $optVal }}" {{ in_array($optVal, (array)$selectedVals) ? 'selected' : '' }}>{{ $optLabel }}</option>
                @endforeach
            </select>
            @break

        @case('toggle')
            <label class="relative inline-flex items-center cursor-pointer gap-3">
                <input type="checkbox"
                       name="{{ $key }}"
                       value="1"
                       class="sr-only peer"
                       {{ $value == '1' || $value === true ? 'checked' : '' }}>
                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer
                            peer-checked:bg-primary
                            after:content-[''] after:absolute after:top-[3px] after:left-[3px]
                            after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all
                            peer-checked:after:translate-x-5 transition-colors"></div>
                <span class="text-sm text-muted">{{ $value == '1' ? __('Enabled') : __('Disabled') }}</span>
            </label>
            @break

        @case('color')
            <input type="color"
                   name="{{ $key }}"
                   value="{{ $value ?: '#000000' }}"
                   class="h-10 w-24 px-1 py-1 border border-main rounded-lg bg-secondary cursor-pointer">
            @break

        @case('image')
            <div class="flex items-center gap-3">
                @if($value)
                    <img src="{{ $value }}" alt="" class="w-16 h-16 object-cover rounded-lg border border-main">
                @endif
                <input type="text"
                       name="{{ $key }}"
                       value="{{ $value }}"
                       placeholder="https://..."
                       class="flex-1 px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary focus:outline-none focus:border-primary">
            </div>
            @break

        @case('repeater')
            @php
                $rows = is_array($value) ? $value : json_decode($value ?? '[]', true);
                $subFields = $field['fields'] ?? [];
            @endphp
            <div class="repeater-wrapper space-y-2" data-field="{{ $key }}">
                @foreach((array)$rows as $rowIdx => $rowData)
                    <div class="repeater-row flex items-start gap-2 p-3 bg-secondary rounded-lg border border-main">
                        @foreach($subFields as $sf)
                            <div class="flex-1">
                                <label class="block text-xs text-muted mb-1">{{ $sf['label'] ?? $sf['key'] }}</label>
                                <input type="text"
                                       name="{{ $key }}[{{ $rowIdx }}][{{ $sf['key'] }}]"
                                       value="{{ $rowData[$sf['key']] ?? '' }}"
                                       class="w-full px-2 py-1.5 text-sm border border-main rounded-lg bg-surface focus:outline-none focus:border-primary">
                            </div>
                        @endforeach
                        <button type="button" class="repeater-remove mt-5 text-red-400 hover:text-red-600 transition-colors flex-shrink-0">
                            <i class="mdi mdi-minus-circle text-lg"></i>
                        </button>
                    </div>
                @endforeach
                <button type="button"
                        class="repeater-add inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary/80 transition-colors"
                        data-template="{{ json_encode($subFields) }}">
                    <i class="mdi mdi-plus-circle text-sm"></i> {{__('Add row')}}
                </button>
            </div>
            @break

        @default
            <input type="text"
                   name="{{ $key }}"
                   value="{{ $value }}"
                   class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary focus:outline-none focus:border-primary">

    @endswitch
</div>
