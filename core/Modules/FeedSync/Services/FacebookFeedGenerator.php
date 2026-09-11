<?php

namespace Modules\FeedSync\Services;

use Modules\Product\Entities\Product;

class FacebookFeedGenerator
{
    public function __construct(
        private readonly string $storeName,
        private readonly string $storeUrl,
        private readonly string $currency,
        private readonly bool   $includeSalePrice,
        private readonly string $defaultAvailability,
    ) {}

    public function generate(): string
    {
        $products = Product::with(['product_category.category', 'brand', 'inventory'])
            ->where('status', 'publish')
            ->get();

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $feed = $dom->createElementNS('http://www.w3.org/2005/Atom', 'feed');
        $feed->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:g', 'http://base.google.com/ns/1.0');

        $baseUrl = rtrim($this->storeUrl, '/');

        foreach ($products as $product) {
            $entry = $dom->createElement('entry');
            $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:id', (string) $product->id));
            $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:title', htmlspecialchars($product->name ?? '')));
            $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:description', htmlspecialchars(strip_tags($product->short_description ?? $product->description ?? ''))));
            $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:link', $baseUrl . '/' . ($product->slug ?? $product->id)));

            // Main image via attachment ID
            $imgData = get_attachment_image_by_id($product->image_id);
            if (!empty($imgData['img_url'])) {
                $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:image_link', $imgData['img_url']));
            }

            // Price: dynamic campaign price takes priority
            $regularPrice = (float) ($product->price ?? 0);
            $salePrice    = (float) ($product->sale_price ?? 0);

            $dynamicPricing = get_product_dynamic_price($product);
            $effectiveSale  = (float) ($dynamicPricing['sale_price'] ?? $salePrice);
            $effectiveBase  = (float) ($dynamicPricing['regular_price'] ?? $regularPrice);

            $feedPrice = $effectiveSale > 0 ? $effectiveSale : $effectiveBase;
            $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:price', number_format($feedPrice, 2, '.', '') . ' ' . $this->currency));

            if ($this->includeSalePrice && $effectiveSale > 0 && $effectiveSale < $effectiveBase) {
                $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:sale_price', number_format($effectiveSale, 2, '.', '') . ' ' . $this->currency));
            }

            // Stock via inventory relation
            $stockCount   = $product->inventory?->stock_count ?? 0;
            $availability = $stockCount > 0 ? 'in stock' : ($this->defaultAvailability ?: 'out of stock');
            $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:availability', $availability));
            $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:condition', 'new'));
            $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:brand', htmlspecialchars($product->brand?->name ?? $this->storeName)));

            $catName = $product->product_category?->category?->name ?? '';
            if ($catName) {
                $entry->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:product_type', htmlspecialchars($catName)));
            }

            $feed->appendChild($entry);
        }

        $dom->appendChild($feed);
        return $dom->saveXML();
    }
}
