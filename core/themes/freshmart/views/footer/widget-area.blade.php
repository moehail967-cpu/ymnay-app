{{-- FreshMart: Footer --}}
<footer class="fm-footer">
    <div class="container">
        <div class="row g-5 pb-5">
            {!! theme_footer_widgets('footer', false) !!}
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="fm-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>{!! theme_footer_copyright() !!}</span>
            <div class="d-flex gap-3">
                <a href="#" class="fm-footer-bottom-link">{{ __('Privacy') }}</a>
                <a href="#" class="fm-footer-bottom-link">{{ __('Terms') }}</a>
                <a href="#" class="fm-footer-bottom-link">{{ __('Sustainability Report') }}</a>
            </div>
        </div>
    </div>
</footer>
