<div class="mc-compare-grid">
    @foreach($product_array as $product)
        <div class="mc-compare-card">
            <div class="mc-compare-img-wrap">
                <a href="javascript:void(0)">
                    {!! render_image_markup_by_attachment_id($product->image, 'mc-compare-img') !!}
                </a>
            </div>
            <div class="mc-compare-body">
                <h3 class="mc-compare-name">
                    <a href="javascript:void(0)">{{ $product->name ?? '' }}</a>
                </h3>
                <div class="mc-compare-price-row">
                    <span class="mc-compare-price">{{ amount_with_currency_symbol($product->price ?? 0) }}</span>
                </div>
                <ul class="mc-compare-attrs">
                    @if(!empty($product->description ?? ''))
                        <li>
                            <span class="mc-compare-attr-key">{{ __('Description:') }}</span>
                            <span class="mc-compare-attr-val">{!! $product->description !!}</span>
                        </li>
                    @endif
                    @if(!empty($product->color_name ?? ''))
                        <li>
                            <span class="mc-compare-attr-key">{{ __('Color:') }}</span>
                            <span class="mc-compare-attr-val">{{ $product->color_name }}</span>
                        </li>
                    @endif
                    @if(!empty($product->size_name ?? ''))
                        <li>
                            <span class="mc-compare-attr-key">{{ __('Size:') }}</span>
                            <span class="mc-compare-attr-val">{{ $product->size_name }}</span>
                        </li>
                    @endif
                </ul>
            </div>
            <button class="mc-compare-remove close-compare">
                <i class="las la-times"></i> {{ __('Remove') }}
            </button>
        </div>
    @endforeach
</div>
