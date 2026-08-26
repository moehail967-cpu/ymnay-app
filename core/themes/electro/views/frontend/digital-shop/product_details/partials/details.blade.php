@php
    $regular_price = $product->regular_price;
    $sale_price = $product->sale_price;

    if (!is_null($product->promotional_date) && !is_null($product->promotional_price)) {
        $sale_price = $product->promotional_price;
    }

    $image = get_attachment_image_by_id($product->image_id);
    $image_url = !empty($image) ? $image['img_url'] : '';
@endphp

<div class="el-digital-pd-main">
    <!-- Cover Image -->
    <div class="el-digital-pd-cover">
        <img src="{{ $image_url }}" alt="{{ $product->name }}" class="el-digital-pd-cover-img">
    </div>

    <!-- Content -->
    <div class="el-digital-pd-content">
        {!! render_product_star_rating_markup_with_count($product) !!}
        <h1 class="el-pd-title mt-2">{{ $product->name }}</h1>

        @if(!empty($product->additionalFields?->author?->name))
            <p class="el-digital-pd-author">
                {{ __('by') }} <strong>{{ $product->additionalFields?->author?->name }}</strong>
            </p>
        @endif

        <!-- Price -->
        <div class="el-pd-price-row mt-3">
            @if($product->accessibility != 'free')
                @if(!empty($sale_price) && $sale_price > 0)
                    <span class="el-pd-price flash-prices">{{ float_amount_with_currency_symbol($sale_price) }}</span>
                    <span class="el-pd-price-old flash-old-prices">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                @else
                    <span class="el-pd-price flash-prices">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                @endif
            @else
                <span class="el-pd-price flash-prices">{{ __('Free') }}</span>
            @endif
        </div>

        @if($product->downloads_count > 0)
            <p class="el-digital-pd-downloads mt-2">
                {{ __('Downloads') }}: <strong>{{ $product->downloads_count }} {{ __('Copies') }}</strong>
            </p>
        @endif

        <!-- CTA Buttons -->
        <div class="el-pd-cta-row mt-4">
            @if(!empty($product->preview_link))
                <a href="{{ $product->preview_link }}"
                   class="el-btn el-btn-outline pdf_preview" target="_blank">
                    <i class="las la-eye"></i> {{ __('Preview') }}
                </a>
            @endif

            @auth('web')
                @php
                    $user = auth('web')->user();
                    $downloaded = \Modules\DigitalProduct\Entities\DigitalProductDownload::where(['user_id' => $user->id, 'product_id' => $product->id])->exists();
                @endphp
            @endauth

            @if(isset($downloaded) && $downloaded)
                <a href="{{ theme_user_download_url($product->slug) }}" class="el-btn el-btn-primary">
                    <i class="las la-download"></i> {{ __('Download') }}
                </a>
            @else
                <a href="javascript:void(0)"
                   class="el-btn el-btn-primary add_to_cart_single_page">
                    <i class="las la-shopping-bag"></i> {{ __('Add to Cart') }}
                </a>
            @endif
        </div>

        <!-- Categories / Tags / Summary -->
        <div class="el-digital-pd-meta mt-4">
            <ul class="el-digital-pd-meta-list">
                <li>
                    <span class="el-digital-pd-meta-key">{{ __('Category:') }}</span>
                    <a class="el-digital-pd-meta-val" href="javascript:void(0)">{{ $product?->category?->name }}</a>
                </li>
                @if(!empty($product->subCategory))
                    <li>
                        <span class="el-digital-pd-meta-key">{{ __('Subcategory:') }}</span>
                        <a class="el-digital-pd-meta-val" href="javascript:void(0)">{{ $product?->subCategory?->name }}</a>
                    </li>
                @endif
                @if(count($product->childCategory) > 0)
                    <li>
                        <span class="el-digital-pd-meta-key">{{ __('Child category:') }}</span>
                        @foreach($product?->childCategory ?? [] as $child_category)
                            <a class="el-digital-pd-meta-val" href="javascript:void(0)">{{ $child_category->name }}</a>{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </li>
                @endif
                @if(!empty($product->tag))
                    <li>
                        <span class="el-digital-pd-meta-key">{{ __('Tags:') }}</span>
                        @foreach($product->tag ?? [] as $tag)
                            <a class="el-digital-pd-meta-val" href="javascript:void(0)">{{ $tag->tag_name }}</a>{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </li>
                @endif
                @if(!empty($product->summary))
                    <li>
                        <span class="el-digital-pd-meta-val">{{ $product->summary }}</span>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Social Share -->
        <div class="el-pd-share mt-4">
            <span style="font-size:13px;color:#888;font-weight:600;">{{ __('Share to:') }}</span>
            <ul class="d-flex mt-2">
                {!! single_post_share_bookpoint($product->slug, $product->name, $image_url) !!}
            </ul>
        </div>
    </div>
</div>
