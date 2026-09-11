<div id="reviews" class="mc-pd-tab-content">
    <div class="mc-pd-tab-inner">
        @if(Auth::guard('web')->check())
            <div class="mc-pd-reviews">
                {{-- Leave a Review Form --}}
                <div class="mc-pd-review-form">
                    <h3 class="mc-pd-review-form-title">{{ __('Leave a Review') }}</h3>
                    <form>
                        <input type="hidden" class="rating-count" value="">
                        <div class="ratings mt-3">
                            <select class="star-rating">
                                <option value="5">{{ __('Excellent') }}</option>
                                <option value="4">{{ __('Very Good') }}</option>
                                <option value="3" selected>{{ __('Average') }}</option>
                                <option value="2">{{ __('Poor') }}</option>
                                <option value="1">{{ __('Terrible') }}</option>
                            </select>
                        </div>
                        <div class="mt-3">
                            <textarea rows="5" name="review_text"
                                      class="mc-form-textarea review-text"
                                      id="review-text"
                                      placeholder="{{ __('Write your review here...') }}"></textarea>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" id="review-submit-btn"
                                    class="mc-btn mc-btn-primary">
                                {{ __('Submit Review') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Existing Reviews --}}
                <div class="all-reviews mt-5">
                    @foreach($product->reviews->take(5) ?? [] as $review)
                        <div class="mc-pd-review-item">
                            <div class="mc-pd-reviewer-avatar">
                                {!! render_image_markup_by_attachment_id($review?->user?->image) !!}
                            </div>
                            <div class="mc-pd-reviewer-content">
                                <h5 class="mc-pd-reviewer-name">
                                    <a href="javascript:void(0)">{{ $review?->user?->name }}</a>
                                </h5>
                                {!! render_star_rating_markup($review->rating) !!}
                                <p class="mc-pd-review-text">{{ $review->review_text }}</p>
                                <span class="mc-pd-review-date">{{ $review->created_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <a href="javascript:void(0)" class="mc-btn mc-btn-outline see-more-review" data-items="5">
                        {{ __('See More') }}
                    </a>
                </div>
            </div>
        @else
            <div class="mc-pd-review-login">
                <div class="mc-auth-card" style="max-width:480px;margin:0 auto;">
                    <div style="text-align:center;margin-bottom:20px;">
                        {!! render_image_markup_by_attachment_id('site_logo') !!}
                    </div>
                    <h4>{{ __('Hello! Let us get started') }}</h4>
                    <p style="color:#888;font-size:14px;margin-bottom:16px;">{{ __('Sign in to leave a review.') }}</p>
                    {!! theme_error_msg() !!}
                    {!! theme_flash_msg() !!}
                    <div id="msg-wrapper"></div>
                    <form class="pt-2" action="" method="post" id="login_form_order_page">
                        <div class="mb-3">
                            <input type="text" name="email" class="mc-form-input" placeholder="{{ __('Username') }}">
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="mc-form-input" placeholder="{{ __('Password') }}">
                        </div>
                        <div class="mb-3">
                            <label style="font-size:13px;color:#555;display:flex;align-items:center;gap:8px;">
                                <input type="checkbox" name="remember" style="accent-color:#1A85ED;">
                                {{ __('Keep me signed in') }}
                            </label>
                        </div>
                        <button type="submit" class="mc-btn mc-btn-primary mc-btn-block" id="login_submit_btn">
                            {{ __('Sign In') }}
                        </button>
                        <p style="text-align:center;margin-top:14px;font-size:13px;color:#888;">
                            {{ __('Do not have an account?') }}
                            <a href="{{ theme_register_url() }}" style="color:#1A85ED;font-weight:600;">{{ __('Create') }}</a>
                        </p>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
