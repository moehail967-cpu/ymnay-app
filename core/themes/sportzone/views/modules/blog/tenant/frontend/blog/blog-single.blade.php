@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="sz-page-banner">
    <div class="container">
        <h1>{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <article style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);overflow:hidden;">

                @if($post->has_image())
                <div style="width:100%;max-height:420px;overflow:hidden;">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                @endif

                <div style="padding:32px;">
                    @if($post->category())
                        <span style="display:inline-block;background:var(--sz-red);color:#fff;font-family:var(--sz-font-head);font-size:11px;padding:4px 14px;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:14px;">
                            <a href="{{ $post->category()->url() }}" style="color:inherit;text-decoration:none;">{{ $post->category()->name() }}</a>
                        </span>
                    @endif

                    <h1 style="font-family:var(--sz-font-head);font-size:26px;font-weight:700;color:var(--sz-dark);line-height:1.2;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;">{{ $post->title() }}</h1>

                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;font-size:13px;color:var(--sz-muted);margin-bottom:24px;padding-bottom:20px;border-bottom:2px solid var(--sz-border);font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:.5px;">
                        @if($post->author()) <span><i class="mdi mdi-account"></i> {{ $post->author() }}</span> @endif
                        <span><i class="mdi mdi-calendar"></i> {{ $post->date('F d, Y') }}</span>
                        <span>
                            <a href="{{ $post->comment_url() }}" style="color:inherit;text-decoration:none;">
                                <i class="mdi mdi-comment-outline"></i> {{ theme_comments_count() }} {{ __('Comments') }}
                            </a>
                        </span>
                    </div>

                    <div style="font-size:15px;line-height:1.8;color:var(--sz-dark);">
                        {!! $post->content() !!}
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:32px;padding-top:20px;border-top:2px solid var(--sz-border);">
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:var(--sz-red-light);border:2px solid var(--sz-red);font-family:var(--sz-font-head);font-size:11px;font-weight:400;color:var(--sz-red);text-decoration:none;text-transform:uppercase;letter-spacing:1px;">
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
            <div style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);padding:28px;margin-top:24px;" id="comment-area">
                <div style="font-family:var(--sz-font-head);font-size:16px;font-weight:700;color:var(--sz-dark);margin-bottom:20px;text-transform:uppercase;letter-spacing:2px;">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) <span style="font-size:13px;color:var(--sz-muted);font-weight:400;">({{ theme_comments_count() }})</span> @endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div style="display:flex;gap:14px;padding:16px 0;border-bottom:1px solid var(--sz-border);">
                            <div style="flex-shrink:0;width:44px;height:44px;border-radius:var(--sz-radius);overflow:hidden;background:var(--sz-red-light);">
                                {!! $comment->author_avatar() !!}
                            </div>
                            <div style="flex:1;">
                                <div style="font-family:var(--sz-font-head);font-weight:700;font-size:14px;color:var(--sz-dark);text-transform:uppercase;letter-spacing:.5px;">
                                    <a href="javascript:void(0)" class="title" data-parent_name="{{ $comment->author() }}" style="color:inherit;text-decoration:none;">{{ $comment->author() }}</a>
                                </div>
                                <div style="font-size:12px;color:var(--sz-muted);margin-bottom:6px;">{{ $comment->date() }}</div>
                                <p style="font-size:14px;color:var(--sz-dark);margin:0 0 8px;">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="btn-replay" style="background:none;border:none;padding:0;cursor:pointer;font-family:var(--sz-font-head);font-size:12px;color:var(--sz-red);text-transform:uppercase;letter-spacing:1px;" data-comment_id="{{ $comment->id() }}">
                                        <i class="mdi mdi-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif
                                @foreach($comment->replies() as $reply)
                                    <div style="display:flex;gap:12px;margin-top:14px;padding:14px 0 0 20px;border-top:1px dashed var(--sz-border);">
                                        <div style="flex-shrink:0;width:36px;height:36px;border-radius:var(--sz-radius);overflow:hidden;background:var(--sz-red-light);">
                                            {!! $reply->author_avatar() !!}
                                        </div>
                                        <div style="flex:1;">
                                            <div style="font-family:var(--sz-font-head);font-weight:700;font-size:13px;color:var(--sz-dark);text-transform:uppercase;letter-spacing:.5px;">{{ $reply->author() }}</div>
                                            <div style="font-size:11px;color:var(--sz-muted);margin-bottom:4px;">{{ $reply->date() }}</div>
                                            <p style="font-size:13px;color:var(--sz-dark);margin:0;">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(theme_comments()->isNotEmpty())
                    <div style="text-align:center;margin-top:20px;">
                        <button class="sz-btn sz-btn-outline" id="load_more_comment_button">{{ __('Load More') }}</button>
                    </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);padding:28px;margin-top:24px;" id="blog-comment-form">
                <div style="font-family:var(--sz-font-head);font-size:16px;font-weight:700;color:var(--sz-dark);margin-bottom:20px;text-transform:uppercase;letter-spacing:2px;">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                @php $inp = 'width:100%;padding:10px 14px;border:2px solid var(--sz-border);border-radius:var(--sz-radius);font-size:14px;font-family:var(--sz-font-body);outline:none;transition:border-color .2s;'; @endphp
                <div class="mb-3">
                    <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" style="{{ $inp }}background:var(--sz-bg);" value="{{ theme_current_user()->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Comment') }} <span style="color:var(--sz-red);">*</span></label>
                    <textarea id="comment_content" style="{{ $inp }}height:100px;resize:vertical;" rows="4" placeholder="{{ __('Write your comment…') }}"
                              onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'"></textarea>
                </div>
                <button type="button" id="comment_submit_btn" class="sz-btn sz-btn-red">
                    <i class="mdi mdi-send"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);padding:32px;margin-top:24px;text-align:center;">
                <p style="color:var(--sz-muted);margin-bottom:16px;">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}" class="sz-btn sz-btn-red">
                    <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="sz-sidebar" style="margin-bottom:24px;">
                <div class="sz-sidebar-head"><i class="mdi mdi-magnify"></i> {{ __('Search') }}</div>
                <div class="sz-sidebar-block">
                    <form action="{{ theme_blog_search_url() }}" method="POST" style="display:flex;gap:8px;">
                        {!! theme_csrf_field() !!}
                        <input type="text" name="search" placeholder="{{ __('Search blogs…') }}"
                               style="flex:1;padding:9px 14px;border:2px solid var(--sz-border);border-radius:var(--sz-radius);font-size:13px;font-family:var(--sz-font-body);outline:none;transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                        <button type="submit" style="background:var(--sz-red);border:0;color:#fff;padding:0 16px;border-radius:var(--sz-radius);cursor:pointer;font-size:16px;"><i class="mdi mdi-magnify"></i></button>
                    </form>
                </div>
            </div>

            @if(theme_blogs()->isNotEmpty())
            <div class="sz-sidebar" style="margin-bottom:24px;">
                <div class="sz-sidebar-head"><i class="mdi mdi-newspaper"></i> {{ __('Recent Posts') }}</div>
                <div class="sz-sidebar-block">
                    @foreach(theme_blogs()->take(4) as $recent)
                    <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--sz-border);">
                        @if($recent->has_image())
                        <div style="width:64px;height:64px;border-radius:var(--sz-radius);overflow:hidden;flex-shrink:0;border:2px solid var(--sz-border);">
                            <a href="{{ $recent->url() }}"><img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}" style="width:100%;height:100%;object-fit:cover;"></a>
                        </div>
                        @endif
                        <div style="flex:1;min-width:0;">
                            <a href="{{ $recent->url() }}" style="font-family:var(--sz-font-head);font-size:12px;font-weight:400;color:var(--sz-dark);text-decoration:none;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-transform:uppercase;letter-spacing:.5px;transition:color .2s;"
                               onmouseover="this.style.color='var(--sz-red)'" onmouseout="this.style.color='var(--sz-dark)'">{!! $recent->title() !!}</a>
                            <div style="font-size:11px;color:var(--sz-muted);margin-top:4px;font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:.5px;"><i class="mdi mdi-calendar"></i> {{ $recent->date() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div class="sz-sidebar" style="margin-bottom:24px;">
                <div class="sz-sidebar-head"><i class="mdi mdi-tag-outline"></i> {{ __('Categories') }}</div>
                <div class="sz-sidebar-block">
                    @foreach(theme_blog_categories() as $cat)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--sz-border);">
                        <a href="{{ $cat->url() }}" style="font-family:var(--sz-font-head);font-size:12px;color:var(--sz-dark);text-decoration:none;text-transform:uppercase;letter-spacing:.5px;{{ $post->category()?->id() == $cat->id() ? 'color:var(--sz-red);' : '' }};transition:color .2s;"
                           onmouseover="this.style.color='var(--sz-red)'" onmouseout="this.style.color='{{ $post->category()?->id() == $cat->id() ? 'var(--sz-red)' : 'var(--sz-dark)' }}'">
                            {{ $cat->name() }}
                        </a>
                        <span style="background:var(--sz-red-light);color:var(--sz-red);font-family:var(--sz-font-head);font-size:11px;padding:2px 10px;">{{ $cat->count() }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="sz-sidebar">
                <div class="sz-sidebar-head"><i class="mdi mdi-pound"></i> {{ __('Tags') }}</div>
                <div class="sz-sidebar-block">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach(theme_blog_tags() as $tag)
                            <a href="{{ $tag->url() }}"
                               style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:var(--sz-bg);border:2px solid var(--sz-border);font-family:var(--sz-font-head);font-size:11px;color:var(--sz-muted);text-decoration:none;text-transform:uppercase;letter-spacing:1px;transition:all .2s;"
                               onmouseover="this.style.background='var(--sz-red)';this.style.color='#fff';this.style.borderColor='var(--sz-red)'"
                               onmouseout="this.style.background='var(--sz-bg)';this.style.color='var(--sz-muted)';this.style.borderColor='var(--sz-border)'">
                                <i class="mdi mdi-tag-outline"></i> {{ $tag->name() }}
                            </a>
                        @endforeach
                    </div>
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
            el.text('{{ __("Submitting") }}...');
            $.ajax({
                url: '{{ theme_blog_comment_store_url() }}', method: 'POST',
                data: { _token: '{{ theme_csrf() }}', blog_id: $('[name=blog_id]').val(), user_id: $('[name=user_id]').val(), comment_id: $('[name=comment_id]').val(), commented_by: $('#commented_by').val(), comment_content: $('#comment_content').val() },
                success: function (data) {
                    $('#comment_content').val(''); $('[name=comment_id]').val('');
                    $('.error-wrap').html('<div class="alert alert-success">' + data.msg + '</div>');
                    el.html('<i class="mdi mdi-send"></i> {{ __("Post Comment") }}');
                    location.reload();
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON?.errors ?? {}; var html = '<div class="alert alert-danger">';
                    $.each(errors, function (k, v) { html += '<p>' + v + '</p>'; }); html += '</div>';
                    $('.error-wrap').html(html);
                    el.html('<i class="mdi mdi-send"></i> {{ __("Post Comment") }}');
                }
            });
        });

        $(document).on('click', '.btn-replay', function () {
            var comment_id  = $(this).data('comment_id');
            var parent_name = $(this).closest('[style*="display:flex"]').find('.title').data('parent_name');
            $('[name=comment_id]').val(comment_id);
            $('#comment_content').attr('placeholder', '{{ __("Replying to") }} ' + parent_name + '..');
            $('html').animate({ scrollTop: $('#comment_content').offset().top - 200 }, 200);
        });

        $(document).on('click', '#load_more_comment_button', function () {
            var el = $(this), commentData = $('#comment_data'), items = commentData.attr('data-items');
            el.text('{{ __("Loading...") }}');
            $.ajax({
                url: '{{ theme_blog_load_comments_url() }}', method: 'POST',
                data: { id: '{{ $post->id() }}', _token: '{{ theme_csrf() }}', items: items },
                success: function (data) {
                    commentData.attr('data-items', parseInt(items) + 5);
                    commentData.append(data.markup);
                    el.text(data.blogComments.length === 0 ? '{{ __("No More Comment Found") }}' : '{{ __("Load More") }}');
                }
            });
        });
    });
})(jQuery);
</script>
<x-custom-js.ajax-login/>
@endsection
