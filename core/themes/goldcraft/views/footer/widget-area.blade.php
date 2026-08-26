{{-- GoldCraft: Footer --}}
<footer class="gc-footer">
    <div class="container">
        <div class="row g-4 pb-5">
            {!! theme_footer_widgets('footer', false) !!}
        </div>
    </div>

    <div class="gc-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>{!! theme_footer_copyright() !!}</span>
            <div class="d-flex gap-3">
                <a href="{{ theme_shop_url() }}" class="gc-footer-bottom-link">{{ __('Privacy') }}</a>
                <a href="{{ theme_shop_url() }}" class="gc-footer-bottom-link">{{ __('Terms') }}</a>
                <a href="{{ theme_shop_url() }}" class="gc-footer-bottom-link">{{ __('Sustainability') }}</a>
            </div>
        </div>
    </div>
</footer>
