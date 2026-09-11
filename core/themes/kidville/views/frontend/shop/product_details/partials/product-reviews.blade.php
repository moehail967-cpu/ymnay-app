{{-- KidVille: product reviews tab --}}

@auth
<div style="background:var(--kv-light);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:24px;margin-bottom:28px;">
    <div style="font-size:13px;font-weight:800;color:var(--kv-red);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <i class="las la-star"></i> {{ __('Leave a Review') }}
    </div>
    <div class="mb-3">
        <label style="font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--kv-muted);display:block;margin-bottom:8px;">{{ __('Rating') }}</label>
        <select class="star-rating"
                style="padding:8px 14px;border:2px solid var(--kv-border);border-radius:var(--kv-radius-sm);font-size:13px;background:#fff;outline:none;cursor:pointer;color:var(--kv-dark);font-weight:600;">
            <option value="5">{{ __('Excellent') }} ★★★★★</option>
            <option value="4">{{ __('Very Good') }} ★★★★</option>
            <option value="3" selected>{{ __('Average') }} ★★★</option>
            <option value="2">{{ __('Poor') }} ★★</option>
            <option value="1">{{ __('Terrible') }} ★</option>
        </select>
    </div>
    <div class="mb-3">
        <label style="font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--kv-muted);display:block;margin-bottom:8px;">{{ __('Your Review') }}</label>
        <textarea id="review-text" rows="4" placeholder="{{ __('Share your experience with this product…') }}"
                  style="width:100%;padding:11px 14px;border:2px solid var(--kv-border);border-radius:var(--kv-radius-sm);font-size:14px;resize:vertical;outline:none;transition:border-color .2s;color:var(--kv-dark);"
                  onfocus="this.style.borderColor='var(--kv-red)'" onblur="this.style.borderColor='var(--kv-border)'"></textarea>
    </div>
    <button type="button" id="review-submit-btn" class="kv-btn kv-btn-red">
        <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
    </button>
</div>
@else
<div style="text-align:center;padding:32px;background:var(--kv-light);border:2px solid var(--kv-border);border-radius:var(--kv-radius);margin-bottom:28px;">
    <i class="las la-user-circle" style="font-size:44px;color:var(--kv-muted);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--kv-muted);font-size:13px;margin-bottom:16px;">{{ __('Sign in to leave your review') }}</p>
    <a href="{{ theme_login_url() }}" class="kv-btn kv-btn-red">
        <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
    </a>
</div>
@endauth

<div class="all-reviews" style="display:flex;flex-direction:column;gap:16px;">
    @forelse($product->reviews->take(5) ?? [] as $review)
    <div style="padding:20px;border:2px solid var(--kv-border);border-radius:var(--kv-radius);background:#fff;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <span style="font-size:14px;font-weight:700;color:var(--kv-dark);">
                {{ $review?->user?->name }}
            </span>
            <span style="font-size:11px;color:var(--kv-muted);">{{ $review->created_at?->diffForHumans() }}</span>
        </div>
        <div style="margin-bottom:8px;">{!! render_star_rating_markup($review->rating) !!}</div>
        <p style="font-size:13px;color:var(--kv-muted);margin:0;line-height:1.7;">{{ $review->review_text }}</p>
    </div>
    @empty
    <p style="font-size:13px;color:var(--kv-muted);">{{ __('No reviews yet. Be the first to review this product!') }}</p>
    @endforelse
</div>

@if(($product->reviews->count() ?? 0) > 5)
<div style="margin-top:16px;">
    <button class="see-more-review kv-btn kv-btn-outline" data-items="5">
        {{ __('See More Reviews') }}
    </button>
</div>
@endif
