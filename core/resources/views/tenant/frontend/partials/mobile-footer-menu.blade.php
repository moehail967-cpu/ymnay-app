<!-- For Mobile nav start -->
<div class="mobile-nav track-icon-list">
    @php
        $cartCount = \Gloudemans\Shoppingcart\Facades\Cart::instance("default")->content()->count();
        $wishlistCount = \Gloudemans\Shoppingcart\Facades\Cart::instance("wishlist")->content()->count();
    @endphp

{{--    <div class="mobile-nav">--}}
{{--        <div class="mobile-nav-item">--}}
{{--            <a href="javascript:void(0)" class="mobile-nav-link search-open">--}}
{{--                <span class="mobile-nav-icon"><i class="las la-search"></i></span>--}}
{{--                <span class="mobile-nav-title">{{__('Search')}}</span>--}}
{{--            </a>--}}
{{--        </div>--}}
{{--        <div class="mobile-nav-item">--}}
{{--            <a href="{{route('tenant.shop.cart')}}" class="mobile-nav-link">--}}
{{--                <span class="mobile-nav-icon"><i class="las la-shopping-cart"></i></span>--}}
{{--                <span class="mobile-nav-title">{{__('Cart')}}</span>--}}
{{--            </a>--}}
{{--        </div>--}}
{{--        <div class="mobile-nav-item">--}}
{{--            <a href="{{route('tenant.shop.compare.product')}}" class="mobile-nav-link">--}}
{{--                <span class="mobile-nav-icon"><i class="las la-retweet"></i></span>--}}
{{--                <span class="mobile-nav-title">{{__('Compare')}}</span>--}}
{{--            </a>--}}
{{--        </div>--}}
{{--        <div class="mobile-nav-item">--}}
{{--            <a href="{{route('tenant.shop.wishlist.page')}}" class="mobile-nav-link">--}}
{{--                <span class="mobile-nav-icon"><i class="las la-shopping-cart"></i></span>--}}
{{--                <span class="mobile-nav-title">{{__('Wishlist')}}</span>--}}
{{--            </a>--}}
{{--        </div>--}}
{{--        <div class="mobile-nav-item">--}}
{{--            @php--}}
{{--                $route = route('tenant.user.login');--}}
{{--                $name = __('Login');--}}
{{--                if (!empty(Auth::guard('web')->user()))--}}
{{--                {--}}
{{--                    $route = route('tenant.user.home');--}}
{{--                    $name = __('Dashboard');--}}
{{--                }--}}
{{--            @endphp--}}
{{--            <a href="{{$route}}" class="mobile-nav-link">--}}
{{--                <span class="mobile-nav-icon"><i class="las la-user"></i></span>--}}
{{--                <span class="mobile-nav-title">{{$name}}</span>--}}
{{--            </a>--}}
{{--        </div>--}}
{{--    </div>--}}
    <div class="mobile-nav-item">
        <a href="javascript:void(0)" class="mobile-nav-link search-open">
            <span class="mobile-nav-icon"><i class="las la-search"></i></span>
            <span class="mobile-nav-title">{{__('Search')}}</span>
        </a>
    </div>
    <div class="mobile-nav-item">
        <a href="{{ route('tenant.shop.cart') }}" class="mobile-nav-link position-relative">
        <span class="mobile-nav-icon">
            <i class="las la-shopping-cart"></i>
            @if($cartCount > 0)
                <span class="position-absolute top-48 start-90 translate-middle badge rounded-circle" style="background: var(--main-color-one); font-size: 12px;">
                    {{ $cartCount > 99 ? '99+' : $cartCount }}
                    <span class="visually-hidden">cart items</span>
                </span>
            @endif
        </span>
            <span class="mobile-nav-title">{{ __('Cart') }}</span>
        </a>
    </div>
    <div class="mobile-nav-item">
        <a href="{{route('tenant.shop.compare.product')}}" class="mobile-nav-link">
            <span class="mobile-nav-icon"><i class="las la-retweet"></i></span>
            <span class="mobile-nav-title">{{__('Compare')}}</span>
        </a>
    </div>
    <div class="mobile-nav-item">
        <a href="{{ route('tenant.shop.wishlist.page') }}" class="mobile-nav-link position-relative">
        <span class="mobile-nav-icon">
            <i class="lar la-heart"></i>
            @if($wishlistCount > 0)
                <span class="position-absolute top-48 start-90 translate-middle badge rounded-circle" style="background: var(--main-color-one); font-size: 12px;">
                    {{ $wishlistCount > 99 ? '99+' : $wishlistCount }}
                    <span class="visually-hidden">wishlist items</span>
                </span>
            @endif
        </span>
            <span class="mobile-nav-title">{{ __('Wishlist') }}</span>
        </a>
    </div>
    <div class="mobile-nav-item">
        @php
            $route = route('tenant.user.login');
            $name = __('Login');
            if (!empty(Auth::guard('web')->user()))
            {
                $route = route('tenant.user.home');
                $name = __('Dashboard');
            }
        @endphp
        <a href="{{$route}}" class="mobile-nav-link">
            <span class="mobile-nav-icon"><i class="las la-user"></i></span>
            <span class="mobile-nav-title">{{$name}}</span>
        </a>
    </div>
</div>
<!-- For Mobile nav end -->
