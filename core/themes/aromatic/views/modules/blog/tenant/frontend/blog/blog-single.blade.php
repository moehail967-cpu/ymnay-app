@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="ar-page-banner">
    <div class="container">
        <h1>{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span>/</span>
            <span class="current">{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="container ar-blog-single-section">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <article class="ar-article">

                {{-- Featured Image --}}
                @if($post->has_image())
                <div class="ar-article-img-wrap">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}">
                </div>
                @endif

                <div class="ar-article-body">
                    {{-- Category --}}
                    @if($post->category())
                        <span class="ar-blog-cat ar-blog-cat-inline">
                            <a href="{{ $post->category()->url() }}">
                                {{ $post->category()->name() }}
                            </a>
                        </span>
                    @endif

                    {{-- Title --}}
                    <h1 class="ar-article-title">{{ $post->title() }}</h1>

                    {{-- Meta --}}
                    <div class="ar-blog-meta ar-article-meta">
                        @if($post->author())
                            <span><i class="mdi mdi-account"></i> {{ $post->author() }}</span>
                        @endif
                        <span><i class="mdi mdi-calendar"></i> {{ $post->date('F d, Y') }}</span>
                        <span>
                            <a href="{{ $post->comment_url() }}">
                                <i class="mdi mdi-comment-outline"></i> {{ theme_comments_count() }} {{ __('Comments') }}
                            </a>
                        </span>
                    </div>

                    {{-- Content --}}
                    <div class="ar-article-content">
                        {!! $post->content() !!}
                    </div>

                    {{-- Tags & Share --}}
                    <div class="ar-article-footer">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}" class="ar-tag-pill">
                                    <i class="mdi mdi-tag-outline"></i> {{ $tag->name() }}
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
            <div class="ar-sb-card mt-4" id="comment-area">
                <div class="ar-sb-title">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) ({{ theme_comments_count() }}) @endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div class="ar-comment-item">
                            <div class="ar-comment-avatar">
                                {!! $comment->author_avatar() !!}
                            </div>
                            <div class="ar-comment-body">
                                <div class="ar-comment-author">
                                    <a href="javascript:void(0)" class="title"
                                       data-parent_name="{{ $comment->author() }}">{{ $comment->author() }}</a>
                                </div>
                                <div class="ar-comment-date">{{ $comment->date() }}</div>
                                <p class="ar-comment-text">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="ar-comment-reply btn-replay"
                                            data-comment_id="{{ $comment->id() }}">
                                        <i class="mdi mdi-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif

                                @foreach($comment->replies() as $reply)
                                    <div class="ar-reply-item">
                                        <div class="ar-reply-avatar">
                                            {!! $reply->author_avatar() !!}
                                        </div>
                                        <div class="ar-comment-body">
                                            <div class="ar-reply-author">{{ $reply->author() }}</div>
                                            <div class="ar-reply-date">{{ $reply->date() }}</div>
                                            <p class="ar-reply-text">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(theme_comments()->isNotEmpty())
                    <div class="text-center mt-3">
                        <button class="ar-btn ar-btn-outline" id="load_more_comment_button">
                            {{ __('Load More') }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div class="ar-sb-card mt-4" id="blog-comment-form">
                <div class="ar-sb-title">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                <div class="mb-3">
                    <label class="ar-auth-label">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" class="ar-auth-input"
                           value="{{ theme_current_user()->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="ar-auth-label">{{ __('Comment') }} <span class="ar-required">*</span></label>
                    <textarea id="comment_content" class="ar-textarea" rows="4"
                              placeholder="{{ __('Write your comment…') }}"></textarea>
                </div>
                <button type="button" id="comment_submit_btn" class="ar-btn ar-btn-red">
                    <i class="mdi mdi-send"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div class="ar-sb-card mt-4 text-center p-4">
                <p class="mb-3 text-muted">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}" class="ar-btn ar-btn-red">
                    <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="ar-sb-card">
                <div class="ar-sb-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="ar-sidebar-search">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit"><i class="mdi mdi-magnify"></i></button>
                </form>
            </div>

            @if(theme_blogs()->isNotEmpty())
            <div class="ar-sb-card">
                <div class="ar-sb-title">{{ __('Recent Posts') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div class="ar-recent-post">
                    @if($recent->has_image())
                    <div class="ar-recent-thumb">
                        <a href="{{ $recent->url() }}">
                            <img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}">
                        </a>
                    </div>
                    @endif
                    <div class="ar-recent-post-body">
                        <a class="ar-recent-title" href="{{ $recent->url() }}">{!! $recent->title() !!}</a>
                        <div class="ar-recent-date"><i class="mdi mdi-calendar"></i> {{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div class="ar-sb-card">
                <div class="ar-sb-title">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                <div class="ar-sb-cat-row">
                    <a href="{{ $cat->url() }}" class="ar-sb-cat-link {{ $post->category()?->id() == $cat->id() ? 'fw-bold' : '' }}">
                        {{ $cat->name() }}
                    </a>
                    <span class="ar-filter-count">{{ $cat->count() }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="ar-sb-card">
                <div class="ar-sb-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="ar-tag-pill">
                            <i class="mdi mdi-tag-outline"></i> {{ $tag->name() }}
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
                    el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}');
                    location.reload();
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON?.errors ?? {};
                    var html = '<div class="alert alert-danger">';
                    $.each(errors, function (k, v) { html += '<p>' + v + '</p>'; });
                    html += '</div>';
                    $('.error-wrap').html(html);
                    el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}');
                }
            });
        });

        $(document).on('click', '.btn-replay', function () {
            var comment_id  = $(this).data('comment_id');
            var parent_name = $(this).closest('[style*="display:flex"]').find('.title').data('parent_name');
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
