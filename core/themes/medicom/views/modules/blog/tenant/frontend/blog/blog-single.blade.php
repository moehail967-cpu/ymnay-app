@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="mc-blog-hero">
    <div class="container">
        <h2>{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h2>

    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <div class="mc-blog-article">

                @if($post->has_image())
                <div class="mc-blog-article-img">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}">
                </div>
                @endif

                <div class="mc-blog-article-body">
                    @if($post->category())
                    <div style="margin-bottom:14px;">
                        <a href="{{ $post->category()->url() }}" class="mc-blog-cat-badge" style="position:static;display:inline-block;">{{ $post->category()->name() }}</a>
                    </div>
                    @endif

                    <h1 class="mc-blog-article-title" style="font-size:26px;font-weight:800;color:#1a1a1a;line-height:1.3;margin:0 0 12px;">{{ $post->title() }}</h1>

                    <div class="mc-blog-article-divider">
                        @if($post->author())<span><i class="las la-user"></i> {{ $post->author() }}</span>@endif
                        <span><i class="las la-calendar"></i> {{ $post->date('F d, Y') }}</span>
                        <span>
                            <a href="{{ $post->comment_url() }}" style="color:#888;text-decoration:none;">
                                <i class="las la-comment"></i> {{ theme_comments_count() }} {{ __('Comments') }}
                            </a>
                        </span>
                    </div>

                    <div class="mc-blog-article-content">
                        {!! $post->content() !!}
                    </div>

                    {{-- Tags & Share --}}
                    <div class="mc-blog-article-footer">
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            @foreach($post->tags() as $tag)
                            <a href="{{ $tag->url() }}" class="mc-blog-article-tag">
                                <i class="las la-tag"></i> {{ $tag->name() }}
                            </a>
                            @endforeach
                        </div>
                        <ul style="display:flex;list-style:none;padding:0;margin:0;gap:8px;">
                            {!! $post->share_links() !!}
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Comments --}}
            <div class="mc-blog-comments-card" id="comment-area">
                <h3 class="mc-blog-comments-title">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) <span style="font-size:13px;color:#888;font-weight:400;">({{ theme_comments_count() }})</span> @endif
                </h3>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                    <div class="mc-comment-item">
                        <div class="mc-comment-avatar">
                            @if($comment->author_avatar())
                                {!! $comment->author_avatar() !!}
                            @else
                                {{ strtoupper(substr($comment->author() ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <div style="flex:1;">
                            <div class="mc-comment-name">
                                <a href="javascript:void(0)" class="title" data-parent_name="{{ $comment->author() }}" style="color:inherit;text-decoration:none;">{{ $comment->author() }}</a>
                            </div>
                            <div class="mc-comment-date">{{ $comment->date() }}</div>
                            <p class="mc-comment-body">{!! $comment->body() !!}</p>
                            @if($comment->can_reply())
                            <button class="mc-comment-reply-btn btn-replay" data-comment_id="{{ $comment->id() }}">
                                <i class="las la-reply"></i> {{ __('Reply') }}
                            </button>
                            @endif
                            @if($comment->replies()->count())
                            <div class="mc-comment-replies">
                                @foreach($comment->replies() as $reply)
                                <div class="mc-comment-item" style="padding:10px 0 0;">
                                    <div class="mc-comment-avatar" style="width:36px;height:36px;font-size:14px;">
                                        {{ strtoupper(substr($reply->author() ?? 'U', 0, 1)) }}
                                    </div>
                                    <div style="flex:1;">
                                        <div class="mc-comment-name">{{ $reply->author() }}</div>
                                        <div class="mc-comment-date">{{ $reply->date() }}</div>
                                        <p class="mc-comment-body" style="margin:0;">{!! $reply->body() !!}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(theme_comments()->count() > 4)
                <div style="text-align:center;margin-top:20px;">
                    <button class="mc-btn mc-btn-outline" id="load_more_comment_button">{{ __('Load More Comments') }}</button>
                </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div class="mc-comment-form" id="blog-comment-form">
                <h3 class="mc-comment-form-title">{{ __('Leave a Comment') }}</h3>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                <div class="mb-3">
                    <label class="mc-form-label">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" class="mc-form-input" value="{{ theme_current_user()->name }}" readonly style="background:#fafafa;">
                </div>
                <div class="mb-3">
                    <label class="mc-form-label">{{ __('Comment') }} <span class="mc-form-required">*</span></label>
                    <textarea id="comment_content" class="mc-form-input mc-form-textarea" rows="4" placeholder="{{ __('Write your comment…') }}"></textarea>
                </div>
                <button type="button" id="comment_submit_btn" class="mc-btn mc-btn-primary">
                    <i class="las la-paper-plane"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div class="mc-comment-form" style="text-align:center;">
                <p style="color:#888;margin-bottom:16px;">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}" class="mc-btn mc-btn-primary">
                    <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Search --}}
            <div class="mc-blog-sidebar-card">
                <div class="mc-blog-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" style="display:contents;">
                    {!! theme_csrf_field() !!}
                    <div class="mc-blog-search-row">
                        <input type="text" name="search" placeholder="{{ __('Search blogs…') }}">
                        <button type="submit" class="mc-blog-search-btn"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>

            {{-- Recent Posts --}}
            @if(theme_blogs()->isNotEmpty())
            <div class="mc-blog-sidebar-card">
                <div class="mc-blog-sidebar-title">{{ __('Recent Posts') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div class="mc-blog-recent-item">
                    @if($recent->has_image())
                    <div class="mc-blog-recent-thumb">
                        <a href="{{ $recent->url() }}"><img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}"></a>
                    </div>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <a href="{{ $recent->url() }}" class="mc-blog-recent-title">{!! $recent->title() !!}</a>
                        <div class="mc-blog-recent-date"><i class="las la-calendar" style="color:#1A85ED;"></i> {{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Categories --}}
            @if(theme_blog_categories()->isNotEmpty())
            <div class="mc-blog-sidebar-card">
                <div class="mc-blog-sidebar-title">{{ __('Categories') }}</div>
                <ul class="mc-blog-cat-list">
                    @foreach(theme_blog_categories() as $cat)
                    <li class="mc-blog-cat-item">
                        <a href="{{ $cat->url() }}" class="mc-blog-cat-link {{ $post->category()?->id() == $cat->id() ? 'active' : '' }}">
                            <span><i class="las la-tag" style="color:#1A85ED;margin-right:6px;"></i>{{ $cat->name() }}</span>
                            <span class="mc-blog-cat-count">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @if(theme_blog_tags()->isNotEmpty())
            <div class="mc-blog-sidebar-card">
                <div class="mc-blog-sidebar-title">{{ __('Tags') }}</div>
                <div class="mc-blog-tag-cloud">
                    @foreach(theme_blog_tags() as $tag)
                    <a href="{{ $tag->url() }}" class="mc-blog-tag">#{{ $tag->name() }}</a>
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
            el.html('<i class="las la-spinner la-spin"></i> {{ __("Submitting…") }}');
            $.ajax({
                url: '{{ theme_blog_comment_store_url() }}', method: 'POST',
                data: {
                    _token:          '{{ theme_csrf() }}',
                    blog_id:         $('[name=blog_id]').val(),
                    user_id:         $('[name=user_id]').val(),
                    comment_id:      $('[name=comment_id]').val(),
                    commented_by:    $('#commented_by').val(),
                    comment_content: $('#comment_content').val(),
                },
                success: function (data) {
                    $('#comment_content').val(''); $('[name=comment_id]').val('');
                    $('.error-wrap').html('<div class="alert alert-success">' + data.msg + '</div>');
                    el.html('<i class="las la-paper-plane"></i> {{ __("Post Comment") }}');
                    location.reload();
                },
                error: function (xhr) {
                    var errors = (xhr.responseJSON || {}).errors || {};
                    var html = '<div class="alert alert-danger">';
                    $.each(errors, function (k, v) { html += '<p>' + v + '</p>'; }); html += '</div>';
                    $('.error-wrap').html(html);
                    el.html('<i class="las la-paper-plane"></i> {{ __("Post Comment") }}');
                }
            });
        });

        $(document).on('click', '.btn-replay', function () {
            var comment_id  = $(this).data('comment_id');
            var parent_name = $(this).closest('.mc-comment-item').find('.title').data('parent_name');
            $('[name=comment_id]').val(comment_id);
            $('#comment_content').attr('placeholder', '{{ __("Replying to") }} ' + parent_name + '..');
            $('html').animate({ scrollTop: $('#comment_content').offset().top - 200 }, 200);
        });

        $(document).on('click', '#load_more_comment_button', function () {
            var el = $(this);
            el.html('{{ __("Loading…") }}');
            var commentData = $('#comment_data');
            var items = commentData.attr('data-items');
            $.ajax({
                url: '{{ theme_blog_load_comments_url() }}', method: 'POST',
                data: { id: '{{ $post->id() }}', _token: '{{ theme_csrf() }}', items: items },
                success: function (data) {
                    commentData.attr('data-items', parseInt(items) + 5);
                    commentData.append(data.markup);
                    el.html(data.blogComments.length === 0
                        ? '{{ __("No More Comments") }}'
                        : '{{ __("Load More Comments") }}');
                }
            });
        });

    });
})(jQuery);
</script>
<x-custom-js.ajax-login/>
@endsection
