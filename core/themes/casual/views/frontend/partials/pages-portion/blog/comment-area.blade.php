<div class="comment-area-full-wrapper" data-padding-top="40">
    <div class="user-comment-area">
        <div class="comment-section-title">
            @if($blogCommentCount > 0)
                <h3 class="cs-blog-comment-count">
                    ({{ $blogCommentCount }}) {{ __('Comments') }}
                </h3>
            @endif
        </div>

        <div class="comments-inner mt-4">
            <div class="comments-flex-contents" id="comment_content_div">
                {{ csrf_field() }}
                <div id="comment_data" data-items="5">
                    @include('tenant.frontend.partials.pages-portion.blog.comment-show-data')
                </div>

                @if($blogComments->count() > 4)
                <div class="load_more_div mt-4">
                    <button class="cs-btn-load-more" id="load_more_comment_button">{{ __('Load More') }}</button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="custom-login" data-padding-top="50">
        @if(!auth()->guard('web')->check())
            @include('landlord.frontend.partials.ajax-user-login-markup', ['title' => get_static_option('blog_single_page_login_title_'.get_user_lang().'_text')])
        @endif
    </div>

    @if(auth()->guard('web')->check())
    <div class="comment-form-area" data-padding-top="0">
        <div class="cs-blog-comment-form-title">{{ __('Post Your Comments') }}</div>

        <form action="{{ theme_blog_comment_store_url() }}" class="comment-form" id="blog-comment-form">
            @csrf
            <div class="error-message"></div>
            <input type="hidden" name="comment_id"/>
            <input type="hidden" name="blog_id" id="blog_id" value="{{ $blog_post->id }}">
            <input type="hidden" name="user_id" id="user_id" value="{{ auth()->guard('web')->user()->id }}">
            <input type="hidden" name="commented_by" id="commented_by" value="{{ auth()->guard('web')->user()->name }}">

            <div class="cs-form-group">
                <textarea name="comment_content" id="comment_content" class="cs-dash-input cs-comment-textarea"
                          placeholder="{{ __('Comments') }}" rows="6"></textarea>
            </div>
            <div class="mt-3">
                <button type="submit" class="cs-checkout-btn" id="submitComment">{{ __('Comment') }}</button>
            </div>
        </form>
    </div>
    @endif
</div>
