<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            if (!Schema::hasColumn('themes', 'version')) {
                $table->string('version')->nullable()->after('is_premium');
            }
            if (!Schema::hasColumn('themes', 'niche')) {
                $table->string('niche')->nullable()->after('version');
            }
            if (!Schema::hasColumn('themes', 'installed_version')) {
                $table->string('installed_version')->nullable()->after('niche');
            }
            if (!Schema::hasColumn('themes', 'update_available')) {
                $table->boolean('update_available')->default(false)->after('installed_version');
            }
        });
    }

    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumnIfExists('version');
            $table->dropColumnIfExists('niche');
            $table->dropColumnIfExists('installed_version');
            $table->dropColumnIfExists('update_available');
        });
    }
};
