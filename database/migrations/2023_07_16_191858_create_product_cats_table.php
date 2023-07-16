<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         // category image and video gallery or filses -> files_table
        // category description -> article_table
        Schema::create('product_cats', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->bigInteger('cid'); // cid parent id category
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
        Schema::dropIfExists('product_cats');

    }
}
