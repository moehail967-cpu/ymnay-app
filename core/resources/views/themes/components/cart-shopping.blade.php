<div class="single-icon cart-shopping">
    <a class="icon" href="{{route('tenant.shop.compare.product.page')}}"> <i
            class="las la-sync"></i> </a>
</div>
<div class="single-icon cart-shopping">
    @php
        $cart = \Gloudemans\Shoppingcart\Facades\Cart::instance("wishlist")->content();
        $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::instance("wishlist")->subtotal();
    @endphp
    <a href="javascript:void(0)" class="icon"> <i class="lar la-heart"></i>
    </a>
    <a href="javascript:void(0)" class="icon-notification"> {{\Gloudemans\Shoppingcart\Facades\Cart::instance("wishlist")->content()->count()}} </a>
    <div class="addto-cart-contents">
        <div class="single-addto-cart-wrappers">
            @forelse($cart as $cart_item)
{{--                @dd($cart_item);--}}
                <div class="single-addto-carts">
                    <div class="addto-cart-flex-contents">
                        <div class="addto-cart-thumb">
                            {!! render_image_markup_by_attachment_id($cart_item?->options?->image) !!}
                        </div>
                        <div class="addto-cart-img-contents">
                            <h6 class="addto-cart-title fs-18">{{Str::words($cart_item->name, 5)}}</h6>
                            <span class="name-subtitle d-block">
                                                                        @if($cart_item?->options?->color_name)
                                    {{__('Color:')}} {{$cart_item?->options?->color_name}} ,
                                @endif

                                @if($cart_item?->options?->size_name)
                                    {{__('Size:')}} {{$cart_item?->options?->size_name}}
                                @endif

                                @if($cart_item?->options?->attributes)
                                    <br>
                                    @foreach($cart_item?->options?->attributes as $key => $attribute)
                                        {{$key.':'}} {{$attribute}}{{!$loop->last ? ',' : ''}}
                                    @endforeach
                                @endif
                                                                </span>

                            <div class="price-updates margin-top-10">
                                <span class="price-title fs-16 fw-500 color-heading"> {{amount_with_currency_symbol($cart_item->price)}} </span>
                            </div>
                        </div>
                    </div>
                    <span class="addto-cart-counts color-heading fw-500"> {{$cart_item->qty}} </span>
                    <a href="javascript:void(0)"
                       class="close-cart"
                       data-id="{{ $cart_item->rowId }}"
                       data-instance="wishlist">
                        <span class="icon-close color-heading"><i class="las la-times"></i></span>
                    </a>

                </div>
            @empty
                <div class="single-addto-carts">
                    <p class="text-center">{{__('No Item in Wishlist')}}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
<div class="single-icon cart-shopping">
    @php
        $cart = \Gloudemans\Shoppingcart\Facades\Cart::instance("default")->content();
        $subtotal = 0;
    @endphp
    <a href="javascript:void(0)" class="icon"> <i class="las la-shopping-cart"></i>
    </a>
    <a href="javascript:void(0)" class="icon-notification"> {{\Gloudemans\Shoppingcart\Facades\Cart::instance("default")->content()->count()}} </a>
    <div class="addto-cart-contents">
        <div class="single-addto-cart-wrappers">
            @forelse($cart as $cart_item)
                <div class="single-addto-carts">
                    <div class="addto-cart-flex-contents">
                        <div class="addto-cart-thumb">
                            {!! render_image_markup_by_attachment_id($cart_item?->options?->image) !!}
                        </div>
                        <div class="addto-cart-img-contents">
                            <h6 class="addto-cart-title fs-18"> {{Str::words($cart_item->name, 5)}} </h6>
                            <span class="name-subtitle d-block">
                                                                        @if($cart_item?->options?->color_name)
                                    {{__('Color:')}} {{$cart_item?->options?->color_name}} ,
                                @endif

                                @if($cart_item?->options?->size_name)
                                    {{__('Size:')}} {{$cart_item?->options?->size_name}}
                                @endif

                                @if($cart_item?->options?->attributes)
                                    <br>
                                    @foreach($cart_item?->options?->attributes as $key => $attribute)
                                        {{$key.':'}} {{$attribute}}{{!$loop->last ? ',' : ''}}
                                    @endforeach
                                @endif
                                                                </span>

                            @php
                                $subtotal += calculatePrice($cart_item->price * $cart_item->qty, $cart_item->options)
                            @endphp

                            <div class="price-updates margin-top-10">
                                <span class="price-title fs-16 fw-500 color-heading"> {{amount_with_currency_symbol(calculatePrice($cart_item->price, $cart_item->options))}} </span>
                            </div>
                        </div>
                    </div>
                    <span class="addto-cart-counts color-heading fw-500"> {{$cart_item->qty}} </span>
                    {{--                                                        <a href="javascript:void(0)" class="close-cart">--}}
                    {{--                                                            <span class="icon-close color-heading"> <i--}}
                    {{--                                                                    class="las la-times"></i> </span>--}}
                    {{--                                                        </a>--}}
                    <a href="javascript:void(0)"
                       class="close-cart"
                       data-id="{{ $cart_item->rowId }}"
                       data-instance="default">
                        <span class="icon-close color-heading"><i class="las la-times"></i></span>
                    </a>

                </div>
            @empty
                <div class="single-addto-carts">
                    <p class="text-center">{{__('No Item in Wishlist')}}</p>
                </div>
            @endforelse
        </div>

        @if($cart->count() != 0)
            <div class="cart-total-amount">
                <h6 class="amount-title"> {{__('Total Amount:')}} </h6> <span
                    class="fs-18 fw-500 color-light"> {{float_amount_with_currency_symbol($subtotal)}} </span></div>
            <div class="btn-wrapper margin-top-20">
                <a href="{{route('tenant.shop.checkout')}}" class="cmn-btn btn-bg-1 radius-0 w-100">
                    {{__('Check Out')}} </a>
            </div>
            <div class="btn-wrapper margin-top-20">
                <a href="{{route('tenant.shop.cart')}}" class="cmn-btn btn-outline-one radius-0 w-100">
                    {{__('View Cart')}} </a>
            </div>
        @endif
    </div>
</div>
