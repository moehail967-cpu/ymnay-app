<div id="tab-grid2" class="tab-content-item active">
    <div class="row mt-4 gy-5">
        @foreach($products as $product)
            @php
                $data = get_product_dynamic_price($product);
                $campaign_name = $data['campaign_name'];
                $regular_price = $data['regular_price'];
                $sale_price = $data['sale_price'];
                $discount = $data['discount'];

                $img_data = get_attachment_image_by_id($product->image_id, 'grid');
                $img = !empty($img_data) ? $img_data['img_url'] : '';
                $alt = !empty($img_data) ? $img_data['img_alt'] : '';
            @endphp

            <div class="col-xxl-4 col-lg-6 col-sm-6">
                <div class="global-card no-shadow radius-0 pb-0 h-100 d-flex flex-column">
                    <div class="global-card-thumb">
                        <a href="{{dynamicRoute($product->slug)}}">
                            <img src="{{$img}}" alt="{{$alt}}">
                        </a>

                        @if($discount != null)
                            <div class="global-card-thumb-badge right-side">
                                <span class="global-card-thumb-badge-box bg-color-two"> {{$discount.'%'}} {{__('Off')}} </span>

                                @if(!empty($product->badge))
                                    <span class="global-card-thumb-badge-box bg-color-new"> {{$product?->badge?->name}} </span>
                                @endif
                            </div>
                        @endif

                        <ul class="global-card-thumb-icons">
                            <li class="icon-list" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('add to cart')}}">
                                <a class="icon cart-loading add-to-cart-btn" data-product_id="{{ $product->id }}" href="javascript:void(0)"> <i class="las la-shopping-cart"></i> </a>
                            </li>
                            <li class="icon-list" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('add to Wishlist')}}">
                                <a class="icon cart-loading add-to-wishlist-btn" data-product_id="{{ $product->id }}" href="javascript:void(0)"> <i class="lar la-heart"></i> </a>
                            </li>
                            <li class="icon-list" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('add to Compare')}}">
                                <a class="icon cart-loading compare-btn" data-product_id="{{ $product->id }}" href="javascript:void(0)"> <i class="las la-retweet"></i> </a>
                            </li>
                            <li class="icon-list" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Preview')}}">
                                <a class="icon popup-modal cart-loading" data-id="{{ $product->id }}" href="javascript:void(0)"> <i class="lar la-eye"></i> </a>
                            </li>
                        </ul>
                    </div>

                    <div class="global-card-contents">
                        <div class="global-card-contents-flex">
                            <h5 class="global-card-contents-title">
                                <a href="{{dynamicRoute($product->slug)}}"> {!! product_limited_text($product->name) !!} </a>
                            </h5>
                            {!! render_product_star_rating_markup_with_count($product) !!}
                        </div>
                        <div class="price-update-through mt-3 mt-auto">
                            {!! product_prices($product, 'color-two') !!}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


            <div class="pagination mt-60">
                <ul class="pagination-list">
                    @if(count($links) > 1)
                        @foreach($links as $link)
                            <li>
                                <a data-page="{{ $loop->iteration }}" href="{{ $link }}" class="page-number {{ $loop->iteration === $current_page ? "current" : ""}}">{{ $loop->iteration }}</a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
    </div>
</div>
