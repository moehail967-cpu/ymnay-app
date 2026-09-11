<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('commission_histories')) {

        Schema::create('commission_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('tenant_id')->nullable();
            $table->unsignedBigInteger('cus_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('uid')->unique()->nullable();
            $table->decimal('order_total', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            // payment status, status, order status
            $table->string('payment_status')->default('pending');
            $table->string('status')->default('pending');
            $table->timestamp('paid_by_tenant_at')->nullable();
            $table->json('tenant_payment_gateway')->nullable();

            $table->unsignedBigInteger('payment_log_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->timestamps();
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
        if(Schema::hasTable('commission_histories')) {
            Schema::dropIfExists('commission_histories');
        }
    }
}
