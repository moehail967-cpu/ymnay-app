@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="bk-page-banner">
    <div class="container">
        <h1>{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h1>
        <div class="bk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <article style="background:#fff;border:1px solid var(--bk-border);border-radius:var(--bk-radius);overflow:hidden;">

                {{-- Featured Image --}}
                @if($post->has_image())
                <div style="width:100%;max-height:420px;overflow:hidden;">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}"
                         style="width:100%;height:100%;object-fit:cover;">
                </div>
                @endif

                <div style="padding:32px;">
                    {{-- Category --}}
                    @if($post->category())
                        <span class="bk-blog-cat" style="display:inline-block;margin-bottom:14px;">
                            <a href="{{ $post->category()->url() }}" style="color:inherit;text-decoration:none;">
                                {{ $post->category()->name() }}
                            </a>
                        </span>
                    @endif

                    {{-- Title --}}
                    <h1 style="font-family:var(--bk-font-head);font-size:28px;font-weight:800;color:var(--bk-dark);line-height:1.3;margin-bottom:16px;">
                        {{ $post->title() }}
                    </h1>

                    {{-- Meta --}}
                    <div class="bk-blog-meta" style="margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--bk-border);">
                        @if($post->author())
                            <span><i class="mdi mdi-account"></i> {{ $post->author() }}</span>
                        @endif
                        <span><i class="mdi mdi-calendar"></i> {{ $post->date('F d, Y') }}</span>
                        <span>
                            <a href="{{ $post->comment_url() }}" style="color:inherit;text-decoration:none;">
                                <i class="mdi mdi-comment-outline"></i> {{ theme_comments_count() }} {{ __('Comments') }}
                            </a>
                        </span>
                    </div>

                    {{-- Content --}}
                    <div style="font-size:15px;line-height:1.8;color:var(--bk-text);">
                        {!! $post->content() !!}
                    </div>

                    {{-- Tags & Share --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:32px;padding-top:20px;border-top:1px solid var(--bk-border);">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}" class="bk-tag-pill">
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
            <div class="bk-sb-card mt-4" id="comment-area">
                <div class="bk-sb-title">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) ({{ theme_comments_count() }}) @endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div style="display:flex;gap:14px;padding:16px 0;border-bottom:1px solid var(--bk-border);">
                            <div style="flex-shrink:0;width:44px;height:44px;border-radius:50%;overflow:hidden;background:var(--bk-light);">
                                {!! $comment->author_avatar() !!}
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700;font-size:14px;color:var(--bk-dark);">
                                    <a href="javascript:void(0)" class="title"
                                       data-parent_name="{{ $comment->author() }}"
                                       style="color:inherit;text-decoration:none;">{{ $comment->author() }}</a>
                                </div>
                                <div style="font-size:12px;color:var(--bk-muted);margin-bottom:6px;">{{ $comment->date() }}</div>
                                <p style="font-size:14px;color:var(--bk-text);margin:0 0 8px;">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="bk-read-more btn-replay" style="background:none;border:none;padding:0;cursor:pointer;font-size:13px;"
                                            data-comment_id="{{ $comment->id() }}">
                                        <i class="mdi mdi-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif

                                @foreach($comment->replies() as $reply)
                                    <div style="display:flex;gap:12px;margin-top:14px;padding-top:14px;border-top:1px dashed var(--bk-border);padding-left:20px;">
                                        <div style="flex-shrink:0;width:36px;height:36px;border-radius:50%;overflow:hidden;background:var(--bk-light);">
                                            {!! $reply->author_avatar() !!}
                                        </div>
                                        <div style="flex:1;">
                                            <div style="font-weight:700;font-size:13px;color:var(--bk-dark);">{{ $reply->author() }}</div>
                                            <div style="font-size:11px;color:var(--bk-muted);margin-bottom:4px;">{{ $reply->date() }}</div>
                                            <p style="font-size:13px;color:var(--bk-text);margin:0;">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(theme_comments()->isNotEmpty())
                    <div class="text-center mt-3">
                        <button class="bk-btn bk-btn-outline" id="load_more_comment_button">
                            {{ __('Load More') }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div class="bk-sb-card mt-4" id="blog-comment-form">
                <div class="bk-sb-title">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                <div class="mb-3">
                    <label class="bk-label">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" class="bk-input"
                           value="{{ theme_current_user()->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="bk-label">{{ __('Comment') }} <span class="bk-required">*</span></label>
                    <textarea id="comment_content" class="bk-textarea" rows="4"
                              placeholder="{{ __('Write your comment…') }}"></textarea>
                </div>
                <button type="button" id="comment_submit_btn" class="bk-btn bk-btn-rose">
                    <i class="mdi mdi-send"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div class="bk-sb-card mt-4 text-center">
                <p class="mb-3" style="color:var(--bk-muted);">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}" class="bk-btn bk-btn-rose">
                    <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Search --}}
            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="bk-input flex-grow-1"
                           placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="bk-btn bk-btn-rose" style="padding:10px 14px;white-space:nowrap;">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </form>
            </div>

            {{-- Recent Posts (sidebar) --}}
            @if(theme_blogs()->isNotEmpty())
            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Recent Posts') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div class="bk-recent-post">
                    @if($recent->has_image())
                    <div class="bk-recent-thumb">
                        <a href="{{ $recent->url() }}">
                            <img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}"
                                 style="width:100%;height:100%;object-fit:cover;">
                        </a>
                    </div>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <a class="bk-recent-name" href="{{ $recent->url() }}">{!! $recent->title() !!}</a>
                        <div class="bk-recent-date"><i class="mdi mdi-calendar"></i> {{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Categories --}}
            @if(theme_blog_categories()->isNotEmpty())
            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <div class="bk-sb-cat-row">
                        <a href="{{ $cat->url() }}"
                           style="color:inherit;text-decoration:none;{{ $post->category()?->id() == $cat->id() ? 'font-weight:700;' : '' }}">
                            {{ $cat->name() }}
                        </a>
                        <span class="bk-filter-count">{{ $cat->count() }}</span>
                    </div>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @if(theme_blog_tags()->isNotEmpty())
            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="bk-tag-pill">
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
                    _token: '{{ theme_csrf() }}',
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
