@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="bp-page-banner">
    <div class="container">
        <h1>{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="container bp-blog-section">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <article class="bp-article">

                {{-- Featured Image --}}
                @if($post->has_image())
                <div class="bp-article-img">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}">
                </div>
                @endif

                <div class="bp-article-body">
                    {{-- Category --}}
                    @if($post->category())
                        <a href="{{ $post->category()->url() }}" class="bp-blog-cat" style="display:inline-block;margin-bottom:14px;">
                            {{ $post->category()->name() }}
                        </a>
                    @endif

                    {{-- Title --}}
                    <h1 class="bp-article-title">{{ $post->title() }}</h1>

                    {{-- Meta --}}
                    <div class="bp-blog-meta bp-article-meta">
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

                    {{-- Content --}}
                    <div class="bp-article-content">
                        {!! $post->content() !!}
                    </div>

                    {{-- Tags & Share --}}
                    <div class="bp-article-footer">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}" class="bp-tag-pill">
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
            <div class="bp-sb-card mt-4" id="comment-area">
                <div class="bp-sb-title">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) ({{ theme_comments_count() }}) @endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div class="bp-comment">
                            <div class="bp-comment-avatar">
                                {!! $comment->author_avatar() !!}
                            </div>
                            <div class="bp-comment-body">
                                <div class="bp-comment-author">
                                    <a href="javascript:void(0)" class="title"
                                       data-parent_name="{{ $comment->author() }}">{{ $comment->author() }}</a>
                                </div>
                                <div class="bp-comment-date">{{ $comment->date() }}</div>
                                <p class="bp-comment-text">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="bp-comment-reply btn-replay"
                                            data-comment_id="{{ $comment->id() }}">
                                        <i class="las la-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif

                                @foreach($comment->replies() as $reply)
                                    <div class="bp-comment bp-comment-nested">
                                        <div class="bp-comment-avatar bp-comment-avatar-sm">
                                            {!! $reply->author_avatar() !!}
                                        </div>
                                        <div class="bp-comment-body">
                                            <div class="bp-comment-author">{{ $reply->author() }}</div>
                                            <div class="bp-comment-date">{{ $reply->date() }}</div>
                                            <p class="bp-comment-text">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(theme_comments()->isNotEmpty())
                    <div class="text-center mt-3">
                        <button class="bp-btn bp-btn-outline" id="load_more_comment_button">
                            {{ __('Load More') }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div class="bp-sb-card mt-4" id="blog-comment-form">
                <div class="bp-sb-title">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                <div class="mb-3">
                    <label class="bp-label">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" class="bp-input"
                           value="{{ theme_current_user()->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="bp-label">{{ __('Comment') }} <span class="bp-required">*</span></label>
                    <textarea id="comment_content" class="bp-input bp-textarea" rows="4"
                              placeholder="{{ __('Write your comment…') }}"></textarea>
                </div>
                <button type="button" id="comment_submit_btn" class="bp-btn bp-btn-green">
                    <i class="las la-paper-plane"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div class="bp-sb-card mt-4 text-center">
                <p class="bp-muted mb-3">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}" class="bp-btn bp-btn-green">
                    <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="bp-sb-card">
                <div class="bp-sb-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="bp-input flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="bp-btn bp-btn-green" style="padding:10px 14px;white-space:nowrap;">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @if(theme_blogs()->isNotEmpty())
            <div class="bp-sb-card">
                <div class="bp-sb-title">{{ __('Recent Posts') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div class="bp-recent-post">
                    @if($recent->has_image())
                    <div class="bp-recent-thumb">
                        <a href="{{ $recent->url() }}">
                            <img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}">
                        </a>
                    </div>
                    @endif
                    <div class="bp-recent-info">
                        <a class="bp-recent-title" href="{{ $recent->url() }}">{!! $recent->title() !!}</a>
                        <div class="bp-recent-date"><i class="las la-calendar"></i> {{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div class="bp-sb-card">
                <div class="bp-sb-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <div class="bp-sb-cat-row">
                        <a href="{{ $cat->url() }}"
                           class="bp-sb-cat-link {{ $post->category()?->id() == $cat->id() ? 'active' : '' }}">
                            {{ $cat->name() }}
                        </a>
                        <span class="bp-filter-count">{{ $cat->count() }}</span>
                    </div>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="bp-sb-card">
                <div class="bp-sb-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="bp-tag-pill">
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
            var parent_name = $(this).closest('.bp-comment').find('.title').data('parent_name');
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
