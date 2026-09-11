@php
    $reviews     = $product->reviews ?? collect();
    $total       = $reviews->count();
    $avg         = $total ? round($reviews->avg('rating'), 1) : 0;
    $star_counts = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
    foreach ($reviews as $r) { $star_counts[(int)$r->rating] = ($star_counts[(int)$r->rating] ?? 0) + 1; }
@endphp

@if($total)
<div class="bk-rating-summary">
    <div class="bk-avg-block">
        <div class="bk-avg-score">{{ $avg }}</div>
        <div class="bk-stars">{{ str_repeat('★', (int)round($avg)) }}{{ str_repeat('☆', 5 - (int)round($avg)) }}</div>
        <div class="bk-avg-label">{{ __('out of 5') }} ({{ $total }} {{ __('reviews') }})</div>
    </div>
    <div class="bk-rating-bars">
        @foreach([5,4,3,2,1] as $star)
        @php $pct = $total ? round($star_counts[$star] / $total * 100) : 0; @endphp
        <div class="bk-rating-row">
            <span class="bk-rating-label">{{ $star }}★</span>
            <div class="bk-rating-bar-wrap"><div class="bk-rating-bar" style="width:{{ $pct }}%;"></div></div>
            <span class="bk-rating-count">{{ $star_counts[$star] }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Existing reviews --}}
@forelse($reviews->take(5) as $review)
<div class="bk-review-card">
    <div class="bk-review-header">
        <div class="bk-review-avatar">
            {!! render_image_markup_by_attachment_id($review?->user?->image) !!}
        </div>
        <div class="bk-review-meta">
            <span class="bk-review-name">{{ $review?->user?->name }}</span>
            <span class="bk-review-date">{{ $review->created_at?->format('M d, Y') }}</span>
        </div>
        <span class="bk-stars">{!! render_star_rating_markup($review->rating) !!}</span>
    </div>
    <p class="bk-review-text">{{ $review->review_text }}</p>
</div>
@empty
@endforelse

@if($total > 5)
<div class="bk-review-more">
    <button class="bk-btn bk-btn-outline bk-btn-sm see-more-review" data-items="5">{{ __('See More') }}</button>
</div>
@endif

@auth('web')
{{-- Review form --}}
<div class="bk-review-form-wrap">
    <h4 class="bk-review-form-title">{{ __('Leave a Review') }}</h4>
    <div class="ratings mb-3">
        <select class="star-rating">
            <option value="5">{{ __('Excellent') }}</option>
            <option value="4">{{ __('Very Good') }}</option>
            <option value="3" selected>{{ __('Average') }}</option>
            <option value="2">{{ __('Poor') }}</option>
            <option value="1">{{ __('Terrible') }}</option>
        </select>
        <input type="hidden" class="rating-count" value="">
    </div>
    <textarea id="review-text" name="review_text" rows="4" class="bk-review-textarea"
              placeholder="{{ __('Share your experience…') }}"></textarea>
    <div class="bk-review-submit-wrap">
        <button id="review-submit-btn" class="bk-btn bk-btn-rose bk-btn-sm">{{ __('Submit Review') }}</button>
    </div>
</div>
@else
<div class="bk-review-login">
    <i class="mdi mdi-account-lock-outline bk-review-login-icon"></i>
    <p class="bk-review-login-msg">{{ __('Sign in to leave a review.') }}</p>
    <a href="{{ theme_login_url() }}" class="bk-btn bk-btn-rose bk-btn-sm">{{ __('Sign In') }}</a>
</div>
@endauth
