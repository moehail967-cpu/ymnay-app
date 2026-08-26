{{-- TechZone: Footer --}}
<footer class="tz-footer">
    <div class="container">

        <div class="tz-footer-top">
            <div class="row g-4 g-lg-5">
                {!! theme_footer_widgets('footer', false) !!}
            </div>
        </div>

        <hr class="tz-footer-hr">

        {{-- Bottom bar --}}
        <div class="tz-footer-bottom">
            <div class="tz-footer-copy">{!! theme_footer_copyright() !!}</div>
            <div class="tz-footer-bottom-links">
                <a href="#">{{ __('Privacy') }}</a>
                <span class="tz-footer-sep">·</span>
                <a href="#">{{ __('Terms') }}</a>
                <span class="tz-footer-sep">·</span>
                <a href="#">{{ __('Authenticity Policy') }}</a>
            </div>
        </div>

    </div>
</footer>
