<section class="py-36" style="background-color: var(--section-bg-3, #F4F8FB)">
    <div class="container mx-auto px-8">
        <div class="max-w-2xl mx-auto text-center">
            <span class="rounded-lg py-1 px-3 border-2 bg-subTitle text-base-200 border-borderCS">
                {{ $badgeText }}
            </span>
            <h3 class="font-urbanist font-semibold text-secondary text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                {{ $title }}
            </h3>
        </div>

        <div class="max-w-[824px] mx-auto mt-16">
            <div class="space-y-4" id="accordion">
                @foreach($items as $item)
                <div class="accordion-item border rounded-xl shadow-sm" style="border-color: var(--extra-light-color, #D1D5D9)">
                    <button class="accordion-header w-full flex justify-between items-center p-4 text-left rounded-xl"
                            style="background-color: var(--section-bg-1, #FFFFFF)"
                            aria-expanded="false">
                        <span class="font-normal text-xl text-secondary">
                            {{ $item['question'] }}
                        </span>
                        <i class="icon-base ti tabler-plus icon text-xl text-secondary"></i>
                    </button>

                    <div class="accordion-content" style="background-color: var(--section-bg-1, #FFFFFF)">
                        <div class="px-6 pb-6 text-base leading-[24px]" style="color: var(--body-color, #666666)">
                            {{ $item['answer'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
