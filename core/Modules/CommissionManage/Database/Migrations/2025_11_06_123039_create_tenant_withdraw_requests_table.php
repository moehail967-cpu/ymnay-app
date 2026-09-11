<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantWithdrawRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('tenant_withdraw_requests')) {

        Schema::create('tenant_withdraw_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->text('account_details');
            $table->string('transaction_id')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['pending', 'complete', 'cancel'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('note')->nullable();

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
        if(Schema::hasTable('tenant_withdraw_requests')) {
            Schema::dropIfExists('tenant_withdraw_requests');
        }
    }
}
