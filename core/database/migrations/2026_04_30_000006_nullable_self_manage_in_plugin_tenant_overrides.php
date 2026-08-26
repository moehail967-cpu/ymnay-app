<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plugin_tenant_overrides', function (Blueprint $table) {
            $table->boolean('self_manage')->nullable()->default(null)->change();
        });

        // Reset existing rows to null — landlord has not explicitly configured self-management yet
        DB::table('plugin_tenant_overrides')->update(['self_manage' => null]);
    }

    public function down(): void
    {
        Schema::table('plugin_tenant_overrides', function (Blueprint $table) {
            $table->boolean('self_manage')->default(false)->change();
        });
    }
};
