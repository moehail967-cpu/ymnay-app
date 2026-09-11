@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="ph-page-banner">
    <div class="container">
        <h1>{{ __('Blog Details') }}</h1>
        <div class="ph-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_product_url(theme_static_option('blog_page')) }}">{{ __('Blog') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <div class="ph-article">
                <div class="ph-article-hero">
                    @if($post->has_image())
                        <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}"
                             style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <span style="font-size:72px;">🐾</span>
                    @endif
                </div>

                <div class="ph-article-body">
                    @if($post->category())
                        <span class="ph-article-cat">
                            <a href="{{ $post->category()->url() }}" style="color:inherit;text-decoration:none;">
                                {{ $post->category()->name() }}
                            </a>
                        </span>
                    @endif

                    <h1 class="ph-article-title">{{ $post->title() }}</h1>

                    <div class="ph-article-meta">
                        @if($post->author())
                            <span><i class="las la-user"></i> {{ $post->author() }}</span>
                        @endif
                        <span><i class="las la-calendar"></i> {{ $post->date('F d, Y') }}</span>
                        <span>
                            <a href="{{ $post->comment_url() }}" style="color:inherit;text-decoration:none;">
                                <i class="las la-comment"></i> {{ theme_comments_count() }} {{ __('Comments') }}
                            </a>
                        </span>
                    </div>

                    <div class="ph-article-content">
                        {!! $post->content() !!}
                    </div>

                    <div class="ph-post-share">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}" class="ph-tag">{{ $tag->name() }}</a>
                            @endforeach
                        </div>
                        <ul class="ph-share-list">
                            {!! $post->share_links() !!}
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Comments --}}
            <div class="ph-sidebar-card mt-4" id="comment-area">
                <div class="ph-sidebar-title">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) ({{ theme_comments_count() }}) @endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div class="ph-comment">
                            <div class="ph-comment-avatar">{!! $comment->author_avatar() !!}</div>
                            <div style="flex:1;">
                                <div class="ph-comment-name">
                                    <a href="javascript:void(0)" class="title"
                                       data-parent_name="{{ $comment->author() }}">{{ $comment->author() }}</a>
                                </div>
                                <div class="ph-comment-date">{{ $comment->date() }}</div>
                                <p class="ph-comment-text">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="ph-reply-btn btn-replay"
                                            data-comment_id="{{ $comment->id() }}">
                                        <i class="las la-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif

                                @foreach($comment->replies() as $reply)
                                    <div class="ph-comment reply mt-3">
                                        <div class="ph-comment-avatar">{!! $reply->author_avatar() !!}</div>
                                        <div style="flex:1;">
                                            <div class="ph-comment-name">{{ $reply->author() }}</div>
                                            <div class="ph-comment-date">{{ $reply->date() }}</div>
                                            <p class="ph-comment-text">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(theme_comments()->isNotEmpty())
                    <div class="text-center mt-3">
                        <button class="ph-btn ph-btn-outline" id="load_more_comment_button">
                            {{ __('Load More') }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div class="ph-sidebar-card mt-4" id="blog-comment-form">
                <div class="ph-sidebar-title">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                <div class="mb-3">
                    <label class="ph-label">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" class="ph-input"
                           value="{{ theme_current_user()->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="ph-label">{{ __('Comment') }} *</label>
                    <textarea id="comment_content" class="ph-input" rows="4"
                              placeholder="{{ __('Write your comment…') }}"
                              style="height:auto;resize:vertical;"></textarea>
                </div>
                <button type="button" id="comment_submit_btn" class="ph-btn ph-btn-terra">
                    {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div class="ph-sidebar-card mt-4 text-center">
                <p class="mb-3" style="color:var(--ph-muted);">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}" class="ph-btn ph-btn-terra">{{ __('Sign In') }}</a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="ph-price-input flex-grow-1"
                           placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="ph-btn ph-btn-terra ph-btn-sm" style="white-space:nowrap;">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <label class="ph-filter-item">
                        <a href="{{ $cat->url() }}" style="color:inherit;text-decoration:none;"
                           class="{{ $post->category()?->id() == $cat->id() ? 'fw-bold' : '' }}">
                            {{ $cat->name() }}
                        </a>
                        <span class="ph-filter-count">{{ $cat->count() }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="ph-tag">{{ $tag->name() }}</a>
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
                    _token: '{{ theme_csrf() }}',
                    blog_id: $('[name=blog_id]').val(),
                    user_id: $('[name=user_id]').val(),
                    comment_id: $('[name=comment_id]').val(),
                    commented_by: $('#commented_by').val(),
                    comment_content: $('#comment_content').val(),
                },
                success: function (data) {
                    $('#comment_content').val('');
                    $('[name=comment_id]').val('');
                    $('.error-wrap').html('<div class="alert alert-success">' + data.msg + '</div>');
                    el.text('{{ __('Post Comment') }}');
                    location.reload();
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON?.errors ?? {}, html = '<div class="alert alert-danger">';
                    $.each(errors, function (k, v) { html += '<p>' + v + '</p>'; });
                    html += '</div>';
                    $('.error-wrap').html(html);
                    el.text('{{ __('Post Comment') }}');
                }
            });
        });

        $(document).on('click', '.btn-replay', function () {
            var comment_id  = $(this).data('comment_id');
            var parent_name = $(this).closest('.ph-comment').find('.title').data('parent_name');
            $('[name=comment_id]').val(comment_id);
            $('#comment_content').attr('placeholder', '{{ __('Replying to') }} ' + parent_name + '..');
            $('html').animate({ scrollTop: $('#comment_content').offset().top - 200 }, 200);
        });

        function loadMoreComments() {
            var commentData = $('#comment_data'), items = commentData.attr('data-items');
            $.ajax({
                url: '{{ theme_blog_load_comments_url() }}', method: 'POST',
                data: { id: '{{ $post->id() }}', _token: '{{ theme_csrf() }}', items: items },
                success: function (data) {
                    commentData.attr('data-items', parseInt(items) + 5);
                    commentData.append(data.markup);
                    var btn = $('#load_more_comment_button');
                    btn.text(data.blogComments.length === 0 ? '{{ __('No More Comment Found') }}' : '{{ __('Load More') }}');
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
