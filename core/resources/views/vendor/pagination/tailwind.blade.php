{{--@if ($paginator->hasPages())--}}
{{--    <div class="flex items-center justify-center gap-2 flex-wrap">--}}
{{--        --}}{{-- Previous --}}
{{--        @if ($paginator->onFirstPage())--}}
{{--            <span class="bg-white border border-gray-200 text-gray-300 text-sm rounded-full flex items-center gap-1 px-4 py-2 font-medium cursor-not-allowed">--}}
{{--                <i class="icon-base ti tabler-chevron-left text-base"></i>--}}
{{--                {{__('Previous')}}--}}
{{--            </span>--}}
{{--        @else--}}
{{--            <a href="{{ $paginator->previousPageUrl() }}"--}}
{{--               class="bg-white border border-gray-200 text-gray-700 hover:text-gray-900 text-sm rounded-full flex items-center gap-1 px-4 py-2 font-medium transition-colors duration-300 active:scale-95">--}}
{{--                <i class="icon-base ti tabler-chevron-left text-base"></i>--}}
{{--                {{__('Previous')}}--}}
{{--            </a>--}}
{{--        @endif--}}

{{--        --}}{{-- Page Numbers --}}
{{--        @foreach ($elements as $element)--}}
{{--            @if (is_string($element))--}}
{{--                <span class="bg-white w-9 h-9 flex items-center justify-center border border-gray-200 text-gray-400 rounded-full font-medium text-sm">--}}
{{--                    {{ $element }}--}}
{{--                </span>--}}
{{--            @endif--}}

{{--            @if (is_array($element))--}}
{{--                @foreach ($element as $page => $url)--}}
{{--                    @if ($page == $paginator->currentPage())--}}
{{--                        <span class="bg-primary w-9 h-9 flex items-center justify-center text-white rounded-full font-medium text-sm shadow-sm">--}}
{{--                            {{ $page }}--}}
{{--                        </span>--}}
{{--                    @else--}}
{{--                        <a href="{{ $url }}"--}}
{{--                           class="bg-white w-9 h-9 flex items-center justify-center border border-gray-200 text-gray-600 rounded-full font-medium text-sm hover:border-sectionC hover:text-sectionC transition-colors duration-300">--}}
{{--                            {{ $page }}--}}
{{--                        </a>--}}
{{--                    @endif--}}
{{--                @endforeach--}}
{{--            @endif--}}
{{--        @endforeach--}}

{{--        --}}{{-- Next --}}
{{--        @if ($paginator->hasMorePages())--}}
{{--            <a href="{{ $paginator->nextPageUrl() }}"--}}
{{--               class="bg-white border border-gray-200 text-gray-700 hover:text-gray-900 text-sm rounded-full flex items-center gap-1 px-4 py-2 font-medium transition-colors duration-300 active:scale-95">--}}
{{--                {{__('Next')}}--}}
{{--                <i class="icon-base ti tabler-chevron-right text-base"></i>--}}
{{--            </a>--}}
{{--        @else--}}
{{--            <span class="bg-white border border-gray-200 text-gray-300 text-sm rounded-full flex items-center gap-1 px-4 py-2 font-medium cursor-not-allowed">--}}
{{--                {{__('Next')}}--}}
{{--                <i class="icon-base ti tabler-chevron-right text-base"></i>--}}
{{--            </span>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--@endif--}}


@if ($paginator->hasPages())
    <div class="flex items-center justify-center gap-1 sm:gap-2 flex-wrap">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="bg-white border border-gray-200 text-gray-300 text-xs sm:text-sm rounded-full flex items-center gap-1 px-3 sm:px-4 py-1.5 sm:py-2 font-medium cursor-not-allowed">
                <i class="icon-base ti tabler-chevron-left text-sm sm:text-base"></i>
                <span class="hidden sm:inline">{{__('Previous')}}</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="bg-white border border-gray-200 text-gray-700 hover:text-gray-900 text-xs sm:text-sm rounded-full flex items-center gap-1 px-3 sm:px-4 py-1.5 sm:py-2 font-medium transition-colors duration-300 active:scale-95">
                <i class="icon-base ti tabler-chevron-left text-sm sm:text-base"></i>
                <span class="hidden sm:inline">{{__('Previous')}}</span>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="bg-white w-7 h-7 sm:w-9 sm:h-9 flex items-center justify-center border border-gray-200 text-gray-400 rounded-full font-medium text-xs sm:text-sm">
                    {{ $element }}
                </span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="bg-primary w-7 h-7 sm:w-9 sm:h-9 flex items-center justify-center text-white rounded-full font-medium text-xs sm:text-sm shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="bg-white w-7 h-7 sm:w-9 sm:h-9 flex items-center justify-center border border-gray-200 text-gray-600 rounded-full font-medium text-xs sm:text-sm hover:border-sectionC hover:text-sectionC transition-colors duration-300">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="bg-white border border-gray-200 text-gray-700 hover:text-gray-900 text-xs sm:text-sm rounded-full flex items-center gap-1 px-3 sm:px-4 py-1.5 sm:py-2 font-medium transition-colors duration-300 active:scale-95">
                <span class="hidden sm:inline">{{__('Next')}}</span>
                <i class="icon-base ti tabler-chevron-right text-sm sm:text-base"></i>
            </a>
        @else
            <span class="bg-white border border-gray-200 text-gray-300 text-xs sm:text-sm rounded-full flex items-center gap-1 px-3 sm:px-4 py-1.5 sm:py-2 font-medium cursor-not-allowed">
                <span class="hidden sm:inline">{{__('Next')}}</span>
                <i class="icon-base ti tabler-chevron-right text-sm sm:text-base"></i>
            </span>
        @endif
    </div>
@endif
