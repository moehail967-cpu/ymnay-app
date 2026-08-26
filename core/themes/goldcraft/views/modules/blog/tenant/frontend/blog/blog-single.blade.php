@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:20px;font-weight:400;color:var(--gc-dark);margin-bottom:8px;font-style:italic;">{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h2>
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <a href="{{ theme_blog_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Blog') }}</a>
            <span>—</span>
            <span>{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <article style="background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);">

                @if($post->has_image())
                <div style="width:100%;max-height:420px;overflow:hidden;">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                @endif

                <div style="padding:32px;">
                    @if($post->category())
                        <span style="display:inline-block;background:var(--gc-rose);color:#fff;font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 14px;border-radius:3px;margin-bottom:14px;">
                            <a href="{{ $post->category()->url() }}" style="color:inherit;text-decoration:none;">{{ $post->category()->name() }}</a>
                        </span>
                    @endif

                    <h1 style="font-size:28px;font-weight:400;color:var(--gc-dark);line-height:1.3;margin-bottom:16px;font-style:italic;">{{ $post->title() }}</h1>

                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;font-size:12px;color:var(--gc-muted);margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--gc-border);font-style:italic;">
                        @if($post->author())
                            <span><i class="mdi mdi-account" style="font-style:normal;"></i> {{ $post->author() }}</span>
                        @endif
                        <span><i class="mdi mdi-calendar" style="font-style:normal;"></i> {{ $post->date('F d, Y') }}</span>
                        <span>
                            <a href="{{ $post->comment_url() }}" style="color:inherit;text-decoration:none;">
                                <i class="mdi mdi-comment-outline" style="font-style:normal;"></i> {{ theme_comments_count() }} {{ __('Comments') }}
                            </a>
                        </span>
                    </div>

                    <div style="font-size:15px;line-height:1.85;color:var(--gc-dark);">
                        {!! $post->content() !!}
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:32px;padding-top:20px;border-top:1px solid var(--gc-border);">
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:5px 14px;background:var(--gc-warm);border:1.5px solid var(--gc-border);border-radius:3px;font-size:11px;color:var(--gc-muted);text-decoration:none;font-style:italic;transition:all .2s;"
                                   onmouseover="this.style.background='var(--gc-rose)';this.style.color='#fff'" onmouseout="this.style.background='var(--gc-warm)';this.style.color='var(--gc-muted)'">
                                    {{ $tag->name() }}
                                </a>
                            @endforeach
                        </div>
                        <ul style="list-style:none;margin:0;padding:0;display:flex;align-items:center;gap:6px;" class="gc-share-list">
                            {!! $post->share_links() !!}
                        </ul>
                    </div>
                </div>
            </article>

            {{-- Comments --}}
            <div style="background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:28px;margin-top:24px;box-shadow:var(--gc-shadow);" id="comment-area">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:20px;">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) <span style="color:var(--gc-rose);">({{ theme_comments_count() }})</span> @endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div style="display:flex;gap:14px;padding:16px 0;border-bottom:1px dashed var(--gc-border);">
                            <div style="flex-shrink:0;width:44px;height:44px;border-radius:50%;overflow:hidden;background:var(--gc-warm);">
                                {!! $comment->author_avatar() !!}
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700;font-size:14px;color:var(--gc-dark);">
                                    <a href="javascript:void(0)" class="title" data-parent_name="{{ $comment->author() }}" style="color:inherit;text-decoration:none;">{{ $comment->author() }}</a>
                                </div>
                                <div style="font-size:11px;color:var(--gc-muted);margin-bottom:6px;font-style:italic;">{{ $comment->date() }}</div>
                                <p style="font-size:14px;color:var(--gc-dark);margin:0 0 8px;font-style:italic;">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="btn-replay" style="background:none;border:none;padding:0;cursor:pointer;font-size:12px;color:var(--gc-rose);font-style:italic;" data-comment_id="{{ $comment->id() }}">
                                        <i class="mdi mdi-reply" style="font-style:normal;"></i> {{ __('Reply') }}
                                    </button>
                                @endif
                                @foreach($comment->replies() as $reply)
                                    <div style="display:flex;gap:12px;margin-top:14px;padding:14px 0 0 20px;border-top:1px dashed var(--gc-border);">
                                        <div style="flex-shrink:0;width:36px;height:36px;border-radius:50%;overflow:hidden;background:var(--gc-warm);">
                                            {!! $reply->author_avatar() !!}
                                        </div>
                                        <div style="flex:1;">
                                            <div style="font-weight:700;font-size:13px;color:var(--gc-dark);">{{ $reply->author() }}</div>
                                            <div style="font-size:11px;color:var(--gc-muted);margin-bottom:4px;font-style:italic;">{{ $reply->date() }}</div>
                                            <p style="font-size:13px;color:var(--gc-dark);margin:0;font-style:italic;">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(theme_comments()->isNotEmpty())
                    <div style="text-align:center;margin-top:20px;">
                        <button id="load_more_comment_button" class="gc-btn gc-btn-ghost">{{ __('Load More') }}</button>
                    </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div style="background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:28px;margin-top:24px;box-shadow:var(--gc-shadow);" id="blog-comment-form">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:20px;">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                @php $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:14px;font-family:Georgia,serif;outline:none;transition:border-color .2s;'; @endphp
                <div class="mb-3">
                    <label style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:6px;display:block;">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" style="{{ $inp }}background:var(--gc-warm);" value="{{ theme_current_user()->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:6px;display:block;">{{ __('Comment') }} <span style="color:var(--gc-rose);">*</span></label>
                    <textarea id="comment_content" style="{{ $inp }}height:100px;resize:vertical;" rows="4" placeholder="{{ __('Share your thoughts on this piece…') }}"
                              onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'"></textarea>
                </div>
                <button type="button" id="comment_submit_btn" class="gc-btn gc-btn-primary">
                    <i class="mdi mdi-send"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div style="background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:32px;margin-top:24px;text-align:center;box-shadow:var(--gc-shadow);">
                <p style="color:var(--gc-muted);margin-bottom:16px;font-style:italic;">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}" class="gc-btn gc-btn-primary">
                    <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div style="background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:16px;">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" style="display:flex;gap:8px;">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" placeholder="{{ __('Search stories…') }}"
                           style="flex:1;padding:9px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:13px;font-family:Georgia,serif;outline:none;font-style:italic;transition:border-color .2s;"
                           onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                    <button type="submit" style="flex-shrink:0;background:var(--gc-rose);border:0;color:#fff;padding:0 16px;border-radius:var(--gc-radius);cursor:pointer;font-size:16px;transition:background .2s;"
                            onmouseover="this.style.background='var(--gc-rose-d)'" onmouseout="this.style.background='var(--gc-rose)'">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </form>
            </div>

            @if(theme_blogs()->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:16px;">{{ __('Recent Stories') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px dashed var(--gc-border);">
                    @if($recent->has_image())
                    <div style="width:64px;height:64px;border-radius:var(--gc-radius);overflow:hidden;flex-shrink:0;background:var(--gc-warm);">
                        <a href="{{ $recent->url() }}"><img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}" style="width:100%;height:100%;object-fit:cover;"></a>
                    </div>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <a href="{{ $recent->url() }}" style="font-size:13px;color:var(--gc-dark);text-decoration:none;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-style:italic;"
                           onmouseover="this.style.color='var(--gc-rose)'" onmouseout="this.style.color='var(--gc-dark)'">{!! $recent->title() !!}</a>
                        <div style="font-size:11px;color:var(--gc-muted);margin-top:4px;font-style:italic;"><i class="mdi mdi-calendar" style="font-style:normal;"></i> {{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:16px;">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px dashed var(--gc-border);">
                    <a href="{{ $cat->url() }}" style="font-size:13px;color:var(--gc-dark);text-decoration:none;font-style:italic;{{ $post->category()?->id() == $cat->id() ? 'color:var(--gc-rose);' : '' }}"
                       onmouseover="this.style.color='var(--gc-rose)'" onmouseout="this.style.color='{{ $post->category()?->id() == $cat->id() ? 'var(--gc-rose)' : 'var(--gc-dark)' }}'">
                        {{ $cat->name() }}
                    </a>
                    <span style="background:var(--gc-warm);color:var(--gc-rose);font-size:10px;font-weight:700;padding:2px 10px;border-radius:3px;">{{ $cat->count() }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:16px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:5px 14px;background:var(--gc-warm);border:1.5px solid var(--gc-border);border-radius:3px;font-size:11px;color:var(--gc-muted);text-decoration:none;font-style:italic;transition:all .2s;"
                           onmouseover="this.style.background='var(--gc-rose)';this.style.color='#fff';this.style.borderColor='var(--gc-rose)'"
                           onmouseout="this.style.background='var(--gc-warm)';this.style.color='var(--gc-muted)';this.style.borderColor='var(--gc-border)'">
                            {{ $tag->name() }}
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
            var el = $(this); el.text('{{ __('Submitting') }}...');
            $.ajax({ url: '{{ theme_blog_comment_store_url() }}', method: 'POST',
                data: { _token: '{{ theme_csrf() }}', blog_id: $('[name=blog_id]').val(), user_id: $('[name=user_id]').val(), comment_id: $('[name=comment_id]').val(), commented_by: $('#commented_by').val(), comment_content: $('#comment_content').val() },
                success: function (data) { $('#comment_content').val(''); $('[name=comment_id]').val(''); $('.error-wrap').html('<div class="alert alert-success">' + data.msg + '</div>'); el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}'); location.reload(); },
                error: function (xhr) { var errors = xhr.responseJSON?.errors ?? {}; var html = '<div class="alert alert-danger">'; $.each(errors, function (k, v) { html += '<p>' + v + '</p>'; }); html += '</div>'; $('.error-wrap').html(html); el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}'); }
            });
        });
        $(document).on('click', '.btn-replay', function () {
            var comment_id = $(this).data('comment_id'), parent_name = $(this).closest('[style*="display:flex"]').find('.title').data('parent_name');
            $('[name=comment_id]').val(comment_id); $('#comment_content').attr('placeholder', '{{ __('Replying to') }} ' + parent_name + '..');
            $('html').animate({ scrollTop: $('#comment_content').offset().top - 200 }, 200);
        });
        $(document).on('click', '#load_more_comment_button', function () {
            var el = $(this), commentData = $('#comment_data'), items = commentData.attr('data-items');
            el.text('{{ __('Loading...') }}');
            $.ajax({ url: '{{ theme_blog_load_comments_url() }}', method: 'POST', data: { id: '{{ $post->id() }}', _token: '{{ theme_csrf() }}', items: items },
                success: function (data) { commentData.attr('data-items', parseInt(items) + 5); commentData.append(data.markup); el.text(data.blogComments.length === 0 ? '{{ __('No More Comment Found') }}' : '{{ __('Load More') }}'); }
            });
        });
    });
})(jQuery);
</script>
<x-custom-js.ajax-login/>
@endsection
