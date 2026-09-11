<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'theme_slug')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('theme_slug')->nullable()->after('status_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'theme_slug')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('theme_slug');
            });
        }
    }
};
