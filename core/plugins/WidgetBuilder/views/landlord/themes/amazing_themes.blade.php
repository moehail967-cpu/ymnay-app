<style>
    /* ── Card container ── */
    .theme-card {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        background: var(--section-bg-1, #fff);
        border: 1px solid var(--extra-light-color, #e0f0f0);
        box-shadow: 0 2px 12px -2px rgba(0,0,0,.05);
        transition: transform 0.28s cubic-bezier(.22,.61,.36,1), box-shadow 0.28s ease;
    }
    .theme-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px -10px rgba(0,0,0,.12);
    }

    /* ── Image area ── */
    .theme-card-img {
        position: relative;
        height: 320px;
        overflow: hidden;
        background: var(--section-bg-2, #F0FCFB);
    }
    .theme-card-img img {
        width: 100%;
        height: auto;
        min-height: 100%;
        object-fit: cover;
        object-position: top center;
        pointer-events: none;
        transition: transform 6s cubic-bezier(.25,.46,.45,.94);
    }
    .theme-card:hover .theme-card-img img {
        transform: translateY(calc(-100% + 320px));
    }

    /* ── Hover gradient overlay ── */
    .theme-card-gradient {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 30%, rgba(15,23,42,.65) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        z-index: 1;
    }
    .theme-card:hover .theme-card-gradient { opacity: 1; }

    /* ── Hover action buttons ── */
    .theme-card-hover-actions {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        right: 1rem;
        display: flex;
        gap: 0.5rem;
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
        z-index: 2;
    }
    .theme-card:hover .theme-card-hover-actions {
        opacity: 1;
        transform: translateY(0);
    }
    .hover-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        flex: 1;
        padding: 0.5rem 1rem;
        border-radius: 0.625rem;
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.15s;
        white-space: nowrap;
        text-decoration: none;
    }
    .hover-action-btn.btn-preview:hover {
        background: var(--main-color-one);
        color: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,.10);
    }
    .hover-action-btn.btn-preview {
        background: rgba(var(--main-color-one-rgb, 13,77,84), 0.1);
        color: var(--main-color-one);
    }

    /* ── Card footer ── */
    .theme-card-footer {
        padding: 0.875rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
</style>
<section class="py-20" style="background-color: var(--section-bg-3, #F4F8FB)">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($themes as $theme)
                <div class="theme-card">
                    <div class="theme-card-img">
                        <img src="{{ $theme['image'] }}" alt="{{ $theme['name'] }}" loading="lazy">
                        <div class="theme-card-gradient"></div>
                        <div class="theme-card-hover-actions">
                            <a href="{{ $theme['url'] }}" target="_blank" class="hover-action-btn btn-preview">
                                <i class="mdi mdi-eye-outline"></i>
                                {{ $buttonText }}
                            </a>
                        </div>
                    </div>
                    <div class="theme-card-footer">
                        <h4 class="text-sm font-bold text-secondary font-urbanist truncate">{{ $theme['name'] }}</h4>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($themeRecords->hasPages())
            <div class="flex items-center justify-start gap-2 mt-6 md:mt-8 lg:mt-12">
                {{-- Previous --}}
                @if($themeRecords->onFirstPage())
                    <span
                        class="border border-borderCS text-lg rounded-[36px] flex items-center gap-1 px-3 py-2 text-gray-400 font-medium cursor-default">
                        <i class="icon-base ti tabler-chevron-left icon-22px"></i>
                        Previous
                    </span>
                @else
                    <a href="{{ $themeRecords->previousPageUrl() }}"
                        class="border border-borderCS text-lg rounded-[36px] flex items-center gap-1 px-3 py-2 text-gray-700 hover:text-text-primarys cursor-pointer font-medium transition-colors duration-300 active:scale-95">
                        <i class="icon-base ti tabler-chevron-left icon-22px"></i>
                        Previous
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach($themeRecords->getUrlRange(1, $themeRecords->lastPage()) as $page => $url)
                    <a href="{{ $url }}"
                        class="page-btn w-10 h-10 flex items-center justify-center border border-index_2text text-pageination rounded-full font-medium text-xl transition-colors {{ $page == $themeRecords->currentPage() ? 'bg-primary text-white' : 'bg-white' }}">
                        {{ $page }}
                    </a>
                @endforeach

                {{-- Next --}}
                @if($themeRecords->hasMorePages())
                    <a href="{{ $themeRecords->nextPageUrl() }}"
                        class="border border-borderCS text-lg rounded-[36px] flex items-center gap-1 px-3 py-2 text-gray-700 hover:text-text-primarys cursor-pointer font-medium transition-colors duration-300 active:scale-95">
                        Next
                        <i class="icon-base ti tabler-chevron-right icon-22px"></i>
                    </a>
                @else
                    <span
                        class="border border-borderCS text-lg rounded-[36px] flex items-center gap-1 px-3 py-2 text-gray-400 font-medium cursor-default">
                        Next
                        <i class="icon-base ti tabler-chevron-right icon-22px"></i>
                    </span>
                @endif
            </div>
        @endif
    </div>
</section>
