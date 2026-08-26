<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticOptionCentralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('static_option_centrals')) {
            Schema::create('static_option_centrals', function (Blueprint $table) {
                $table->id();
                $table->string('option_name');
                $table->longText('option_value')->nullable();
                $table->string('unique_key')->nullable();
                $table->timestamps();
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
        if(Schema::hasTable('static_option_centrals')){
            Schema::dropIfExists('static_option_centrals');
        }

    }
}
