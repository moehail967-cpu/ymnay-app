<?php

namespace Modules\TierPricing\Src;

use Illuminate\Support\Facades\DB;

class TierPricingService
{
    private ?int $tenantId;

    public function __construct(?int $tenantId)
    {
        $this->tenantId = $tenantId;
    }

    private function query()
    {
        return DB::table('product_tier_prices')->where('tenant_id', $this->tenantId);
    }

    /** All tiers for a product, ordered min_qty ASC */
    public function getTiersForProduct(int $productId): array
    {
        return $this->query()
            ->where('product_id', $productId)
            ->orderBy('min_qty')
            ->get()
            ->all();
    }

    /** Returns products (from products table) that have tiers configured, paginated */
    public function getProductsWithTiers(int $perPage = 20)
    {
        $productIds = $this->query()
            ->distinct()
            ->pluck('product_id')
            ->all();

        return DB::table('products')
            ->whereIn('id', $productIds)
            ->select('id', 'name', 'slug', 'regular_price', 'status')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Find the best applicable tier for a given product + quantity.
     * Returns the tier object or null.
     */
    public function getApplicableTier(int $productId, int $qty): ?object
    {
        $tiers = $this->getTiersForProduct($productId);
        $best  = null;

        foreach ($tiers as $tier) {
            if ($qty >= $tier->min_qty) {
                $best = $tier;
            }
        }

        return $best;
    }

    /**
     * Calculate the per-unit price after applying the tier discount.
     * $regularPrice — the product's base price (before any tier).
     */
    public function calculateTierPrice(object $tier, float $regularPrice): float
    {
        return match ($tier->discount_type) {
            'percentage'  => max(0, $regularPrice - ($regularPrice * $tier->discount_value / 100)),
            'flat'        => max(0, $regularPrice - $tier->discount_value),
            'fixed_price' => max(0, (float) $tier->discount_value),
            default       => $regularPrice,
        };
    }

    /** Replace all tiers for a product with the given array */
    public function setTiers(int $productId, array $tiers): void
    {
        $this->query()->where('product_id', $productId)->delete();

        $rows = [];
        foreach ($tiers as $t) {
            if (empty($t['min_qty']) || ! isset($t['discount_value'])) {
                continue;
            }
            $rows[] = [
                'tenant_id'      => $this->tenantId,
                'product_id'     => $productId,
                'min_qty'        => (int) $t['min_qty'],
                'discount_type'  => $t['discount_type'] ?? 'percentage',
                'discount_value' => (float) $t['discount_value'],
                'label'          => $t['label'] ?? null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        if ($rows) {
            DB::table('product_tier_prices')->insert($rows);
        }
    }

    public function deleteTiersForProduct(int $productId): void
    {
        $this->query()->where('product_id', $productId)->delete();
    }

    /** Pre-load tiers for a set of product IDs (for cart batch processing) */
    public function getTiersForProducts(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $rows = $this->query()
            ->whereIn('product_id', $productIds)
            ->orderBy('product_id')
            ->orderBy('min_qty')
            ->get();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row->product_id][] = $row;
        }

        return $grouped;
    }
}
