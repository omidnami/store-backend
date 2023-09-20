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
            $table->json('cid'); // product cat id " 0 = all cats"
            $table->string('type')->nullable(); //input type (radio, select, textInput)
            $table->json('data')->nullable(); // radio select data values
            $table->string('dataType')->nullable(); // radio select data values
            $table->string('lang');
            $table->string('uniqueId');
            $table->string('gp')->default(null)->nullable();
            $table->string('link')->nullable();
            $table->tinyInteger('status')->default(1);
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
