<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignSuggest extends Command
{
    protected $signature   = 'campaigns:suggest {--days=60 : Days without sales to consider slow-moving} {--min-stock=10 : Minimum stock units to trigger suggestion}';
    protected $description = 'Find slow-moving products with high stock and store campaign suggestions for admin review.';

    public function handle(): int
    {
        $days     = (int) $this->option('days');
        $minStock = (int) $this->option('min-stock');
        $cutoff   = now()->subDays($days)->toDateTimeString();

        Log::info("campaigns:suggest started — days={$days}, min-stock={$minStock}");

        // Get all tenants that have the product_inventories table (multi-tenant: one DB per tenant or shared?)
        // Using the products table filtered by tenant_id (shared DB pattern)
        $tenantIds = DB::table('products')->distinct()->pluck('tenant_id')->filter()->all();

        $totalSuggestions = 0;

        // Ensure suggestions table exists
        if (! \Schema::hasTable('campaign_suggestions')) {
            \Schema::create('campaign_suggestions', function ($table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable()->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->string('product_name');
                $table->unsignedInteger('stock_count')->default(0);
                $table->unsignedInteger('sales_last_n_days')->default(0);
                $table->unsignedInteger('days_window')->default(60);
                $table->enum('status', ['pending', 'dismissed', 'acted'])->default('pending');
                $table->timestamps();
                $table->unique(['tenant_id', 'product_id']);
            });
        }

        foreach ($tenantIds as $tenantId) {
            // Products with stock but no order_products in the last N days
            $slowProducts = DB::table('products as p')
                ->join('product_inventories as pi', 'pi.product_id', '=', 'p.id')
                ->leftJoin('order_products as op', function ($join) use ($cutoff) {
                    $join->on('op.product_id', '=', 'p.id')
                         ->where('op.created_at', '>=', $cutoff);
                })
                ->where('p.tenant_id', $tenantId)
                ->where('p.status', 'publish')
                ->where('pi.stock_count', '>=', $minStock)
                ->whereNull('op.product_id') // no orders in window
                ->select('p.id', 'p.name', 'pi.stock_count')
                ->get();

            foreach ($slowProducts as $product) {
                // Skip if already has an active campaign
                $hasCampaign = DB::table('campaign_products')
                    ->join('campaigns', 'campaigns.id', '=', 'campaign_products.campaign_id')
                    ->where('campaign_products.product_id', $product->id)
                    ->where('campaigns.status', 'publish')
                    ->where('campaigns.end_date', '>=', now())
                    ->exists();

                if ($hasCampaign) {
                    continue;
                }

                // Upsert suggestion
                DB::table('campaign_suggestions')->updateOrInsert(
                    ['tenant_id' => $tenantId, 'product_id' => $product->id],
                    [
                        'product_name'       => $product->name,
                        'stock_count'        => $product->stock_count,
                        'sales_last_n_days'  => 0,
                        'days_window'        => $days,
                        'status'             => 'pending',
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]
                );

                $totalSuggestions++;
            }
        }

        $this->info("Done — {$totalSuggestions} suggestions stored.");
        Log::info("campaigns:suggest finished — {$totalSuggestions} suggestions.");

        return self::SUCCESS;
    }
}
