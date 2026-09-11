<!-- Feedback -->
<section class="pb-36" style="background-color: var(--section-bg-3, #F4F8FB)">
    <div class="container mx-auto px-8">
        <div class="max-w-2xl mx-auto text-center">
            <span class="rounded-lg py-1 px-3 border-2 bg-subTitle text-base-200 border-borderCS">
                {{ $badgeText }}
            </span>
            <h3 class="font-urbanist font-semibold text-secondary text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                {{ $title }}
            </h3>
        </div>

        <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6 mt-[4.375rem]">
            @foreach($testimonials as $testimonial)
            <div class="testimonial-card break-inside-avoid rounded-3xl border p-8 flex flex-col"
                 style="background-color: var(--section-bg-1, #FFFFFF); border-color: var(--extra-light-color, #e5e7eb)">
                <!-- Quote Icon -->
                @if(!empty($quoteIcon))
                <div class="mb-4 shrink-0">
                    <img src="{{ $quoteIcon }}" alt="" class="w-8">
                </div>
                @endif

                <!-- Text -->
                <p class="testimonial-text leading-[24px] mb-6 flex-grow line-clamp-8 transition-all duration-300"
                   style="color: var(--body-color, #666666)">
                    {{ $testimonial['quote'] }}
                </p>

                <button class="text-primary font-medium text-sm self-start hidden" data-toggle>
                    See more
                </button>

                <!-- Footer -->
                <div class="border-t pt-6 flex items-center gap-4 shrink-0" style="border-color: var(--extra-light-color, #e5e7eb)">
                    @if(!empty($testimonial['author_avatar']))
                        {!! render_image_markup_by_attachment_id($testimonial['author_avatar'], 'w-12 h-12 rounded-full shrink-0', 'full', false) !!}
                    @endif
                    <div>
                        <div class="font-semibold text-secondary">{{ $testimonial['author_name'] }}</div>
                        <div class="text-sm" style="color: var(--body-color, #666666)">{{ $testimonial['author_role'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
