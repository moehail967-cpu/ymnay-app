<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pages', 'use_page_builder')) {
            return;
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('use_page_builder')->default(false)->after('page_content');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('use_page_builder');
        });
    }
};
