<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('plan_payment_gateways')) {

            Schema::create('plan_payment_gateways', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('plan_id');
                $table->string('payment_gateway_name');
                $table->boolean('status')->default(0);
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
        if(Schema::hasTable('plan_payment_gateways')) {
            Schema::dropIfExists('plan_payment_gateways');
        }
    }
};
