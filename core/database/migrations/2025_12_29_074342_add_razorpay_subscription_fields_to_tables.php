<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add Razorpay subscription fields to payment_logs table
        Schema::table('payment_logs', function (Blueprint $table) {
            $table->string('razorpay_subscription_id')->nullable()->after('transaction_id');
            $table->string('razorpay_plan_id')->nullable()->after('razorpay_subscription_id');
            $table->boolean('is_recurring')->default(0)->after('is_renew');
            $table->enum('subscription_status', ['active', 'paused', 'cancelled', 'pending', 'expired'])->nullable()->after('is_recurring');
            $table->timestamp('next_billing_date')->nullable()->after('expire_date');
        });

        // Add Razorpay subscription fields to tenants table
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('razorpay_subscription_id')->nullable()->after('renewal_payment_log_id');
            $table->boolean('recurring_enabled')->default(0)->after('razorpay_subscription_id');
            $table->integer('payment_grace_days')->default(3)->after('recurring_enabled');
            $table->timestamp('grace_period_end')->nullable()->after('payment_grace_days');
        });

        // Add Razorpay plan ID to price_plans table
        Schema::table('price_plans', function (Blueprint $table) {
            $table->string('razorpay_plan_id')->nullable()->after('price');
            $table->timestamp('razorpay_synced_at')->nullable()->after('razorpay_plan_id');
        });

        // Add webhook secret to payment_gateways credentials
        // Note: This will be handled via JSON update in credentials field
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_logs', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_subscription_id',
                'razorpay_plan_id',
                'is_recurring',
                'subscription_status',
                'next_billing_date'
            ]);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_subscription_id',
                'recurring_enabled',
                'payment_grace_days',
                'grace_period_end'
            ]);
        });

        Schema::table('price_plans', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_plan_id',
                'razorpay_synced_at'
            ]);
        });
    }
};
