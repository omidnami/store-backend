<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchEnginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_engins', function (Blueprint $table) {
            $table->id();
            $table->string('meta_key')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('canonical')->nullable();
            $table->bigInteger('pid');
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_engins');
    }
}
