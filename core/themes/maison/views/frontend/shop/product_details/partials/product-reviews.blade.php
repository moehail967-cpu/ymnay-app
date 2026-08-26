{{-- Maison: product reviews tab --}}
@php
    $reviews      = $product->reviews ?? collect();
    $review_count = $reviews->count();
    $avg_rating   = $review_count ? round($reviews->avg('rating'), 1) : 0;
@endphp

{{-- Rating Summary --}}
@if($review_count > 0)
<div style="display:flex;align-items:center;gap:14px;margin-bottom:28px;padding:20px;background:var(--ms-warm);border-radius:var(--ms-radius);border:1px solid var(--ms-border);">
    <div style="text-align:center;min-width:70px;">
        <div style="font-size:36px;font-weight:300;color:var(--ms-dark);line-height:1;">{{ number_format($avg_rating, 1) }}</div>
        <div style="font-size:10px;color:var(--ms-muted);margin-top:4px;letter-spacing:.06em;text-transform:uppercase;">{{ __('out of 5') }}</div>
    </div>
    <div style="border-left:1px solid var(--ms-border);padding-left:14px;">
        <div style="display:flex;gap:3px;margin-bottom:6px;">
            @for($i = 1; $i <= 5; $i++)
                <i class="las {{ $i <= $avg_rating ? 'la-star' : ($i - $avg_rating < 1 ? 'la-star-half-alt' : 'la-star') }}"
                   style="font-size:18px;color:{{ $i <= $avg_rating ? '#C8A45A' : 'var(--ms-border)' }};"></i>
            @endfor
        </div>
        <div style="font-size:12px;color:var(--ms-muted);">{{ $review_count }} {{ __('review(s)') }}</div>
    </div>
</div>
@endif

{{-- Reviews List --}}
<div class="all-reviews" style="display:flex;flex-direction:column;gap:16px;margin-bottom:32px;">
    @forelse($reviews->take(5) as $review)
    <div style="border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:18px;background:#fff;">
        <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
            {{-- Avatar --}}
            <div style="width:40px;height:40px;border-radius:50%;background:var(--ms-linen);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#fff;flex-shrink:0;">
                {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;margin-bottom:4px;">
                    <div style="font-weight:600;font-size:14px;color:var(--ms-dark);">
                        {{ $review->user?->name ?? __('Anonymous') }}
                    </div>
                    <div style="font-size:11px;color:var(--ms-muted);">
                        {{ $review->created_at?->format('d M Y') }}
                    </div>
                </div>
                <div style="display:flex;gap:2px;margin-bottom:8px;">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="las {{ $i <= $review->rating ? 'la-star' : 'la-star' }}"
                           style="font-size:14px;color:{{ $i <= $review->rating ? '#C8A45A' : 'var(--ms-border)' }};"></i>
                    @endfor
                </div>
            </div>
        </div>
        <p style="font-size:14px;color:var(--ms-charcoal);line-height:1.7;margin:0;">{{ $review->review_text }}</p>
    </div>
    @empty
    <div style="text-align:center;padding:40px;border:1px dashed var(--ms-border);border-radius:var(--ms-radius);background:#fff;">
        <i class="mdi mdi-comment-outline" style="font-size:36px;color:var(--ms-border);display:block;margin-bottom:10px;"></i>
        <p style="color:var(--ms-muted);font-size:14px;">{{ __('No reviews yet. Be the first to share your experience.') }}</p>
    </div>
    @endforelse
</div>

@if($review_count > 5)
<div style="text-align:center;margin-bottom:28px;">
    <button class="ms-btn-border see-more-review" data-items="5">
        <i class="mdi mdi-chevron-down" style="margin-right:4px;"></i>
        {{ __('See More Reviews') }}
    </button>
</div>
@endif

{{-- Review Form --}}
@auth('web')
<div style="background:var(--ms-warm);border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:24px;">
    <div style="font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:18px;">
        {{ __('Write a Review') }}
    </div>

    {{-- Star Rating --}}
    <div style="margin-bottom:16px;">
        <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:10px;">
            {{ __('Your Rating') }}
        </div>
        <div class="ratings">
            <select class="star-rating">
                <option value="5">{{ __('Excellent') }}</option>
                <option value="4">{{ __('Very Good') }}</option>
                <option value="3" selected>{{ __('Average') }}</option>
                <option value="2">{{ __('Poor') }}</option>
                <option value="1">{{ __('Terrible') }}</option>
            </select>
            <input type="hidden" class="rating-count" value="">
        </div>
    </div>

    <div style="margin-bottom:16px;">
        <textarea id="review-text" rows="4"
                  style="width:100%;background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);color:var(--ms-dark);padding:12px 16px;font-size:14px;font-family:inherit;outline:none;resize:vertical;transition:border-color .2s;"
                  placeholder="{{ __('Share your thoughts about this product…') }}"
                  onfocus="this.style.borderColor='var(--ms-linen)'"
                  onblur="this.style.borderColor='var(--ms-border)'"></textarea>
    </div>

    <button id="review-submit-btn"
            data-product="{{ $product->id }}"
            class="ms-btn-dark">
        <i class="mdi mdi-send-outline" style="margin-right:6px;"></i>
        {{ __('Submit Review') }}
    </button>
</div>
@else
<div style="text-align:center;padding:32px;background:var(--ms-warm);border:1px solid var(--ms-border);border-radius:var(--ms-radius);">
    <i class="mdi mdi-account-lock-outline" style="font-size:36px;color:var(--ms-border);display:block;margin-bottom:10px;"></i>
    <p style="color:var(--ms-muted);font-size:14px;margin-bottom:16px;">{{ __('Sign in to leave your review') }}</p>
    <a href="{{ theme_login_url() }}" class="ms-btn-dark" style="display:inline-flex;">
        <i class="mdi mdi-login" style="margin-right:6px;"></i> {{ __('Sign In') }}
    </a>
</div>
@endauth

