@php
    $reviews     = $product->reviews ?? collect();
    $review_count = $reviews->count();
@endphp

<div style="margin-bottom:24px;">
    <h4 style="font-size:16px;font-weight:700;color:#fff;margin-bottom:4px;">{{ __('Customer Reviews') }}</h4>
    <p style="font-size:13px;color:var(--tz-muted);">{{ $review_count }} {{ __('reviews') }}</p>
</div>

<div class="all-reviews">
    @forelse($reviews->take(5) as $review)
    <div style="display:flex;gap:12px;padding:16px 0;border-bottom:1px solid var(--tz-border);">
        <div style="width:40px;height:40px;border-radius:50%;background:var(--tz-blue-glow);border:1px solid var(--tz-blue);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:var(--tz-blue);flex-shrink:0;">
            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
        </div>
        <div style="flex:1;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                <span style="font-size:13px;font-weight:700;color:#fff;">{{ $review->user?->name ?? __('Anonymous') }}</span>
                <span style="font-size:11px;color:var(--tz-muted);">{{ $review->created_at?->format('M d, Y') }}</span>
            </div>
            <div style="display:flex;gap:2px;margin-bottom:6px;">
                @for($i=1;$i<=5;$i++)
                    <i class="{{ $i <= ($review->rating ?? 0) ? 'las la-star' : 'lar la-star' }}" style="font-size:13px;color:{{ $i <= ($review->rating ?? 0) ? '#F59E0B' : 'var(--tz-border)' }};"></i>
                @endfor
            </div>
            <p style="font-size:13px;color:var(--tz-text);margin:0;">{{ $review->review_text }}</p>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:32px;color:var(--tz-muted);">
        <i class="mdi mdi-comment-outline" style="font-size:36px;display:block;margin-bottom:8px;"></i>
        {{ __('No reviews yet. Be the first to review this product.') }}
    </div>
    @endforelse
</div>

@if($review_count > 5)
<div style="text-align:center;margin-top:16px;">
    <button class="see-more-review" data-items="5"
            style="background:transparent;color:var(--tz-blue);border:1px solid var(--tz-blue);padding:9px 20px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:600;cursor:pointer;font-family:var(--tz-font);transition:all .2s;"
            onmouseover="this.style.background='var(--tz-blue)';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='var(--tz-blue)'">
        {{ __('See More') }}
    </button>
</div>
@endif

@auth('web')
<div style="margin-top:28px;background:var(--tz-surface);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;">
    <h5 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">{{ __('Write a Review') }}</h5>
    <div style="margin-bottom:12px;">
        <div style="font-size:12px;color:var(--tz-muted);margin-bottom:6px;">{{ __('Rating') }}</div>
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
    <textarea id="review-text" rows="4"
              style="width:100%;padding:10px 14px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:13px;font-family:var(--tz-font);outline:none;resize:vertical;margin-bottom:12px;"
              placeholder="{{ __('Share your thoughts about this product…') }}"
              onfocus="this.style.borderColor='var(--tz-blue)'" onblur="this.style.borderColor='var(--tz-border)'"></textarea>
    <button id="review-submit-btn"
            style="background:var(--tz-blue);color:#fff;border:0;padding:10px 20px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:background .2s;"
            onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
        {{ __('Submit Review') }}
    </button>
</div>
@endauth
