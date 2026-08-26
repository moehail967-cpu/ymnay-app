@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('style')
<style>
.ms-share-list { list-style:none;margin:0;padding:0;display:flex;gap:8px;flex-wrap:wrap; }
.ms-share-list li a { display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border:1px solid var(--ms-border);color:var(--ms-muted);font-size:15px;text-decoration:none;transition:all .2s; }
.ms-share-list li a:hover { background:var(--ms-linen);border-color:var(--ms-linen);color:var(--ms-dark); }
</style>
@endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:32px 0 24px;">
    <div class="container">
        <div style="font-size:9px;font-weight:400;letter-spacing:5px;text-transform:uppercase;color:var(--ms-linen);margin-bottom:10px;">{{ __('The Maison Journal') }}</div>
        <h2 style="font-size:18px;font-weight:300;color:var(--ms-dark);margin-bottom:10px;">{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:var(--ms-muted);letter-spacing:.5px;">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen);text-decoration:none;">{{ __('Home') }}</a>
            <span>›</span>
            <a href="{{ theme_blog_url() }}" style="color:var(--ms-linen);text-decoration:none;">{{ __('Journal') }}</a>
            <span>›</span>
            <span>{{ \Illuminate\Support\Str::words($post->title(), 5) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:72px;">
    <div class="row g-5">

        <div class="col-lg-8">
            <article style="background:var(--ms-cream);border:1px solid var(--ms-border);overflow:hidden;">
                @if($post->has_image())
                <div style="width:100%;max-height:460px;overflow:hidden;">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                @endif
                <div style="padding:36px;">
                    @if($post->category())
                        <div style="display:inline-block;background:var(--ms-olive);color:#fff;font-size:9px;font-weight:600;letter-spacing:2.5px;text-transform:uppercase;padding:4px 14px;margin-bottom:16px;">
                            <a href="{{ $post->category()->url() }}" style="color:inherit;text-decoration:none;">{{ $post->category()->name() }}</a>
                        </div>
                    @endif
                    <h1 style="font-size:26px;font-weight:300;color:var(--ms-dark);line-height:1.25;margin-bottom:16px;">{{ $post->title() }}</h1>
                    <div style="display:flex;gap:20px;flex-wrap:wrap;font-size:11px;letter-spacing:.5px;color:var(--ms-muted);margin-bottom:28px;padding-bottom:20px;border-bottom:1px solid var(--ms-border);">
                        @if($post->author()) <span><i class="mdi mdi-account-outline"></i> {{ $post->author() }}</span> @endif
                        <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date('F d, Y') }}</span>
                        <span><a href="{{ $post->comment_url() }}" style="color:inherit;text-decoration:none;"><i class="mdi mdi-comment-outline"></i> {{ theme_comments_count() }} {{ __('Comments') }}</a></span>
                    </div>
                    <div style="font-size:15px;line-height:1.85;color:var(--ms-charcoal);font-weight:300;">{!! $post->content() !!}</div>
                    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:32px;padding-top:20px;border-top:1px solid var(--ms-border);">
                        <div style="display:flex;flex-wrap:wrap;gap:6px;">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}" style="display:inline-block;padding:4px 14px;background:var(--ms-warm);border:1px solid var(--ms-border);font-size:9px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--ms-muted);text-decoration:none;" onmouseover="this.style.background='var(--ms-linen)';this.style.color='var(--ms-dark)'" onmouseout="this.style.background='var(--ms-warm)';this.style.color='var(--ms-muted)'">{{ $tag->name() }}</a>
                            @endforeach
                        </div>
                        <ul class="ms-share-list">{!! $post->share_links() !!}</ul>
                    </div>
                </div>
            </article>

            <div style="background:var(--ms-cream);border:1px solid var(--ms-border);padding:32px;margin-top:20px;" id="comment-area">
                <div style="font-size:9px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--ms-muted);margin-bottom:20px;">{{ __('Comments') }} @if(theme_comments_count()) <span style="color:var(--ms-linen);">({{ theme_comments_count() }})</span> @endif</div>
                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div style="display:flex;gap:14px;padding:16px 0;border-bottom:1px solid var(--ms-border);">
                            <div style="flex-shrink:0;width:40px;height:40px;border-radius:50%;overflow:hidden;background:var(--ms-warm);">{!! $comment->author_avatar() !!}</div>
                            <div style="flex:1;">
                                <div style="font-size:13px;font-weight:600;color:var(--ms-dark);"><a href="javascript:void(0)" class="title" data-parent_name="{{ $comment->author() }}" style="color:inherit;text-decoration:none;">{{ $comment->author() }}</a></div>
                                <div style="font-size:11px;color:var(--ms-muted);margin-bottom:8px;">{{ $comment->date() }}</div>
                                <p style="font-size:14px;font-weight:300;color:var(--ms-charcoal);margin:0 0 8px;">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="btn-replay" style="background:none;border:none;padding:0;cursor:pointer;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:var(--ms-linen);font-weight:600;" data-comment_id="{{ $comment->id() }}">{{ __('Reply') }}</button>
                                @endif
                                @foreach($comment->replies() as $reply)
                                    <div style="display:flex;gap:12px;margin-top:14px;padding:14px 0 0 20px;border-top:1px solid var(--ms-border);">
                                        <div style="flex-shrink:0;width:32px;height:32px;border-radius:50%;overflow:hidden;background:var(--ms-warm);">{!! $reply->author_avatar() !!}</div>
                                        <div style="flex:1;">
                                            <div style="font-size:12px;font-weight:600;color:var(--ms-dark);">{{ $reply->author() }}</div>
                                            <div style="font-size:11px;color:var(--ms-muted);margin-bottom:4px;">{{ $reply->date() }}</div>
                                            <p style="font-size:13px;font-weight:300;color:var(--ms-charcoal);margin:0;">{!! $reply->body() !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(theme_comments()->isNotEmpty())
                    <div style="text-align:center;margin-top:20px;">
                        <button id="load_more_comment_button" style="background:transparent;color:var(--ms-linen);border:1px solid var(--ms-linen);padding:10px 28px;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;cursor:pointer;font-family:inherit;" onmouseover="this.style.background='var(--ms-linen)';this.style.color='var(--ms-dark)'" onmouseout="this.style.background='transparent';this.style.color='var(--ms-linen)'">{{ __('Load More') }}</button>
                    </div>
                @endif
            </div>

            @if(theme_is_logged_in())
            <div style="background:var(--ms-cream);border:1px solid var(--ms-border);padding:32px;margin-top:20px;" id="blog-comment-form">
                <div style="font-size:9px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--ms-muted);margin-bottom:20px;">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                @php $inp = 'width:100%;padding:10px 14px;background:var(--ms-warm);border:1px solid var(--ms-border);color:var(--ms-dark);font-size:14px;font-family:inherit;outline:none;font-weight:300;'; @endphp
                <div class="mb-3"><label style="font-size:9px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--ms-muted);display:block;margin-bottom:8px;">{{ __('Name') }}</label><input type="text" id="commented_by" style="{{ $inp }}opacity:.7;" value="{{ theme_current_user()->name }}" readonly></div>
                <div class="mb-3"><label style="font-size:9px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--ms-muted);display:block;margin-bottom:8px;">{{ __('Comment') }}</label><textarea id="comment_content" style="{{ $inp }}height:100px;resize:vertical;" rows="4" placeholder="{{ __('Share your thoughts…') }}"></textarea></div>
                <button type="button" id="comment_submit_btn" style="background:var(--ms-linen);color:var(--ms-dark);border:0;padding:12px 28px;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;cursor:pointer;font-family:inherit;" onmouseover="this.style.background='var(--ms-linen-d)'" onmouseout="this.style.background='var(--ms-linen)'">{{ __('Post Comment') }}</button>
            </div>
            @else
            <div style="background:var(--ms-cream);border:1px solid var(--ms-border);padding:32px;margin-top:20px;text-align:center;">
                <p style="color:var(--ms-muted);margin-bottom:16px;font-size:14px;font-weight:300;">{{ __('Sign in to leave a comment') }}</p>
                <a href="{{ theme_login_url() }}" style="display:inline-block;background:var(--ms-linen);color:var(--ms-dark);padding:12px 28px;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;text-decoration:none;">{{ __('Sign In') }}</a>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div style="background:var(--ms-cream);border:1px solid var(--ms-border);padding:24px;margin-bottom:20px;">
                <div style="font-size:9px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--ms-muted);margin-bottom:16px;">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" style="display:flex;overflow:hidden;border:1px solid var(--ms-border);">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" placeholder="{{ __('Search the journal…') }}" style="flex:1;padding:9px 12px;border:0;background:var(--ms-warm);color:var(--ms-dark);font-size:13px;font-family:inherit;outline:none;font-weight:300;">
                    <button type="submit" style="background:var(--ms-linen);border:0;color:var(--ms-dark);padding:0 14px;cursor:pointer;font-size:16px;"><i class="mdi mdi-magnify"></i></button>
                </form>
            </div>

            @if(theme_blogs()->isNotEmpty())
            <div style="background:var(--ms-cream);border:1px solid var(--ms-border);padding:24px;margin-bottom:20px;">
                <div style="font-size:9px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--ms-muted);margin-bottom:16px;">{{ __('Recent Posts') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--ms-border);">
                    @if($recent->has_image())<div style="width:56px;height:56px;overflow:hidden;flex-shrink:0;background:var(--ms-warm);"><a href="{{ $recent->url() }}"><img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}" style="width:100%;height:100%;object-fit:cover;"></a></div>@endif
                    <div style="flex:1;min-width:0;">
                        <a href="{{ $recent->url() }}" style="font-size:13px;font-weight:400;color:var(--ms-dark);text-decoration:none;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" onmouseover="this.style.color='var(--ms-olive)'" onmouseout="this.style.color='var(--ms-dark)'">{!! $recent->title() !!}</a>
                        <div style="font-size:10px;color:var(--ms-muted);margin-top:3px;">{{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:var(--ms-cream);border:1px solid var(--ms-border);padding:24px;margin-bottom:20px;">
                <div style="font-size:9px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--ms-muted);margin-bottom:16px;">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--ms-border);">
                    <a href="{{ $cat->url() }}" style="font-size:13px;font-weight:300;color:var(--ms-charcoal);text-decoration:none;" onmouseover="this.style.color='var(--ms-olive)'" onmouseout="this.style.color='var(--ms-charcoal)'">{{ $cat->name() }}</a>
                    <span style="background:var(--ms-surface);color:var(--ms-muted);font-size:10px;font-weight:600;padding:2px 10px;">{{ $cat->count() }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div style="background:var(--ms-cream);border:1px solid var(--ms-border);padding:24px;">
                <div style="font-size:9px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--ms-muted);margin-bottom:16px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" style="display:inline-block;padding:5px 12px;background:var(--ms-warm);border:1px solid var(--ms-border);font-size:9px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--ms-muted);text-decoration:none;" onmouseover="this.style.background='var(--ms-linen)';this.style.color='var(--ms-dark)'" onmouseout="this.style.background='var(--ms-warm)';this.style.color='var(--ms-muted)'">{{ $tag->name() }}</a>
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
            var el = $(this); el.text('{{ __('Submitting…') }}');
            $.ajax({
                url: '{{ theme_blog_comment_store_url() }}', method: 'POST',
                data: { _token: '{{ theme_csrf() }}', blog_id: $('[name=blog_id]').val(), user_id: $('[name=user_id]').val(), comment_id: $('[name=comment_id]').val(), commented_by: $('#commented_by').val(), comment_content: $('#comment_content').val() },
                success: function (data) { $('#comment_content').val(''); $('[name=comment_id]').val(''); $('.error-wrap').html('<div style="padding:10px;font-size:13px;color:var(--ms-olive);">' + data.msg + '</div>'); el.text('{{ __('Post Comment') }}'); location.reload(); },
                error: function (xhr) { var msgs = []; $.each(xhr.responseJSON?.errors ?? {}, function (k, v) { msgs.push(v); }); $('.error-wrap').html('<div style="padding:10px;font-size:13px;color:#c0392b;">' + msgs.join('<br>') + '</div>'); el.text('{{ __('Post Comment') }}'); }
            });
        });
        $(document).on('click', '.btn-replay', function () { var id = $(this).data('comment_id'); var name = $(this).closest('[style*="display:flex"]').find('.title').data('parent_name'); $('[name=comment_id]').val(id); $('#comment_content').attr('placeholder', '{{ __('Replying to') }} ' + name + '…'); $('html').animate({ scrollTop: $('#comment_content').offset().top - 200 }, 200); });
        $(document).on('click', '#load_more_comment_button', function () {
            var el = $(this); el.text('{{ __('Loading…') }}');
            var commentData = $('#comment_data'); var items = commentData.attr('data-items');
            $.ajax({ url: '{{ theme_blog_load_comments_url() }}', method: 'POST', data: { id: '{{ $post->id() }}', _token: '{{ theme_csrf() }}', items: items }, success: function (data) { commentData.attr('data-items', parseInt(items) + 5); commentData.append(data.markup); el.text(data.blogComments.length === 0 ? '{{ __('No More Comments') }}' : '{{ __('Load More') }}'); } });
        });
    });
})(jQuery);
</script>
<x-custom-js.ajax-login/>
@endsection
