@php
    $uid       = 'gb-cf-' . substr(md5(uniqid()), 0, 8);
    $themeSlug = tenant()?->theme_slug ?? 'default';
    $rawColor  = get_static_option('main_color_one_' . $themeSlug) ?? '#0d6efd';

    // DB may store color as 'rgb(r, g, b)' or '#rrggbb' — handle both
    if (preg_match('/rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/i', $rawColor, $m)) {
        [$pr, $pg, $pb] = [(int)$m[1], (int)$m[2], (int)$m[3]];
    } else {
        $hex = ltrim(trim($rawColor), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $pr = hexdec(substr($hex, 0, 2));
        $pg = hexdec(substr($hex, 2, 2));
        $pb = hexdec(substr($hex, 4, 2));
    }
@endphp
<section id="{{ $uid }}" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="row g-5">

            {{-- Info column --}}
            <div class="col-lg-5">
                @if(!empty($title))
                    <h2 class="{{ $uid }}-title">{{ $title }}</h2>
                @endif
                @if(!empty($description))
                    <p class="{{ $uid }}-desc">{{ $description }}</p>
                @endif

                @if(!empty($info_items) && count($info_items) > 0)
                    <div class="{{ $uid }}-info-list">
                        @foreach($info_items as $item)
                        @if(!empty($item['label']) || !empty($item['value']))
                        <div class="{{ $uid }}-info-item">
                            @if(!empty($item['icon']))
                                <div class="{{ $uid }}-info-icon"><i class="{{ $item['icon'] }}"></i></div>
                            @endif
                            <div class="{{ $uid }}-info-body">
                                @if(!empty($item['label']))
                                    <div class="{{ $uid }}-info-label">{{ $item['label'] }}</div>
                                @endif
                                @if(!empty($item['value']))
                                    <div class="{{ $uid }}-info-value">{{ $item['value'] }}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Form column --}}
            <div class="col-lg-7">
                <div class="{{ $uid }}-form-card">
                    @if($contact_form)
                        {!! \App\Helpers\FormBuilderCustom::render_form($contact_form->id, null, null, 'btn-default') !!}
                    @else
                        <div class="{{ $uid }}-no-form">
                            <i class="las la-exclamation-circle"></i>
                            <p>{{ __('No form selected. Please choose a Custom Form in the widget settings.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>

<style>
#{{ $uid }} { }
.{{ $uid }}-title { font-size: clamp(1.4rem, 2.5vw, 2rem); font-weight: 700; color: var(--heading-color, #1a1a2e); margin-bottom: 12px; }
.{{ $uid }}-desc { font-size: 0.95rem; color: var(--body-color, #5a6474); line-height: 1.7; margin-bottom: 28px; }
.{{ $uid }}-info-list { display: flex; flex-direction: column; gap: 20px; }
.{{ $uid }}-info-item { display: flex; align-items: flex-start; gap: 14px; }
.{{ $uid }}-info-icon { width: 44px; height: 44px; border-radius: 10px; background: rgba({{ $pr }},{{ $pg }},{{ $pb }},.1); background: color-mix(in srgb, var(--main-color-one, rgba({{ $pr }},{{ $pg }},{{ $pb }},1)) 10%, transparent); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.{{ $uid }}-info-icon i { font-size: 1.2rem; color: var(--main-color-one, #0d6efd); }
.{{ $uid }}-info-label { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--light-color, #9aa0ab); margin-bottom: 2px; }
.{{ $uid }}-info-value { font-size: 0.95rem; font-weight: 600; color: var(--heading-color, #1a1a2e); }
.{{ $uid }}-form-card { border-radius: 14px; padding: 36px; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
.{{ $uid }}-no-form { text-align: center; padding: 40px; color: var(--light-color, #aaa); }
.{{ $uid }}-no-form i { font-size: 2.5rem; display: block; margin-bottom: 10px; }
.{{ $uid }}-no-form p { font-size: 0.9rem; }
@media (max-width: 767px) {
    .{{ $uid }}-form-card { padding: 20px; }
}
</style>

<script>
(function(){
    if (window['__gbcf_{{ $uid }}']) return;
    window['__gbcf_{{ $uid }}'] = true;
    $(document).on('submit', '#{{ $uid }} .custom-form-builder-ten', function(e) {
        e.preventDefault();
        var btn    = $(this).find('[type="submit"]');
        var form   = $(this);
        var formID = form.attr('id');
        var msgBox = form.find('.error-message');
        var fd     = new FormData(document.getElementById(formID));
        msgBox.html('');
        btn.html('<i class="las la-spinner la-spin"></i> {{ __("Sending...") }}').prop('disabled', true);
        $.ajax({
            url: '{{ route(route_prefix()."frontend.form.builder.custom.submit") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            processData: false,
            contentType: false,
            data: fd,
            success: function(data) {
                msgBox.html('<div class="alert alert-' + data.type + '">' + data.msg + '</div>');
                btn.html('{{ __("Send Message") }}').prop('disabled', false);
                form[0].reset();
            },
            error: function(xhr) {
                var markup = '<ul class="alert alert-danger">';
                $.each(xhr.responseJSON?.errors ?? {}, function(i, v){ markup += '<li>' + v + '</li>'; });
                markup += '</ul>';
                msgBox.html(markup);
                btn.html('{{ __("Send Message") }}').prop('disabled', false);
            }
        });
    });
})();
</script>
