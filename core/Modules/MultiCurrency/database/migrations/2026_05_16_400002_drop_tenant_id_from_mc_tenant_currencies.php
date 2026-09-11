<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mc_tenant_currencies')) {
            return;
        }

        if (Schema::hasColumn('mc_tenant_currencies', 'tenant_id')) {
            Schema::table('mc_tenant_currencies', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mc_tenant_currencies')) {
            Schema::table('mc_tenant_currencies', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            });
        }
    }
};
