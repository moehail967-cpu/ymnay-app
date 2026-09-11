{{-- Pharmacy: Reviews tab --}}
@php
    $reviews      = $product->reviews ?? collect();
    $review_count = $reviews->count();
    $avg_rating   = $review_count ? round($reviews->avg('rating'), 1) : 0;
@endphp

@if($review_count > 0)
<div style="display:flex;align-items:center;gap:20px;padding:20px;background:var(--pf-teal-light);border-radius:var(--pf-radius);border:1.5px solid var(--pf-border);margin-bottom:24px;">
    <div style="text-align:center;min-width:72px;">
        <div style="font-size:42px;font-weight:800;color:var(--pf-teal);line-height:1;">{{ number_format($avg_rating, 1) }}</div>
        <div style="font-size:11px;color:var(--pf-muted);margin-top:4px;font-weight:700;">{{ __('out of 5') }}</div>
    </div>
    <div style="border-left:2px solid var(--pf-border);padding-left:20px;">
        <div style="display:flex;gap:4px;margin-bottom:6px;">
            @for($i=1;$i<=5;$i++)
                <i class="las la-star" style="font-size:20px;color:{{ $i <= $avg_rating ? '#FFC107' : 'var(--pf-border)' }};"></i>
            @endfor
        </div>
        <div style="font-size:13px;color:var(--pf-muted);font-weight:600;">{{ $review_count }} {{ __('review(s)') }}</div>
    </div>
</div>
@endif

<div class="all-reviews" style="display:flex;flex-direction:column;gap:14px;margin-bottom:32px;">
    @forelse($reviews->take(5) as $review)
    <div style="background:var(--pf-white);border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);padding:18px;">
        <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:10px;">
            <div style="width:42px;height:42px;border-radius:50%;background:var(--pf-teal);color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;flex-shrink:0;">
                {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
            </div>
            <div style="flex:1;">
                <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:4px;margin-bottom:4px;">
                    <span style="font-weight:700;font-size:14px;color:var(--pf-dark);">{{ $review->user?->name ?? __('Anonymous') }}</span>
                    <span style="font-size:11px;color:var(--pf-muted);">{{ $review->created_at?->format('d M Y') }}</span>
                </div>
                <div style="display:flex;gap:2px;margin-bottom:8px;">
                    @for($i=1;$i<=5;$i++)
                        <i class="las la-star" style="font-size:14px;color:{{ $i <= $review->rating ? '#FFC107' : 'var(--pf-border)' }};"></i>
                    @endfor
                </div>
            </div>
        </div>
        <p style="font-size:14px;color:var(--pf-muted);line-height:1.7;margin:0;">{{ $review->review_text }}</p>
    </div>
    @empty
    <div style="text-align:center;padding:48px;border:2px dashed var(--pf-border);border-radius:var(--pf-radius);background:var(--pf-bg);">
        <i class="las la-comment-alt" style="font-size:40px;color:var(--pf-border);display:block;margin-bottom:12px;"></i>
        <p style="color:var(--pf-muted);font-size:14px;margin:0;">{{ __('No reviews yet. Be the first to share your thoughts!') }}</p>
    </div>
    @endforelse
</div>

@if($review_count > 5)
<div style="text-align:center;margin-bottom:28px;">
    <button class="pf-btn pf-btn-ghost see-more-review" data-items="5">
        <i class="las la-chevron-down"></i> {{ __('See More Reviews') }}
    </button>
</div>
@endif

{{-- Review Form --}}
@auth('web')
<div style="background:var(--pf-bg);border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);padding:28px;">
    <div style="font-size:14px;font-weight:700;color:var(--pf-dark);margin-bottom:18px;">
        <i class="las la-pen" style="color:var(--pf-teal);"></i> {{ __('Write a Review') }}
    </div>

    <div style="margin-bottom:16px;">
        <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:10px;">{{ __('Your Rating') }}</label>
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
                  placeholder="{{ __('Share your experience with this product…') }}"
                  style="width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);resize:vertical;outline:none;background:#fff;"
                  onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'"></textarea>
    </div>

    <button id="review-submit-btn" data-product="{{ $product->id }}" class="pf-btn pf-btn-teal">
        <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
    </button>
</div>
@else
<div style="text-align:center;padding:36px;background:var(--pf-bg);border:2px dashed var(--pf-border);border-radius:var(--pf-radius);">
    <i class="las la-lock" style="font-size:36px;color:var(--pf-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--pf-muted);font-size:14px;margin-bottom:16px;">{{ __('Please sign in to leave a review') }}</p>
    <a href="{{ theme_login_url() }}" class="pf-btn pf-btn-teal">
        <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
    </a>
</div>
@endauth

