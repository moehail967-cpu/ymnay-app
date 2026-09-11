{{-- SportZone: Footer --}}
<footer class="sz-footer">

    <div class="container">

        {{-- Top: widget columns --}}
        <div class="sz-footer-top">
            <div class="row g-4 g-lg-5">
                {!! theme_footer_widgets('footer', false) !!}
            </div>
        </div>

        {{-- Divider --}}
        <hr class="sz-footer-hr">

        {{-- Bottom bar --}}
        <div class="sz-footer-bottom">
            <div class="sz-footer-copy">{!! theme_footer_copyright() !!}</div>
            <div class="sz-footer-bottom-links">
                <a href="#">{{ __('Privacy') }}</a>
                <span class="sz-footer-sep">·</span>
                <a href="#">{{ __('Terms') }}</a>
                <span class="sz-footer-sep">·</span>
                <a href="#">{{ __('Authenticity Guarantee') }}</a>
            </div>
        </div>

    </div>

</footer>
