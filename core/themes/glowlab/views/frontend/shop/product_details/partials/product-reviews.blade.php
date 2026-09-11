{{-- GlowLab: product reviews tab --}}

@auth
<div style="background:var(--gl-gold-pale);border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:28px;">
    <div style="font-size:13px;font-weight:700;color:var(--gl-dark);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <i class="mdi mdi-comment-outline" style="color:var(--gl-gold);"></i> {{ __('Leave a Review') }}
    </div>
    <div class="mb-3">
        <label style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gl-muted);display:block;margin-bottom:8px;">{{ __('Rating') }}</label>
        <select class="star-rating"
                style="padding:8px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:13px;font-family:inherit;background:#fff;outline:none;cursor:pointer;">
            <option value="5">{{ __('Excellent') }} ★★★★★</option>
            <option value="4">{{ __('Very Good') }} ★★★★</option>
            <option value="3" selected>{{ __('Average') }} ★★★</option>
            <option value="2">{{ __('Poor') }} ★★</option>
            <option value="1">{{ __('Terrible') }} ★</option>
        </select>
    </div>
    <div class="mb-3">
        <label style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gl-muted);display:block;margin-bottom:8px;">{{ __('Your Review') }}</label>
        <textarea id="review-text" rows="4" placeholder="{{ __('Share your experience with this product…') }}"
                  style="width:100%;padding:11px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:14px;font-family:inherit;resize:vertical;outline:none;transition:border-color .2s;"
                  onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'"></textarea>
    </div>
    <button type="button" id="review-submit-btn"
            style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
            onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
        <i class="mdi mdi-send"></i> {{ __('Submit Review') }}
    </button>
</div>
@else
<div style="text-align:center;padding:32px;background:var(--gl-gold-pale);border:1px solid var(--gl-border);border-radius:var(--gl-radius);margin-bottom:28px;">
    <i class="mdi mdi-account-outline" style="font-size:40px;color:var(--gl-gold);opacity:.5;display:block;margin-bottom:12px;"></i>
    <p style="color:var(--gl-muted);font-size:13px;margin-bottom:16px;">{{ __('Sign In To Leave Your Review') }}</p>
    <a href="{{ theme_login_url() }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
       onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
        <i class="mdi mdi-login"></i> {{ __('Sign In') }}
    </a>
</div>
@endauth

<div class="all-reviews" style="display:flex;flex-direction:column;gap:16px;">
    @forelse($product->reviews->take(5) ?? [] as $review)
    <div style="padding:20px;border:1px solid var(--gl-border);border-radius:var(--gl-radius);background:#fff;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <span style="font-size:13px;font-weight:600;color:var(--gl-dark);display:flex;align-items:center;gap:6px;">
                <i class="mdi mdi-account-circle-outline" style="color:var(--gl-gold);font-size:16px;"></i>
                {{ $review?->user?->name }}
            </span>
            <span style="font-size:11px;color:var(--gl-muted);">{{ $review->created_at?->diffForHumans() }}</span>
        </div>
        <div style="margin-bottom:8px;">{!! render_star_rating_markup($review->rating) !!}</div>
        <p style="font-size:13px;color:var(--gl-muted);margin:0;line-height:1.6;">{{ $review->review_text }}</p>
    </div>
    @empty
    <p style="font-size:13px;color:var(--gl-muted);">{{ __('No reviews yet. Be the first to review this product.') }}</p>
    @endforelse
</div>

@if(($product->reviews->count() ?? 0) > 5)
<div style="margin-top:16px;">
    <button class="see-more-review" data-items="5"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);background:transparent;color:var(--gl-dark);font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;"
            onmouseover="this.style.borderColor='var(--gl-dark)'" onmouseout="this.style.borderColor='var(--gl-border)'">
        <i class="mdi mdi-chevron-down"></i> {{ __('See More') }}
    </button>
</div>
@endif
