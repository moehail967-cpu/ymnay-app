<section class="py-16 lg:py-24 px-4" style="background-color: var(--section-bg-3, #F4F8FB)">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        @if(!empty($noticeContent))
        <div class="border border-primary rounded-xl p-8" style="background-color: rgba(var(--main-color-one-rgb, 92,231,32), 0.08)">
            <h4 class="text-xl font-medium text-secondary leading-7">{{ $noticeTitle }}</h4>
            <p class="font-normal text-base mt-4" style="color: var(--body-color, #666666)">{{ $noticeContent }}</p>
        </div>
        @endif

        @if(!empty($sections))
        <div class="rounded-2xl shadow-sm p-4 lg:p-8 border border-borderCS mt-8 min-h-screen" style="background-color: var(--section-bg-3, #F8FAFB)">
            <article class="space-y-8">
                @foreach($sections as $section)
                <section class="space-y-4">
                    @if(!empty($section['title']))
                    <h4 class="text-xl font-urbanist font-semibold text-secondary lg:text-3xl">{{ $section['title'] }}</h4>
                    @endif
                    @if(!empty($section['content']))
                    <div class="text-base leading-6 wrap-break-word terms-content" style="color: var(--body-color, #666666)">
                        {!! nl2br(e($section['content'])) !!}
                    </div>
                    @endif
                </section>
                @endforeach
            </article>
        </div>
        @endif

    </div>
</section>
