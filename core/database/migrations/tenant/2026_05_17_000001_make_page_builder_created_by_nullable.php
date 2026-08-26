<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The vendor PageBuilderController calls updateOrCreate() with created_by in the
        // update values, but the API route uses 'api' middleware (no session) so
        // Auth::guard('admin')->id() returns null. Making this nullable avoids the
        // NOT NULL constraint violation on subsequent saves.
        Schema::table('page_builder_content', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->change();
        });

        Schema::table('page_builder_widgets', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('page_builder_content', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
        });

        Schema::table('page_builder_widgets', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
        });
    }
};
