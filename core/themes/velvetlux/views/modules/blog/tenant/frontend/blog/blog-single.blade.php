@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Editorial') }}</div>
        <h2 style="font-size:24px;font-weight:400;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:1px;">{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:var(--vl-muted);letter-spacing:1px;text-transform:uppercase;">
            <a href="{{ theme_home_url() }}" style="color:var(--vl-champagne);">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <a href="{{ theme_blog_url() }}" style="color:var(--vl-champagne);">{{ __('Journal') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <span>{{ \Illuminate\Support\Str::words($post->title(), 5) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:48px;padding-bottom:80px;">
    <div class="row g-5">

        <div class="col-lg-8">
            <article style="background:var(--vl-card);border:1px solid var(--vl-border);">
                @if($post->has_image())
                <div style="width:100%;max-height:460px;overflow:hidden;">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                @endif

                <div style="padding:40px;">
                    @if($post->category())
                        <span style="display:inline-block;background:var(--vl-plum);color:var(--vl-champagne-l);font-size:9px;font-weight:400;letter-spacing:3px;text-transform:uppercase;padding:5px 14px;margin-bottom:16px;">
                            <a href="{{ $post->category()->url() }}" style="color:inherit;text-decoration:none;">{{ $post->category()->name() }}</a>
                        </span>
                    @endif

                    <h1 style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:400;color:var(--vl-ivory);line-height:1.3;margin-bottom:20px;letter-spacing:1px;">{{ $post->title() }}</h1>

                    <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;font-size:11px;color:var(--vl-muted);letter-spacing:1px;margin-bottom:28px;padding-bottom:24px;border-bottom:1px solid var(--vl-border);">
                        @if($post->author())<span><i class="mdi mdi-account"></i> {{ $post->author() }}</span>@endif
                        <span><i class="mdi mdi-calendar"></i> {{ $post->date('F d, Y') }}</span>
                        <span><a href="{{ $post->comment_url() }}" style="color:inherit;text-decoration:none;"><i class="mdi mdi-comment-outline"></i> {{ theme_comments_count() }} {{ __('Comments') }}</a></span>
                    </div>

                    <div style="font-size:15px;line-height:1.9;color:var(--vl-muted);">{!! $post->content() !!}</div>

                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-top:36px;padding-top:24px;border-top:1px solid var(--vl-border);">
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}" style="display:inline-block;padding:5px 14px;background:var(--vl-surface);border:1px solid var(--vl-border);font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);text-decoration:none;">{{ $tag->name() }}</a>
                            @endforeach
                        </div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            @php $share_url = urlencode($post->url()); $share_title = urlencode($post->title()); @endphp
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $share_url }}" target="_blank" rel="noopener"
                               style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-muted);font-size:16px;text-decoration:none;transition:all .2s;"
                               onmouseover="this.style.borderColor='var(--vl-champagne)';this.style.color='var(--vl-champagne)'" onmouseout="this.style.borderColor='var(--vl-border)';this.style.color='var(--vl-muted)'">
                                <i class="mdi mdi-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ $share_url }}&text={{ $share_title }}" target="_blank" rel="noopener"
                               style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-muted);font-size:16px;text-decoration:none;transition:all .2s;"
                               onmouseover="this.style.borderColor='var(--vl-champagne)';this.style.color='var(--vl-champagne)'" onmouseout="this.style.borderColor='var(--vl-border)';this.style.color='var(--vl-muted)'">
                                <i class="mdi mdi-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $share_url }}" target="_blank" rel="noopener"
                               style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-muted);font-size:16px;text-decoration:none;transition:all .2s;"
                               onmouseover="this.style.borderColor='var(--vl-champagne)';this.style.color='var(--vl-champagne)'" onmouseout="this.style.borderColor='var(--vl-border)';this.style.color='var(--vl-muted)'">
                                <i class="mdi mdi-linkedin"></i>
                            </a>
                            <a href="https://pinterest.com/pin/create/button/?url={{ $share_url }}&description={{ $share_title }}" target="_blank" rel="noopener"
                               style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-muted);font-size:16px;text-decoration:none;transition:all .2s;"
                               onmouseover="this.style.borderColor='var(--vl-champagne)';this.style.color='var(--vl-champagne)'" onmouseout="this.style.borderColor='var(--vl-border)';this.style.color='var(--vl-muted)'">
                                <i class="mdi mdi-pinterest"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Comments --}}
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:32px;margin-top:24px;" id="comment-area">
                <div style="font-size:11px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:20px;">
                    {{ __('Comments') }} @if(theme_comments_count())({{ theme_comments_count() }})@endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div style="display:flex;gap:14px;padding:16px 0;border-bottom:1px solid var(--vl-border);">
                            <div style="flex-shrink:0;width:44px;height:44px;overflow:hidden;background:var(--vl-surface);">{!! $comment->author_avatar() !!}</div>
                            <div style="flex:1;">
                                <div style="font-size:14px;color:var(--vl-ivory);letter-spacing:.5px;"><a href="javascript:void(0)" class="title" data-parent_name="{{ $comment->author() }}" style="color:inherit;text-decoration:none;">{{ $comment->author() }}</a></div>
                                <div style="font-size:11px;color:var(--vl-muted);letter-spacing:.5px;margin-bottom:8px;">{{ $comment->date() }}</div>
                                <p style="font-size:14px;color:var(--vl-muted);margin:0 0 8px;line-height:1.7;">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="btn-replay" style="background:none;border:none;padding:0;cursor:pointer;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);" data-comment_id="{{ $comment->id() }}">
                                        <i class="mdi mdi-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif
                                @foreach($comment->replies() as $reply)
                                    <div style="display:flex;gap:12px;margin-top:16px;padding-top:16px;border-top:1px dashed var(--vl-border);padding-left:20px;">
                                        <div style="flex-shrink:0;width:36px;height:36px;overflow:hidden;background:var(--vl-surface);">{!! $reply->author_avatar() !!}</div>
                                        <div style="flex:1;">
                                            <div style="font-size:13px;color:var(--vl-ivory);">{{ $reply->author() }}</div>
                                            <div style="font-size:11px;color:var(--vl-muted);margin-bottom:4px;">{{ $reply->date() }}</div>
                                            <p style="font-size:13px;color:var(--vl-muted);margin:0;">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(theme_comments()->isNotEmpty())
                    <div style="text-align:center;margin-top:20px;">
                        <button id="load_more_comment_button" style="background:transparent;border:1px solid var(--vl-border);color:var(--vl-champagne);padding:12px 28px;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer;font-family:inherit;">{{ __('Load More') }}</button>
                    </div>
                @endif
            </div>

            @if(theme_is_logged_in())
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:32px;margin-top:24px;" id="blog-comment-form">
                <div style="font-size:11px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:20px;">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                @php $inp = 'width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;'; $lbl = 'font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-muted);display:block;margin-bottom:8px;'; @endphp
                <div class="mb-3"><label style="{{ $lbl }}">{{ __('Name') }}</label><input type="text" id="commented_by" style="{{ $inp }}background:var(--vl-dark);" value="{{ theme_current_user()->name }}" readonly></div>
                <div class="mb-3"><label style="{{ $lbl }}">{{ __('Comment') }} *</label><textarea id="comment_content" style="{{ $inp }}height:100px;resize:vertical;" rows="4" placeholder="{{ __('Write your comment…') }}"></textarea></div>
                <button type="button" id="comment_submit_btn" style="background:var(--vl-champagne);color:var(--vl-dark);border:0;padding:12px 28px;font-size:10px;letter-spacing:3px;text-transform:uppercase;cursor:pointer;font-family:inherit;">
                    <i class="mdi mdi-send"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:36px;margin-top:24px;text-align:center;">
                <p style="color:var(--vl-muted);margin-bottom:16px;font-size:13px;">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}" style="display:inline-block;background:var(--vl-champagne);color:var(--vl-dark);padding:12px 28px;font-size:10px;letter-spacing:3px;text-transform:uppercase;text-decoration:none;">{{ __('Sign In') }}</a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:28px;margin-bottom:24px;">
                <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:16px;">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" style="display:flex;">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" placeholder="{{ __('Search journal…') }}" style="flex:1;padding:10px 14px;border:1px solid var(--vl-border);border-right:0;background:var(--vl-surface);color:var(--vl-ivory);font-size:13px;font-family:inherit;outline:none;">
                    <button type="submit" style="background:var(--vl-champagne);border:0;color:var(--vl-dark);padding:0 16px;cursor:pointer;font-size:16px;"><i class="mdi mdi-magnify"></i></button>
                </form>
            </div>

            @if(theme_blogs()->isNotEmpty())
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:28px;margin-bottom:24px;">
                <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:16px;">{{ __('Recent Articles') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid rgba(58,36,68,.5);">
                    @if($recent->has_image())
                    <div style="width:60px;height:60px;overflow:hidden;flex-shrink:0;"><a href="{{ $recent->url() }}"><img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}" style="width:100%;height:100%;object-fit:cover;"></a></div>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <a href="{{ $recent->url() }}" style="font-size:13px;color:var(--vl-ivory);text-decoration:none;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;letter-spacing:.3px;">{!! $recent->title() !!}</a>
                        <div style="font-size:11px;color:var(--vl-muted);margin-top:4px;letter-spacing:.5px;"><i class="mdi mdi-calendar"></i> {{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:28px;margin-bottom:24px;">
                <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:16px;">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(58,36,68,.5);">
                    <a href="{{ $cat->url() }}" style="font-size:13px;color:var(--vl-muted);text-decoration:none;letter-spacing:.5px;{{ $post->category()?->id() == $cat->id() ? 'color:var(--vl-champagne);' : '' }}"
                       onmouseover="this.style.color='var(--vl-champagne)'" onmouseout="this.style.color='{{ $post->category()?->id() == $cat->id() ? 'var(--vl-champagne)' : 'var(--vl-muted)' }}'">
                        {{ $cat->name() }}
                    </a>
                    <span style="font-size:11px;color:var(--vl-plum);background:var(--vl-surface);padding:2px 10px;">{{ $cat->count() }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:28px;">
                <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:16px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" style="display:inline-block;padding:5px 12px;background:var(--vl-surface);border:1px solid var(--vl-border);font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.borderColor='var(--vl-champagne)';this.style.color='var(--vl-champagne)'" onmouseout="this.style.borderColor='var(--vl-border)';this.style.color='var(--vl-muted)'">
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
            $.ajax({ url: '{{ theme_blog_comment_store_url() }}', method: 'POST', data: { _token: '{{ theme_csrf() }}', blog_id: $('[name=blog_id]').val(), user_id: $('[name=user_id]').val(), comment_id: $('[name=comment_id]').val(), commented_by: $('#commented_by').val(), comment_content: $('#comment_content').val() },
                success: function (data) { $('#comment_content').val(''); $('[name=comment_id]').val(''); $('.error-wrap').html('<div class="alert alert-success">' + data.msg + '</div>'); el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}'); location.reload(); },
                error: function (xhr) { var errors = xhr.responseJSON?.errors ?? {}; var html = '<div class="alert alert-danger">'; $.each(errors, function (k, v) { html += '<p>' + v + '</p>'; }); html += '</div>'; $('.error-wrap').html(html); el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}'); }
            });
        });
        $(document).on('click', '.btn-replay', function () { var comment_id = $(this).data('comment_id'); var parent_name = $(this).closest('[style*="display:flex"]').find('.title').data('parent_name'); $('[name=comment_id]').val(comment_id); $('#comment_content').attr('placeholder', '{{ __('Replying to') }} ' + parent_name + '..'); $('html').animate({ scrollTop: $('#comment_content').offset().top - 200 }, 200); });
        $(document).on('click', '#load_more_comment_button', function () {
            var el = $(this); el.text('{{ __('Loading...') }}'); var commentData = $('#comment_data'); var items = commentData.attr('data-items');
            $.ajax({ url: '{{ theme_blog_load_comments_url() }}', method: 'POST', data: { id: '{{ $post->id() }}', _token: '{{ theme_csrf() }}', items: items },
                success: function (data) { commentData.attr('data-items', parseInt(items) + 5); commentData.append(data.markup); el.text(data.blogComments.length === 0 ? '{{ __('No More Comment Found') }}' : '{{ __('Load More') }}'); }
            });
        });
    });
})(jQuery);
</script>
<x-custom-js.ajax-login/>
@endsection
