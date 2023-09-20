<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //attr data for single product
        Schema::create('product_attrs', function (Blueprint $table) {
            $table->id();
            $table->string('value')->nullable();
            $table->bigInteger('aid')->nullable(); // attr id
            $table->bigInteger('pid'); // product id
            $table->json('data')->nullable(); // product id
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
        Schema::dropIfExists('product_attrs');
    }
}
