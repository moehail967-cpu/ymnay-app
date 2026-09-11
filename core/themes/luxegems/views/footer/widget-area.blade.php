{{-- LuxeGems: Footer --}}
<footer class="lg-footer">
    <div class="container">
        <div class="row g-4">
            {!! theme_footer_widgets('footer', false) !!}
        </div>

        {{-- Bottom Bar --}}
        <div class="lg-footer-bottom">
            <span>{!! theme_footer_copyright() !!}</span>
            <span>
                <a href="#" style="color:inherit;text-decoration:none;margin-right:12px;">{{ __('Privacy') }}</a>
                <a href="#" style="color:inherit;text-decoration:none;margin-right:12px;">{{ __('Terms') }}</a>
                <a href="#" style="color:inherit;text-decoration:none;">{{ __('Ethical Sourcing') }}</a>
            </span>
        </div>
    </div>
</footer>
