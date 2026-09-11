@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_post()->title() }} @endsection
@section('page-title') {{ theme_post()->title() }} @endsection

@section('meta-data')
    {!! render_page_meta_data($blog_post) !!}
@endsection

@section('content')
@php $post = theme_post(); @endphp

<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:22px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">{{ \Illuminate\Support\Str::words($post->title(), 8) }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <a href="{{ theme_blog_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Blog') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{{ \Illuminate\Support\Str::words($post->title(), 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Article --}}
        <div class="col-lg-8">
            <article style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);">

                @if($post->has_image())
                <div style="width:100%;max-height:420px;overflow:hidden;">
                    <img src="{{ $post->image_url('full') }}" alt="{{ $post->title() }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                @endif

                <div style="padding:32px;">
                    @if($post->category())
                        <span style="display:inline-block;background:var(--gl-gold);color:#fff;font-size:10px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;padding:4px 14px;border-radius:50px;margin-bottom:14px;">
                            <a href="{{ $post->category()->url() }}" style="color:inherit;text-decoration:none;">{{ $post->category()->name() }}</a>
                        </span>
                    @endif

                    <h1 style="font-size:26px;font-weight:300;color:var(--gl-dark);line-height:1.3;margin-bottom:16px;letter-spacing:-.3px;">{{ $post->title() }}</h1>

                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;font-size:12px;color:var(--gl-muted);margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--gl-border);">
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

                    <div style="font-size:15px;line-height:1.8;color:var(--gl-dark);">
                        {!! $post->content() !!}
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:32px;padding-top:20px;border-top:1px solid var(--gl-border);">
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            @foreach($post->tags() as $tag)
                                <a href="{{ $tag->url() }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:5px 14px;background:var(--gl-gold-pale);border:1.5px solid var(--gl-border);border-radius:50px;font-size:11px;font-weight:600;color:var(--gl-gold);text-decoration:none;transition:all .2s;"
                                   onmouseover="this.style.background='var(--gl-gold)';this.style.color='#fff'" onmouseout="this.style.background='var(--gl-gold-pale)';this.style.color='var(--gl-gold)'">
                                    <i class="mdi mdi-tag-outline"></i> {{ $tag->name() }}
                                </a>
                            @endforeach
                        </div>
                        @php
                            $share_url   = urlencode($post->url());
                            $share_title = str_replace(' ', '%20', $post->title());
                        @endphp
                        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                            <span style="font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gl-muted);">{{ __('Share:') }}</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $share_url }}" target="_blank" rel="noopener noreferrer"
                               style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:#1877F2;color:#fff;font-size:15px;text-decoration:none;transition:opacity .2s;"
                               onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                                <i class="mdi mdi-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ $share_url }}&text={{ $share_title }}" target="_blank" rel="noopener noreferrer"
                               style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:#1DA1F2;color:#fff;font-size:15px;text-decoration:none;transition:opacity .2s;"
                               onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                                <i class="mdi mdi-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $share_url }}" target="_blank" rel="noopener noreferrer"
                               style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:#0A66C2;color:#fff;font-size:15px;text-decoration:none;transition:opacity .2s;"
                               onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                                <i class="mdi mdi-linkedin"></i>
                            </a>
                            <a href="https://www.pinterest.com/pin/create/button/?url={{ $share_url }}" target="_blank" rel="noopener noreferrer"
                               style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:#E60023;color:#fff;font-size:15px;text-decoration:none;transition:opacity .2s;"
                               onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                                <i class="mdi mdi-pinterest"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Comments --}}
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:28px;margin-top:24px;box-shadow:var(--gl-shadow);" id="comment-area">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:20px;">
                    {{ __('Comments') }}
                    @if(theme_comments_count()) <span style="color:var(--gl-gold);">({{ theme_comments_count() }})</span> @endif
                </div>

                <div id="comment_data" data-items="{{ theme_comments()->count() }}">
                    @foreach(theme_comments() as $comment)
                        <div style="display:flex;gap:14px;padding:16px 0;border-bottom:1px dashed var(--gl-border);">
                            <div style="flex-shrink:0;width:44px;height:44px;border-radius:50%;overflow:hidden;background:var(--gl-blush);">
                                {!! $comment->author_avatar() !!}
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700;font-size:14px;color:var(--gl-dark);">
                                    <a href="javascript:void(0)" class="title" data-parent_name="{{ $comment->author() }}" style="color:inherit;text-decoration:none;">{{ $comment->author() }}</a>
                                </div>
                                <div style="font-size:11px;color:var(--gl-muted);margin-bottom:6px;">{{ $comment->date() }}</div>
                                <p style="font-size:14px;color:var(--gl-dark);margin:0 0 8px;">{!! $comment->body() !!}</p>
                                @if($comment->can_reply())
                                    <button class="btn-replay" style="background:none;border:none;padding:0;cursor:pointer;font-size:12px;color:var(--gl-gold);font-weight:600;letter-spacing:.3px;" data-comment_id="{{ $comment->id() }}">
                                        <i class="mdi mdi-reply"></i> {{ __('Reply') }}
                                    </button>
                                @endif
                                @foreach($comment->replies() as $reply)
                                    <div style="display:flex;gap:12px;margin-top:14px;padding:14px 0 0 20px;border-top:1px dashed var(--gl-border);">
                                        <div style="flex-shrink:0;width:36px;height:36px;border-radius:50%;overflow:hidden;background:var(--gl-blush);">
                                            {!! $reply->author_avatar() !!}
                                        </div>
                                        <div style="flex:1;">
                                            <div style="font-weight:700;font-size:13px;color:var(--gl-dark);">{{ $reply->author() }}</div>
                                            <div style="font-size:11px;color:var(--gl-muted);margin-bottom:4px;">{{ $reply->date() }}</div>
                                            <p style="font-size:13px;color:var(--gl-dark);margin:0;">{!! $reply->body() !!}</p>
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
                                style="padding:10px 28px;background:transparent;color:var(--gl-dark);border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:all .2s;"
                                onmouseover="this.style.borderColor='var(--gl-dark)'" onmouseout="this.style.borderColor='var(--gl-border)'">
                            {{ __('Load More') }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- Comment Form --}}
            @if(theme_is_logged_in())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:28px;margin-top:24px;box-shadow:var(--gl-shadow);" id="blog-comment-form">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:20px;">{{ __('Leave a Comment') }}</div>
                <input type="hidden" name="blog_id" value="{{ $post->id() }}">
                <input type="hidden" name="user_id" value="{{ theme_auth()->id() }}">
                <input type="hidden" name="comment_id" value="">
                <div class="error-wrap mb-3"></div>
                @php $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;'; @endphp
                <div class="mb-3">
                    <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Name') }}</label>
                    <input type="text" id="commented_by" style="{{ $inp }}background:var(--gl-gold-pale);" value="{{ theme_current_user()->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Comment') }} <span style="color:var(--gl-gold);">*</span></label>
                    <textarea id="comment_content" style="{{ $inp }}height:100px;resize:vertical;" rows="4" placeholder="{{ __('Share your skincare thoughts…') }}"
                              onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'"></textarea>
                </div>
                <button type="button" id="comment_submit_btn"
                        style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                    <i class="mdi mdi-send"></i> {{ __('Post Comment') }}
                </button>
            </div>
            @else
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:32px;margin-top:24px;text-align:center;box-shadow:var(--gl-shadow);">
                <p style="color:var(--gl-muted);margin-bottom:16px;">{{ __('Sign In To Leave Your Comment') }}</p>
                <a href="{{ theme_login_url() }}"
                   style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
                   onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                    <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;">
                        <input type="text" name="search" placeholder="{{ __('Search articles…') }}"
                               style="flex:1;padding:9px 14px;border:0;font-size:13px;font-family:inherit;outline:none;">
                        <button type="submit" style="background:var(--gl-dark);border:0;color:#fff;padding:0 16px;cursor:pointer;font-size:16px;transition:background .2s;"
                                onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </form>
            </div>

            @if(theme_blogs()->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Recent Posts') }}</div>
                @foreach(theme_blogs()->take(4) as $recent)
                <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px dashed var(--gl-border);">
                    @if($recent->has_image())
                    <div style="width:64px;height:64px;border-radius:var(--gl-radius);overflow:hidden;flex-shrink:0;background:var(--gl-blush);">
                        <a href="{{ $recent->url() }}"><img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}" style="width:100%;height:100%;object-fit:cover;"></a>
                    </div>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <a href="{{ $recent->url() }}" style="font-size:13px;font-weight:600;color:var(--gl-dark);text-decoration:none;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                           onmouseover="this.style.color='var(--gl-gold)'" onmouseout="this.style.color='var(--gl-dark)'">{!! $recent->title() !!}</a>
                        <div style="font-size:11px;color:var(--gl-muted);margin-top:4px;"><i class="mdi mdi-calendar"></i> {{ $recent->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px dashed var(--gl-border);">
                    <a href="{{ $cat->url() }}" style="font-size:13px;color:var(--gl-dark);text-decoration:none;{{ $post->category()?->id() == $cat->id() ? 'font-weight:700;color:var(--gl-gold);' : '' }}"
                       onmouseover="this.style.color='var(--gl-gold)'" onmouseout="this.style.color='{{ $post->category()?->id() == $cat->id() ? 'var(--gl-gold)' : 'var(--gl-dark)' }}'">
                        {{ $cat->name() }}
                    </a>
                    <span style="background:var(--gl-gold-pale);color:var(--gl-gold);font-size:10px;font-weight:700;padding:2px 10px;border-radius:50px;">{{ $cat->count() }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:5px 14px;background:var(--gl-gold-pale);border:1.5px solid var(--gl-border);border-radius:50px;font-size:11px;font-weight:600;color:var(--gl-muted);text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.background='var(--gl-gold)';this.style.color='#fff';this.style.borderColor='var(--gl-gold)'"
                           onmouseout="this.style.background='var(--gl-gold-pale)';this.style.color='var(--gl-muted)';this.style.borderColor='var(--gl-border)'">
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
                    $('.error-wrap').html('<div class="alert alert-success">' + data.msg + '</div>');
                    el.html('<i class="mdi mdi-send"></i> {{ __('Post Comment') }}');
                    location.reload();
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON?.errors ?? {}; var html = '<div class="alert alert-danger">';
                    $.each(errors, function (k, v) { html += '<p>' + v + '</p>'; }); html += '</div>';
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
