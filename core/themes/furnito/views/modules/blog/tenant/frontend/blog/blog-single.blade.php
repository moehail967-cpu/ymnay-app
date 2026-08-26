@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="fn-page-banner">
    <div class="container">
        <h1>{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h1>
        <div class="fn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="container fn-blog-section">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <article class="fn-article">

                @if($post->has_image())
                <div class="fn-article-img">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}">
                </div>
                @endif

                <div class="fn-article-body">
                    @if($post->category())
                        <a href="{{ $post->category()->url() }}" class="fn-article-cat">
                            {{ $post->category()->name() }}
                        </a>
                    @endif

                    <h1 class="fn-article-title">{{ $post->title() }}</h1>

                    <div class="fn-blog-meta fn-article-meta">
                        @if($post->author())
                            <span><i class="las la-user"></i> {{ $post->author() }}</span>
                        @endif
                        <span><i class="las la-calendar"></i> {{ $post->date('F d, Y') }}</span>
                        <span>
                            <a href="{{ $post->comment_url() }}">
                                <i class="las la-comment"></i> {{ theme_comments_count() }} {{ __('Comments') }}
                            </a>
                        </span>
                    </div>

                    <div class="fn-article-content">
                        {!! $post->content() !!}
                    </div>

                    <div class="fn-article-footer">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}" class="fn-tag-pill">
                                    <i class="las la-tag"></i> {{ $tag->name() }}
                                </a>
                            @endforeach
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            {!! $post->share_links() !!}
                        </div>
                    </div>
                </div>
            </article>

            {{-- Comments --}}
            <div class="fn-sb-card mt-4" id="comment-area">
                <div class="fn-sb-title">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) ({{ theme_comments_count() }}) @endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div class="fn-comment">
                            <div class="fn-comment-avatar">
                                {!! $comment->author_avatar() !!}
                            </div>
                            <div class="fn-comment-body">
                                <div class="fn-comment-author">
                                    <a href="javascript:void(0)" class="title"
                                       data-parent_name="{{ $comment->author() }}">{{ $comment->author() }}</a>
                                </div>
                                <div class="fn-comment-date">{{ $comment->date() }}</div>
                                <p class="fn-comment-text">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="fn-comment-reply btn-replay"
                                            data-comment_id="{{ $comment->id() }}">
                                        <i class="las la-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif

                                @foreach($comment->replies() as $reply)
                                    <div class="fn-comment fn-comment-nested">
                                        <div class="fn-comment-avatar fn-comment-avatar-sm">
                                            {!! $reply->author_avatar() !!}
                                        </div>
                                        <div class="fn-comment-body">
                                            <div class="fn-comment-author">{{ $reply->author() }}</div>
                                            <div class="fn-comment-date">{{ $reply->date() }}</div>
                                            <p class="fn-comment-text">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(theme_comments()->isNotEmpty())
                    <div class="text-center mt-3">
                        <button class="fn-btn fn-btn-outline" id="load_more_comment_button">
                            {{ __('Load More') }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div class="fn-sb-card mt-4" id="blog-comment-form">
                <div class="fn-sb-title">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                <div class="mb-3">
                    <label class="fn-label">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" class="fn-input"
                           value="{{ theme_current_user()->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="fn-label">{{ __('Comment') }} <span class="fn-required">*</span></label>
                    <textarea id="comment_content" class="fn-input fn-textarea" rows="4"
                              placeholder="{{ __('Write your comment…') }}"></textarea>
                </div>
                <button type="button" id="comment_submit_btn" class="fn-btn fn-btn-gold">
                    <i class="las la-paper-plane"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div class="fn-sb-card mt-4 text-center">
                <p class="fn-muted mb-3">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}" class="fn-btn fn-btn-gold">
                    <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="fn-sb-card">
                <div class="fn-sb-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="fn-input flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="fn-btn fn-btn-gold fn-sb-search-btn">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @if(theme_blogs()->isNotEmpty())
            <div class="fn-sb-card">
                <div class="fn-sb-title">{{ __('Recent Posts') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div class="fn-recent-post">
                    @if($recent->has_image())
                    <div class="fn-recent-thumb">
                        <a href="{{ $recent->url() }}">
                            <img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}">
                        </a>
                    </div>
                    @endif
                    <div class="fn-recent-info">
                        <a class="fn-recent-title" href="{{ $recent->url() }}">{!! $recent->title() !!}</a>
                        <div class="fn-recent-date"><i class="las la-calendar"></i> {{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div class="fn-sb-card">
                <div class="fn-sb-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <div class="fn-sb-cat-row">
                        <a href="{{ $cat->url() }}"
                           class="fn-sb-cat-link {{ $post->category()?->id() == $cat->id() ? 'active' : '' }}">
                            {{ $cat->name() }}
                        </a>
                        <span class="fn-filter-count">{{ $cat->count() }}</span>
                    </div>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="fn-sb-card">
                <div class="fn-sb-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="fn-tag-pill">
                            <i class="las la-tag"></i> {{ $tag->name() }}
                        </a>
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
            el.text('{{ __('Submitting') }}...');
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
                    el.html('<i class="las la-paper-plane"></i> {{ __('Post Comment') }}');
                    location.reload();
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON?.errors ?? {};
                    var html = '<div class="alert alert-danger">';
                    $.each(errors, function (k, v) { html += '<p>' + v + '</p>'; });
                    html += '</div>';
                    $('.error-wrap').html(html);
                    el.html('<i class="las la-paper-plane"></i> {{ __('Post Comment') }}');
                }
            });
        });

        $(document).on('click', '.btn-replay', function () {
            var comment_id  = $(this).data('comment_id');
            var parent_name = $(this).closest('.fn-comment').find('.title').data('parent_name');
            $('[name=comment_id]').val(comment_id);
            $('#comment_content').attr('placeholder', '{{ __('Replying to') }} ' + parent_name + '..');
            $('html').animate({ scrollTop: $('#comment_content').offset().top - 200 }, 200);
        });

        function loadMoreComments() {
            var commentData = $('#comment_data');
            var items = commentData.attr('data-items');
            $.ajax({
                url: '{{ theme_blog_load_comments_url() }}',
                method: 'POST',
                data: { id: '{{ $post->id() }}', _token: '{{ theme_csrf() }}', items: items },
                success: function (data) {
                    commentData.attr('data-items', parseInt(items) + 5);
                    commentData.append(data.markup);
                    var btn = $('#load_more_comment_button');
                    btn.text(data.blogComments.length === 0
                        ? '{{ __('No More Comment Found') }}'
                        : '{{ __('Load More') }}');
                }
            });
        }

        $(document).on('click', '#load_more_comment_button', function () {
            $(this).text('{{ __('Loading...') }}');
            loadMoreComments();
        });

    });
})(jQuery);
</script>
<x-custom-js.ajax-login/>
@endsection
