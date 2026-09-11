<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('email');
            $table->text('question');
            $table->text('answer')->nullable();
            $table->enum('status', ['pending', 'answered', 'rejected'])->default('pending')->index();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'product_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_questions');
    }
};
