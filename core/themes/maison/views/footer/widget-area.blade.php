{{-- Maison: Footer --}}
@php
    // Dynamic menu columns (admin configures via Menus panel)
    $shop_links = theme_footer_menu_links(\App\Models\Menu::where('status', 'shop')->first()?->id);
    $help_links = theme_footer_menu_links(\App\Models\Menu::where('status', 'help')->first()?->id);

    // Fallbacks when menus are not configured
    if (empty($shop_links)) {
        $shop_cats = theme_categories()->take(6);
        $shop_links = $shop_cats->map(fn($c) => [
            'label' => $c->name,
            'url'   => theme_category_url($c->slug),
        ])->toArray();
        if (empty($shop_links)) {
            $shop_links = [['label' => __('All Products'), 'url' => theme_shop_url()]];
        }
    }
    if (empty($help_links)) {
        $help_links = [
            ['label' => __('Delivery Info'),       'url' => theme_home_url()],
            ['label' => __('Returns'),             'url' => theme_home_url()],
            ['label' => __('Track Order'),         'url' => theme_shop_url()],
            ['label' => __('Design Consultation'), 'url' => theme_home_url()],
            ['label' => __('Contact'),             'url' => theme_home_url()],
        ];
    }

    // Our Promise items
    $promise_items = [
        ['icon' => '♻️', 'text' => __('Sustainably Sourced Materials')],
        ['icon' => '🔒', 'text' => __('5-Year Craftsmanship Warranty')],
        ['icon' => '🚚', 'text' => __('Free Delivery & Assembly Available')],
        ['icon' => '🌿', 'text' => __('Carbon-Neutral Shipping')],
    ];

    // Socials — use the same option keys as other themes
    $socials = [
        ['icon' => 'lab la-instagram',   'url' => get_static_option('instagram_url')],
        ['icon' => 'lab la-pinterest-p', 'url' => get_static_option('pinterest_url')],
        ['icon' => 'lab la-youtube',     'url' => get_static_option('youtube_url')],
        ['icon' => 'lab la-twitter',     'url' => get_static_option('twitter_url')],
        ['icon' => 'lab la-facebook-f',  'url' => get_static_option('facebook_url')],
    ];

    $footer_desc = get_static_option('footer_description') ?: get_static_option('site_description');
@endphp

<footer class="ms-footer">
    <div class="container">
        <div class="row g-4 pt-5 pb-5">

            {{-- Col 1: Brand --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="ms-footer-brand">
                    <a href="{{ theme_home_url() }}" class="ms-footer-logo-link">
                        {!! theme_logo_html() !!}
                    </a>
                    @if($footer_desc)
                        <p class="ms-footer-desc">{{ $footer_desc }}</p>
                    @endif
                    <div class="ms-footer-socials">
                        @foreach($socials as $s)
                            @if(!empty($s['url']))
                                <a href="{{ $s['url'] }}" class="ms-social-link" target="_blank" rel="noopener noreferrer">
                                    <i class="{{ $s['icon'] }}"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Col 2: Shop --}}
            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="ms-footer-h">{{ __('SHOP') }}</h6>
                @foreach($shop_links as $link)
                    <a href="{{ $link['url'] }}" class="ms-footer-link">{{ $link['label'] }}</a>
                @endforeach
            </div>

            {{-- Col 3: Help --}}
            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="ms-footer-h">{{ __('HELP') }}</h6>
                @foreach($help_links as $link)
                    <a href="{{ $link['url'] }}" class="ms-footer-link">{{ $link['label'] }}</a>
                @endforeach
            </div>

            {{-- Col 4: Our Promise --}}
            <div class="col-12 col-md-6 col-lg-4">
                <h6 class="ms-footer-h">{{ __('OUR PROMISE') }}</h6>
                @foreach($promise_items as $item)
                    <div class="ms-footer-promise-item">
                        <span class="ms-footer-promise-icon">{{ $item['icon'] }}</span>
                        <span>{{ $item['text'] }}</span>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <div class="ms-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>{!! theme_footer_copyright() !!}</span>
            <div class="d-flex align-items-center gap-3">
                {!! theme_footer_widgets('footer_bottom_right', false) !!}
            </div>
        </div>
    </div>
</footer>
