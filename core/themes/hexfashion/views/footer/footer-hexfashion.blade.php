<footer class="footer-area hf-footer">
    <!-- Top Footer: Follow Us, Logo/About, Contact Us -->
    <div class="hf-footer-top padding-top-60 padding-bottom-60">
        <div class="container container-one">
            <div class="row align-items-center">
                <!-- Column 1: Follow Us -->
                <div class="col-lg-4 col-md-6 hf-footer-col hf-border-right text-center text-lg-start">
                    <h4 class="hf-footer-title">Follow Us</h4>
                    <div class="hf-footer-socials mt-3">
                        @php $socials = \App\Models\TopbarInfo::all(); @endphp
                        @if($socials->isNotEmpty())
                            @foreach($socials as $social)
                                <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" class="hf-social-icon">
                                    <i class="{{ $social->icon }}"></i>
                                </a>
                            @endforeach
                        @else
                            <a href="#" class="hf-social-icon"><i class="lab la-facebook-f"></i></a>
                            <a href="#" class="hf-social-icon"><i class="lab la-twitter"></i></a>
                            <a href="#" class="hf-social-icon"><i class="lab la-instagram"></i></a>
                            <a href="#" class="hf-social-icon"><i class="lab la-youtube"></i></a>
                        @endif
                    </div>
                </div>

                <!-- Column 2: Logo and Text -->
                <div class="col-lg-4 col-md-6 hf-footer-col hf-border-right text-center mt-4 mt-md-0">
                    <div class="hf-footer-logo-wrap mb-3">
                        <a href="{{ url('/') }}">
                            @php $logo = theme_logo_url(); @endphp
                            @if($logo)
                                <img src="{{ $logo }}" alt="{{ get_static_option('site_title') ?? 'Logo' }}" style="max-height:50px;">
                            @else
                                <h3 class="hf-footer-logo-text">{{ get_static_option('site_title') ?? 'Hexfashion' }}</h3>
                            @endif
                        </a>
                    </div>
                    @php $desc = get_static_option('site_description') ?? 'There\'s a voice that keeps on calling me. Down the road, that\'s where I\'ll always be. Every stop I make, I make a new friend. Can\'t stay for long'; @endphp
                    @if($desc)
                        <p class="hf-footer-desc">{{ $desc }}</p>
                    @endif
                </div>

                <!-- Column 3: Contact Us -->
                <div class="col-lg-4 col-md-12 hf-footer-col text-center text-lg-end mt-4 mt-lg-0">
                    <h4 class="hf-footer-title">Contact Us</h4>
                    <div class="hf-footer-contact-info mt-3">
                        @php 
                            $email = get_static_option('contact_email') ?? get_static_option('footer_email') ?? 'misujom01@gmail.com';
                            $phone = get_static_option('contact_phone') ?? get_static_option('footer_phone') ?? '02083483945';
                        @endphp
                        @if($email)
                            <p class="mb-2"><a href="mailto:{{ $email }}" class="hf-contact-link">{{ $email }}</a></p>
                        @endif
                        @if($phone)
                            <p class="mb-0"><a href="tel:{{ $phone }}" class="hf-contact-link">{{ $phone }}</a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Footer: Menu and Payments -->
    <div class="hf-footer-middle section-bg-2 py-3">
        <div class="container container-one">
            <div class="row align-items-center justify-content-between">
                <!-- Left: Footer Menu (rendered from footer_bottom_left sidebar) -->
                <div class="col-lg-6 col-md-6 text-center text-md-start mb-3 mb-md-0 hf-footer-menu-area">
                    {!! render_frontend_sidebar('footer_bottom_left',['column' => false]) !!}
                </div>

                <!-- Right: Payment Methods (rendered from footer_bottom_right or static) -->
                <div class="col-lg-6 col-md-6 text-center text-md-end hf-footer-payment-area">
                    {!! render_frontend_sidebar('footer_bottom_right',['column' => false]) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Footer: Copyright -->
    <div class="hf-footer-bottom py-3">
        <div class="container container-one">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="hf-copyright-text text-center">
                        <span>{!! get_footer_copyright_text() ?? '© 2026 Copyright All Right Reserved by Electro' !!}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
