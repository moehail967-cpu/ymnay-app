{{-- KidVille: Footer --}}
<footer class="kv-footer">
    <div class="container">
        <div class="row g-4 pb-4">
            {!! theme_footer_widgets('footer', false) !!}
        </div>
    </div>

    <div class="kv-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>{!! theme_footer_copyright() !!}</span>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ theme_shop_url() }}" class="kv-footer-policy-link">{{ __('Privacy') }}</a>
                <span class="kv-footer-sep">·</span>
                <a href="{{ theme_shop_url() }}" class="kv-footer-policy-link">{{ __('Terms') }}</a>
                <span class="kv-footer-sep">·</span>
                <a href="{{ theme_shop_url() }}" class="kv-footer-policy-link">{{ __('Safety Policy') }}</a>
            </div>
        </div>
    </div>
</footer>
