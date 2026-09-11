<?php

namespace Modules\ProductBundles\Src;

use Illuminate\Support\Facades\DB;
use Gloudemans\Shoppingcart\Facades\Cart;

class BundleService
{
    private ?int $tenantId;

    public function __construct(?int $tenantId)
    {
        $this->tenantId = $tenantId;
    }

    private function query()
    {
        return DB::table('product_bundles')->where('tenant_id', $this->tenantId);
    }

    public function getActiveBundles()
    {
        return $this->query()->where('status', 'active')->get();
    }

    public function getAllBundles(int $perPage = 20)
    {
        return $this->query()
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function find(int $id): ?object
    {
        return $this->query()->where('id', $id)->first();
    }

    public function getItems(int $bundleId): array
    {
        return DB::table('product_bundle_items')
            ->where('bundle_id', $bundleId)
            ->pluck('product_id')
            ->all();
    }

    public function getBundlesForProduct(int $productId): array
    {
        $bundleIds = DB::table('product_bundle_items')
            ->where('product_id', $productId)
            ->pluck('bundle_id')
            ->all();

        if (empty($bundleIds)) {
            return [];
        }

        return $this->query()
            ->where('status', 'active')
            ->whereIn('id', $bundleIds)
            ->get()
            ->map(function ($bundle) {
                $productIds = $this->getItems($bundle->id);
                $products   = DB::table('products')
                    ->whereIn('id', $productIds)
                    ->select('id', 'name', 'slug', 'image')
                    ->get();
                $bundle->products = $products;
                return $bundle;
            })
            ->all();
    }

    /**
     * Check if any active bundle is fully satisfied by the current cart.
     * Returns [bundle, discount_amount] or null.
     */
    public function checkCartForBundle(): ?array
    {
        $cartProductIds = collect(Cart::instance('default')->content())
            ->pluck('options.product_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($cartProductIds)) {
            return null;
        }

        foreach ($this->getActiveBundles() as $bundle) {
            $bundleProductIds = $this->getItems($bundle->id);
            if (empty($bundleProductIds)) {
                continue;
            }

            $allPresent = count(array_intersect($bundleProductIds, $cartProductIds)) === count($bundleProductIds);

            if ($allPresent) {
                $discount = $this->calculateDiscount($bundle, $cartProductIds, $bundleProductIds);
                if ($discount > 0) {
                    return [$bundle, $discount];
                }
            }
        }

        return null;
    }

    private function calculateDiscount(object $bundle, array $cartProductIds, array $bundleProductIds): float
    {
        if ($bundle->discount_type === 'flat') {
            return (float) $bundle->discount_value;
        }

        // Percentage — applied to the subtotal of bundle items in cart
        $bundleSubtotal = 0;
        foreach (Cart::instance('default')->content() as $item) {
            $pid = $item->options->product_id ?? null;
            if ($pid && in_array($pid, $bundleProductIds)) {
                $bundleSubtotal += (float) $item->subtotal;
            }
        }

        return round($bundleSubtotal * $bundle->discount_value / 100, 2);
    }

    public function create(array $data, array $productIds): int
    {
        $id = DB::table('product_bundles')->insertGetId([
            'tenant_id'      => $this->tenantId,
            'name'           => $data['name'],
            'description'    => $data['description'] ?? null,
            'discount_type'  => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'status'         => $data['status'] ?? 'active',
            'sold_count'     => 0,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $this->syncItems($id, $productIds);
        return $id;
    }

    public function update(int $id, array $data, array $productIds): void
    {
        DB::table('product_bundles')->where('id', $id)->update([
            'name'           => $data['name'],
            'description'    => $data['description'] ?? null,
            'discount_type'  => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'status'         => $data['status'] ?? 'active',
            'updated_at'     => now(),
        ]);

        $this->syncItems($id, $productIds);
    }

    private function syncItems(int $bundleId, array $productIds): void
    {
        DB::table('product_bundle_items')->where('bundle_id', $bundleId)->delete();

        $rows = array_map(fn($pid) => ['bundle_id' => $bundleId, 'product_id' => $pid], $productIds);
        if ($rows) {
            DB::table('product_bundle_items')->insert($rows);
        }
    }

    public function delete(int $id): void
    {
        DB::table('product_bundles')->where('id', $id)->delete();
    }

    public function incrementSoldCount(int $bundleId): void
    {
        DB::table('product_bundles')->where('id', $bundleId)->increment('sold_count');
    }
}
