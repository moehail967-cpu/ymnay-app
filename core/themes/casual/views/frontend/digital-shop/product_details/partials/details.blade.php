@php
    $regular_price = $product->regular_price;
    $sale_price    = $product->sale_price;
    if (!is_null($product->promotional_date) && !is_null($product->promotional_price)) {
        $sale_price = $product->promotional_price;
    }
    $image     = get_attachment_image_by_id($product->image_id);
    $image_url = !empty($image) ? $image['img_url'] : '';
@endphp

<div class="cs-digi-detail-wrap">
    <div class="cs-digi-detail-flex">

        {{-- Thumb --}}
        <div class="cs-digi-thumb-wrap">
            <div class="cs-digi-thumb">
                @if($image_url)
                    <img src="{{ $image_url }}" alt="{{ $product->name }}">
                @else
                    <div class="casual-new-thumb-placeholder"><i class="las la-book"></i></div>
                @endif
            </div>
        </div>

        {{-- Contents --}}
        <div class="cs-digi-contents">
            {!! render_product_star_rating_markup_with_count($product) !!}

            <h2 class="cs-digi-title mt-2">{{ $product->name }}</h2>

            @if($product->additionalFields?->author?->name)
            <div class="cs-digi-author">{{ __('by') }} {{ $product->additionalFields->author->name }}</div>
            @endif

            <div class="cs-digi-price-row">
                @if($product->accessibility != 'free')
                    @if(!empty($sale_price) && $sale_price > 0)
                        <span class="cs-digi-price-sale">{{ float_amount_with_currency_symbol($sale_price) }}</span>
                        <span class="cs-digi-price-regular">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                    @else
                        <span class="cs-digi-price-sale">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                @else
                    <span class="cs-digi-price-free">{{ __('Free') }}</span>
                @endif
            </div>

            @if($product->downloads_count > 0)
            <div class="cs-digi-downloads">
                {{ __('Downloads:') }} <span>{{ $product->downloads_count }} {{ __('Copies') }}</span>
            </div>
            @endif

            <div class="cs-digi-btns">
                @if(!empty($product->preview_link))
                    <a href="{{ $product->preview_link }}" class="cs-digi-btn-outline pdf_preview">
                        <i class="las la-eye"></i> {{ __('Preview') }}
                    </a>
                @endif

                @auth('web')
                    @php
                        $user       = auth('web')->user();
                        $downloaded = \Modules\DigitalProduct\Entities\DigitalProductDownload::where([
                            'user_id'    => $user->id,
                            'product_id' => $product->id,
                        ])->exists();
                    @endphp
                @endauth

                @if(isset($downloaded) && $downloaded)
                    <a href="{{ theme_user_download_url($product->slug) }}" class="cs-digi-btn-primary">
                        <i class="las la-download"></i> {{ __('Download') }}
                    </a>
                @else
                    <a href="javascript:void(0)" class="cs-digi-btn-primary add_to_cart_single_page">
                        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                    </a>
                @endif
            </div>

            {{-- Meta --}}
            <ul class="cs-digi-meta-list">
                @if($product->category?->name)
                <li class="cs-digi-meta-item">
                    <span class="cs-digi-meta-label">{{ __('Category') }}</span>
                    <span class="cs-digi-meta-value"><a href="javascript:void(0)">{{ $product->category->name }}</a></span>
                </li>
                @endif

                @if(!empty($product->subCategory))
                <li class="cs-digi-meta-item">
                    <span class="cs-digi-meta-label">{{ __('Subcategory') }}</span>
                    <span class="cs-digi-meta-value"><a href="javascript:void(0)">{{ $product->subCategory->name }}</a></span>
                </li>
                @endif

                @if(count($product->childCategory) > 0)
                <li class="cs-digi-meta-item">
                    <span class="cs-digi-meta-label">{{ __('Child Category') }}</span>
                    <span class="cs-digi-meta-value">
                        @foreach($product->childCategory ?? [] as $child)
                            <a href="javascript:void(0)">{{ $child->name }}</a>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </span>
                </li>
                @endif

                @if(!empty($product->tag))
                <li class="cs-digi-meta-item">
                    <span class="cs-digi-meta-label">{{ __('Tags') }}</span>
                    <span class="cs-digi-meta-value">
                        @foreach($product->tag ?? [] as $tag)
                            <a href="javascript:void(0)">{{ $tag->tag_name }}</a>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </span>
                </li>
                @endif

                @if($product->summary)
                <li class="cs-digi-meta-item">
                    <span class="cs-digi-meta-label">{{ __('Summary') }}</span>
                    <span class="cs-digi-meta-value">{{ $product->summary }}</span>
                </li>
                @endif
            </ul>

            {{-- Share --}}
            <div class="cs-digi-share">
                <span class="cs-digi-share-label">{{ __('Share:') }}</span>
                {!! single_post_share_bookpoint($product->slug, $product->name, $image_url) !!}
            </div>
        </div>

    </div>
</div>
