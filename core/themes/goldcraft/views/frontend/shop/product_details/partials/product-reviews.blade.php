{{-- GoldCraft: product reviews tab --}}

@auth
<div style="background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;margin-bottom:28px;">
    <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:16px;">{{ __('Leave a Review') }}</div>
    <div class="mb-3">
        <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-muted);display:block;margin-bottom:8px;">{{ __('Rating') }}</label>
        <select class="star-rating"
                style="padding:8px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:13px;font-family:Georgia,serif;background:#fff;outline:none;cursor:pointer;color:var(--gc-dark);">
            <option value="5">{{ __('Excellent') }} ★★★★★</option>
            <option value="4">{{ __('Very Good') }} ★★★★</option>
            <option value="3" selected>{{ __('Average') }} ★★★</option>
            <option value="2">{{ __('Poor') }} ★★</option>
            <option value="1">{{ __('Terrible') }} ★</option>
        </select>
    </div>
    <div class="mb-3">
        <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-muted);display:block;margin-bottom:8px;">{{ __('Your Review') }}</label>
        <textarea id="review-text" rows="4" placeholder="{{ __('Share your experience with this piece…') }}"
                  style="width:100%;padding:11px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:14px;font-family:Georgia,serif;resize:vertical;outline:none;transition:border-color .2s;font-style:italic;color:var(--gc-dark);"
                  onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'"></textarea>
    </div>
    <button type="button" id="review-submit-btn" class="gc-btn gc-btn-primary">
        <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
    </button>
</div>
@else
<div style="text-align:center;padding:32px;background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);margin-bottom:28px;">
    <span style="font-size:36px;display:block;margin-bottom:12px;">✍️</span>
    <p style="color:var(--gc-muted);font-size:13px;margin-bottom:16px;font-style:italic;">{{ __('Sign in to leave your review') }}</p>
    <a href="{{ theme_login_url() }}" class="gc-btn gc-btn-primary">
        <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
    </a>
</div>
@endauth

<div class="all-reviews" style="display:flex;flex-direction:column;gap:16px;">
    @forelse($product->reviews->take(5) ?? [] as $review)
    <div style="padding:20px;border:1px solid var(--gc-border);border-radius:var(--gc-radius);background:#fff;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <span style="font-size:13px;font-weight:400;color:var(--gc-dark);font-style:italic;">
                {{ $review?->user?->name }}
            </span>
            <span style="font-size:11px;color:var(--gc-muted);font-style:italic;">{{ $review->created_at?->diffForHumans() }}</span>
        </div>
        <div style="margin-bottom:8px;">{!! render_star_rating_markup($review->rating) !!}</div>
        <p style="font-size:13px;color:var(--gc-muted);margin:0;line-height:1.7;font-style:italic;">{{ $review->review_text }}</p>
    </div>
    @empty
    <p style="font-size:13px;color:var(--gc-muted);font-style:italic;">{{ __('No reviews yet. Be the first to review this piece.') }}</p>
    @endforelse
</div>

@if(($product->reviews->count() ?? 0) > 5)
<div style="margin-top:16px;">
    <button class="see-more-review" data-items="5" class="gc-btn gc-btn-ghost">
        {{ __('See More Reviews') }}
    </button>
</div>
@endif
