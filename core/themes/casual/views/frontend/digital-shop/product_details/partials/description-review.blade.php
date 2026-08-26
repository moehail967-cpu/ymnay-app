<section class="cs-digi-tabs-section">
    <div class="container">
        <div class="row g-4">

            {{-- Tabs + Reviews --}}
            <div class="col-lg-8">

                <ul class="cs-digi-tab-list tabs">
                    <li class="active" data-tab="description">
                        <span class="cs-digi-tab-btn active">{{ __('Description') }}</span>
                    </li>
                    <li data-tab="reviews">
                        <span class="cs-digi-tab-btn">{{ __('Reviews') }}</span>
                    </li>
                </ul>

                {{-- Description Panel --}}
                <div id="description" class="cs-digi-tab-panel tab-content-item active">
                    <div class="cs-digi-desc-body">
                        {!! $product->description !!}
                    </div>
                </div>

                {{-- Reviews Panel --}}
                <div id="reviews" class="cs-digi-tab-panel tab-content-item">

                    @forelse($reviews ?? [] as $review)
                    @php
                        $reviewer    = $review?->user;
                        $rImg        = get_attachment_image_by_id($reviewer?->image);
                        $rImgUrl     = !empty($rImg) ? $rImg['img_url'] : '';
                    @endphp
                    <div class="cs-digi-review-item">
                        <div class="cs-digi-review-header">
                            <div class="cs-digi-review-avatar">
                                @if($rImgUrl)
                                    <img src="{{ $rImgUrl }}" alt="{{ $reviewer?->name }}">
                                @else
                                    <div class="casual-new-thumb-placeholder"><i class="las la-user"></i></div>
                                @endif
                            </div>
                            <div>
                                <div class="cs-digi-review-author">{{ $reviewer?->name }}</div>
                                <div class="cs-digi-review-date">{{ $review->created_at?->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div>{!! render_star_rating_markup($review->rating) !!}</div>
                        <p class="cs-digi-review-body mt-2">{{ $review->review_text }}</p>
                    </div>
                    @empty
                    <p class="cs-no-data">{{ __('No reviews yet.') }}</p>
                    @endforelse

                    {{-- Review Form --}}
                    @if(auth()->guard('web')->check())
                    <div class="cs-digi-review-form">
                        <div class="cs-digi-review-form-title">{{ __('Leave a Review') }}</div>
                        <form>
                            <div class="mb-3">
                                <select class="cs-dash-input star-rating">
                                    <option value="5">{{ __('Excellent') }}</option>
                                    <option value="4" selected>{{ __('Very Good') }}</option>
                                    <option value="3">{{ __('Average') }}</option>
                                    <option value="2">{{ __('Poor') }}</option>
                                    <option value="1">{{ __('Terrible') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea name="review_text" id="review-text"
                                          class="cs-digi-review-textarea"
                                          placeholder="{{ __('Write your review…') }}"></textarea>
                            </div>
                            <div class="cs-digi-review-submit">
                                <button type="submit" id="review-submit-btn" class="cs-dash-submit-btn">
                                    <i class="las la-paper-plane"></i> {{ __('Submit Review') }}
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="cs-digi-login-hint">
                        <i class="las la-user-lock cs-digi-login-icon"></i>
                        {{ __('Sign in to leave a review.') }}
                        <a href="{{ theme_login_url() }}">{{ __('Sign In') }}</a>
                    </div>
                    @endif

                </div>
            </div>

            {{-- Related Products --}}
            <div class="col-lg-4">
                @include(include_theme_path('digital-shop.product_details.partials.related-products'))
            </div>

        </div>
    </div>
</section>
