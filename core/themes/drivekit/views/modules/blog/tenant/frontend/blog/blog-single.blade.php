@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection
@section('meta-data') {!! render_page_meta_data($blog_post) !!} @endsection

@section('content')
@php $post = theme_post(); @endphp

{{-- Breadcrumb --}}
<div class="dk-breadcrumb-bar">
    <div class="container">
        <div class="dk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            {{ \Illuminate\Support\Str::words($post->title(), 6) }}
        </div>
    </div>
</div>

{{-- Article + Sidebar --}}
<section class="py-5" style="background:var(--dk-carbon);">
    <div class="container">
        <div class="row g-4">

            {{-- Article Column --}}
            <div class="col-lg-8">

                <div class="dk-article">

                    {{-- Featured Image --}}
                    @if($post->has_image())
                    <div class="dk-featured-img">
                        <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}">
                    </div>
                    @endif

                    {{-- Article Header --}}
                    <div class="dk-article-header">
                        @if($post->category())
                            <a href="{{ $post->category()->url() }}" class="dk-article-cat">{{ $post->category()->name() }}</a>
                        @endif
                        <h1 class="dk-article-title">{{ $post->title() }}</h1>
                        <div class="dk-article-meta">
                            @if($post->author())
                            <div class="d-flex align-items-center gap-2">
                                <div class="dk-author-avatar"><i class="mdi mdi-account" style="font-size:20px;color:var(--dk-silver);"></i></div>
                                <span><strong style="color:var(--dk-text);">{{ $post->author() }}</strong></span>
                            </div>
                            @endif
                            <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date('F d, Y') }}</span>
                            <span>
                                <a href="{{ $post->comment_url() }}">
                                    <i class="mdi mdi-comment-outline"></i> {{ theme_comments_count() }} {{ __('Comments') }}
                                </a>
                            </span>
                        </div>
                    </div>

                    {{-- Article Content --}}
                    <div class="dk-article-content">
                        {!! $post->content() !!}
                    </div>

                    {{-- Tags & Share --}}
                    <div class="dk-article-footer">
                        <div>
                            <span class="dk-article-tags-label">{{ __('Tags:') }}</span>
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}" class="dk-tag">{{ $tag->name() }}</a>
                            @endforeach
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="dk-share-label">{{ __('Share:') }}</span>
                            <div class="dk-share-btns">
                                {!! $post->share_links() !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Comments List --}}
                <div class="dk-comments-section" id="comment-area">
                    <div class="dk-section-heading">
                        <i class="mdi mdi-comment-multiple-outline"></i>
                        {{ __('Comments') }}
                        @if(theme_comments_count())
                            <span class="dk-section-heading-count">({{ theme_comments_count() }})</span>
                        @endif
                    </div>

                    <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                        @foreach(theme_comments() as $comment)
                        <div class="dk-comment-card">
                            <div class="dk-comment-head">
                                <div class="dk-comment-avatar">{!! $comment->author_avatar() !!}</div>
                                <div>
                                    <div class="dk-commenter-name">
                                        <a href="javascript:void(0)" class="title" data-parent_name="{{ $comment->author() }}" style="color:inherit;text-decoration:none;">{{ $comment->author() }}</a>
                                    </div>
                                    <div class="dk-comment-date">{{ $comment->date() }}</div>
                                </div>
                            </div>
                            <p class="dk-comment-text">{!! $comment->body() !!}</p>
                            @if($comment->can_reply())
                                <button class="dk-comment-reply btn-replay" data-comment_id="{{ $comment->id() }}">
                                    <i class="mdi mdi-reply"></i> {{ __('Reply') }}
                                </button>
                            @endif
                            @foreach($comment->replies() as $reply)
                                <div class="dk-comment-reply-wrap">
                                    <div class="dk-comment-avatar sm">{!! $reply->author_avatar() !!}</div>
                                    <div style="flex:1;">
                                        <div class="dk-commenter-name sm">{{ $reply->author() }}</div>
                                        <div class="dk-comment-date sm">{{ $reply->date() }}</div>
                                        <p class="dk-comment-text sm">{!! $reply->body() !!}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>

                    @if(theme_comments()->isNotEmpty())
                    <div style="text-align:center;margin-top:20px;">
                        <button class="dk-btn dk-btn-ghost dk-btn-sm" id="load_more_comment_button">{{ __('Load More') }}</button>
                    </div>
                    @endif
                </div>

                {{-- Comment Form --}}
                @if(theme_is_logged_in())
                <div class="dk-comment-form" id="blog-comment-form">
                    <div class="dk-section-heading">
                        <i class="mdi mdi-comment-edit-outline"></i> {{ __('Leave a Comment') }}
                    </div>
                    <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                    <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                    <input type="hidden" name="comment_id" value="">
                    <div class="error-wrap mb-3"></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="dk-input-label">{{ __('Name') }}</label>
                            <input type="text" id="commented_by" class="dk-input" value="{{ theme_current_user()->name }}" readonly style="background:var(--dk-surface);">
                        </div>
                        <div class="col-12">
                            <label class="dk-input-label">{{ __('Comment') }} <span style="color:var(--dk-red);">*</span></label>
                            <textarea id="comment_content" class="dk-input" rows="4" placeholder="{{ __('Write your comment…') }}"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="button" id="comment_submit_btn" class="dk-btn dk-btn-red dk-btn-sm">
                                <i class="mdi mdi-send"></i> {{ __('Post Comment') }}
                            </button>
                        </div>
                    </div>
                </div>
                @else
                <div class="dk-comment-form locked">
                    <i class="mdi mdi-account-lock-outline dk-comment-locked-icon"></i>
                    <p style="color:var(--dk-silver);margin-bottom:16px;font-size:13px;">{{ __('Sign In To Leave Your Comment') }}</p>
                    <a href="{{ theme_login_url() }}" class="dk-btn dk-btn-red dk-btn-sm">
                        <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                    </a>
                </div>
                @endif

                {{-- Related Posts --}}
                @if(theme_blogs()->count() > 1)
                <div class="dk-related-section">
                    <h3 class="dk-related-heading">
                        {{ __('Related') }} <span>{{ __('Articles') }}</span>
                    </h3>
                    <div class="row g-3">
                        @foreach(theme_blogs()->where('id', '!=', $post->id())->take(3) as $related)
                        <div class="col-md-4">
                            <a href="{{ $related->url() }}" class="dk-related-blog">
                                <div class="dk-related-thumb">
                                    @if($related->has_image())
                                        <img src="{{ $related->image_url() }}" alt="{{ $related->title() }}">
                                    @else
                                        <i class="mdi mdi-car-wrench" style="color:var(--dk-red);opacity:.2;"></i>
                                    @endif
                                </div>
                                <div class="dk-related-body">
                                    @if($related->category())
                                        <div class="dk-related-cat">{{ $related->category()->name() }}</div>
                                    @endif
                                    <div class="dk-related-title">{!! $related->title() !!}</div>
                                    <div class="dk-related-date">{{ $related->date() }}</div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">

                {{-- Search --}}
                <div class="dk-sidebar-panel">
                    <div class="dk-sidebar-title"><i class="mdi mdi-magnify"></i> {{ __('Search') }}</div>
                    <form action="{{ theme_blog_search_url() }}" method="POST" style="display:flex;gap:8px;">
                        {!! theme_csrf_field() !!}
                        <input type="text" name="search" placeholder="{{ __('Search blogs…') }}" class="dk-input" style="padding:9px 14px;">
                        <button type="submit" class="dk-btn dk-btn-red dk-btn-sm" style="white-space:nowrap;">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </form>
                </div>

                {{-- Recent Posts --}}
                @if(theme_blogs()->isNotEmpty())
                <div class="dk-sidebar-panel">
                    <div class="dk-sidebar-title"><i class="mdi mdi-clock-outline"></i> {{ __('Recent Posts') }}</div>
                    @foreach(theme_blogs()->take(5) as $recent)
                    <div class="dk-recent-post">
                        <div class="dk-recent-thumb">
                            @if($recent->has_image())
                                <img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}">
                            @else
                                <i class="mdi mdi-car-wrench" style="color:var(--dk-red);opacity:.3;"></i>
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <a href="{{ $recent->url() }}" class="dk-recent-title">{!! $recent->title() !!}</a>
                            <div class="dk-recent-date"><i class="mdi mdi-calendar" style="color:var(--dk-red);"></i> {{ $recent->date() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Tags --}}
                @if(theme_blog_categories()->isNotEmpty())
                <div class="dk-sidebar-panel">
                    <div class="dk-sidebar-title"><i class="mdi mdi-tag-outline"></i> {{ __('Article Tags') }}</div>
                    <div>
                        @foreach(theme_blog_tags() as $tag)
                            <a href="{{ $tag->url() }}" class="dk-tag"><i class="mdi mdi-tag-outline"></i> {{ $tag->name() }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Shop CTA --}}
                <div class="dk-shop-cta">
                    <i class="mdi mdi-car-wrench dk-shop-cta-icon"></i>
                    <div class="dk-shop-cta-title">{{ __('Shop Our Parts') }}</div>
                    <p class="dk-shop-cta-desc">{{ __('OEM and performance parts for every make and model.') }}</p>
                    <a href="{{ theme_shop_url() }}" class="dk-btn dk-btn-red w-100" style="justify-content:center;">
                        <i class="mdi mdi-magnify"></i> {{ __('Browse Parts') }}
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
(function ($) {
    $(document).ready(function () {

        $(document).on('click', '#comment_submit_btn', function () {
            var el = $(this);
            el.html('<i class="mdi mdi-loading mdi-spin"></i> {{ __('Submitting') }}...');
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
                    $('.error-wrap').html('<div style="padding:10px 14px;background:rgba(76,175,80,.1);border:1px solid rgba(76,175,80,.3);border-radius:var(--dk-radius);color:#4CAF50;font-size:13px;margin-bottom:8px;">' + data.msg + '</div>');
                    el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}');
                    location.reload();
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON?.errors ?? {}; var html = '<div style="padding:10px 14px;background:rgba(204,34,0,.1);border:1px solid rgba(204,34,0,.3);border-radius:var(--dk-radius);color:var(--dk-red);font-size:13px;margin-bottom:8px;">';
                    $.each(errors, function (k, v) { html += '<p style="margin:0;">' + v + '</p>'; }); html += '</div>';
                    $('.error-wrap').html(html);
                    el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}');
                }
            });
        });

        $(document).on('click', '.btn-replay', function () {
            var comment_id  = $(this).data('comment_id');
            var parent_name = $(this).closest('.dk-comment-card').find('.title').data('parent_name');
            $('[name=comment_id]').val(comment_id);
            $('#comment_content').attr('placeholder', '{{ __('Replying to') }} ' + parent_name + '..');
            $('html').animate({ scrollTop: $('#comment_content').offset().top - 200 }, 200);
        });

        $(document).on('click', '#load_more_comment_button', function () {
            var el = $(this);
            el.text('{{ __('Loading...') }}');
            var commentData = $('#comment_data');
            var items = commentData.attr('data-items');
            $.ajax({
                url: '{{ theme_blog_load_comments_url() }}', method: 'POST',
                data: { id: '{{ $post->id() }}', _token: '{{ theme_csrf() }}', items: items },
                success: function (data) {
                    commentData.attr('data-items', parseInt(items) + 5);
                    commentData.append(data.markup);
                    el.text(data.blogComments.length === 0 ? '{{ __('No More Comment Found') }}' : '{{ __('Load More') }}');
                }
            });
        });

    });
})(jQuery);
</script>
<x-custom-js.ajax-login/>
@endsection
