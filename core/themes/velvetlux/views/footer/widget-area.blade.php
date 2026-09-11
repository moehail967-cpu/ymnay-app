{{-- VelvetLux: Footer --}}
<footer class="vl-footer">
    <div class="container">
        <div class="row g-4 pb-4">
            {!! theme_footer_widgets('footer', false) !!}
        </div>
    </div>

    <div class="vl-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>{!! theme_footer_copyright() !!}</span>
            <div class="d-flex gap-3">
                <a href="#">{{ __('Privacy') }}</a>
                <a href="#">{{ __('Terms') }}</a>
                <a href="#">{{ __('Sustainability') }}</a>
            </div>
        </div>
    </div>
</footer>
