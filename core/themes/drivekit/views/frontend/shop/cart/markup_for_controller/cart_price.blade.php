<div class="dk-order-row">
    <span class="dk-order-row-label">{{ __('Subtotal') }}</span>
    <span class="dk-order-row-value">{{ amount_with_currency_symbol(theme_cart_subtotal()) }}</span>
</div>
<div class="dk-order-row">
    <span class="dk-order-row-label">{{ __('Tax (Incl)') }}</span>
    <span class="dk-order-row-value">--</span>
</div>
<div class="dk-order-total">
    <span class="dk-order-total-label">{{ __('Total') }}</span>
    <span class="dk-order-total-value">{{ amount_with_currency_symbol(theme_cart_subtotal()) }}</span>
</div>
