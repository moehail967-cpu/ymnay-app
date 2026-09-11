@php
    $reviews     = $product->reviews ?? collect();
    $total       = $reviews->count();
    $avg         = $total ? round($reviews->avg('rating'), 1) : 0;
    $star_counts = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
    foreach ($reviews as $r) { $star_counts[(int)$r->rating] = ($star_counts[(int)$r->rating] ?? 0) + 1; }
@endphp

@if($total)
<div class="bp-rating-summary">
    <div class="bp-avg-block">
        <div class="bp-avg-score">{{ $avg }}</div>
        <div class="bp-avg-stars">
            @for($s = 1; $s <= 5; $s++)
                <i class="{{ $s <= round($avg) ? 'las la-star' : 'lar la-star' }}"></i>
            @endfor
        </div>
        <div class="bp-avg-label">{{ __('out of 5') }} ({{ $total }} {{ __('reviews') }})</div>
    </div>
    <div class="bp-rating-bars">
        @foreach([5,4,3,2,1] as $star)
            @php $pct = $total ? round($star_counts[$star] / $total * 100) : 0; @endphp
            <div class="bp-rating-row">
                <span class="bp-rating-label">{{ $star }}<i class="las la-star"></i></span>
                <div class="bp-rating-bar-wrap"><div class="bp-rating-bar" style="width:{{ $pct }}%;"></div></div>
                <span class="bp-rating-count">{{ $star_counts[$star] }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Reviews list --}}
<div class="all-reviews">
    @forelse($reviews->take(5) as $review)
    <div class="bp-review-card">
        <div class="bp-review-header">
            <div class="bp-review-avatar">
                {!! render_image_markup_by_attachment_id($review?->user?->image) !!}
            </div>
            <div class="bp-review-meta">
                <span class="bp-review-name">{{ $review?->user?->name }}</span>
                <span class="bp-review-date">{{ $review->created_at?->format('M d, Y') }}</span>
            </div>
            <span class="bp-review-stars">
                @for($s = 1; $s <= 5; $s++)
                    <i class="{{ $s <= $review->rating ? 'las la-star' : 'lar la-star' }}" style="color:#f59e0b;font-size:13px;"></i>
                @endfor
            </span>
        </div>
        <p class="bp-review-text">{{ $review->review_text }}</p>
    </div>
    @empty
    @endforelse
</div>

@if($total > 5)
<div class="bp-review-more">
    <button class="bp-btn bp-btn-outline see-more-review" data-items="5">{{ __('See More') }}</button>
</div>
@endif

@auth('web')
{{-- Review form --}}
<div class="bp-review-form-wrap">
    <h4 class="bp-review-form-title">{{ __('Leave a Review') }}</h4>
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
    <textarea id="review-text" name="review_text" rows="4" class="bp-review-textarea bp-input"
              placeholder="{{ __('Share your experience…') }}"></textarea>
    <div class="bp-review-submit-wrap">
        <button id="review-submit-btn" class="bp-btn bp-btn-green">{{ __('Submit Review') }}</button>
    </div>
</div>
@else
<div class="bp-review-login">
    <i class="las la-lock bp-review-login-icon"></i>
    <p class="bp-review-login-msg">{{ __('Sign in to leave a review.') }}</p>
    <a href="{{ theme_login_url() }}" class="bp-btn bp-btn-green">{{ __('Sign In') }}</a>
</div>
@endauth
