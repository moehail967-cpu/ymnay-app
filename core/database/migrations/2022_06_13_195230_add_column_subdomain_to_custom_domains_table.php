<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSubdomainToCustomDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('custom_domains', 'unique_key')) {
            Schema::table('custom_domains', function (Blueprint $table) {
                $table->string('unique_key')->nullable();
            });
        }

    }


    public function down()
    {
        if(Schema::hasColumn('custom_domains', 'unique_key')) {
            Schema::table('custom_domains', function (Blueprint $table) {
                $table->dropColumn('unique_key');
            });
        }
    }
}
