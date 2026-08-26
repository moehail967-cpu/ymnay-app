<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['affiliates', 'affiliate_commissions', 'affiliate_payouts'] as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('tenant_id')->change();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['affiliates', 'affiliate_commissions', 'affiliate_payouts'] as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('tenant_id')->change();
                });
            }
        }
    }
};
