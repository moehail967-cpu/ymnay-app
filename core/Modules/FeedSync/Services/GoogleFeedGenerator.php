<?php

namespace Modules\FeedSync\Services;

use Modules\Product\Entities\Product;

class GoogleFeedGenerator
{
    public function __construct(
        private readonly string $storeName,
        private readonly string $storeUrl,
        private readonly string $currency,
        private readonly string $shippingCountry,
        private readonly string $shippingPrice,
        private readonly string $brandField,
        private readonly string $customBrand,
        private readonly string $condition,
    ) {}

    public function generate(): string
    {
        $products = Product::with(['product_category.category', 'brand', 'inventory', 'gallery_images'])
            ->where('status', 'publish')
            ->get();

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $rss = $dom->createElement('rss');
        $rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
        $rss->setAttribute('version', '2.0');

        $channel = $dom->createElement('channel');
        $channel->appendChild($dom->createElement('title', htmlspecialchars($this->storeName)));
        $channel->appendChild($dom->createElement('link', $this->storeUrl));
        $channel->appendChild($dom->createElement('description', 'Product feed'));

        $baseUrl = rtrim($this->storeUrl, '/');

        foreach ($products as $product) {
            $item = $dom->createElement('item');
            $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:id', (string) $product->id));
            $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:title', htmlspecialchars($product->name ?? '')));
            $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:description', htmlspecialchars(strip_tags($product->short_description ?? $product->description ?? ''))));
            $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:link', $baseUrl . '/' . ($product->slug ?? $product->id)));

            // Main image via attachment ID
            $imgData = get_attachment_image_by_id($product->image_id);
            if (!empty($imgData['img_url'])) {
                $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:image_link', $imgData['img_url']));
            }

            // Gallery additional images
            foreach ($product->gallery_images ?? [] as $galleryMedia) {
                $gData = get_attachment_image_by_id($galleryMedia->id);
                if (!empty($gData['img_url'])) {
                    $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:additional_image_link', $gData['img_url']));
                }
            }

            // Price: use sale_price if set, otherwise regular price
            $regularPrice = (float) ($product->price ?? 0);
            $salePrice    = (float) ($product->sale_price ?? 0);

            // Dynamic campaign price takes priority over sale_price
            $dynamicPricing = get_product_dynamic_price($product);
            $effectiveSale  = (float) ($dynamicPricing['sale_price'] ?? $salePrice);
            $effectiveBase  = (float) ($dynamicPricing['regular_price'] ?? $regularPrice);

            $feedPrice = $effectiveSale > 0 ? $effectiveSale : $effectiveBase;
            $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:price', number_format($feedPrice, 2, '.', '') . ' ' . $this->currency));

            if ($effectiveSale > 0 && $effectiveBase > 0 && $effectiveSale < $effectiveBase) {
                $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:sale_price', number_format($effectiveSale, 2, '.', '') . ' ' . $this->currency));
            }

            // Stock via inventory relation
            $stockCount  = $product->inventory?->stock_count ?? 0;
            $availability = $stockCount > 0 ? 'in stock' : 'out of stock';
            $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:availability', $availability));
            $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:condition', $this->condition ?: 'new'));

            $brand = match ($this->brandField) {
                'store_name' => $this->storeName,
                'custom'     => $this->customBrand,
                default      => $product->brand?->name ?? $this->storeName,
            };
            $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:brand', htmlspecialchars($brand)));

            $catName = $product->product_category?->category?->name ?? '';
            if ($catName) {
                $item->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:google_product_category', htmlspecialchars($catName)));
            }

            if ($this->shippingCountry) {
                $shipping = $dom->createElementNS('http://base.google.com/ns/1.0', 'g:shipping');
                $shipping->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:country', $this->shippingCountry));
                $shipping->appendChild($dom->createElementNS('http://base.google.com/ns/1.0', 'g:price', $this->shippingPrice . ' ' . $this->currency));
                $item->appendChild($shipping);
            }

            $channel->appendChild($item);
        }

        $rss->appendChild($channel);
        $dom->appendChild($rss);

        return $dom->saveXML();
    }
}
