@if(Auth::guard('web')->check())
<div class="row g-4">
    <div class="col-lg-5">
        <div style="background:var(--tr-cream);border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:24px;">
            <h4 style="font-size:15px;font-weight:800;color:var(--tr-bark);margin-bottom:20px;"><i class="mdi mdi-star" style="color:var(--tr-sand);"></i> {{ __('Leave a Review') }}</h4>
            <form>
                <input type="hidden" class="rating-count" value="">
                <div class="mb-3">
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);display:block;margin-bottom:6px;">{{ __('Rating') }}</label>
                    <select class="star-rating" style="width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;outline:none;background:#fff;">
                        <option value="5">{{ __('Excellent') }}</option>
                        <option value="4">{{ __('Very Good') }}</option>
                        <option value="3" selected>{{ __('Average') }}</option>
                        <option value="2">{{ __('Poor') }}</option>
                        <option value="1">{{ __('Terrible') }}</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);display:block;margin-bottom:6px;">{{ __('Your Review') }}</label>
                    <textarea rows="5" name="review_text" id="review-text"
                              style="width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;font-family:inherit;outline:none;resize:vertical;"
                              onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'"></textarea>
                </div>
                <button type="submit" id="review-submit-btn" class="tr-btn tr-btn-primary">
                    <i class="mdi mdi-send"></i> {{ __('Submit Review') }}
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="all-reviews">
            @forelse($product->reviews->take(5) ?? [] as $review)
            <div style="display:flex;gap:14px;padding-bottom:20px;margin-bottom:20px;border-bottom:1px solid var(--tr-border);">
                <div style="width:44px;height:44px;border-radius:50%;background:var(--tr-olive);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:900;flex-shrink:0;">
                    {{ strtoupper(substr($review?->user?->name ?? 'A', 0, 1)) }}
                </div>
                <div style="flex:1;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-size:14px;font-weight:700;color:var(--tr-bark);">{{ $review?->user?->name }}</span>
                        <span style="font-size:11px;color:var(--tr-stone);">{{ $review->created_at?->diffForHumans() }}</span>
                    </div>
                    <div style="margin-bottom:8px;">{!! render_star_rating_markup($review->rating) !!}</div>
                    <p style="font-size:13px;color:#555;margin:0;line-height:1.6;">{{ $review->review_text }}</p>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:40px 0;color:var(--tr-stone);">
                <i class="mdi mdi-star-outline" style="font-size:48px;display:block;margin-bottom:12px;opacity:.3;"></i>
                <p style="font-size:14px;">{{ __('No reviews yet. Be the first to share your trail experience!') }}</p>
            </div>
            @endforelse
        </div>
        @if(isset($product->reviews) && $product->reviews->count() > 5)
        <button class="tr-btn see-more-review" data-items="5" style="margin-top:12px;background:transparent;border:1.5px solid var(--tr-olive);color:var(--tr-olive);">
            <i class="mdi mdi-refresh"></i> {{ __('See More') }}
        </button>
        @endif
    </div>
</div>
@else
<div style="text-align:center;padding:40px 0;">
    <i class="mdi mdi-lock-outline" style="font-size:48px;color:var(--tr-olive);display:block;margin-bottom:16px;opacity:.4;"></i>
    <p style="font-size:14px;color:var(--tr-stone);margin-bottom:16px;">{{ __('Sign in to leave a review') }}</p>
    <a href="{{ theme_login_url() }}" class="tr-btn tr-btn-primary">
        <i class="mdi mdi-login"></i> {{ __('Sign In') }}
    </a>
</div>
@endif
