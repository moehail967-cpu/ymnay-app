{{-- TinyNest: Footer — dynamic, matching design --}}
@php
    $shop_links = theme_footer_menu_links(\App\Models\Menu::where('status', 'shop')->first()?->id);
    $help_links = theme_footer_menu_links(\App\Models\Menu::where('status', 'help')->first()?->id);

    if (empty($shop_links)) {
        $shop_cats  = theme_categories()->take(6);
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
            ['label' => __('Size Guide'),      'url' => theme_home_url()],
            ['label' => __('Delivery Info'),   'url' => theme_home_url()],
            ['label' => __('Returns'),         'url' => theme_home_url()],
            ['label' => __('Gift Registry'),   'url' => theme_home_url()],
            ['label' => __('Contact'),         'url' => theme_home_url()],
        ];
    }

    $promise_items = [
        ['icon' => '🌿', 'text' => __('Organic Cotton Certified')],
        ['icon' => '🐣', 'text' => __('Cruelty Free')],
        ['icon' => '✅', 'text' => __('EN71 Toy Safety Approved')],
        ['icon' => '♻️', 'text' => __('Eco Packaging Only')],
    ];

    $socials = [
        ['icon' => 'lab la-instagram',  'url' => get_static_option('instagram_url')],
        ['icon' => 'lab la-facebook-f', 'url' => get_static_option('facebook_url')],
        ['icon' => 'lab la-pinterest',  'url' => get_static_option('pinterest_url')],
        ['icon' => 'lab la-youtube',    'url' => get_static_option('youtube_url')],
    ];

    $footer_desc = get_static_option('footer_description') ?: get_static_option('site_description');
@endphp

<footer class="tn-footer">
    <div class="tn-footer-body">
        <div class="container">
            <div class="row g-4">

                {{-- Col 1: Brand --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="tn-footer-brand">
                        <p href="{{ theme_home_url() }}" class="tn-footer-logo-link">
                            {!! theme_logo_html() !!}
                        </p>
                        @if($footer_desc)
                            <p class="tn-footer-desc">{{ $footer_desc }}</p>
                        @endif
                        <div class="tn-footer-socials">
                            @foreach($socials as $s)
                                @if(!empty($s['url']))
                                    <a href="{{ $s['url'] }}" class="tn-footer-social-link"
                                       target="_blank" rel="noopener noreferrer">
                                        <i class="{{ $s['icon'] }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Col 2: Shop --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="tn-footer-col-heading">{{ __('SHOP') }}</h6>
                    @foreach($shop_links as $link)
                        <a href="{{ $link['url'] }}" class="tn-footer-col-link">{{ $link['label'] }}</a>
                    @endforeach
                </div>

                {{-- Col 3: Help --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="tn-footer-col-heading">{{ __('HELP') }}</h6>
                    @foreach($help_links as $link)
                        <a href="{{ $link['url'] }}" class="tn-footer-col-link">{{ $link['label'] }}</a>
                    @endforeach
                </div>

                {{-- Col 4: Our Promise --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <h6 class="tn-footer-col-heading">{{ __('OUR PROMISE') }}</h6>
                    @foreach($promise_items as $item)
                        <div class="tn-footer-promise-item">
                            <span class="tn-footer-promise-icon">{{ $item['icon'] }}</span>
                            <span>{{ $item['text'] }}</span>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <div class="tn-footer-bottom-bar">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>{!! theme_footer_copyright() !!}</span>
            <div class="d-flex align-items-center gap-3">
                {!! theme_footer_widgets('footer_bottom_right', false) !!}
            </div>
        </div>
    </div>
</footer>
