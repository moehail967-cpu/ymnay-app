@extends('landlord.frontend.frontend-page-master')
@php
    $post_img = null;
    $blog_url = route(route_prefix().'frontend.blog.single', $blog_post->slug);
@endphp

@section('page-title')
    {{ __('Blog Details') }}
@endsection

@section('title')
    {{ $blog_post->title }}
@endsection

@section('content')
    <section class="py-20 bg-white">
        <div class="container mx-auto px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">

                <div class="lg:col-span-2">
                    <div class="pb-12">
                        <!-- Date -->
                        <div class="flex items-center gap-2 mb-6">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"></line>
                                <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"></line>
                                <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"></line>
                            </svg>
                            <span class="text-quate text-base">{{ $blog_post->created_at->format('d F, Y') }}</span>
                        </div>

                        <!-- Main Heading -->
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold font-urbanist text-secondary leading-10 mb-6">
                            {{ $blog_post->title }}
                        </h1>

                        <!-- Description -->
                        <p class="text-quate text-base leading-6 mb-10">
                            {{ Str::limit(strip_tags($blog_post->blog_content), 200) }}
                        </p>

                        <!-- Author Info and Actions -->
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <!-- Author Section -->
                            <div class="flex items-center gap-3">
                                @php
                                    $author_image = optional($blog_post->user)->image ?? optional($blog_post->admin)->image ?? get_static_option('blog_avatar_image');
                                @endphp
                                @if($author_image)
                                    {!! render_image_markup_by_attachment_id($author_image, 'w-12 h-12 rounded-full object-cover', 'thumb') !!}
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-secondary text-lg font-medium">{{ substr($blog_post->author ?? 'A', 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-secondary text-xl font-medium">
                                        {{ $blog_post->author ?? optional($blog_post->user)->name ?? __('Admin') }}
                                    </h3>
                                    @if(optional($blog_post->category)->title)
                                        <p class="text-quate text-base font-normal">
                                            <a href="{{ route(route_prefix().'frontend.blog.category', ['id' => optional($blog_post->category)->id, 'any' => Str::slug(optional($blog_post->category)->title) ?: 'any']) }}">
                                                {{ optional($blog_post->category)->title }}
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-4">
                                <!-- Share Button -->
                                <button onclick="if(navigator.share){navigator.share({title:'{{ addslashes($blog_post->title) }}',url:window.location.href})}"
                                    class="flex items-center gap-2 text-secondary hover:text-gray-300 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                                        </path>
                                    </svg>
                                    <span class="text-base text-secondary">{{ __('Share') }}</span>
                                </button>

                                <!-- Save Button -->
{{--                                <button class="flex items-center gap-2 text-secondary hover:text-gray-300 transition-colors">--}}
{{--                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
{{--                                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>--}}
{{--                                    </svg>--}}
{{--                                    <span class="text-base text-secondary">{{ __('Save') }}</span>--}}
{{--                                </button>--}}
                            </div>
                        </div>
                    </div>


                    <article>
                        <!-- Featured Image Section -->
                        <div class="mb-4">
                            <div class="blurred-img" {!! render_preloaded_image($blog_post->image) !!}>
                                {!! render_image_markup_by_attachment_id($blog_post->image, 'w-full h-64 sm:h-80 md:h-96 lg:h-125 object-cover rounded-2xl shadow-lg', 'full', false, is_lazy: true) !!}
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="blog-content-area">
                            <div class="prose max-w-none">
                                {!! $blog_post->blog_content !!}
                            </div>
                        </div>

                        <!-- Tags Section -->
                        @if($blog_post->tags)
                            <div class="bg-[#F8FAFB] border border-borderCS rounded-lg px-5 py-4 shadow-sm mt-10">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="text-secondary font-urbanist text-2xl font-semibold">{{ __('Tags:') }}</span>
                                    <div class="flex items-center gap-3 flex-wrap">
                                        @php
                                            $all_tags = explode(',', $blog_post->tags);
                                        @endphp
                                        @foreach($all_tags as $tag)
                                            @php
                                                $slug = Str::slug($tag);
                                            @endphp
                                            @if(!empty($slug))
                                                <a href="{{ route(route_prefix().'frontend.blog.tags.page', ['any' => $slug]) }}"
                                                   class="bg-white text-quate border border-borderCS px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-50 transition-colors">
                                                    {{ trim($tag) }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </article>


                    <!-- Comments List Section -->
                    @include('blog::landlord.frontend.new-blog.comment-show-data')

                    <!-- Comment Form Section -->
                    <div class="rounded-3xl p-6 sm:p-8 bg-[#F8FAFB] shadow-lg border border-borderCS mt-8">
                        <h2 class="text-2xl md:text-3xl font-bold text-primarys mb-6 flex items-center justify-between flex-wrap gap-4">
                            <span id="comment-form-title">{{ __('Leave a Comment') }}</span>
                            <button type="button" id="cancel_reply" class="text-sm text-red-500 hover:underline font-normal" style="display: none;">
                                {{ __('Cancel Reply') }}
                            </button>
                        </h2>
                        <div class="error-message"></div>

                        @php
                            $user = auth('web')->user() ?? auth('admin')->user();
                        @endphp

                        @if(!empty($user))
                            @include('blog::landlord.frontend.new-blog.comment-area')
                        @else
                            <div class="details-comment-content">
                                <div class="sign-in register signIn-signUp-wrapper">
                                    <h4 class="text-xl font-semibold text-secondary mb-4">{{ __('Sign In to Comment') }}</h4>
                                    <div class="form-wrapper custom-form mt-4">
                                        <x-error-msg/>
                                        <x-flash-msg/>
                                        <form action="" method="post" enctype="multipart/form-data" class="space-y-5" id="login_form_order_page">
                                            <div class="error-wrap"></div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                <div>
                                                    <label for="blogLoginUsername" class="sr-only">{{ __('Username') }}</label>
                                                    <input type="text" name="username" class="w-full px-4 py-3 rounded-xl bg-bg-counter border border-gray-200 text-text-primarys placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sectionC focus:border-transparent transition-all duration-200" id="blogLoginUsername" placeholder="{{ __('Type your username') }}">
                                                </div>
                                                <div>
                                                    <label for="blogLoginPassword" class="sr-only">{{ __('Password') }}</label>
                                                    <input type="password" name="password" class="w-full px-4 py-3 rounded-xl bg-bg-counter border border-gray-200 text-text-primarys placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sectionC focus:border-transparent transition-all duration-200" id="blogLoginPassword" placeholder="{{ __('Password') }}">
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between flex-wrap gap-3">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" name="remember" class="form-check-input check-input" id="rememberMeBlog">
                                                    <label class="text-sm text-quate" for="rememberMeBlog">{{ __('Remember me') }}</label>
                                                </div>
                                                <a href="{{ route(route_prefix().'user.forget.password') }}" class="text-sm text-sectionC hover:underline">{{ __('Forgot Password?') }}</a>
                                            </div>

                                            <button type="submit" id="login_btn"
                                                class="w-full bg-primary text-white font-semibold py-4 rounded-full transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-teal-800">
                                                {{ __('Sign In') }}
                                            </button>
                                        </form>
                                        <p class="text-center text-sm text-quate mt-3">
                                            {{ __("Don't have an account") }}
                                            <a href="{{ route(route_prefix().'user.register') }}" class="text-sectionC font-semibold hover:underline">{{ __('Sign up') }}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>


                <!-- Sidebar -->
                <div class="col-span-1">
                    <aside class="space-y-5 sticky top-32">

                        <!-- Recent/Popular Posts Section -->
                        @if(isset($recent_blogs) && $recent_blogs->count() > 0)
                            <div class="bg-[#F8FAFB] border border-borderCS p-8 px-6 rounded-xl">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Popular post') }}</h2>
                                <ul id="recentPostsList" class="space-y-4">
                                    @foreach($recent_blogs as $recent)
                                        <li class="post-item flex items-start gap-3 {{ !$loop->last ? 'pb-7 border-b border-gray-200' : '' }} cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                                            <a href="{{ route(route_prefix().'frontend.blog.single', $recent->slug) }}" class="flex items-start gap-3 w-full">
                                                <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0">
                                                    {!! render_image_markup_by_attachment_id($recent->image, 'w-20 h-20 rounded-xl object-cover shrink-0', 'thumb') !!}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm text-gray-600 mb-1">{{ $recent->created_at->format('F d, Y') }}</p>
                                                    <p class="text-gray-800 leading-snug">{{ Str::limit($recent->title, 50) }}</p>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Share Section -->
                        <div class="bg-[#F8FAFB] border border-borderCS p-8 px-6 rounded-xl">
                            <h3 class="text-base font-medium text-gray-900 mb-4">{{ __('Share This Articles:') }}</h3>
                            <div class="flex gap-3">
                                <!-- Instagram -->
                                <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer"
                                    class="social-btn w-10 h-10 bg-primary rounded flex items-center justify-center text-white transition-all hover:scale-110"
                                    aria-label="{{ __('Share on Instagram') }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </a>

                                <!-- Facebook -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($blog_url) }}" target="_blank" rel="noopener noreferrer"
                                    class="social-btn w-10 h-10 bg-primary rounded flex items-center justify-center text-white transition-all hover:scale-110"
                                    aria-label="{{ __('Share on Facebook') }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>

                                <!-- LinkedIn -->
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($blog_url) }}&title={{ urlencode($blog_post->title) }}" target="_blank" rel="noopener noreferrer"
                                    class="social-btn w-10 h-10 bg-primary rounded flex items-center justify-center text-white transition-all hover:scale-110"
                                    aria-label="{{ __('Share on LinkedIn') }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                </a>

                                <!-- X (Twitter) -->
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode($blog_url) }}&text={{ urlencode($blog_post->title) }}" target="_blank" rel="noopener noreferrer"
                                    class="social-btn w-10 h-10 bg-primary rounded flex items-center justify-center text-white transition-all hover:scale-110"
                                    aria-label="{{ __('Share on X') }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                    </aside>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        (function($){
            "use strict";

            $(document).ready(function(){
                //Blog Comment Insert
                $(document).on('click', '#submitComment', function (e) {
                    e.preventDefault();
                    var erContainer = $(".error-message");
                    var el = $(this);
                    var form = $('#blog-comment-form');
                    var user_id = $('#user_id').val();
                    var blog_id = $('#blog_id').val();
                    var commented_by = $('#commented_by').val();
                    var comment_content = $('#comment_content').val();
                    let comment_id = $('#blog-comment-form input[name=comment_id]').val();

                    el.text('{{ __('Submitting') }}...');

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            user_id: user_id,
                            blog_id: blog_id,
                            commented_by: commented_by,
                            comment_id: comment_id,
                            comment_content: comment_content,
                        },
                        success: function (data){
                            $('#comment_content').val('');

                            let alertClass = data.type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700';
                            erContainer.html('<div class="mt-3 mb-0 px-4 py-3 rounded-xl ' + alertClass + '">' + data.msg + '</div>');

                            // Build avatar HTML directly from the image URL returned by the server
                            var avatarHtml = '';
                            if (data.user_image_url) {
                                avatarHtml = '<img src="' + data.user_image_url + '" class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover flex-shrink-0" alt="">';
                            } else {
                                avatarHtml = '<div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 md:w-6 md:h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg></div>';
                            }

                            var today = new Date();
                            var dateStr = today.getDate().toString().padStart(2, '0') + ' ' +
                                today.toLocaleString('default', { month: 'long' }) + ' ' +
                                today.getFullYear();

                            var newCommentHtml = '<article class="comment-item bg-white p-4 border-b border-borderCS">' +
                                '<div class="flex items-start gap-4">' +
                                avatarHtml +
                                '<div class="flex-1 min-w-0">' +
                                '<div class="flex flex-wrap items-center gap-3 mb-2">' +
                                '<h3 class="text-xl font-medium text-secondary">' +
                                '<a href="javascript:void(0)">' + $('<div>').text(data.commented_by).html() + '</a>' +
                                '</h3>' +
                                '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-[#F1F6F7] text-quate">' + dateStr + '</span>' +
                                '</div>' +
                                '<p class="text-gray-700 font-normal text-base leading-6 mb-1">' + $('<div>').text(data.comment_content).html() + '</p>' +
                                '</div></div></article>';

                            if (comment_id && comment_id !== '') {
                                // It's a reply — indent it under the parent
                                var parentArticle = $('.btn-replay[data-comment_id="' + comment_id + '"]').closest('article');
                                var replyHtml = newCommentHtml.replace('comment-item bg-white p-4 border-b border-borderCS', 'comment-item bg-white p-4 border-b border-borderCS ml-8 md:ml-16');
                                parentArticle.after(replyHtml);
                            } else {
                                // Top-level comment — prepend to list
                                $('#comments-list').prepend(newCommentHtml);
                                // Update count
                                let countEl = $('#comments-count');
                                let currentCount = parseInt(countEl.text()) || 0;
                                countEl.text((currentCount + 1).toString().padStart(2, '0'));
                                // Update data-items
                                let commentData = $('#comment_data');
                                commentData.attr('data-items', parseInt(commentData.attr('data-items')) + 1);
                            }

                            $('#blog-comment-form input[name=comment_id]').val('');
                            $('#comment-form-title').text('{{ __('Leave a Comment') }}');
                            $('#cancel_reply').hide();
                            $('#comment_content').attr('placeholder','{{ __('Post Comments') }}');
                            el.text('{{ __('Comment') }}');
                        },
                        error: function (data) {
                            var errors = data.responseJSON;
                            erContainer.html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mt-3"><ul class="list-disc list-inside"></ul></div>');

                            if (errors && errors.errors) {
                                $.each(errors.errors, function (index, value) {
                                    erContainer.find('ul').append('<li>' + value + '</li>');
                                });
                            } else {
                                erContainer.find('ul').append('<li>' + (data.statusText || '{{ __('Something went wrong. Please try again.') }}') + '</li>');
                            }
                            el.text('{{ __('Comment') }}');
                        },
                    });
                });

                //Blog Reply
                $(document).on('click', '.btn-replay', function (e) {
                    e.preventDefault();
                    let el = $(this);
                    let comment_id = el.data('comment_id');
                    let parent_name = el.closest('.flex-1').find('h3 a').data('parent_name') || el.closest('.flex-1').find('h3').text().trim();

                    $('#blog-comment-form input[name=comment_id]').val(comment_id);
                    $('#comment-form-title').text('{{ __('Replying to') }} ' + parent_name);
                    $('#cancel_reply').show();
                    $('#comment_content').attr('placeholder','{{ __('Replying to') }} '+ parent_name + '..');

                    $('html, body').animate({
                        scrollTop: $("#comment_content").offset().top - 200
                    }, 100, 'linear');

                    $('#comment_content').focus();
                });

                $(document).on('click', '#cancel_reply', function (e) {
                    $('#blog-comment-form input[name=comment_id]').val('');
                    $('#comment-form-title').text('{{ __('Leave a Comment') }}');
                    $(this).hide();
                    $('#comment_content').attr('placeholder','{{ __('Post Comments') }}');
                });

                function load_comment_data(id, type) {
                    var commentData = $('#comment_data');
                    var items = commentData.attr('data-items');

                    $.ajax({
                        url: "{{ route(route_prefix().'frontend.load.blog.comment.data') }}",
                        method: "POST",
                        data: {id: id, _token: "{{ csrf_token() }}", items: items, type: type},
                        success: function (data) {
                            if (type === 'load-one') {
                                commentData.find('#comments-list').prepend(data.markup);
                                commentData.attr('data-items', parseInt(items) + 1);
                                // Increment visually
                                let countEl = $('#comments-count');
                                let currentCount = parseInt(countEl.text());
                                countEl.text((currentCount + 1).toString().padStart(2, '0'));
                            } else {
                                commentData.find('#comments-list').append(data.markup);
                                commentData.attr('data-items', parseInt(items) + 5);
                            }

                            $('#load_more_comment_button').text('{{ __('Load More') }}');

                            if (data.blogComments.length === 0 && type !== 'load-one') {
                                $('#load_more_comment_button').text('{{ __('No More Comment Found') }}');
                            }
                        }
                    })
                }

                $(document).on('click', '#load_more_comment_button', function () {
                    $(this).text('{{ __('Loading...') }}');
                    load_comment_data('{{ $blog_post->id }}', 'load-more');
                });

                // Robust AJAX Login for Blog Page
                $(document).on('submit', '#login_form_order_page', function (e) {
                    e.preventDefault();
                    let form = $(this);
                    let el = form.find('#login_btn');
                    let errorWrap = form.find('.error-wrap');
                    let username = form.find('input[name="username"]').val();
                    let password = form.find('input[name="password"]').val();
                    let remember = form.find('input[name="remember"]').is(':checked');

                    el.text('{{ __('Please Wait...') }}').prop('disabled', true);
                    errorWrap.html('');

                    $.ajax({
                        url: "{{ route(route_prefix().'user.ajax.login') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            username: username,
                            password: password,
                            remember: remember
                        },
                        success: function (data) {
                            el.prop('disabled', false);
                            if (data.status === 'invalid') {
                                el.text('{{ __('Sign In') }}');
                                errorWrap.html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">' + (data.msg || '{{ __('Invalid credentials') }}') + '</div>');
                            } else {
                                errorWrap.html('<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4">' + (data.msg || '{{ __('Login Successful') }}') + '</div>');
                                el.text('{{ __('Success! Redirecting...') }}');
                                setTimeout(function() {
                                    location.reload();
                                }, 800);
                            }
                        },
                        error: function (data) {
                            el.text('{{ __('Sign In') }}').prop('disabled', false);
                            let errorHtml = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4"><ul class="list-disc list-inside">';

                            if (data.responseJSON && data.responseJSON.errors) {
                                $.each(data.responseJSON.errors, function (key, value) {
                                    errorHtml += '<li>' + value + '</li>';
                                });
                            } else if (data.responseJSON && data.responseJSON.msg) {
                                errorHtml += '<li>' + data.responseJSON.msg + '</li>';
                            } else {
                                errorHtml += '<li>{{ __('Something went wrong. Please try again.') }}</li>';
                            }

                            errorHtml += '</ul></div>';
                            errorWrap.html(errorHtml);
                        }
                    });
                });
            });
        })(jQuery);
    </script>
@endsection
