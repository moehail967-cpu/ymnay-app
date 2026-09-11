<section class="py-32 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">

            <div class="lg:col-span-2">
                <div class="pb-12">
                    <!-- Date -->
                    <div class="flex items-center gap-2 mb-6">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"></line>
                            <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"></line>
                            <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"></line>
                        </svg>
                        <span class="text-quate text-base">{{ $blog->created_at ? $blog->created_at->format('d F, Y') : '' }}</span>
                    </div>

                    <!-- Main Heading -->
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold font-urbanist text-secondary leading-10 mb-6">
                        {{ $blog->title }}
                    </h1>

                    <!-- Description -->
                    @if(!empty($blog->excerpt))
                    <p class="text-quate text-base leading-6 mb-10">
                        {{ $blog->excerpt }}
                    </p>
                    @endif

                    <!-- Author Info and Actions -->
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <!-- Author Section -->
                        <div class="flex items-center gap-3">
                            @if($author && isset($author->image))
                            {!! render_image_markup_by_attachment_id($author->image, 'w-12 h-12 rounded-full object-cover') !!}
                            @endif
                            <div>
                                <h3 class="text-secondary text-xl font-medium">{{ $authorName }}</h3>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-4">
                            <button onclick="navigator.share && navigator.share({title: '{{ addslashes($blog->title) }}', url: window.location.href})"
                                class="flex items-center gap-2 text-secondary hover:text-gray-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                                    </path>
                                </svg>
                                <span class="text-base text-secondary">Share</span>
                            </button>
                        </div>
                    </div>
                </div>

                <article>
                    <!-- Featured Image -->
                    @if(!empty($blog->image))
                    <div class="mb-4">
                        {!! render_image_markup_by_attachment_id($blog->image, 'w-full h-64 sm:h-80 md:h-96 lg:h-125 object-cover rounded-2xl shadow-lg', 'full', false) !!}
                    </div>
                    @endif

                    <!-- Content -->
                    <div class="blog-content">
                        {!! $blog->blog_content !!}
                    </div>

                    <!-- Tags -->
                    @if(!empty($tags))
                    <div class="bg-[#F8FAFB] border border-borderCS rounded-lg px-5 py-4 shadow-sm mt-8">
                        <div class="flex items-center gap-4 flex-wrap">
                            <span class="text-secondary font-urbanist text-2xl font-semibold">Tags:</span>
                            <div class="flex items-center gap-3 flex-wrap">
                                @foreach($tags as $tag)
                                <span class="bg-white text-quate border border-borderCS px-4 py-2 rounded-full text-sm font-medium">
                                    {{ $tag }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </article>

                <!-- Comments Section -->
                @if($comments->count() > 0)
                <section class="mb-12">
                    <h2 class="text-2xl md:text-3xl font-urbanist font-semibold text-secondary my-8">
                        {{ $commentsHeading }} (<span>{{ str_pad($comments->count(), 2, '0', STR_PAD_LEFT) }}</span>)
                    </h2>

                    <div class="space-y-6">
                        @foreach($comments as $comment)
                            <article class="comment-item bg-white p-4 border-b border-borderCS">
                            <div class="flex items-start gap-4">
                                @php $commentImage = optional($comment->user)->image ?? optional($comment->admin)->image ?? null; @endphp
                                @if($commentImage)
                                {!! render_image_markup_by_attachment_id($commentImage, 'w-8 h-8 md:w-10 md:h-10 rounded-full object-cover flex-shrink-0') !!}
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-3 mb-2">
                                        <h3 class="text-xl font-medium text-secondary">{{ $comment->commented_by ?? ($comment->user->name ?? '') }}</h3>
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-base font-medium bg-[#F1F6F7] text-quate">
                                            {{ $comment->created_at ? $comment->created_at->format('F d, Y') : '' }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 font-normal text-base leading-6 mb-1">
                                        {{ $comment->comment_content }}
                                    </p>
                                    <button class="reply-btn inline-flex items-center text-xl font-normal text-sectionC transition-colors duration-200">
                                        {{ $replyText }}
                                    </button>
                                </div>
                            </div>
                        </article>

                        {{-- Replies --}}
                            @foreach($comment->reply as $reply)
                        <article class="comment-item bg-white p-4 border-b border-borderCS ml-8 md:ml-16">
                            <div class="flex items-start gap-4">
                                @php $replyImage = optional($reply->user)->image ?? optional($reply->admin)->image ?? null; @endphp
                                @if($replyImage)
                                {!! render_image_markup_by_attachment_id($replyImage, 'w-8 h-8 md:w-10 md:h-10 rounded-full object-cover shrink-0') !!}
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-3 mb-2">
                                        <h3 class="text-xl font-medium text-secondary">{{ $reply->commented_by ?? ($reply->user->name ?? '') }}</h3>
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-base font-medium bg-[#F1F6F7] text-quate">
                                            {{ $reply->created_at ? $reply->created_at->format('F d, Y') : '' }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 font-normal text-base leading-6 mb-1">
                                        {{ $reply->comment_content }}
                                    </p>
                                    <button class="reply-btn inline-flex items-center text-xl font-normal text-sectionC transition-colors duration-200">
                                        {{ $replyText }}
                                    </button>
                                </div>
                            </div>
                        </article>
                        @endforeach
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- Comment Form -->
                <div class="rounded-3xl p-6 sm:p-8 bg-[#F8FAFB] shadow-lg border border-borderCS">
                    <h2 class="text-2xl md:text-3xl font-bold text-primarys mb-6">{{ $formHeading }}</h2>

                    <form id="blog-comment-form" action="{{ $commentStoreUrl }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="blog_id" value="{{ $blog->id }}">
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="commented_by" class="sr-only">Name</label>
                                <input type="text" id="commented_by" name="commented_by" placeholder="Your Name" required
                                       value="{{ auth()->user()->name ?? '' }}"
                                       class="w-full px-4 py-3 rounded-xl bg-bg-counter border border-gray-200 text-text-primarys placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sectionC focus:border-transparent transition-all duration-200">
                            </div>
                            <div>
                                <label for="email" class="sr-only">Email</label>
                                <input type="email" id="email" name="email" placeholder="Email Address"
                                       value="{{ auth()->user()->email ?? '' }}"
                                       class="w-full px-4 py-3 rounded-xl bg-bg-counter border border-gray-200 text-text-primarys placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sectionC focus:border-transparent transition-all duration-200">
                            </div>
                        </div>

                        <div>
                            <label for="comment_content" class="sr-only">Comment</label>
                            <textarea id="comment_content" name="comment_content" rows="6" placeholder="Enter Your Comment" required
                                      class="w-full px-4 py-3 rounded-xl bg-bg-counter border border-gray-200 text-text-primarys placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sectionC focus:border-transparent transition-all duration-200 resize-none"></textarea>
                        </div>

                        <button type="submit"
                                class="w-full bg-primary text-white font-semibold py-4 rounded-full transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-teal-800">
                            {{ $submitText }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-1">
                <aside class="space-y-5 sticky top-32">
                    <!-- Table Of Content -->
                    <div class="bg-[#F8FAFB] border border-borderCS p-8 px-6 rounded-xl">
                        <h3 class="font-semibold font-urbanist text-secondary text-xl">{{ $tocHeading }}</h3>
                        <ul class="mt-6 space-y-5" id="toc-list">
                        </ul>
                    </div>

                    <!-- Popular Posts -->
                    @if($popularPosts->count() > 0)
                    <div class="bg-[#F8FAFB] border border-borderCS p-8 px-6 rounded-xl">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $popularHeading }}</h2>
                        <ul class="space-y-4">
                            @foreach($popularPosts as $index => $popular)
                            @php
                                try {
                                    $popularUrl = route('landlord.frontend.blog.single', $popular->slug);
                                } catch (\Exception $e) {
                                    $popularUrl = '#';
                                }
                            @endphp
                            <li class="flex items-start gap-3 {{ !$loop->last ? 'pb-7 border-b border-gray-200' : '' }} cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                                <a href="{{ $popularUrl }}" class="flex items-start gap-3 w-full">
                                    @if(!empty($popular->image))
                                    {!! render_image_markup_by_attachment_id($popular->image, 'w-20 h-20 rounded-xl object-cover shrink-0', 'thumb') !!}
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-600 mb-1">{{ $popular->created_at ? $popular->created_at->format('F d, Y') : '' }}</p>
                                        <p class="text-gray-800 leading-snug">{{ \Illuminate\Support\Str::limit($popular->title, 60) }}</p>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Share Section -->
                    <div class="bg-[#F8FAFB] border border-borderCS p-8 px-6 rounded-xl">
                        <h3 class="text-base font-medium text-gray-900 mb-4">{{ $shareHeading }}</h3>
                        <div class="flex gap-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank"
                               class="w-10 h-10 bg-primary rounded flex items-center justify-center text-white transition-all hover:scale-110">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($blog->title) }}" target="_blank"
                               class="w-10 h-10 bg-primary rounded flex items-center justify-center text-white transition-all hover:scale-110">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($blog->title) }}" target="_blank"
                               class="w-10 h-10 bg-primary rounded flex items-center justify-center text-white transition-all hover:scale-110">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-generate Table of Contents from blog content headings
        const content = document.querySelector('.blog-content');
        const tocList = document.getElementById('toc-list');
        if (content && tocList) {
            const headings = content.querySelectorAll('h2, h3');
            headings.forEach(function (heading, index) {
                heading.id = 'section-' + index;
                const li = document.createElement('li');
                li.className = 'text-quate font-normal text-base cursor-pointer hover:text-sectionC transition-colors';
                li.textContent = heading.textContent;
                li.addEventListener('click', function () {
                    heading.scrollIntoView({behavior: 'smooth'});
                });
                tocList.appendChild(li);
            });
        }
    });
</script>
