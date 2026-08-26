<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionSelectedGateways extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('commission_selected_gateways')) {

                Schema::create('commission_selected_gateways', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('commission_setting_id');
                    $table->unsignedBigInteger('original_gateway_id'); // from payment_gateways.id
                    $table->string('name');
                    $table->string('image');
                    $table->text('description')->nullable();
                    $table->tinyInteger('status')->default(1);
                    $table->tinyInteger('test_mode')->default(0);
                    $table->longText('credentials')->nullable();
                    $table->timestamps();

                    $table->foreign('commission_setting_id')->references('id')->on('commission_settings')->onDelete('cascade');
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
        Schema::dropIfExists('commission_selected_gateways');
    }
}
