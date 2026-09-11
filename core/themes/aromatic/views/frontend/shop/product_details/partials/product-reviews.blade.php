@php
    $reviews     = $product->reviews ?? collect();
    $total       = $reviews->count();
    $avg         = $total ? round($reviews->avg('rating'), 1) : 0;
    $star_counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    foreach ($reviews as $r) { $star_counts[(int)$r->rating] = ($star_counts[(int)$r->rating] ?? 0) + 1; }
@endphp

@if($total)
<div class="ar-rating-summary">
    <div class="ar-avg-block">
        <div class="ar-avg-score">{{ $avg }}</div>
        <div class="ar-avg-stars">{{ str_repeat('★', (int)round($avg)) }}{{ str_repeat('☆', 5 - (int)round($avg)) }}</div>
        <div class="ar-avg-label">{{ __('out of 5') }} ({{ $total }} {{ __('reviews') }})</div>
    </div>
    <div class="ar-rating-bars">
        @foreach([5, 4, 3, 2, 1] as $star)
            @php $pct = $total ? round($star_counts[$star] / $total * 100) : 0; @endphp
            <div class="ar-rating-row">
                <span class="ar-rating-label">{{ $star }}★</span>
                <div class="ar-rating-bar-wrap"><div class="ar-rating-bar" style="width:{{ $pct }}%;"></div></div>
                <span class="ar-rating-count">{{ $star_counts[$star] }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Existing reviews --}}
<div class="all-reviews">
    @forelse($reviews->take(5) as $review)
    <div class="ar-review-card">
        <div class="ar-review-header">
            <div class="ar-review-avatar">
                {!! render_image_markup_by_attachment_id($review?->user?->image) !!}
            </div>
            <div class="ar-review-meta">
                <span class="ar-review-name">{{ $review?->user?->name }}</span>
                <span class="ar-review-date">{{ $review->created_at?->format('M d, Y') }}</span>
            </div>
            <span class="ar-review-stars">{!! render_star_rating_markup($review->rating) !!}</span>
        </div>
        <p class="ar-review-text">{{ $review->review_text }}</p>
    </div>
    @empty
    @endforelse
</div>

@if($total > 5)
<div class="mt-3">
    <button class="ar-btn ar-btn-outline see-more-review" data-items="5">{{ __('See More') }}</button>
</div>
@endif

@auth('web')
{{-- Review form --}}
<div class="ar-review-form-wrap">
    <h4 class="ar-review-form-title">{{ __('Leave a Review') }}</h4>
    <div class="ar-pd-ratings mb-3">
        <select class="star-rating">
            <option value="5">{{ __('Excellent') }}</option>
            <option value="4">{{ __('Very Good') }}</option>
            <option value="3" selected>{{ __('Average') }}</option>
            <option value="2">{{ __('Poor') }}</option>
            <option value="1">{{ __('Terrible') }}</option>
        </select>
        <input type="hidden" class="rating-count" value="">
    </div>
    <textarea id="review-text" name="review_text" rows="4" class="ar-review-textarea ar-auth-input"
              placeholder="{{ __('Share your experience…') }}"></textarea>
    <div class="mt-3">
        <button id="review-submit-btn" class="ar-btn ar-btn-red">{{ __('Submit Review') }}</button>
    </div>
</div>
@else
<div class="ar-review-login">
    <i class="mdi mdi-account-lock-outline ar-review-login-icon"></i>
    <p class="ar-review-login-msg">{{ __('Sign in to leave a review.') }}</p>
    <a href="{{ theme_login_url() }}" class="ar-btn ar-btn-red">{{ __('Sign In') }}</a>
</div>
@endauth
