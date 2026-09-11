{{-- Landlord Admin – Tailwind Pagination --}}
@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">

        {{-- Info --}}
        <p class="text-[11px] text-muted">
            {!! __('Showing') !!}
            <span class="font-semibold text-dark">{{ $paginator->firstItem() }}</span>
            {!! __('to') !!}
            <span class="font-semibold text-dark">{{ $paginator->lastItem() }}</span>
            {!! __('of') !!}
            <span class="font-semibold text-dark">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        {{-- Page buttons --}}
        <div class="flex items-center gap-1 flex-wrap">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 rounded-lg border border-main bg-surface flex items-center justify-center text-muted/40 cursor-not-allowed">
                    <i class="mdi mdi-chevron-left text-base"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="w-8 h-8 rounded-lg border border-main bg-surface flex items-center justify-center text-muted hover:bg-primary-soft hover:text-primary hover:border-primary transition">
                    <i class="mdi mdi-chevron-left text-base"></i>
                </a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs text-muted">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 rounded-lg bg-primary border border-primary flex items-center justify-center text-xs font-bold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="w-8 h-8 rounded-lg border border-main bg-surface flex items-center justify-center text-xs font-medium text-dark hover:bg-primary-soft hover:text-primary hover:border-primary transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="w-8 h-8 rounded-lg border border-main bg-surface flex items-center justify-center text-muted hover:bg-primary-soft hover:text-primary hover:border-primary transition">
                    <i class="mdi mdi-chevron-right text-base"></i>
                </a>
            @else
                <span class="w-8 h-8 rounded-lg border border-main bg-surface flex items-center justify-center text-muted/40 cursor-not-allowed">
                    <i class="mdi mdi-chevron-right text-base"></i>
                </span>
            @endif

        </div>
    </div>
@endif
