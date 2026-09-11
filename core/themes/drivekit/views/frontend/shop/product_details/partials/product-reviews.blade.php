<div class="dk-tab-panel">

    {{-- Review form --}}
    @auth
    <div class="dk-review-form">
        <div class="dk-review-form-title"><i class="mdi mdi-comment-edit-outline"></i> {{ __('Leave a Review') }}</div>
        <div class="mb-3">
            <label class="dk-form-label">{{ __('Rating') }}</label>
            <select class="star-rating dk-form-select" style="width:auto;">
                <option value="5">{{ __('Excellent') }} ★★★★★</option>
                <option value="4">{{ __('Very Good') }} ★★★★</option>
                <option value="3" selected>{{ __('Average') }} ★★★</option>
                <option value="2">{{ __('Poor') }} ★★</option>
                <option value="1">{{ __('Terrible') }} ★</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="dk-form-label">{{ __('Your Review') }}</label>
            <textarea id="review-text" class="dk-input" rows="4" placeholder="{{ __('Share your experience with this product...') }}"></textarea>
        </div>
        <button type="button" id="review-submit-btn" class="dk-btn dk-btn-red dk-btn-sm">
            <i class="mdi mdi-send"></i> {{ __('Submit Review') }}
        </button>
    </div>
    @else
    <div class="dk-comment-form locked" style="text-align:center;padding:32px;">
        <i class="mdi mdi-account-lock-outline" style="font-size:40px;color:var(--dk-red);opacity:.4;display:block;margin-bottom:12px;"></i>
        <p style="color:var(--dk-muted);font-size:13px;margin-bottom:16px;">{{ __('Sign In To Leave Your Review') }}</p>
        <a href="{{ theme_login_url() }}" class="dk-btn dk-btn-red dk-btn-sm">
            <i class="mdi mdi-login"></i> {{ __('Sign In') }}
        </a>
    </div>
    @endauth

    {{-- Reviews list --}}
    <div class="all-reviews">
        @forelse($product->reviews->take(5) ?? [] as $review)
        <div class="dk-review-card">
            <div class="dk-review-head">
                <span class="dk-reviewer"><i class="mdi mdi-account-circle-outline"></i> {{ $review?->user?->name }}</span>
                <span class="dk-review-date">{{ $review->created_at?->diffForHumans() }}</span>
            </div>
            <div class="dk-review-stars">{!! render_star_rating_markup($review->rating) !!}</div>
            <p class="dk-review-text">{{ $review->review_text }}</p>
        </div>
        @empty
        <p style="color:var(--dk-muted);font-size:13px;">{{ __('No reviews yet. Be the first to review this product.') }}</p>
        @endforelse
    </div>

    @if(($product->reviews->count() ?? 0) > 5)
    <div class="mt-3">
        <button class="dk-btn dk-btn-ghost dk-btn-sm see-more-review" data-items="5">
            <i class="mdi mdi-chevron-down"></i> {{ __('See More') }}
        </button>
    </div>
    @endif
</div>
