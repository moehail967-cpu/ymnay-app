<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Runs via PluginBase::run_migrations() (called from on_activate()).
 *
 * The wallet catalog and each tenant's activation/field-values are stored
 * through the platform's own Settings system (get_option/update_option),
 * NOT in this table -- see PluginOptions.php. This table only holds the
 * customer-submitted transfer screenshots that need to be listed, filtered
 * by status, and paginated in the tenant admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('catalog_wallet_id');   // references the wallet's id inside the landlord catalog (settings JSON)
            $table->string('wallet_name');         // denormalized snapshot, in case the catalog entry changes/is removed later
            $table->string('screenshot_path');
            $table->string('verification_status')->default('pending'); // pending|approved|rejected
            $table->text('admin_note')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_payment_proofs');
    }
};
