<?php

namespace Modules\FeedSync\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\FeedSync\Models\FeedSyncCache;
use Modules\FeedSync\Services\GoogleFeedGenerator;
use Modules\FeedSync\Services\FacebookFeedGenerator;

class RegenerateFeedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $tenantId,
        private readonly array  $options
    ) {}

    public function handle(): void
    {
        $o = $this->options;

        $storeName = $o['store_name'] ?? 'Store';
        $storeUrl  = $o['store_url']  ?? url('/');
        $currency  = strtoupper($o['currency'] ?? 'USD');

        if (!empty($o['google_merchant_enabled'])) {
            $generator = new GoogleFeedGenerator(
                storeName:       $storeName,
                storeUrl:        $storeUrl,
                currency:        $currency,
                shippingCountry: $o['google_shipping_country'] ?? 'US',
                shippingPrice:   $o['google_shipping_price']   ?? '0.00',
                brandField:      $o['google_brand_field']       ?? 'product_brand',
                customBrand:     $o['google_custom_brand']      ?? '',
                condition:       $o['google_condition']         ?? 'new',
            );

            FeedSyncCache::updateOrInsert(
                ['tenant_id' => $this->tenantId, 'feed_type' => 'google'],
                ['content' => $generator->generate(), 'generated_at' => now()]
            );
        }

        if (!empty($o['facebook_catalog_enabled'])) {
            $generator = new FacebookFeedGenerator(
                storeName:           $storeName,
                storeUrl:            $storeUrl,
                currency:            $currency,
                includeSalePrice:    (bool) ($o['facebook_include_sale_price'] ?? false),
                defaultAvailability: $o['facebook_availability'] ?? 'in stock',
            );

            FeedSyncCache::updateOrInsert(
                ['tenant_id' => $this->tenantId, 'feed_type' => 'facebook'],
                ['content' => $generator->generate(), 'generated_at' => now()]
            );
        }
    }
}
