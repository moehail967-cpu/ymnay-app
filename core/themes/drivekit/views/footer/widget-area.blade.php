<footer class="dk-footer">
    <div class="container">
        <div class="row g-4 pb-5">

            {{-- Col 1: Brand --}}
            <div class="col-lg-4 col-md-6">
                @php $logo = theme_white_logo_url() ?? theme_logo_url(); @endphp
                @if($logo)
                    <a href="{{ theme_home_url() }}" class="dk-footer-logo d-inline-block mb-3">
                        <img src="{{ $logo }}" alt="{{ get_static_option('site_title') }}">
                    </a>
                @else
                    <a href="{{ theme_home_url() }}" class="dk-footer-brand-text">DRIVE<span>KIT</span></a>
                @endif
                @if(get_static_option('site_description'))
                    <p class="dk-footer-desc">{{ get_static_option('site_description') }}</p>
                @endif
                @if($all_social_icons->isNotEmpty())
                    <div class="dk-footer-social">
                        @foreach($all_social_icons as $social)
                            <a href="{{ $social->url }}" target="_blank" rel="noopener">
                                <i class="{{ $social->icon }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Col 2: Shop menu (status = 'shop') --}}
            @php
                $shop_menu_id = \App\Models\Menu::where('status', 'shop')->first()?->id;
                $shop_links   = theme_footer_menu_links($shop_menu_id);
            @endphp
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="dk-footer-h">{{ __('Shop') }}</h6>
                @if($shop_links)
                    @foreach($shop_links as $link)
                        <a href="{{ $link['url'] }}" class="dk-footer-link">{{ $link['label'] }}</a>
                    @endforeach
                @else
                    <a href="{{ theme_shop_url() }}" class="dk-footer-link">{{ __('Engine Parts') }}</a>
                    <a href="{{ theme_shop_url() }}" class="dk-footer-link">{{ __('Electrical') }}</a>
                    <a href="{{ theme_shop_url() }}" class="dk-footer-link">{{ __('Tyres') }}</a>
                    <a href="{{ theme_shop_url() }}" class="dk-footer-link">{{ __('Tools') }}</a>
                    <a href="{{ theme_shop_url() }}" class="dk-footer-link">{{ __('Performance') }}</a>
                @endif
            </div>

            {{-- Col 3: Help menu (status = 'help') --}}
            @php
                $help_menu_id = \App\Models\Menu::where('status', 'help')->first()?->id;
                $help_links   = theme_footer_menu_links($help_menu_id);
            @endphp
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="dk-footer-h">{{ __('Help') }}</h6>
                @if($help_links)
                    @foreach($help_links as $link)
                        <a href="{{ $link['url'] }}" class="dk-footer-link">{{ $link['label'] }}</a>
                    @endforeach
                @else
                    <a href="{{ theme_home_url() }}" class="dk-footer-link">{{ __('Part Finder') }}</a>
                    <a href="{{ theme_home_url() }}" class="dk-footer-link">{{ __('Trade Account') }}</a>
                    <a href="{{ theme_home_url() }}" class="dk-footer-link">{{ __('Returns') }}</a>
                    <a href="{{ theme_home_url() }}" class="dk-footer-link">{{ __('Track Order') }}</a>
                    <a href="{{ theme_home_url() }}" class="dk-footer-link">{{ __('Contact') }}</a>
                @endif
            </div>

            {{-- Col 4: Why DriveKit (widget area → fallback to hardcoded) --}}
            @php $trust_html = theme_footer_widgets('footer_trust', false); @endphp
            <div class="col-lg-4 col-md-6">
                <h6 class="dk-footer-h">{{ __('Why DriveKit') }}</h6>
                @if(trim($trust_html))
                    {!! $trust_html !!}
                @else
                    <div class="dk-footer-trust-item"><span>✅</span> {{ __('OEM & Aftermarket Parts') }}</div>
                    <div class="dk-footer-trust-item"><span>🚚</span> {{ __('Same-Day Dispatch Before 2pm') }}</div>
                    <div class="dk-footer-trust-item"><span>🔒</span> {{ __('2-Year Warranty on Tools') }}</div>
                    <div class="dk-footer-trust-item"><span>👥</span> {{ __('Direct from Manufacturer') }}</div>
                @endif
            </div>

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="dk-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>{!! theme_footer_copyright() !!}</span>
            <div class="d-flex gap-3">
                <a href="{{ theme_home_url() }}">{{ __('Privacy') }}</a>
                <a href="{{ theme_home_url() }}">{{ __('Terms') }}</a>
                <a href="{{ theme_home_url() }}">{{ __('Returns Policy') }}</a>
            </div>
        </div>
    </div>
</footer>
