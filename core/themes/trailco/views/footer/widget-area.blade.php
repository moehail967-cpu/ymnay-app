{{-- TrailCo: Footer — dynamic --}}
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
            $shop_links = [
                ['label' => __('Hiking'),   'url' => theme_shop_url()],
                ['label' => __('Camping'),  'url' => theme_shop_url()],
                ['label' => __('Climbing'), 'url' => theme_shop_url()],
                ['label' => __('Cycling'),  'url' => theme_shop_url()],
                ['label' => __('Clothing'), 'url' => theme_shop_url()],
            ];
        }
    }

    if (empty($help_links)) {
        $help_links = [
            ['label' => __('Gear Guides'),  'url' => theme_home_url()],
            ['label' => __('Size Charts'),  'url' => theme_home_url()],
            ['label' => __('Returns'),      'url' => theme_home_url()],
            ['label' => __('Track Order'),  'url' => theme_home_url()],
            ['label' => __('Contact'),      'url' => theme_home_url()],
        ];
    }

    $promise_items = [
        ['icon' => '🌿', 'text' => __('Sustainable Materials Where Possible')],
        ['icon' => '✅', 'text' => __('Every Product Trail Tested')],
        ['icon' => '🚚', 'text' => __('Free Returns within 60 days')],
        ['icon' => '🤝', 'text' => __('1% for the Planet Member')],
    ];

    $socials = [
        ['icon' => 'mdi mdi-instagram',  'url' => get_static_option('instagram_url')],
        ['icon' => 'mdi mdi-youtube',    'url' => get_static_option('youtube_url')],
        ['icon' => 'mdi mdi-facebook',   'url' => get_static_option('facebook_url')],
        ['icon' => 'mdi mdi-twitter',    'url' => get_static_option('twitter_url')],
    ];

    $footer_desc = get_static_option('footer_description') ?: get_static_option('site_description');
    $site_name   = theme_site_name();
@endphp

<footer class="tc-footer">
    <div class="container">
        <div class="row g-4">

            {{-- Brand col --}}
            <div class="col-lg-4 col-md-6">
                <div class="tc-footer-brand">
                    <a href="{{ theme_home_url() }}" class="d-inline-flex align-items-center gap-2 text-white text-decoration-none"
                       style="font-size:24px;font-weight:700;">
                        {!! theme_logo_html('', 'tc-footer-logo-img', '') !!}
                    </a>
                </div>
                <p class="tc-footer-desc">
                    {{ $footer_desc ?: __('Outfitting adventurers since 1998. We only sell gear we\'d trust on any trail — tested by our team of guides, climbers, and explorers.') }}
                </p>
                <div class="tc-socials">
                    @foreach($socials as $s)
                        @if(!empty($s['url']))
                            <a href="{{ $s['url'] }}" class="tc-social-btn"
                               target="_blank" rel="noopener noreferrer">
                                <i class="{{ $s['icon'] }}"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Gear col --}}
            <div class="col-lg-2 col-md-6">
                <div class="tc-footer-title">{{ __('Gear') }}</div>
                <ul class="tc-footer-links">
                    @foreach($shop_links as $link)
                        <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Help col --}}
            <div class="col-lg-2 col-md-6">
                <div class="tc-footer-title">{{ __('Help') }}</div>
                <ul class="tc-footer-links">
                    @foreach($help_links as $link)
                        <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Promise col --}}
            <div class="col-lg-4 col-md-6">
                <div class="tc-footer-title">{{ __('Our Promise') }}</div>
                @foreach($promise_items as $item)
                    <div class="tc-footer-promise-row">
                        <span>{{ $item['icon'] }}</span>
                        <span>{{ $item['text'] }}</span>
                    </div>
                @endforeach
            </div>

        </div>

        <div class="tc-footer-bottom">
            <span>{!! theme_footer_copyright() !!}</span>
            <div class="tc-footer-bottom-links">
                {!! theme_footer_widgets('footer_bottom_right', false) !!}
            </div>
        </div>
    </div>
</footer>
