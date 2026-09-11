@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div class="tz-page-banner">
    <div class="container tz-page-banner-content">
        <h1>{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h1>
        <div class="tz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right sep"></i>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <i class="mdi mdi-chevron-right sep"></i>
            <span class="current">{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <article style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);overflow:hidden;">

                @if($post->has_image())
                <div style="width:100%;max-height:420px;overflow:hidden;">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                @endif

                <div style="padding:28px;">
                    @if($post->category())
                        <span style="display:inline-block;background:var(--tz-blue);color:#fff;font-size:10px;font-weight:700;padding:3px 12px;border-radius:var(--tz-radius-sm);margin-bottom:12px;">
                            <a href="{{ $post->category()->url() }}" style="color:inherit;text-decoration:none;">{{ $post->category()->name() }}</a>
                        </span>
                    @endif

                    <h1 style="font-size:22px;font-weight:800;color:#fff;line-height:1.3;margin-bottom:14px;">{{ $post->title() }}</h1>

                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;font-size:12px;color:var(--tz-muted);margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--tz-border);">
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

                    <div style="font-size:14px;line-height:1.8;color:var(--tz-text);">
                        {!! $post->content() !!}
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:28px;padding-top:16px;border-top:1px solid var(--tz-border);">
                        <div style="display:flex;flex-wrap:wrap;gap:6px;">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);font-size:11px;font-weight:600;color:var(--tz-muted);text-decoration:none;transition:all .2s;"
                                   onmouseover="this.style.background='var(--tz-blue)';this.style.color='#fff';this.style.borderColor='var(--tz-blue)'"
                                   onmouseout="this.style.background='var(--tz-mid)';this.style.color='var(--tz-muted)';this.style.borderColor='var(--tz-border)'">
                                    <i class="mdi mdi-tag-outline"></i> {{ $tag->name() }}
                                </a>
                            @endforeach
                        </div>
                        <ul class="tz-share-list">
                            {!! $post->share_links() !!}
                        </ul>
                    </div>
                </div>
            </article>

            {{-- Comments --}}
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:24px;margin-top:20px;" id="comment-area">
                <div style="font-size:15px;font-weight:700;color:#fff;margin-bottom:20px;">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) <span style="font-size:12px;color:var(--tz-muted);">({{ theme_comments_count() }})</span> @endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div style="display:flex;gap:12px;padding:14px 0;border-bottom:1px solid var(--tz-border);">
                            <div style="flex-shrink:0;width:40px;height:40px;border-radius:50%;overflow:hidden;background:var(--tz-mid);">
                                {!! $comment->author_avatar() !!}
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700;font-size:13px;color:var(--tz-text);">
                                    <a href="javascript:void(0)" class="title" data-parent_name="{{ $comment->author() }}" style="color:inherit;text-decoration:none;">{{ $comment->author() }}</a>
                                </div>
                                <div style="font-size:11px;color:var(--tz-muted);margin-bottom:6px;">{{ $comment->date() }}</div>
                                <p style="font-size:13px;color:var(--tz-text);margin:0 0 8px;">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="btn-replay" style="background:none;border:none;padding:0;cursor:pointer;font-size:12px;color:var(--tz-blue);font-weight:600;" data-comment_id="{{ $comment->id() }}">
                                        <i class="mdi mdi-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif
                                @foreach($comment->replies() as $reply)
                                    <div style="display:flex;gap:10px;margin-top:12px;padding:12px 0 0 16px;border-top:1px solid var(--tz-border);">
                                        <div style="flex-shrink:0;width:32px;height:32px;border-radius:50%;overflow:hidden;background:var(--tz-mid);">
                                            {!! $reply->author_avatar() !!}
                                        </div>
                                        <div style="flex:1;">
                                            <div style="font-weight:700;font-size:12px;color:var(--tz-text);">{{ $reply->author() }}</div>
                                            <div style="font-size:11px;color:var(--tz-muted);margin-bottom:4px;">{{ $reply->date() }}</div>
                                            <p style="font-size:12px;color:var(--tz-text);margin:0;">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(theme_comments()->isNotEmpty())
                    <div style="text-align:center;margin-top:20px;">
                        <button id="load_more_comment_button"
                                style="background:transparent;color:var(--tz-blue);border:1px solid var(--tz-blue);padding:9px 24px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:all .2s;"
                                onmouseover="this.style.background='var(--tz-blue)';this.style.color='#fff'"
                                onmouseout="this.style.background='transparent';this.style.color='var(--tz-blue)'">
                            {{ __('Load More') }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:24px;margin-top:20px;" id="blog-comment-form">
                <div style="font-size:15px;font-weight:700;color:#fff;margin-bottom:20px;">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                @php $inp = 'width:100%;padding:10px 14px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:14px;font-family:var(--tz-font);outline:none;'; @endphp
                <div class="mb-3">
                    <label style="font-size:12px;font-weight:600;color:var(--tz-muted);display:block;margin-bottom:6px;">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" style="{{ $inp }}opacity:.7;" value="{{ theme_current_user()->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label style="font-size:12px;font-weight:600;color:var(--tz-muted);display:block;margin-bottom:6px;">{{ __('Comment') }} *</label>
                    <textarea id="comment_content" style="{{ $inp }}height:100px;resize:vertical;" rows="4" placeholder="{{ __('Write your comment…') }}"></textarea>
                </div>
                <button type="button" id="comment_submit_btn"
                        style="background:var(--tz-blue);color:#fff;border:0;padding:10px 24px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:700;cursor:pointer;font-family:var(--tz-font);display:inline-flex;align-items:center;gap:6px;"
                        onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                    <i class="mdi mdi-send"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:32px;margin-top:20px;text-align:center;">
                <p style="color:var(--tz-muted);margin-bottom:16px;">{{ __('Sign In to leave a comment') }}</p>
                <a href="{{ theme_login_url() }}"
                   style="display:inline-flex;align-items:center;gap:6px;background:var(--tz-blue);color:#fff;border:0;padding:10px 24px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:700;text-decoration:none;">
                    <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;margin-bottom:20px;">
                <div style="font-size:13px;font-weight:700;color:var(--tz-text);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" style="display:flex;gap:0;border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);overflow:hidden;">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" placeholder="{{ __('Search posts…') }}"
                           style="flex:1;padding:9px 12px;border:0;background:var(--tz-mid);color:var(--tz-text);font-size:13px;font-family:var(--tz-font);outline:none;">
                    <button type="submit" style="background:var(--tz-blue);border:0;color:#fff;padding:0 14px;cursor:pointer;font-size:16px;">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </form>
            </div>

            @if(theme_blogs()->isNotEmpty())
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;margin-bottom:20px;">
                <div style="font-size:13px;font-weight:700;color:var(--tz-text);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">{{ __('Recent Posts') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div style="display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--tz-border);">
                    @if($recent->has_image())
                    <div style="width:56px;height:56px;border-radius:var(--tz-radius-sm);overflow:hidden;flex-shrink:0;background:var(--tz-mid);">
                        <a href="{{ $recent->url() }}"><img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}" style="width:100%;height:100%;object-fit:cover;"></a>
                    </div>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <a href="{{ $recent->url() }}" style="font-size:12px;font-weight:600;color:var(--tz-text);text-decoration:none;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                           onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-text)'">{!! $recent->title() !!}</a>
                        <div style="font-size:11px;color:var(--tz-muted);margin-top:3px;"><i class="mdi mdi-calendar"></i> {{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;margin-bottom:20px;">
                <div style="font-size:13px;font-weight:700;color:var(--tz-text);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--tz-border);">
                    <a href="{{ $cat->url() }}" style="font-size:13px;color:var(--tz-muted);text-decoration:none;{{ $post->category()?->id() == $cat->id() ? 'color:var(--tz-blue);font-weight:700;' : '' }}"
                       onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='{{ $post->category()?->id() == $cat->id() ? 'var(--tz-blue)' : 'var(--tz-muted)' }}'">
                        <i class="mdi mdi-chip" style="color:var(--tz-blue);margin-right:4px;"></i>{{ $cat->name() }}
                    </a>
                    <span style="background:var(--tz-blue-glow);color:var(--tz-blue);font-size:10px;font-weight:700;padding:2px 8px;border-radius:var(--tz-radius-sm);">{{ $cat->count() }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;">
                <div style="font-size:13px;font-weight:700;color:var(--tz-text);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}"
                           style="display:inline-block;padding:3px 10px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);font-size:11px;font-weight:600;color:var(--tz-muted);text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.background='var(--tz-blue)';this.style.color='#fff';this.style.borderColor='var(--tz-blue)'"
                           onmouseout="this.style.background='var(--tz-mid)';this.style.color='var(--tz-muted)';this.style.borderColor='var(--tz-border)'">
                            #{{ $tag->name() }}
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
            el.html('<i class="mdi mdi-loading mdi-spin"></i> {{ __('Submitting…') }}');
            $.ajax({
                url: '{{ theme_blog_comment_store_url() }}', method: 'POST',
                data: {
                    _token: '{{ theme_csrf() }}',
                    blog_id:         $('[name=blog_id]').val(),
                    user_id:         $('[name=user_id]').val(),
                    comment_id:      $('[name=comment_id]').val(),
                    commented_by:    $('#commented_by').val(),
                    comment_content: $('#comment_content').val(),
                },
                success: function (data) {
                    $('#comment_content').val(''); $('[name=comment_id]').val('');
                    $('.error-wrap').html('<div style="background:var(--tz-blue-glow);color:var(--tz-blue);border:1px solid var(--tz-blue);border-radius:var(--tz-radius-sm);padding:8px 12px;font-size:13px;margin-bottom:8px;">' + data.msg + '</div>');
                    el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}');
                    location.reload();
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON?.errors ?? {}; var msgs = [];
                    $.each(errors, function (k, v) { msgs.push(v); });
                    $('.error-wrap').html('<div style="background:rgba(255,77,77,.1);color:#ff4d4d;border:1px solid rgba(255,77,77,.3);border-radius:var(--tz-radius-sm);padding:8px 12px;font-size:13px;margin-bottom:8px;">' + msgs.join('<br>') + '</div>');
                    el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}');
                }
            });
        });

        $(document).on('click', '.btn-replay', function () {
            var comment_id  = $(this).data('comment_id');
            var parent_name = $(this).closest('[style*="display:flex"]').find('.title').data('parent_name');
            $('[name=comment_id]').val(comment_id);
            $('#comment_content').attr('placeholder', '{{ __('Replying to') }} ' + parent_name + '…');
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
                    el.text(data.blogComments.length === 0 ? '{{ __('No More Comments') }}' : '{{ __('Load More') }}');
                }
            });
        });

    });
})(jQuery);
</script>
<x-custom-js.ajax-login/>
@endsection
