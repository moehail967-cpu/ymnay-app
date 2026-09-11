<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('loyalty_transactions')) return;

        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->string('tenant_id', 255)->nullable()->change();
        });
    }

    public function down(): void {}
};
