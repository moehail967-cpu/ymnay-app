<!-- Comments List Section -->
<section class="mb-12" id="comment-area">
    <div>
        <h2 id="comments-title" class="text-2xl md:text-3xl font-urbanist font-semibold text-secondary my-8">
            {{ __('Comments') }}
            (<span id="comments-count">{{ $blog_comments->count() > 0 ? str_pad($blog_comments->count(), 2, '0', STR_PAD_LEFT) : '0' }}</span>)
        </h2>

        <div id="comment_data" data-items="{{ $blog_comments->count() }}">
            <div id="comments-list" class="space-y-6">

                @forelse($blog_comments as $data)
                    @php
                           $user_image = optional($data->user)->image ?? optional($data->admin)->image ?? get_static_option('blog_avatar_image');
                           $image_markup = render_image_markup_by_attachment_id($user_image, 'w-8 h-8 md:w-10 md:h-10 rounded-full object-cover flex-shrink-0', 'thumb');
                    @endphp
                    <!-- Parent Comment -->
                    <article class="comment-item bg-white p-4 border-b border-borderCS">
                        <div class="flex items-start gap-4">
                            @if(!empty($image_markup))
                                {!! $image_markup !!}
                            @else
                                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="text-xl font-medium text-secondary">
                                        <a href="javascript:void(0)" data-parent_name="{{ optional($data->user)->name }}">
                                            {{ $data->commented_by ?? '' }}
                                        </a>
                                    </h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-[#F1F6F7] text-quate">
                                        {{ date('d F Y', strtotime($data->created_at ?? '')) }}
                                    </span>
                                </div>

                                <p class="text-gray-700 font-normal text-base leading-6 mb-1">
                                    {!! $data->comment_content ?? '' !!}
                                </p>

                                @if(auth('web')->check() || auth('admin')->check())
                                    <button class="reply-btn btn-replay inline-flex items-center text-sm font-normal text-sectionC transition-colors duration-200 underline"
                                            data-comment_id="{{ $data->id }}">
                                        {{ __('Reply') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </article>

                    <!-- Child Replies (Nested/Indented) -->
                    @foreach($data->reply as $repData)
                        @php
                            $child_user_image = optional($repData->user)->image ?? optional($repData->admin)->image ?? get_static_option('blog_avatar_image');
                            $child_image_markup = render_image_markup_by_attachment_id($child_user_image, 'w-8 h-8 md:w-10 md:h-10 rounded-full object-cover shrink-0', 'thumb');
                        @endphp
                        <article class="comment-item bg-white p-4 border-b border-borderCS ml-8 md:ml-16">
                            <div class="flex items-start gap-4">
                                @if(!empty($child_image_markup))
                                    {!! $child_image_markup !!}
                                @else
                                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-3 mb-2">
                                        <h3 class="text-xl font-medium text-secondary">
                                            {{ optional($repData->user)->name ?? $repData->commented_by ?? '' }}
                                        </h3>
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-[#F1F6F7] text-quate">
                                            {{ date('d F Y', strtotime($repData->created_at ?? '')) }}
                                        </span>
                                    </div>

                                    <p class="text-gray-700 font-normal text-base leading-6 mb-1">
                                        {!! $repData->comment_content ?? '' !!}
                                    </p>
                                </div>
                            </div>
                        </article>
                    @endforeach

                @empty
                    <div class="text-center py-8 text-quate">
                        {{ __('No Comment Available') }}
                    </div>
                @endforelse
            </div>
        </div>

        @if($blog_comments->count() > 0)
            <div class="mt-6 text-center">
                <a href="javascript:void(0)"
                   class="inline-block px-6 py-3 border border-borderCS rounded-full text-secondary hover:bg-gray-50 transition-colors"
                   id="load_more_comment_button">
                    {{ __('Show More') }}
                </a>
            </div>
        @endif
    </div>
</section>
