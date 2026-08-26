@php
    $reviews     = $product->reviews ?? collect();
    $review_count = $reviews->count();
@endphp

<div class="row g-4">
    {{-- Review List --}}
    <div class="col-lg-7">
        <div style="font-family:var(--sz-font-head);font-size:14px;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-dark);margin-bottom:16px;">
            {{ $review_count }} {{ __('Reviews') }}
        </div>

        @forelse($reviews as $review)
        <div style="display:flex;gap:14px;padding:16px 0;border-bottom:1px solid var(--sz-border);">
            <div style="width:44px;height:44px;background:var(--sz-navy);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:var(--sz-font-head);font-size:18px;font-weight:700;color:#fff;flex-shrink:0;">
                {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
            </div>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;margin-bottom:4px;">
                    <strong style="font-family:var(--sz-font-head);font-size:15px;text-transform:uppercase;letter-spacing:.5px;color:var(--sz-dark);">{{ $review->user?->name ?? __('Anonymous') }}</strong>
                    <div style="color:#FDD835;font-size:13px;">
                        @for($i=1;$i<=5;$i++)<i class="mdi mdi-star{{ $i <= $review->rating ? '' : '-outline' }}"></i>@endfor
                    </div>
                </div>
                <div style="font-size:11px;color:var(--sz-muted);margin-bottom:6px;">{{ $review->created_at?->format('d M Y') }}</div>
                <p style="font-size:14px;color:var(--sz-muted);margin:0;line-height:1.6;">{{ $review->review_text }}</p>
            </div>
        </div>
        @empty
        <p style="color:var(--sz-muted);font-size:14px;">{{ __('No reviews yet. Be the first to review this product.') }}</p>
        @endforelse

        {{-- Load More --}}
        @if($review_count >= 5)
        <div style="margin-top:16px;text-align:center;">
            <button type="button" class="sz-btn sz-btn-outline sz-btn-sm see-more-review" data-items="5">
                <i class="mdi mdi-refresh"></i> {{ __('Load More') }}
            </button>
        </div>
        @endif
    </div>

    {{-- Review Form --}}
    <div class="col-lg-5">
        <div style="background:var(--sz-bg);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);padding:24px;">
            <div style="font-family:var(--sz-font-head);font-size:14px;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-dark);margin-bottom:16px;">{{ __('Write a Review') }}</div>

            @auth('web')
            <div style="margin-bottom:14px;">
                <div style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:8px;">{{ __('Your Rating') }}</div>
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

            <div style="margin-bottom:14px;">
                <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Review') }}</label>
                <textarea id="review-text" rows="4"
                          style="width:100%;padding:10px 14px;border:2px solid var(--sz-border);border-radius:var(--sz-radius);font-size:14px;font-family:var(--sz-font-body);outline:none;resize:vertical;"
                          placeholder="{{ __('Share your experience…') }}"
                          onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'"></textarea>
            </div>

            <button type="button" id="review-submit-btn" class="sz-btn sz-btn-red w-100" style="justify-content:center;">
                <i class="mdi mdi-send-outline"></i> {{ __('Submit Review') }}
            </button>
            @else
            <div style="text-align:center;padding:20px 0;">
                <i class="mdi mdi-lock-outline" style="font-size:36px;color:var(--sz-muted);opacity:.5;"></i>
                <p style="font-size:14px;color:var(--sz-muted);margin:12px 0 16px;">{{ __('Please log in to leave a review.') }}</p>
                <a href="{{ theme_login_url() }}" class="sz-btn sz-btn-red sz-btn-sm" style="gap:6px;">
                    <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                </a>
            </div>
            @endauth
        </div>
    </div>
</div>
