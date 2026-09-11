<div class="el-compare-grid">
    @foreach($product_array as $product)
        <div class="el-compare-card">
            <div class="el-compare-img-wrap">
                <a href="javascript:void(0)">
                    {!! render_image_markup_by_attachment_id($product->image, 'el-compare-img') !!}
                </a>
            </div>
            <div class="el-compare-body">
                <h3 class="el-compare-name">
                    <a href="javascript:void(0)">{{ $product->name ?? '' }}</a>
                </h3>
                <div class="el-compare-price-row">
                    <span class="el-compare-price">{{ amount_with_currency_symbol($product->price ?? 0) }}</span>
                </div>
                <ul class="el-compare-attrs">
                    @if(!empty($product->description ?? ''))
                        <li>
                            <span class="el-compare-attr-key">{{ __('Description:') }}</span>
                            <span class="el-compare-attr-val">{!! $product->description !!}</span>
                        </li>
                    @endif
                    @if(!empty($product->color_name ?? ''))
                        <li>
                            <span class="el-compare-attr-key">{{ __('Color:') }}</span>
                            <span class="el-compare-attr-val">{{ $product->color_name }}</span>
                        </li>
                    @endif
                    @if(!empty($product->size_name ?? ''))
                        <li>
                            <span class="el-compare-attr-key">{{ __('Size:') }}</span>
                            <span class="el-compare-attr-val">{{ $product->size_name }}</span>
                        </li>
                    @endif
                </ul>
            </div>
            <button class="el-compare-remove close-compare">
                <i class="las la-times"></i> {{ __('Remove') }}
            </button>
        </div>
    @endforeach
</div>
