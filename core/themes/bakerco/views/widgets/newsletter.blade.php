<section style="padding-top:{{ $padding_top }}px; padding-bottom:{{ $padding_bottom }}px; background:var(--bk-cream);">
<div class="container">
    <div class="bk-newsletter">
        @if(!empty($title))<h3>{{ $title }}</h3>@endif
        @if(!empty($subtitle))<p>{{ $subtitle }}</p>@endif
        {!! theme_newsletter_form('bk-newsletter-form', $button_text ?? 'Subscribe') !!}
    </div>
</div>
</section>
