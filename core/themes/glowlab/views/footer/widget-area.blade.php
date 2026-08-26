{{-- GlowLab: Footer --}}
<footer class="gl-footer">
    <div class="container">
        <div class="row g-5 pb-5">
            {!! theme_footer_widgets('footer', false) !!}
        </div>
    </div>

    <div class="gl-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>{!! theme_footer_copyright() !!}</span>
            <div class="d-flex gap-3">
                <a href="{{ theme_shop_url() }}" class="gl-footer-bottom-link">{{ __('Privacy Policy') }}</a>
                <a href="{{ theme_shop_url() }}" class="gl-footer-bottom-link">{{ __('Terms') }}</a>
                <a href="{{ theme_shop_url() }}" class="gl-footer-bottom-link">{{ __('Cookies') }}</a>
            </div>
        </div>
    </div>
</footer>
