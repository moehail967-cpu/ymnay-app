{{-- FreshMart: product reviews tab --}}
@if(Auth::guard('web')->check())
<div class="row g-4">
    <div class="col-lg-5">
        <div class="fm-form-card">
            <div class="fm-form-section-title"><i class="las la-star"></i> {{ __('Leave a Review') }}</div>
            <form>
                <input type="hidden" class="rating-count" value="">
                <div class="mb-3">
                    <label class="fm-label">{{ __('Rating') }}</label>
                    <select class="star-rating fm-select">
                        <option value="5">{{ __('Excellent') }}</option>
                        <option value="4">{{ __('Very Good') }}</option>
                        <option value="3" selected>{{ __('Average') }}</option>
                        <option value="2">{{ __('Poor') }}</option>
                        <option value="1">{{ __('Terrible') }}</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="fm-label">{{ __('Your Review') }}</label>
                    <textarea rows="5" name="review_text" class="fm-input" id="review-text" style="height:auto;resize:vertical;"></textarea>
                </div>
                <button type="submit" id="review-submit-btn" class="fm-btn fm-btn-green">
                    <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="all-reviews">
            @forelse($product->reviews->take(5) ?? [] as $review)
            <div class="d-flex gap-3 mb-4 pb-4" style="border-bottom:1px solid var(--fm-border);">
                <div style="width:44px;height:44px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--fm-border);">
                    {!! render_image_markup_by_attachment_id($review?->user?->image) !!}
                </div>
                <div style="flex:1;">
                    <div style="font-weight:700;color:var(--fm-dark);font-size:14px;">{{ $review?->user?->name }}</div>
                    <div style="margin:3px 0;">{!! render_star_rating_markup($review->rating) !!}</div>
                    <p style="font-size:13px;color:var(--fm-dark);margin:6px 0 4px;">{{ $review->review_text }}</p>
                    <span style="font-size:11px;color:var(--fm-muted);">{{ $review->created_at?->diffForHumans() }}</span>
                </div>
            </div>
            @empty
            <p style="color:var(--fm-muted);font-size:14px;">{{ __('No reviews yet. Be the first!') }}</p>
            @endforelse
        </div>
        @if($product->reviews->count() > 5)
        <button class="fm-btn fm-btn-outline see-more-review" data-items="5">
            {{ __('See More') }}
        </button>
        @endif
    </div>
</div>
@else
<div class="fm-form-card" style="max-width:420px;">
    <div class="fm-form-section-title"><i class="las la-lock"></i> {{ __('Sign in to leave a review') }}</div>
    <a href="{{ theme_login_url() }}" class="fm-btn fm-btn-green">
        <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
    </a>
</div>
@endif
