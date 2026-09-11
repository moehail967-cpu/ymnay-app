<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateCommissionSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('commission_settings')) {

            Schema::create('commission_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->constrained()->nullOnDelete();

                // Commission Type
                $table->enum('commission_type', [
                    'subscription_only',
                    'subscription_and_commission',
                    'commission_only'
                ])->default('subscription_only');

                // Payment Gateway Source
                $table->enum('payment_gateway_source', [
                    'landlord_gateway',
                    'tenant_default_gateway'
                ])->default('tenant_default_gateway');

                // Selected Gateway (required only if payment_gateway_source = landlord_gateway)
                $table->string('selected_gateway')->nullable();

                // Commission Rate (percentage for per-order commission)
                $table->decimal('commission_rate')->default(0);
                $table->json('payment_gateways')->nullable();
                $table->timestamps();

                // Ensure one setting per landlord

                // Indexes for better performance
                $table->index(['user_id', 'commission_type']);
                $table->index('payment_gateway_source');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('commission_settings')) {

            Schema::dropIfExists('commission_settings');
        }
    }
}
