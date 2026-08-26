<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Affiliate accounts — one per user who joins the programme
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('code', 32)->unique();        // unique referral code
            $table->string('status', 20)->default('active'); // active | paused | banned
            $table->decimal('balance', 12, 2)->default(0);   // unpaid earnings
            $table->decimal('total_earned', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['tenant_id', 'code']);
        });

        // Commission earned per order
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('affiliate_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('referred_user_id')->nullable();
            $table->decimal('order_total', 12, 2);
            $table->decimal('rate', 5, 2);               // commission % at time of order
            $table->decimal('amount', 12, 2);            // actual commission earned
            $table->string('status', 20)->default('pending'); // pending | approved | paid | cancelled
            $table->timestamps();

            $table->index(['affiliate_id', 'status']);
            $table->index(['tenant_id', 'order_id']);
        });

        // Payout records
        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('affiliate_id');
            $table->decimal('amount', 12, 2);
            $table->string('method', 40)->default('manual'); // manual | paypal | bank
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['affiliate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_payouts');
        Schema::dropIfExists('affiliate_commissions');
        Schema::dropIfExists('affiliates');
    }
};
