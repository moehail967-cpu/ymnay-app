{{-- Frontend language switcher — injected via nazmart:frontend_footer hook --}}
@php
    $currentLocale = app()->getLocale();
    $switchBase = route('tenant.frontend.langchange');
@endphp

<div class="ml-lang-switcher">
    <span class="ml-lang-label">
        <i class="mdi mdi-translate"></i>
    </span>
    <select class="ml-lang-select" onchange="window.location.href='{{ $switchBase }}?lang='+this.value">
        @foreach($langs as $lang)
            <option value="{{ $lang->slug }}" {{ $currentLocale === $lang->slug ? 'selected' : '' }}>
                {{ $lang->name }}
            </option>
        @endforeach
    </select>
</div>
