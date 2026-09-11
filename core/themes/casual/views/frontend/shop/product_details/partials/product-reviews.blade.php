{{-- Casual: Product reviews tab --}}
@php
    $review_count = $product->reviews->count();
    $avg_rating   = $review_count ? round($product->reviews->avg('rating'), 1) : 0;
    $rating_dist  = [];
    for ($s = 5; $s >= 1; $s--) {
        $cnt = $product->reviews->where('rating', $s)->count();
        $rating_dist[$s] = ['count' => $cnt, 'pct' => $review_count ? round($cnt / $review_count * 100) : 0];
    }
@endphp

{{-- Rating summary --}}
@if($review_count)
<div class="cs-review-summary">
    <div class="cs-review-avg">
        <span class="cs-review-avg-num">{{ $avg_rating }}</span>
        <div class="cs-review-avg-stars">{!! render_star_rating_markup($avg_rating) !!}</div>
        <span class="cs-review-avg-count">{{ $review_count }} {{ __('reviews') }}</span>
    </div>
    <div class="cs-review-bars">
        @foreach($rating_dist as $star => $rd)
        <div class="cs-review-bar-row">
            <span class="cs-review-bar-label">{{ $star }} ★</span>
            <div class="cs-review-bar-track">
                <div class="cs-review-bar-fill" style="width:{{ $rd['pct'] }}%"></div>
            </div>
            <span class="cs-review-bar-cnt">{{ $rd['count'] }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Review list --}}
<div class="all-reviews cs-review-list">
    @foreach($product->reviews->take(5) as $review)
    <div class="cs-review-card">
        <div class="cs-review-avatar">
            {!! render_image_markup_by_attachment_id($review?->user?->image) !!}
        </div>
        <div class="cs-review-body">
            <div class="cs-review-meta">
                <strong class="cs-review-author">{{ $review?->user?->name }}</strong>
                <span class="cs-review-date">{{ $review->created_at?->diffForHumans() }}</span>
            </div>
            <div class="cs-review-stars">{!! render_star_rating_markup($review->rating) !!}</div>
            <p class="cs-review-text">{{ $review->review_text }}</p>
        </div>
    </div>
    @endforeach
</div>

@if($review_count > 5)
<div class="cs-review-more-wrap">
    <a href="javascript:void(0)" class="see-more-review cs-review-more-btn" data-items="5">
        {{ __('See More') }}
    </a>
</div>
@endif

{{-- Write review / login prompt --}}
@auth('web')
<div class="cs-review-form">
    <h4 class="cs-review-form-title">{{ __('Leave a Review') }}</h4>
    <form>
        <input type="hidden" class="rating-count" value="">
        <div class="cs-review-stars-input">
            <select class="star-rating">
                <option value="5">{{ __('Excellent') }}</option>
                <option value="4">{{ __('Very Good') }}</option>
                <option value="3" selected>{{ __('Average') }}</option>
                <option value="2">{{ __('Poor') }}</option>
                <option value="1">{{ __('Terrible') }}</option>
            </select>
        </div>
        <textarea id="review-text" class="cs-review-textarea review-text" rows="5"
                  placeholder="{{ __('Write your review…') }}"></textarea>
        <button type="submit" id="review-submit-btn" class="cs-apply-btn">
            {{ __('Submit Review') }}
        </button>
    </form>
</div>
@else
<div class="cs-review-login-prompt">
    <p>{{ __('Please') }}
        <a href="{{ theme_login_url() }}" class="cs-review-login-link">{{ __('sign in') }}</a>
        {{ __('to write a review.') }}
    </p>
</div>
@endauth
