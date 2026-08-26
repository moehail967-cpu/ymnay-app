<!-- Blogs -->
<section class="pt-14 pb-14 md:pt-24 md:pb-24 xl:pt-30 xl:pb-30 2xl:pt-35 2xl:pb-35" style="background-color: var(--section-bg-3, #F4F8FB)">
    <div class="container mx-auto max-w-8xl px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
            @foreach($posts as $post)
            <div class="w-full overflow-hidden flex flex-col h-full">
                <div class="relative h-64 md:h-72 overflow-hidden rounded-tr-2xl rounded-tl-2xl">
                    <a href="{{ $post['detail_url'] }}">
                        {!! render_image_markup_by_attachment_id($post['image'], 'w-full h-full object-cover transition-transform duration-500 ease-in-out hover:scale-110', 'full', false) !!}
                    </a>
                </div>

                <div class="py-4 flex flex-col flex-1">
                    <div class="flex items-center gap-2">
                        @if(!empty($calendarIcon))
                        <img src="{{ $calendarIcon }}" alt="calendar">
                        @endif
                        <span class="text-base" style="color: var(--body-color, #666666)">{{ $post['date'] }}</span>
                    </div>

                    <h2 class="text-[20px] font-urbanist text-secondary leading-7 font-semibold mt-2 mb-3 overflow-hidden text-ellipsis [display:-webkit-box] [-webkit-line-clamp:2] [-webkit-box-orient:vertical]">
                        <a href="{{ $post['detail_url'] }}"> {{ $post['title'] }}</a>
                    </h2>

                    <a href="{{ $post['detail_url'] }}" class="mt-auto text-sm sm:text-base flex items-center gap-2 cursor-pointer group hover:text-primary" style="color: var(--body-color, #666666)">
                        <span class="font-medium">{{ $readMoreText }}</span>
                        <svg viewBox="0 0 24 24" class="w-5 h-5 sm:w-6 sm:h-6 transition-all duration-300 group-hover:translate-x-1 group-hover:-translate-y-1" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 7L7 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8 7H17V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($blogs->hasPages())
        <div class="flex items-center justify-start gap-2 mt-6 md:mt-8 lg:mt-12">
            {{-- Previous --}}
            @if($blogs->onFirstPage())
            <span class="border border-borderCS text-lg rounded-[36px] flex items-center gap-1 px-3 py-2 font-medium"
                  style="background-color: var(--section-bg-1, #fff); color: var(--extra-light-color, #9ca3af)">
                <i class="icon-base ti tabler-chevron-left icon-22px"></i>
                Previous
            </span>
            @else
            <a href="{{ $blogs->previousPageUrl() }}"
               class="border border-borderCS text-lg rounded-[36px] flex items-center gap-1 px-3 py-2 cursor-pointer font-medium transition-colors duration-300 active:scale-95"
               style="background-color: var(--section-bg-1, #fff); color: var(--heading-color, #374253)">
                <i class="icon-base ti tabler-chevron-left icon-22px"></i>
                Previous
            </a>
            @endif

            {{-- Page Numbers --}}
            @foreach($blogs->getUrlRange(1, $blogs->lastPage()) as $page => $url)
                @if($page == $blogs->currentPage())
                <span class="w-10 h-10 flex items-center justify-center border border-borderCS bg-primary text-white rounded-full font-medium text-xl">
                    {{ $page }}
                </span>
                @else
                <a href="{{ $url }}"
                   class="w-10 h-10 flex items-center justify-center border border-borderCS rounded-full font-medium text-xl transition-colors hover:bg-primary hover:text-white"
                   style="background-color: var(--section-bg-1, #fff); color: var(--heading-color, #374253)">
                    {{ $page }}
                </a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($blogs->hasMorePages())
            <a href="{{ $blogs->nextPageUrl() }}"
               class="border border-borderCS text-lg rounded-[36px] flex items-center gap-1 px-3 py-2 cursor-pointer font-medium transition-colors duration-300 active:scale-95"
               style="background-color: var(--section-bg-1, #fff); color: var(--heading-color, #374253)">
                Next
                <i class="icon-base ti tabler-chevron-right icon-22px"></i>
            </a>
            @else
            <span class="border border-borderCS text-lg rounded-[36px] flex items-center gap-1 px-3 py-2 font-medium"
                  style="background-color: var(--section-bg-1, #fff); color: var(--extra-light-color, #9ca3af)">
                Next
                <i class="icon-base ti tabler-chevron-right icon-22px"></i>
            </span>
            @endif
        </div>
        @endif
    </div>
</section>
