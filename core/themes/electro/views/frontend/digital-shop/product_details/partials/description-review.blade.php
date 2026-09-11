<section class="el-page-section" style="background:#f9fafb;">
    <div class="container">
        <div class="row gy-5">
            <!-- Left: Description + Reviews -->
            <div class="col-xxl-8 col-lg-7">
                <div class="el-pd-tabs">
                    <div class="el-pd-tab-bar">
                        <button class="el-pd-tab-btn active" data-tab="description">{{ __('Description') }}</button>
                        <button class="el-pd-tab-btn" data-tab="reviews">{{ __('Reviews') }}</button>
                    </div>

                    <!-- Description Tab -->
                    <div id="description" class="el-pd-tab-content active">
                        <div class="el-pd-tab-inner mt-4">
                            <div class="single-description-tab-content">
                                {!! $product->description !!}
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Tab -->
                    <div id="reviews" class="el-pd-tab-content">
                        <div class="el-pd-tab-inner mt-4">

                            <!-- Existing Reviews -->
                            <div class="all-reviews">
                                @foreach($reviews ?? [] as $review)
                                    @php
                                        $reviewer = $review?->user;
                                        $rev_img_data = get_attachment_image_by_id($reviewer?->image);
                                        $rev_img_url  = !empty($rev_img_data) ? $rev_img_data['img_url'] : '';
                                    @endphp
                                    <div class="el-pd-review-item">
                                        <div class="el-pd-reviewer-avatar">
                                            <img src="{{ $rev_img_url }}" alt="{{ $reviewer?->name }}" class="radius-parcent-50" style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
                                        </div>
                                        <div class="el-pd-reviewer-content">
                                            <h5 class="el-pd-reviewer-name">
                                                <a href="javascript:void(0)">{{ $reviewer?->name }}</a>
                                            </h5>
                                            {!! render_star_rating_markup($review->rating) !!}
                                            <p class="el-pd-review-text">{{ $review->review_text }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Review Form -->
                            @if(!empty(Auth::guard('web')->user()))
                                <div class="el-pd-review-form mt-5">
                                    <h3 class="el-pd-review-form-title">{{ __('Leave a Review') }}</h3>
                                    <form>
                                        <input type="hidden" class="rating-count" value="">
                                        <div class="ratings mt-3">
                                            <select class="star-rating">
                                                <option value="5">{{ __('Excellent') }}</option>
                                                <option value="4" selected>{{ __('Very Good') }}</option>
                                                <option value="3">{{ __('Average') }}</option>
                                                <option value="2">{{ __('Poor') }}</option>
                                                <option value="1">{{ __('Terrible') }}</option>
                                            </select>
                                        </div>
                                        <div class="mt-3">
                                            <textarea rows="5" name="review_text"
                                                      class="el-form-textarea review-text"
                                                      id="review-text"
                                                      placeholder="{{ __('Write your review here...') }}"></textarea>
                                        </div>
                                        <div class="text-end mt-3">
                                            <button type="submit" id="review-submit-btn" class="el-btn el-btn-primary">
                                                {{ __('Submit Review') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="el-auth-card mt-5" style="max-width:480px;">
                                    <div style="text-align:center;margin-bottom:20px;">
                                        {!! render_image_markup_by_attachment_id('site_logo') !!}
                                    </div>
                                    <h4>{{ __('Hello! Let us get started') }}</h4>
                                    <p style="color:#888;font-size:14px;margin-bottom:16px;">{{ __('Sign in to continue.') }}</p>
                                    {!! theme_error_msg() !!}
                                    {!! theme_flash_msg() !!}
                                    <div id="msg-wrapper"></div>
                                    <form class="pt-2" action="" method="post" id="login_form_order_page">
                                        <div class="mb-3">
                                            <input type="text" name="email" class="el-form-input" placeholder="{{ __('Username') }}">
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" name="password" class="el-form-input" placeholder="{{ __('Password') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label style="font-size:13px;color:#555;display:flex;align-items:center;gap:8px;">
                                                <input type="checkbox" name="remember" style="accent-color:#E8603C;">
                                                {{ __('Keep me signed in') }}
                                            </label>
                                        </div>
                                        <button type="submit" class="el-btn el-btn-primary el-btn-block" id="login_submit_btn">
                                            {{ __('Sign In') }}
                                        </button>
                                        <p style="text-align:center;margin-top:14px;font-size:13px;color:#888;">
                                            {{ __('Do not have an account?') }}
                                            <a href="{{ theme_register_url() }}" style="color:#E8603C;font-weight:600;">{{ __('Create') }}</a>
                                        </p>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Related Products -->
            @include(include_theme_path('digital-shop.product_details.partials.related-products'))
        </div>
    </div>
</section>
