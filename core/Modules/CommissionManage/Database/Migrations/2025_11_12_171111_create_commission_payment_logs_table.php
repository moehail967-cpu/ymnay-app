<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionPaymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_payment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_gateway');
            $table->string('transaction_id')->nullable();
            $table->enum('payment_status', ['pending', 'complete', 'failed'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->json('commission_ids')->nullable(); // Store which commissions were paid
            $table->string('order_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('commission_payment_logs');
    }
}
