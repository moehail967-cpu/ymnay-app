{{-- LuxeGems: Reviews tab --}}
<div class="all-reviews">
    @forelse($product->reviews ?? [] as $review)
    <div style="display:flex;gap:16px;padding:20px 0;border-bottom:1px solid var(--lx-border);">
        <div style="width:42px;height:42px;border-radius:50%;background:var(--lx-surface);border:1px solid var(--lx-border);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:600;color:var(--lx-gold);flex-shrink:0;">
            {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
        </div>
        <div style="flex:1;">
            <div style="font-size:13px;font-weight:600;color:var(--lx-white);">{{ $review->user?->name ?? __('Anonymous') }}</div>
            <div style="font-size:11px;color:var(--lx-gold);margin:3px 0;">
                @for($i=1;$i<=5;$i++)
                    <i class="{{ $i <= $review->rating ? 'las la-star' : 'lar la-star' }}"></i>
                @endfor
            </div>
            <p style="font-size:13px;color:var(--lx-muted);margin:6px 0 0;line-height:1.6;">{{ $review->review_text }}</p>
        </div>
    </div>
    @empty
    <p style="color:var(--lx-muted);font-size:13px;">{{ __('No reviews yet. Be the first to write one.') }}</p>
    @endforelse
</div>

@if(count($product->reviews ?? []) >= 5)
<button class="lx-btn lx-btn-outline see-more-review mt-3" data-items="10" style="font-size:11px;padding:10px 24px;">
    {{ __('See More') }}
</button>
@endif

{{-- Review form --}}
@auth('web')
<div style="margin-top:32px;padding-top:24px;border-top:1px solid var(--lx-border);">
    <h5 style="font-size:13px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--lx-white);margin-bottom:20px;">{{ __('Write a Review') }}</h5>
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
    <textarea id="review-text" class="lg-form-control" rows="4"
              placeholder="{{ __('Share your experience…') }}" style="margin-bottom:12px;"></textarea>
    <button id="review-submit-btn" class="lx-btn lx-btn-primary" style="font-size:12px;">
        <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
    </button>
</div>
@else
<p style="margin-top:20px;font-size:13px;color:var(--lx-muted);">
    {{ __('Please') }}
    <a href="{{ theme_login_url() }}" style="color:var(--lx-gold);">{{ __('sign in') }}</a>
    {{ __('to write a review.') }}
</p>
@endauth
