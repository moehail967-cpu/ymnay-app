<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $connection = config('tenancy.database.central_connection', config('database.default'));
        Schema::connection($connection)
            ->create('ymnay_manual_wallets', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191);
                $table->text('description');
                $table->string('logo')->nullable();
                $table->boolean('status')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });

        if (Schema::connection($connection)->hasTable('price_plans') && Schema::connection($connection)->hasTable('plan_payment_gateways')) {
            foreach (DB::connection($connection)->table('price_plans')->pluck('id') as $planId) {
                DB::connection($connection)->table('plan_payment_gateways')->updateOrInsert(
                    ['plan_id' => $planId, 'payment_gateway_name' => 'ymnay_manual_wallet'],
                    ['status' => 1]
                );
            }
        }
    }

    public function down(): void
    {
        $connection = config('tenancy.database.central_connection', config('database.default'));
        if (Schema::connection($connection)->hasTable('plan_payment_gateways')) {
            DB::connection($connection)->table('plan_payment_gateways')->where('payment_gateway_name', 'ymnay_manual_wallet')->delete();
        }
        Schema::connection($connection)
            ->dropIfExists('ymnay_manual_wallets');
    }
};
