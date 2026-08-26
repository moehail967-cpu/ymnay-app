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
        if(!Schema::hasColumn('themes','theme_url')) {
            Schema::table('themes', function (Blueprint $table) {
                $table->string('theme_url')->nullable();
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
        if(Schema::hasColumn('themes','theme_url')) {
            Schema::table('theme', function (Blueprint $table) {
                $table->dropColumn('theme_url');
            });
        }
    }
};
