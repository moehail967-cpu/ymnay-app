<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'referred_by_affiliate')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('referred_by_affiliate')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referred_by_affiliate');
        });
    }
};
