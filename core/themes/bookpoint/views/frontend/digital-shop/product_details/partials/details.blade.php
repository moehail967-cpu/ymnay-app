@php
    $regular_price = $product->regular_price;
    $sale_price = $product->sale_price;

    if (!is_null($product->promotional_date) && !is_null($product->promotional_price)) {
        $sale_price = $product->promotional_price;
    }

    $image = get_attachment_image_by_id($product->image_id);
    $image_url = !empty($image) ? $image['img_url'] : '';
@endphp

<div class="bp-pd-wrapper" style="background:#fff;border:1px solid #e8f5f1;border-radius:12px;overflow:hidden;">
    <div class="row g-0">
        <div class="col-md-4" style="border-right:1px solid #e8f5f1;">
            <div style="padding:32px;display:flex;align-items:center;justify-content:center;min-height:320px;background:#f8fdfb;">
                @if($image_url)
                    <img src="{{ $image_url }}" alt="{{ $product->name }}" style="max-width:100%;max-height:300px;object-fit:contain;border-radius:8px;">
                @else
                    <div style="width:180px;height:240px;background:#e8f5f1;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="las la-book" style="font-size:64px;color:#ccc;"></i>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-8">
            <div style="padding:32px;">
                {!! render_product_star_rating_markup_with_count($product) !!}

                <h2 class="bp-pd-title mt-2" style="font-size:24px;font-weight:800;color:var(--heading-color,#1a1a1a);line-height:1.3;">
                    {{ $product->name }}
                </h2>

                @if(!empty($product?->additionalFields) && !empty($product?->additionalFields?->author))
                <div style="font-size:14px;color:#888;margin-top:6px;">
                    <span>{{ __('by') }}</span>
                    <a href="{{ theme_shop_url() }}" style="color:var(--bp-accent);font-weight:600;text-decoration:none;">
                        {{ $product?->additionalFields?->author?->name }}
                    </a>
                </div>
                @endif

                <div class="bp-pd-price mt-3" style="display:flex;align-items:center;gap:12px;">
                    @if($product->accessibility != 'free')
                        @if(!empty($sale_price) && $sale_price > 0)
                            <span style="font-size:28px;font-weight:800;color:var(--bp-accent);">{{ float_amount_with_currency_symbol($sale_price) }}</span>
                            <span style="font-size:16px;color:#aaa;text-decoration:line-through;">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                        @else
                            <span style="font-size:28px;font-weight:800;color:var(--bp-accent);">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                        @endif
                    @else
                        <span style="font-size:28px;font-weight:800;color:var(--bp-accent);">{{ __('Free') }}</span>
                    @endif
                </div>

                @if($product->downloads_count > 0)
                <div style="font-size:13px;color:#888;margin-top:10px;">
                    <i class="las la-download" style="color:var(--bp-accent);"></i>
                    {{ __('Downloads:') }} <strong>{{ $product->downloads_count }} {{ __('Copies') }}</strong>
                </div>
                @endif

                <div class="d-flex flex-wrap gap-2 mt-4">
                    @if(!empty($product->preview_link))
                        <a href="{{ $product->preview_link }}" class="bp-btn bp-btn-outline pdf_preview" target="_blank">
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
                        <a href="{{ theme_user_download_url($product->slug) }}" class="bp-btn bp-btn-green">
                            <i class="las la-download"></i> {{ __('Download') }}
                        </a>
                    @else
                        <a href="javascript:void(0)" class="bp-btn bp-btn-green add_to_cart_single_page">
                            <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                        </a>
                    @endif
                </div>

                <div style="border-top:1px solid #e8f5f1;margin-top:24px;padding-top:20px;">
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                        <li style="font-size:13px;color:#555;">
                            <strong style="color:#1a1a1a;min-width:110px;display:inline-block;">{{ __('Category:') }}</strong>
                            <a href="{{ theme_shop_url() }}" style="color:var(--bp-accent);text-decoration:none;">
                                {{ $product?->category?->name }}
                            </a>
                        </li>

                        @if(!empty($product->subCategory))
                        <li style="font-size:13px;color:#555;">
                            <strong style="color:#1a1a1a;min-width:110px;display:inline-block;">{{ __('Subcategory:') }}</strong>
                            <a href="{{ theme_shop_url() }}" style="color:var(--bp-accent);text-decoration:none;">
                                {{ $product?->subCategory?->name }}
                            </a>
                        </li>
                        @endif

                        @if(count($product->childCategory) > 0)
                        <li style="font-size:13px;color:#555;">
                            <strong style="color:#1a1a1a;min-width:110px;display:inline-block;">{{ __('Child category:') }}</strong>
                            @foreach($product?->childCategory ?? [] as $child_category)
                                <a href="{{ theme_shop_url() }}" style="color:var(--bp-accent);text-decoration:none;">
                                    {{ $child_category->name }}
                                </a>{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </li>
                        @endif

                        @if(!empty($product->tag))
                        <li style="font-size:13px;color:#555;">
                            <strong style="color:#1a1a1a;min-width:110px;display:inline-block;">{{ __('Tags:') }}</strong>
                            @foreach($product->tag ?? [] as $tag)
                                <a href="{{ theme_shop_url() }}" style="color:var(--bp-accent);text-decoration:none;">
                                    {{ $tag->tag_name }}
                                </a>{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </li>
                        @endif

                        @if(!empty($product->summary))
                        <li style="font-size:13px;color:#555;line-height:1.6;">
                            <strong style="color:#1a1a1a;min-width:110px;display:inline-block;vertical-align:top;">{{ __('Summary:') }}</strong>
                            {{ $product->summary }}
                        </li>
                        @endif
                    </ul>
                </div>

                <div class="d-flex align-items-center gap-3 mt-4">
                    <span style="font-size:13px;font-weight:600;color:#555;">{{ __('Share to:') }}</span>
                    <div>
                        {!! single_post_share_bookpoint($product->slug, $product->name, $image_url) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
