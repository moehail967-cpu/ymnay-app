# YmnayCustom

Ymnay-owned extensions that must remain independent from upstream Nazmart payment gateways.

## Manual wallets

- The landlord creates the approved wallet catalog; no wallets are seeded.
- Each tenant enables catalog wallets and stores its own receiving details in tenant static options.
- Package and shop checkouts store a snapshot of the selected wallet plus the receipt image.
- Payments stay pending until the landlord or tenant administrator approves them.
- Rejection stores a reason for the customer and sends email/SMS through the platform services.

## Data

- Central table: `ymnay_manual_wallets`.
- Tenant configuration: `ymnay_manual_wallet_settings` and `ymnay_manual_wallet_status` static options.
- Package snapshot: `payment_logs.custom_fields` and `payment_logs.attachments`.
- Shop-order snapshot: `product_orders.payment_meta` and `product_orders.checkout_image_path`.

Run the module migration only after the feature branch has passed staging acceptance tests.
