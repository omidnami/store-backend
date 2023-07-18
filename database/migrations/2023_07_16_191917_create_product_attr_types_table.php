<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttrTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //this table attribute product title
        Schema::create('product_attr_types', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->bigInteger('cid'); // product cat id " 0 = all cats"
            $table->string('type'); //input type (radio, select, textInput)
            $table->string('data')->nullable(); // radio select data values
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
        Schema::dropIfExists('product_attr_types');

    }
}
