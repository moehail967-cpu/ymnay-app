<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Widgets are tenant-owned. Some legacy installs loaded this duplicate
        // migration centrally, where the table intentionally does not exist.
        if (Schema::hasTable('widgets') && !Schema::hasColumn('widgets', 'widget_namespace')) {
            Schema::table('widgets', function (Blueprint $table) {
                $table->string("widget_namespace")->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('widgets') && Schema::hasColumn('widgets', 'widget_namespace')) {
            Schema::table('widgets', function (Blueprint $table) {
                $table->dropColumn('widget_namespace');
            });
        }
    }
};
