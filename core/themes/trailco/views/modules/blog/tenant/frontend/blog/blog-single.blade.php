@extends('tenant.frontend.frontend-page-master')

@php $post = theme_post(); @endphp
@section('title') {{ $post->title() }} @endsection
@section('page-title') {{ $post->title() }} @endsection

@section('content')
<div style="background:var(--tr-bark);padding:24px 0;border-bottom:2px solid var(--tr-olive);">
    <div class="container">
        <h1 style="font-size:26px;font-weight:900;color:#fff;margin-bottom:6px;">{{ $post->title() }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
            <a href="{{ theme_home_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <a href="{{ theme_blog_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Blog') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ Str::limit($post->title(), 40) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row g-5">

        {{-- Post Content --}}
        <div class="col-lg-8">
            <article style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;box-shadow:var(--tr-shadow);">
                @if($post->has_image())
                <div style="aspect-ratio:16/6;overflow:hidden;">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                @endif
                <div style="padding:40px;">
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
                        @if($post->category())
                        <span style="background:var(--tr-olive);color:#fff;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;padding:4px 12px;border-radius:4px;">{{ $post->category()->name() }}</span>
                        @endif
                        <span style="font-size:12px;color:var(--tr-stone);"><i class="mdi mdi-calendar-outline"></i> {{ $post->date('F d, Y') }}</span>
                        <span style="font-size:12px;color:var(--tr-stone);"><i class="mdi mdi-comment-outline"></i> {{ theme_comments_count() }} {{ __('comments') }}</span>
                    </div>

                    <h1 style="font-size:26px;font-weight:900;color:var(--tr-bark);margin-bottom:24px;line-height:1.25;">{{ $post->title() }}</h1>

                    <div style="font-size:14px;line-height:1.8;color:#444;">
                        {!! $post->content() !!}
                    </div>

                    {{-- Tags --}}
                    @if($post->has_tags())
                    <div style="margin-top:32px;padding-top:24px;border-top:1px solid var(--tr-border);display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
                        <span style="font-size:12px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.5px;margin-right:4px;"><i class="mdi mdi-tag-outline"></i> {{ __('Tags:') }}</span>
                        @foreach($post->tags() as $tag)
                        <a href="{{ $tag->url() }}" style="background:var(--tr-cream);color:var(--tr-stone);font-size:12px;font-weight:600;padding:4px 12px;border-radius:4px;text-decoration:none;border:1px solid var(--tr-border);">{{ $tag->name() }}</a>
                        @endforeach
                    </div>
                    @endif

                    {{-- Share --}}
                    <div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--tr-border);display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        <span style="font-size:12px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.5px;">{{ __('Share:') }}</span>
                        @php $shareUrl = urlencode(request()->url()); $shareTitle = urlencode($post->title()); @endphp
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" style="width:34px;height:34px;border-radius:50%;background:#1877F2;color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;text-decoration:none;"><i class="mdi mdi-facebook"></i></a>
                        <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}" target="_blank" style="width:34px;height:34px;border-radius:50%;background:#1DA1F2;color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;text-decoration:none;"><i class="mdi mdi-twitter"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $shareUrl }}&title={{ $shareTitle }}" target="_blank" style="width:34px;height:34px;border-radius:50%;background:#0A66C2;color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;text-decoration:none;"><i class="mdi mdi-linkedin"></i></a>
                    </div>
                </div>
            </article>

            {{-- Comments --}}
            <div style="margin-top:40px;">
                <h3 style="font-size:18px;font-weight:800;color:var(--tr-bark);margin-bottom:24px;padding-bottom:12px;border-bottom:2px solid var(--tr-olive);">
                    <i class="mdi mdi-comment-multiple-outline" style="color:var(--tr-olive);margin-right:8px;"></i>{{ __('Comments') }} <span id="comment_count" style="font-size:14px;font-weight:600;color:var(--tr-stone);">({{ theme_comments_count() }})</span>
                </h3>

                <div id="comment_list">
                    @foreach(theme_comments() as $comment)
                    <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:20px;margin-bottom:16px;">
                        <div style="display:flex;align-items:flex-start;gap:14px;">
                            <div style="width:44px;height:44px;border-radius:50%;background:var(--tr-olive);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:900;flex-shrink:0;">{{ strtoupper(substr($comment->name ?? 'U', 0, 1)) }}</div>
                            <div style="flex:1;">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                                    <span style="font-size:14px;font-weight:700;color:var(--tr-bark);">{{ $comment->name }}</span>
                                    <span style="font-size:12px;color:var(--tr-stone);">{{ $comment->created_at?->format('M d, Y') }}</span>
                                </div>
                                <p style="font-size:13px;color:#555;line-height:1.65;margin:0 0 10px;">{{ $comment->comment }}</p>
                                <button onclick="tcShowReply({{ $comment->id }})" style="font-size:12px;color:var(--tr-olive);font-weight:700;background:none;border:none;cursor:pointer;padding:0;"><i class="mdi mdi-reply"></i> {{ __('Reply') }}</button>
                                <div id="reply_form_{{ $comment->id }}" style="display:none;margin-top:14px;">
                                    <form onsubmit="tcSubmitComment(event, {{ $post->id() }}, {{ $comment->id }})">
                                        @csrf
                                        <div style="display:flex;gap:8px;">
                                            <input type="text" name="name" placeholder="{{ __('Your name') }}" required style="flex:1;padding:8px 12px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:13px;outline:none;">
                                            <input type="text" name="comment" placeholder="{{ __('Your reply') }}" required style="flex:2;padding:8px 12px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:13px;outline:none;">
                                            <button type="submit" class="tr-btn tr-btn-primary" style="padding:8px 16px;font-size:12px;">{{ __('Reply') }}</button>
                                        </div>
                                    </form>
                                </div>
                                @if($comment->replies && $comment->replies->count())
                                @foreach($comment->replies as $reply)
                                <div style="background:var(--tr-cream);border-radius:var(--tr-radius);padding:14px;margin-top:12px;border-left:3px solid var(--tr-olive);">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                                        <span style="font-size:13px;font-weight:700;color:var(--tr-bark);">{{ $reply->name }}</span>
                                        <span style="font-size:11px;color:var(--tr-stone);">{{ $reply->created_at?->format('M d, Y') }}</span>
                                    </div>
                                    <p style="font-size:13px;color:#555;margin:0;">{{ $reply->comment }}</p>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div id="load_more_wrap" style="text-align:center;margin:24px 0;">
                    <button id="tc_load_more_btn" class="tr-btn" style="background:transparent;border:1.5px solid var(--tr-olive);color:var(--tr-olive);" onclick="tcLoadMoreComments()">
                        <i class="mdi mdi-refresh"></i> {{ __('Load More Comments') }}
                    </button>
                </div>

                {{-- Add Comment --}}
                <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:32px;margin-top:32px;">
                    <h4 style="font-size:16px;font-weight:800;color:var(--tr-bark);margin-bottom:20px;">{{ __('Leave a Comment') }}</h4>
                    <form onsubmit="tcSubmitComment(event, {{ $post->id() }}, null)">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="name" placeholder="{{ __('Your Name') }}" required style="width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;outline:none;font-family:inherit;" onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" placeholder="{{ __('Email (optional)') }}" style="width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;outline:none;font-family:inherit;" onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                            </div>
                            <div class="col-12">
                                <textarea name="comment" rows="4" placeholder="{{ __('Your comment…') }}" required style="width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;outline:none;font-family:inherit;resize:vertical;" onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="tr-btn tr-btn-primary">
                                    <i class="mdi mdi-send"></i> {{ __('Post Comment') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Search --}}
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:24px;margin-bottom:28px;">
                <h3 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--tr-bark);margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid var(--tr-olive);">{{ __('Search') }}</h3>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;">
                        <input type="text" name="s" placeholder="{{ __('Search posts…') }}" value="{{ request('s') }}" style="flex:1;padding:9px 14px;border:0;outline:none;font-size:13px;background:#fff;">
                        <button type="submit" style="background:var(--tr-olive);color:#fff;border:0;padding:0 16px;font-size:16px;cursor:pointer;"><i class="mdi mdi-magnify"></i></button>
                    </div>
                </form>
            </div>

            {{-- Categories --}}
            @php $cats = theme_blog_categories(); @endphp
            @if($cats && $cats->count())
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:24px;margin-bottom:28px;">
                <h3 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--tr-bark);margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid var(--tr-olive);">{{ __('Categories') }}</h3>
                <ul style="list-style:none;padding:0;margin:0;">
                    @foreach($cats as $cat)
                    <li style="border-bottom:1px solid var(--tr-border);">
                        <a href="{{ $cat->url() }}" style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;font-size:13px;color:var(--tr-bark);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='var(--tr-olive)'" onmouseout="this.style.color='var(--tr-bark)'">
                            <span><i class="mdi mdi-tag-outline" style="color:var(--tr-olive);margin-right:6px;"></i>{{ $cat->name() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @php $tags = theme_blog_tags(); @endphp
            @if($tags && $tags->count())
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:24px;">
                <h3 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--tr-bark);margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid var(--tr-olive);">{{ __('Tags') }}</h3>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach($tags as $tag)
                    <a href="{{ $tag->url() }}" style="background:var(--tr-cream);color:var(--tr-stone);font-size:12px;font-weight:600;padding:5px 12px;border-radius:4px;text-decoration:none;border:1px solid var(--tr-border);">{{ $tag->name() }}</a>
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
var tcCommentPage = 1;
function tcShowReply(id) { var el = document.getElementById('reply_form_' + id); el.style.display = el.style.display === 'none' ? 'block' : 'none'; }
function tcSubmitComment(e, postId, parentId) {
    e.preventDefault();
    var form = e.target;
    var data = { _token: '{{ csrf_token() }}', blog_id: postId, name: form.name.value, comment: form.comment.value };
    if (parentId) data.parent_id = parentId;
    if (form.email) data.email = form.email.value;
    $.ajax({ url: '{{ theme_blog_comment_store_url() }}', type: 'POST', data: data,
        success: function(res) { if (res.type === 'success') { toastr.success(res.msg); form.reset(); location.reload(); } else { toastr.error(res.msg); } },
        error: function() { toastr.error('{{ __("Something went wrong.") }}'); }
    });
}
function tcLoadMoreComments() {
    tcCommentPage++;
    $.ajax({ url: '{{ theme_blog_load_comments_url() }}', data: { blog_id: {{ $post->id() }}, page: tcCommentPage },
        success: function(res) { if (res.markup) { $('#comment_list').append(res.markup); } if (!res.has_more) { $('#load_more_wrap').hide(); } }
    });
}
</script>
@endsection
