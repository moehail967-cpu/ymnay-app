{{-- TinyNest: Newsletter Widget --}}
<section class="tn-newsletter-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="tn-newsletter-v2">
            @if($title)
                <h3>{{ $title }} 🖤</h3>
            @endif
            @if($subtitle)
                <p>{{ $subtitle }}</p>
            @endif
            <div class="form-message-show"></div>
            {!! theme_newsletter_form('tn-newsletter-form-v2 footer-widget', $button_text) !!}
        </div>
    </div>
</section>
