<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the unique index before changing column type (MySQL requires this)
        Schema::table('plugin_options', function (Blueprint $table) {
            $table->dropUnique('unique_plugin_option');
            $table->dropIndex(['plugin_id', 'tenant_id']);
        });

        // Change tenant_id from unsignedBigInteger to string (Tenancy uses domain as ID)
        DB::statement('ALTER TABLE plugin_options MODIFY COLUMN tenant_id VARCHAR(255) NULL');

        // Recreate indexes
        Schema::table('plugin_options', function (Blueprint $table) {
            $table->unique(['plugin_id', 'tenant_id', 'option_key'], 'unique_plugin_option');
            $table->index(['plugin_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('plugin_options', function (Blueprint $table) {
            $table->dropUnique('unique_plugin_option');
            $table->dropIndex(['plugin_id', 'tenant_id']);
        });

        DB::statement('ALTER TABLE plugin_options MODIFY COLUMN tenant_id BIGINT UNSIGNED NULL');

        Schema::table('plugin_options', function (Blueprint $table) {
            $table->unique(['plugin_id', 'tenant_id', 'option_key'], 'unique_plugin_option');
            $table->index(['plugin_id', 'tenant_id']);
        });
    }
};
