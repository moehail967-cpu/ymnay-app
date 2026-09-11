{{-- BakerCo: Footer — widget areas rendered from admin panel settings --}}

<footer class="bk-footer">
    <div class="container">
        <div class="row g-4 pb-4">
            {{-- Main footer widget area (admin panel: Appearance → Widgets → footer) --}}
            {!! theme_footer_widgets('footer', true) !!}
        </div>
    </div>

    <div class="bk-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            {{-- Left bottom widget area --}}
            <div class="d-flex align-items-center gap-3">
                {!! theme_footer_widgets('footer_bottom_left', false) !!}
            </div>

            {{-- Copyright text (admin panel: Appearance → Footer → Copyright) --}}
            <span>{!! theme_footer_copyright() !!}</span>

            {{-- Right bottom widget area --}}
            <div class="d-flex align-items-center gap-3">
                {!! theme_footer_widgets('footer_bottom_right', false) !!}
            </div>
        </div>
    </div>
</footer>
