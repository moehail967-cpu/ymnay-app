@extends(route_prefix().'frontend.frontend-page-master')

@section('title')
    {{__('Wishlist Page')}}
@endsection

@section('page-title')
    {{__('Wishlist Page')}}
@endsection

@section('content')
    <section class="hf-cart-section">
        <div class="container">
            @if($cart_data->count())
                <div class="hf-cart-layout">
                    @include(include_theme_path('shop.cart.partials.cart_left_contents'))
                    @include(include_theme_path('shop.cart.partials.cart_right_contents'))
                </div>
            @else
                @include(include_theme_path('shop.cart.cart_empty'))
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        (function ($) {
            'use strict'

            $(document).on('change', '.quantity-input', function (e) {
                let el = $(this);
                let product_qty = el.val();
                let product_unique_id = el.closest('[data-product-id]').data('product-id');
                let product_variant_id = el.closest('[data-variant-id]').data('product-variant-id');
                getSubtotal(product_unique_id, product_qty, product_variant_id)
            });

            $(document).on('click', '.plus', function () {
                var selectedInput = $(this).prev('.quantity-input');
                if (selectedInput.val()) {
                    selectedInput[0].stepUp(1);
                    let el = $(this);
                    let product_qty = el.parent().find('.quantity-input').val();
                    let product_unique_id = el.closest('[data-product-id]').data('product-id');
                    let product_variant_id = el.closest('[data-variant-id]').data('product-variant-id');
                    getSubtotal(product_unique_id, product_qty, product_variant_id)
                }
            });

            $(document).on('click', '.substract', function () {
                var selectedInput = $(this).next('.quantity-input');
                if (selectedInput.val() > 1) {
                    selectedInput[0].stepDown(1);
                    let el = $(this);
                    let product_qty = el.parent().find('.quantity-input').val();
                    let product_unique_id = el.closest('[data-product-id]').data('product-id');
                    let product_variant_id = el.closest('[data-variant-id]').data('product-variant-id');
                    getSubtotal(product_unique_id, product_qty, product_variant_id)
                }
            });

            $(document).on('click', '.clear-cart-btn', function (){
                $('.loader').show();
                setTimeout(() => { $(location).attr('href', '{{theme_cart_clear_url()}}'); }, 300)
            });

            $(document).on('click', '.hf-cart-action[data-label="Close"]', function (e){
                let el = $(this);
                let product_hash_id = el.data('product_hash_id');
                $.ajax({
                    url: '{{theme_cart_remove_url()}}',
                    type: 'GET',
                    data: { 'product_hash_id': product_hash_id },
                    beforeSend: function (){ $('.loader').show(); },
                    success: function (data){
                        if (data.msg) {
                            toastr.success(data.msg);
                            if (data.empty_cart !== '') {
                                $('.hf-cart-section .container').html(data.empty_cart).hide();
                                $('.hf-cart-section .container').fadeIn();
                            }
                            $('.coupon-contents').parent().load(location.href + ' .coupon-contents');
                            $('.navbar-right-flex .cart-shopping').load(location.href + ' .cart-shopping');
                        }
                        $('.loader').hide();
                    },
                    error: function (){}
                })
            });

            function getSubtotal(productId, qty, variantId) {
                sendAjaxRequest(productId, qty, variantId, '{{theme_cart_update_url()}}', 'GET');
            }

            function sendAjaxRequest(productId, qty, variant_id, url, type) {
                $.ajax({
                    url: url, type: type,
                    data: { 'product_id': productId, 'quantity': qty, 'variant_id': variant_id },
                    beforeSend: function (){ $('.loader').show(); },
                    success: function (data){
                        if (data.type === 'success') {
                            toastr.success(data.msg);
                            $('#cart_tbody').html(data.markup);
                            $('.coupon-contents').html(data.cart_price_markup);
                        } else if(data.quantity_msg) {
                            toastr.warning(data.quantity_msg);
                        }
                        $('.loader').hide();
                    },
                    error: function (){ $('.loader').hide(); }
                })
            }
        })(jQuery)
    </script>
@endsection
