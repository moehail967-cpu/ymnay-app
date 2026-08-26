<section class="py-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($data['repeater_title_'] ?? [] as $key => $title)
                <div class="single-promo center-text single-border promo-padding">
                    <div class="single-promo-icon">
                        <i class="{{ $data['repeater_icon_'][$key] ?? '' }}"></i>
                    </div>
                    <div class="single-promo-contents mt-2">
                        <h4 class="single-promo-contents-title fw-500">
                            <a href="javascript:void(0)">{{ esc_html($title) }}</a>
                        </h4>
                        <p class="single-promo-contents-para mt-2">
                            {{ esc_html($data['repeater_subtitle_'][$key] ?? '') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
