@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <a href="{{ theme_blog_url() }}" class="cs-breadcrumb-link">{{ __('Blog') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="cs-blog-section">
    <div class="container">
        <div class="row g-4">

            {{-- Article --}}
            <div class="col-lg-8">
                <article class="cs-blog-article">

                    {{-- Featured Image --}}
                    @if($post->has_image())
                    <div class="cs-blog-article-img">
                        <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}">
                    </div>
                    @endif

                    <div class="cs-blog-article-body">
                        {{-- Category --}}
                        @if($post->category())
                        <a href="{{ $post->category()->url() }}" class="cs-blog-cat cs-blog-cat-block">
                            {{ $post->category()->name() }}
                        </a>
                        @endif

                        {{-- Title --}}
                        <h1 class="cs-blog-article-title">{{ $post->title() }}</h1>

                        {{-- Meta --}}
                        <div class="cs-blog-article-meta">
                            @if($post->author())
                                <span><i class="las la-user"></i> {{ $post->author() }}</span>
                            @endif
                            <span><i class="las la-calendar-alt"></i> {{ $post->date('F d, Y') }}</span>
                            <span>
                                <a href="{{ $post->comment_url() }}">
                                    <i class="las la-comment-alt"></i> {{ theme_comments_count() }} {{ __('Comments') }}
                                </a>
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="cs-blog-article-content">
                            {!! $post->content() !!}
                        </div>

                        {{-- Tags & Share --}}
                        <div class="cs-blog-article-footer">
                            <div class="cs-blog-tags">
                                @foreach($post->tags() as $tag)
                                    <a href="{{ $tag->url() }}" class="cs-sb-tag">
                                        <i class="las la-tag"></i> {{ $tag->name() }}
                                    </a>
                                @endforeach
                            </div>
                            <div class="cs-blog-share">
                                {!! $post->share_links() !!}
                            </div>
                        </div>
                    </div>
                </article>

                {{-- Comments --}}
                <div class="cs-sb-card mt-4" id="comment-area">
                    <div class="cs-sb-title">
                        {{ __('Comments') }}
                        @if(theme_comments_count()) ({{ theme_comments_count() }}) @endif
                    </div>

                    <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                        @foreach(theme_comments() as $comment)
                        <div class="cs-comment">
                            <div class="cs-comment-avatar">
                                {!! $comment->author_avatar() !!}
                            </div>
                            <div class="cs-comment-body">
                                <div class="cs-comment-author">
                                    <a href="javascript:void(0)" class="title"
                                       data-parent_name="{{ $comment->author() }}">{{ $comment->author() }}</a>
                                </div>
                                <div class="cs-comment-date">{{ $comment->date() }}</div>
                                <p class="cs-comment-text">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                <button class="cs-comment-reply btn-replay" data-comment_id="{{ $comment->id() }}">
                                    <i class="las la-reply"></i> {{ __('Reply') }}
                                </button>
                                @endif

                                @foreach($comment->replies() as $reply)
                                <div class="cs-comment cs-comment-reply-item">
                                    <div class="cs-comment-avatar cs-comment-avatar-sm">
                                        {!! $reply->author_avatar() !!}
                                    </div>
                                    <div class="cs-comment-body">
                                        <div class="cs-comment-author">{{ $reply->author() }}</div>
                                        <div class="cs-comment-date">{{ $reply->date() }}</div>
                                        <p class="cs-comment-text">{!! $reply->body() !!}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if(theme_comments()->isNotEmpty())
                    <div class="text-center mt-3">
                        <button class="cs-blog-load-more" id="load_more_comment_button">
                            {{ __('Load More') }}
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Comment Form --}}
                @if(theme_is_logged_in())
                <div class="cs-sb-card mt-4" id="blog-comment-form">
                    <div class="cs-sb-title">{{ __('Leave a Comment') }}</div>
                    <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                    <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                    <input type="hidden" name="comment_id" value="">
                    <div class="error-wrap mb-3"></div>
                    <div class="mb-3">
                        <label class="cs-dash-label">{{ __('Name') }}</label>
                        <input type="text" id="commented_by" class="cs-dash-input"
                               value="{{ theme_current_user()->name }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="cs-dash-label">{{ __('Comment') }} <span class="cs-required">*</span></label>
                        <textarea id="comment_content" class="cs-dash-input cs-dash-textarea" rows="4"
                                  placeholder="{{ __('Write your comment…') }}"></textarea>
                    </div>
                    <button type="button" id="comment_submit_btn" class="cs-dash-submit-btn">
                        <i class="las la-paper-plane"></i> {{ __('Post Comment') }}
                    </button>
                </div>
                @else
                <div class="cs-sb-card mt-4 text-center">
                    <p class="cs-blog-login-hint">{{ __('Sign in to leave a comment') }}</p>
                    <a href="{{ theme_login_url() }}" class="cs-dash-submit-btn cs-inline-flex">
                        <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
                    </a>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                @include('theme::modules.blog.tenant.frontend.blog.partials.sidebar')
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';
    $(document).ready(function () {

        $(document).on('click', '#comment_submit_btn', function () {
            var el = $(this);
            el.html('<i class="las la-spinner la-spin"></i> {{ __("Submitting…") }}');
            $.ajax({
                url: '{{ theme_blog_comment_store_url() }}',
                method: 'POST',
                data: {
                    _token:          '{{ theme_csrf() }}',
                    blog_id:         $('[name=blog_id]').val(),
                    user_id:         $('[name=user_id]').val(),
                    comment_id:      $('[name=comment_id]').val(),
                    commented_by:    $('#commented_by').val(),
                    comment_content: $('#comment_content').val(),
                },
                success: function (data) {
                    $('#comment_content').val('');
                    $('[name=comment_id]').val('');
                    $('.error-wrap').html('<div class="alert alert-success">' + data.msg + '</div>');
                    el.html('<i class="las la-paper-plane"></i> {{ __("Post Comment") }}');
                    location.reload();
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON?.errors ?? {};
                    var html = '<div class="alert alert-danger">';
                    $.each(errors, function (k, v) { html += '<p>' + v + '</p>'; });
                    html += '</div>';
                    $('.error-wrap').html(html);
                    el.html('<i class="las la-paper-plane"></i> {{ __("Post Comment") }}');
                }
            });
        });

        $(document).on('click', '.btn-replay', function () {
            var comment_id  = $(this).data('comment_id');
            var parent_name = $(this).closest('.cs-comment').find('.title').data('parent_name');
            $('[name=comment_id]').val(comment_id);
            $('#comment_content').attr('placeholder', '{{ __("Replying to") }} ' + parent_name + '…');
            $('html').animate({ scrollTop: $('#comment_content').offset().top - 200 }, 200);
        });

        $(document).on('click', '#load_more_comment_button', function () {
            var btn = $(this);
            btn.text('{{ __("Loading…") }}');
            var commentData = $('#comment_data');
            var items = commentData.attr('data-items');
            $.ajax({
                url: '{{ theme_blog_load_comments_url() }}',
                method: 'POST',
                data: { id: '{{ $post->id() }}', _token: '{{ theme_csrf() }}', items: items },
                success: function (data) {
                    commentData.attr('data-items', parseInt(items) + 5);
                    commentData.append(data.markup);
                    btn.text(data.blogComments.length === 0 ? '{{ __("No More Comments") }}' : '{{ __("Load More") }}');
                }
            });
        });

    });
})(jQuery);
</script>
@endsection
