@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="tn-blog-single-banner">
    <div class="container">
        <h1>{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ \Illuminate\Support\Str::words($post->title(), 5) }}</span>
        </div>
    </div>
</div>

<div class="container tn-page-wrap">
    <div class="row g-4">

        <div class="col-lg-8">
            <article class="tn-article">
                @if($post->has_image())
                <div class="tn-article-hero"><img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}"></div>
                @endif
                <div class="tn-article-body">
                    @if($post->category())
                        <a href="{{ $post->category()->url() }}" class="tn-article-cat">{{ $post->category()->name() }}</a>
                    @endif
                    <h2 class="tn-article-title">{{ $post->title() }}</h2>
                    <div class="tn-article-meta">
                        @if($post->author()) <span><i class="mdi mdi-account"></i> {{ $post->author() }}</span> @endif
                        <span><i class="mdi mdi-calendar"></i> {{ $post->date('F d, Y') }}</span>
                        <span><a href="{{ $post->comment_url() }}"><i class="mdi mdi-comment-outline"></i> {{ theme_comments_count() }} {{ __('Comments') }}</a></span>
                    </div>
                    <div class="tn-article-content">{!! $post->content() !!}</div>
                    <div class="tn-article-footer">
                        <div class="tn-article-tags">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}" class="tn-post-tag">#{{ $tag->name() }}</a>
                            @endforeach
                        </div>
                        <ul class="tn-share-links">{!! $post->share_links() !!}</ul>
                    </div>
                </div>
            </article>

            <div class="tn-comment-area" id="comment-area">
                <div class="tn-comment-heading">
                    {{ __('Comments') }}
                    @if(theme_comments_count())
                        <span class="tn-comment-count">({{ theme_comments_count() }})</span>
                    @endif
                </div>
                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div class="tn-comment-item">
                            <div class="tn-comment-avatar">{!! $comment->author_avatar() !!}</div>
                            <div class="tn-comment-body">
                                <a href="javascript:void(0)" class="tn-comment-author title"
                                   data-parent_name="{{ $comment->author() }}">{{ $comment->author() }}</a>
                                <div class="tn-comment-date">{{ $comment->date() }}</div>
                                <p class="tn-comment-text">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="tn-reply-btn btn-replay" data-comment_id="{{ $comment->id() }}">
                                        <i class="mdi mdi-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif
                                @foreach($comment->replies() as $reply)
                                    <div class="tn-comment-reply">
                                        <div class="tn-comment-reply-avatar">{!! $reply->author_avatar() !!}</div>
                                        <div class="tn-comment-reply-body">
                                            <div class="tn-comment-reply-author">{{ $reply->author() }}</div>
                                            <div class="tn-comment-reply-date">{{ $reply->date() }}</div>
                                            <p class="tn-comment-reply-text">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(theme_comments()->isNotEmpty())
                    <div class="tn-load-more-wrap">
                        <button id="load_more_comment_button" class="tn-load-more-btn">{{ __('Load More') }}</button>
                    </div>
                @endif
            </div>

            @if(theme_is_logged_in())
            <div class="tn-comment-form" id="blog-comment-form">
                <div class="tn-form-heading">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                <div class="mb-3">
                    <label class="tn-form-label">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" class="tn-input" value="{{ theme_current_user()->name }}" readonly style="opacity:.7;">
                </div>
                <div class="mb-3">
                    <label class="tn-form-label">{{ __('Comment') }} *</label>
                    <textarea id="comment_content" class="tn-input tn-input-textarea" rows="4"
                              placeholder="{{ __('Share your thoughts…') }}"></textarea>
                </div>
                <button type="button" id="comment_submit_btn" class="tn-submit-btn">
                    <i class="mdi mdi-send"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div class="tn-auth-prompt">
                <p>{{ __('Sign in to leave a comment') }}</p>
                <a href="{{ theme_login_url() }}" class="tn-btn tn-btn-primary">{{ __('Sign In') }}</a>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="tn-sidebar-search">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="tn-sidebar-search-input" placeholder="{{ __('Search posts…') }}">
                    <button type="submit" class="tn-sidebar-search-btn"><i class="mdi mdi-magnify"></i></button>
                </form>
            </div>

            @if(theme_blogs()->isNotEmpty())
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Recent Posts') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div class="tn-recent-item">
                    @if($recent->has_image())
                    <div class="tn-recent-img">
                        <a href="{{ $recent->url() }}">
                            <img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}">
                        </a>
                    </div>
                    @endif
                    <div class="tn-recent-info">
                        <a href="{{ $recent->url() }}" class="tn-recent-title">{!! $recent->title() !!}</a>
                        <div class="tn-recent-date"><i class="mdi mdi-calendar"></i> {{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                <div class="tn-cat-item">
                    <a href="{{ $cat->url() }}" class="tn-cat-link">
                        <i class="mdi mdi-baby-carriage"></i> {{ $cat->name() }}
                    </a>
                    <span class="tn-cat-count">{{ $cat->count() }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="tn-tag">#{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    $(document).ready(function () {
        $(document).on('click', '#comment_submit_btn', function () {
            var el = $(this);
            el.html('<i class="mdi mdi-loading mdi-spin"></i> {{ __('Submitting…') }}');
            $.ajax({
                url: '{{ theme_blog_comment_store_url() }}',
                method: 'POST',
                data: {
                    _token: '{{ theme_csrf() }}',
                    blog_id: $('[name=blog_id]').val(),
                    user_id: $('[name=user_id]').val(),
                    comment_id: $('[name=comment_id]').val(),
                    commented_by: $('#commented_by').val(),
                    comment_content: $('#comment_content').val()
                },
                success: function (data) {
                    $('#comment_content').val('');
                    $('[name=comment_id]').val('');
                    $('.error-wrap').html('<div class="alert alert-success">' + data.msg + '</div>');
                    el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}');
                    location.reload();
                },
                error: function (xhr) {
                    var msgs = [];
                    $.each(xhr.responseJSON?.errors ?? {}, function (k, v) { msgs.push(v); });
                    $('.error-wrap').html('<div class="alert alert-danger">' + msgs.join('<br>') + '</div>');
                    el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}');
                }
            });
        });

        $(document).on('click', '.btn-replay', function () {
            $('[name=comment_id]').val($(this).data('comment_id'));
            var name = $(this).closest('.tn-comment-item').find('.title').data('parent_name');
            $('#comment_content').attr('placeholder', '{{ __('Replying to') }} ' + name + '…');
            $('html').animate({ scrollTop: $('#comment_content').offset().top - 200 }, 200);
        });

        $(document).on('click', '#load_more_comment_button', function () {
            var el = $(this);
            el.text('{{ __('Loading…') }}');
            var commentData = $('#comment_data');
            var items = commentData.attr('data-items');
            $.ajax({
                url: '{{ theme_blog_load_comments_url() }}',
                method: 'POST',
                data: { id: '{{ $post->id() }}', _token: '{{ theme_csrf() }}', items: items },
                success: function (data) {
                    commentData.attr('data-items', parseInt(items) + 5);
                    commentData.append(data.markup);
                    el.text(data.blogComments.length === 0 ? '{{ __('No More Comments') }}' : '{{ __('Load More') }}');
                }
            });
        });
    });
})(jQuery);
</script>
<x-custom-js.ajax-login/>
@endsection
