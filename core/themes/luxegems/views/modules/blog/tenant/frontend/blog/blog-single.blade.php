@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_product_url(theme_static_option('blog_page')) }}">{{ __('Blog') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <div class="lg-article">
                <div class="lg-article-hero">
                    @if($post->has_image())
                        <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}"
                             style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <span style="font-size:72px;color:var(--lx-gold);">◈</span>
                    @endif
                </div>

                <div class="lg-article-body">
                    @if($post->category())
                        <span class="lg-article-cat">
                            <a href="{{ $post->category()->url() }}" style="color:inherit;text-decoration:none;">
                                {{ $post->category()->name() }}
                            </a>
                        </span>
                    @endif

                    <h1 class="lg-article-title">{{ $post->title() }}</h1>

                    <div class="lg-article-meta">
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

                    <div class="lg-article-content">
                        {!! $post->content() !!}
                    </div>

                    {{-- Tags & Share --}}
                    <div class="lg-post-share">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}" class="lg-tag">{{ $tag->name() }}</a>
                            @endforeach
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            {!! $post->share_links() !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Comments --}}
            <div class="lg-sidebar-card mt-4" id="comment-area">
                <div class="lg-sidebar-title">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) ({{ theme_comments_count() }}) @endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div class="lg-comment">
                            <div class="lg-comment-avatar">{!! $comment->author_avatar() !!}</div>
                            <div style="flex:1;">
                                <div class="lg-comment-name">
                                    <a href="javascript:void(0)" class="title"
                                       data-parent_name="{{ $comment->author() }}">{{ $comment->author() }}</a>
                                </div>
                                <div class="lg-comment-date">{{ $comment->date() }}</div>
                                <p class="lg-comment-text">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="lg-reply-btn btn-replay"
                                            data-comment_id="{{ $comment->id() }}">
                                        <i class="las la-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif

                                @foreach($comment->replies() as $reply)
                                    <div class="lg-comment reply mt-3">
                                        <div class="lg-comment-avatar">{!! $reply->author_avatar() !!}</div>
                                        <div style="flex:1;">
                                            <div class="lg-comment-name">{{ $reply->author() }}</div>
                                            <div class="lg-comment-date">{{ $reply->date() }}</div>
                                            <p class="lg-comment-text">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(theme_comments()->isNotEmpty())
                    <div class="text-center mt-3">
                        <button class="lx-btn lx-btn-outline" id="load_more_comment_button">
                            {{ __('Load More') }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div class="lg-sidebar-card mt-4" id="blog-comment-form">
                <div class="lg-sidebar-title">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                <div class="mb-3">
                    <label class="lg-form-label">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" class="lg-form-control"
                           value="{{ theme_current_user()->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="lg-form-label">{{ __('Comment') }} *</label>
                    <textarea id="comment_content" class="lg-form-control" rows="4"
                              placeholder="{{ __('Write your comment…') }}"
                              style="height:auto;resize:vertical;"></textarea>
                </div>
                <button type="button" id="comment_submit_btn" class="lx-btn lx-btn-primary">
                    {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div class="lg-sidebar-card mt-4 text-center">
                <p class="mb-3" style="color:var(--lx-muted);">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}" class="lx-btn lx-btn-primary">{{ __('Sign In') }}</a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="lg-sidebar-card">
                <div class="lg-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="lg-form-control flex-grow-1"
                           placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="lx-btn lx-btn-primary" style="white-space:nowrap;padding:10px 14px;">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="lg-sidebar-card">
                <div class="lg-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <div class="lg-blog-filter-item">
                        <a href="{{ $cat->url() }}" style="color:inherit;text-decoration:none;"
                           class="{{ $post->category()?->id() == $cat->id() ? 'fw-bold' : '' }}">
                            {{ $cat->name() }}
                        </a>
                        <span class="lg-filter-count">{{ $cat->count() }}</span>
                    </div>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="lg-sidebar-card">
                <div class="lg-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="lg-tag">{{ $tag->name() }}</a>
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
                    blog_id:     $('[name=blog_id]').val(),
                    user_id:     $('[name=user_id]').val(),
                    comment_id:  $('[name=comment_id]').val(),
                    commented_by:    $('#commented_by').val(),
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
                    var errors = xhr.responseJSON?.errors ?? {};
                    var html = '<div class="alert alert-danger">';
                    $.each(errors, function (k, v) { html += '<p>' + v + '</p>'; });
                    html += '</div>';
                    $('.error-wrap').html(html);
                    el.text('{{ __('Post Comment') }}');
                }
            });
        });

        $(document).on('click', '.btn-replay', function () {
            var comment_id  = $(this).data('comment_id');
            var parent_name = $(this).closest('.lg-comment').find('.title').data('parent_name');
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
