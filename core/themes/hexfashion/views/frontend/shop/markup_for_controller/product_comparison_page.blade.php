<div class="hf-compare-grid">
    @foreach($product_array as $product)
        <div class="hf-compare-card">
            <div class="hf-compare-img-wrap">
                <a href="javascript:void(0)">
                    {!! render_image_markup_by_attachment_id($product->image, 'hf-compare-img') !!}
                </a>
            </div>
            <div class="hf-compare-body">
                <h3 class="hf-compare-name">
                    <a href="javascript:void(0)">{{ $product->name ?? '' }}</a>
                </h3>
                <div class="hf-compare-price-row">
                    <span class="hf-compare-price">{{ amount_with_currency_symbol($product->price ?? 0) }}</span>
                </div>
                <ul class="hf-compare-attrs">
                    @if(!empty($product->description ?? ''))
                        <li>
                            <span class="hf-compare-attr-key">{{ __('Description:') }}</span>
                            <span class="hf-compare-attr-val">{!! $product->description !!}</span>
                        </li>
                    @endif
                    @if(!empty($product->color_name ?? ''))
                        <li>
                            <span class="hf-compare-attr-key">{{ __('Color:') }}</span>
                            <span class="hf-compare-attr-val">{{ $product->color_name }}</span>
                        </li>
                    @endif
                    @if(!empty($product->size_name ?? ''))
                        <li>
                            <span class="hf-compare-attr-key">{{ __('Size:') }}</span>
                            <span class="hf-compare-attr-val">{{ $product->size_name }}</span>
                        </li>
                    @endif
                </ul>
            </div>
            <button class="hf-compare-remove close-compare">
                <i class="las la-times"></i> {{ __('Remove') }}
            </button>
        </div>
    @endforeach
</div>
