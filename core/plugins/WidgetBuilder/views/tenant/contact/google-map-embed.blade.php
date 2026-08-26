@php $uid = 'gb-gm-' . substr(md5(uniqid()), 0, 8); @endphp
<section id="{{ $uid }}" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="{{ $uid }}-wrap">
        <iframe
            src="https://maps.google.com/maps?q={{ urlencode($location) }}&output=embed"
            width="100%"
            height="{{ $map_height }}"
            style="border:0;display:block;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="{{ __('Map') }}">
        </iframe>
    </div>
</section>

<style>
#{{ $uid }} { }
.{{ $uid }}-wrap { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
</style>
