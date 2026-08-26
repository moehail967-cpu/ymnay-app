<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_setting_id')->constrained('commission_settings')->onDelete('cascade');
            $table->string('gateway_id')->unique(); // e.g., gateway_1234567890
            $table->string('name'); //
            $table->json('fields')->nullable(); // Custom fields configuration
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraw_gateways');
    }
}
