{{-- Electro: Footer --}}
<footer class="hf-footer">
    <div class="container">

        {{-- Top bar: social | logo | payment icons --}}
        <div class="hf-footer-top">

            {{-- Social icons --}}
            <div class="hf-footer-social">
                @if(!empty($all_social_icons) && $all_social_icons->isNotEmpty())
                    @foreach($all_social_icons as $social)
                        @if(!empty($social->url))
                        <a href="{{ $social->url }}" class="hf-footer-social-icon" target="_blank" rel="noopener noreferrer">
                            <i class="{{ $social->icon ?? 'lab la-link' }}"></i>
                        </a>
                        @endif
                    @endforeach
                @else
                    <a href="#" class="hf-footer-social-icon"><i class="lab la-facebook-f"></i></a>
                    <a href="#" class="hf-footer-social-icon"><i class="lab la-twitter"></i></a>
                    <a href="#" class="hf-footer-social-icon"><i class="lab la-instagram"></i></a>
                    <a href="#" class="hf-footer-social-icon"><i class="lab la-youtube"></i></a>
                @endif
            </div>

            {{-- Logo + tagline --}}
            <div class="hf-footer-logo-wrap">
                <a href="{{ theme_home_url() }}">
                    @php $logoUrl = theme_logo_url(); @endphp
                    @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ get_static_option('site_title') ?? config('app.name') }}" class="hf-footer-logo">
                    @else
                    <span class="hf-footer-logo-text">{{ get_static_option('site_title') ?? config('app.name') }}</span>
                    @endif
                </a>
                @php $tagline = get_static_option('site_description') ?? ''; @endphp
                @if($tagline)
                <p class="hf-footer-tagline">{{ $tagline }}</p>
                @endif
            </div>

            {{-- Payment gateway logos --}}
            <div class="hf-footer-payment">
                @php
                    $gateways = [];
                    try { $gateways = theme_payment_gateways(); } catch(\Throwable $e) {}
                @endphp
                @if(!empty($gateways))
                <div class="hf-footer-payment-icons">
                    @foreach($gateways as $gw)
                    @php
                        $gwImg = null;
                        if (!empty($gw['image'])) {
                            if (is_numeric($gw['image'])) {
                                $gd = get_attachment_image_by_id((int) $gw['image']);
                                $gwImg = $gd['img_url'] ?? null;
                            } elseif (filter_var($gw['image'], FILTER_VALIDATE_URL) || str_starts_with($gw['image'], '/')) {
                                $gwImg = $gw['image'];
                            }
                        }
                    @endphp
                    <div class="hf-footer-pay-item" title="{{ ucfirst($gw['name'] ?? '') }}">
                        @if($gwImg)
                        <img src="{{ $gwImg }}" alt="{{ $gw['name'] ?? 'Pay' }}" class="hf-footer-pay-logo">
                        @else
                        <span class="hf-footer-pay-badge">{{ ucfirst($gw['name'] ?? 'Pay') }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="hf-footer-payment-icons">
                    <span class="hf-footer-pay-badge">PayPal</span>
                    <span class="hf-footer-pay-badge">Stripe</span>
                    <span class="hf-footer-pay-badge">Visa</span>
                </div>
                @endif
            </div>

        </div>
    </div>

    <div class="hf-footer-divider"></div>

    <div class="container">
        {{-- Main 4-col grid --}}
        <div class="hf-footer-main">

            {{-- Col 1: Quick Links --}}
            <div class="hf-footer-col">
                <h4 class="hf-footer-col-title">{{ __('Quick Links') }}</h4>
                <ul class="hf-footer-links">
                    <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
                    <li><a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a></li>
                    <li><a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a></li>
                    @if(function_exists('theme_digital_shop_url'))
                    <li><a href="{{ theme_digital_shop_url() }}">{{ __('Digital Products') }}</a></li>
                    @endif
                    <li><a href="{{ theme_cart_url() }}">{{ __('Cart') }}</a></li>
                    <li><a href="{{ theme_wishlist_url() }}">{{ __('Wishlist') }}</a></li>
                </ul>
            </div>

            {{-- Col 2: My Account --}}
            <div class="hf-footer-col">
                <h4 class="hf-footer-col-title">{{ __('My Account') }}</h4>
                <ul class="hf-footer-links">
                    <li><a href="{{ theme_login_url() }}">{{ __('Login') }}</a></li>
                    <li><a href="{{ theme_register_url() }}">{{ __('Register') }}</a></li>
                    <li><a href="{{ theme_user_dashboard_url() }}">{{ __('Dashboard') }}</a></li>
                    <li><a href="{{ theme_order_track_url() }}">{{ __('Track Order') }}</a></li>
                    <li><a href="{{ theme_compare_page_url() }}">{{ __('Compare') }}</a></li>
                </ul>
            </div>

            {{-- Col 3: Contact Info --}}
            <div class="hf-footer-col">
                <h4 class="hf-footer-col-title">{{ __('Our Information') }}</h4>
                @php
                    $address = get_static_option('topbar_address') ?? get_static_option('contact_address') ?? get_static_option('footer_address') ?? '';
                    $phone   = get_static_option('topbar_phone')   ?? get_static_option('contact_phone')   ?? get_static_option('footer_phone')   ?? '';
                    $email   = get_static_option('topbar_email')   ?? get_static_option('contact_email')   ?? get_static_option('footer_email')   ?? '';
                @endphp
                <ul class="hf-footer-contact">
                    @if($address)
                    <li class="hf-footer-contact-item">
                        <i class="las la-map-marker-alt hf-footer-contact-icon"></i>
                        <span>{{ $address }}</span>
                    </li>
                    @else
                    <li class="hf-footer-contact-item">
                        <i class="las la-map-marker-alt hf-footer-contact-icon"></i>
                        <span>41/1, Hilton Mall, NY City</span>
                    </li>
                    @endif
                    @if($phone)
                    <li class="hf-footer-contact-item">
                        <i class="las la-phone hf-footer-contact-icon"></i>
                        <a href="tel:{{ $phone }}">{{ $phone }}</a>
                    </li>
                    @else
                    <li class="hf-footer-contact-item">
                        <i class="las la-phone hf-footer-contact-icon"></i>
                        <a href="tel:+01278901234">+012-78901234</a>
                    </li>
                    @endif
                    @if($email)
                    <li class="hf-footer-contact-item">
                        <i class="las la-envelope hf-footer-contact-icon"></i>
                        <a href="mailto:{{ $email }}">{{ $email }}</a>
                    </li>
                    @else
                    <li class="hf-footer-contact-item">
                        <i class="las la-envelope hf-footer-contact-icon"></i>
                        <a href="mailto:help@mail.com">help@mail.com</a>
                    </li>
                    @endif
                </ul>
            </div>

            {{-- Col 4: Newsletter --}}
            <div class="hf-footer-col">
                <h4 class="hf-footer-col-title">{{ __('Get In Touch') }}</h4>
                <p class="hf-footer-newsletter-text">{{ __('Sign up to our mailing list now!') }}</p>
                <form class="hf-footer-newsletter-form newsletter-form" action="{{ url('/newsletter/subscribe') }}" method="POST">
                    @csrf
                    <div class="hf-footer-newsletter-row">
                        <input type="email" name="email" placeholder="{{ __('Your Mail Here') }}" class="hf-footer-email-input" required>
                        <button type="submit" class="hf-footer-subscribe-btn"><i class="las la-arrow-right"></i></button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div class="hf-footer-divider"></div>

    {{-- Copyright --}}
    <div class="hf-footer-bottom">
        <div class="container">
            <div class="hf-footer-copyright">
                {!! theme_footer_copyright() !!}
            </div>
        </div>
    </div>
</footer>
