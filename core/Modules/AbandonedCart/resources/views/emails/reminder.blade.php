<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; background: #f9f9f9; color: #333; }
.wrap { max-width: 580px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.header { background: #c8855a; padding: 32px; text-align: center; color: #fff; }
.header h2 { font-size: 22px; font-weight: bold; margin: 0; }
.content { padding: 32px; }
.content p { margin-bottom: 16px; font-size: 15px; line-height: 1.6; color: #444; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
th { background: #f5f5f5; padding: 10px 12px; text-align: left; font-size: 12px; font-weight: bold; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 14px; color: #444; }
.total-row td { font-weight: bold; color: #222; border-bottom: none; background: #fafafa; }
.cta { display: block; background: #c8855a; color: #fff !important; text-align: center; padding: 14px 32px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 16px; margin: 24px 0; }
.footer { background: #f5f5f5; padding: 16px 32px; font-size: 12px; color: #999; text-align: center; }
.footer a { color: #999; text-decoration: underline; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h2>You left something behind!</h2>
    </div>
    <div class="content">
        <p>Hi {{ $cart->email }},</p>

        @php
            $bodyText = $body ?? '';
            $cartLink = url('/cart');
            $storeName = $sender_name ?? config('app.name', 'Our Store');
            $bodyText = str_replace(
                ['{customer_name}', '{cart_total}', '{cart_link}', '{store_name}'],
                [$cart->email, $cart->currency . ' ' . number_format($cart->cart_total, 2), $cartLink, $storeName],
                $bodyText
            );
        @endphp

        @if($bodyText)
        <p style="white-space: pre-line;">{{ $bodyText }}</p>
        @endif

        @php
            $items = json_decode($cart->cart_items, true) ?? [];
        @endphp

        @if(!empty($items))
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item['name'] ?? ($item['options']['name'] ?? 'Product') }}</td>
                    <td>{{ $item['qty'] ?? 1 }}</td>
                    <td>{{ $cart->currency }} {{ number_format($item['price'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2">Cart Total</td>
                    <td>{{ $cart->currency }} {{ number_format($cart->cart_total, 2) }}</td>
                </tr>
            </tbody>
        </table>
        @else
        <p><strong>Cart Total: {{ $cart->currency }} {{ number_format($cart->cart_total, 2) }}</strong></p>
        @endif

        <a href="{{ url('/cart') }}" class="cta">Return to Cart &rarr;</a>

        <p style="font-size:13px; color:#888;">If you have any questions, feel free to reply to this email.</p>
    </div>
    <div class="footer">
        You received this email because you shopped at {{ $sender_name ?? config('app.name') }}. &nbsp;|&nbsp;
        <a href="{{ url('/') }}">Visit Store</a>
    </div>
</div>
</body>
</html>
