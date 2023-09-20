<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDynamicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // dynamic colors, size and... for single product
        //type
        //value
        //status
        //depo
        //price
        //img
        //pid
        //data
        Schema::create('product_dynamics', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->string('type'); // type title (color, size, ...)
            $table->bigInteger('pid'); // product id
            $table->boolean('status')->default(true);
            $table->json('depo')->nullable();
            $table->json('price')->nullable();
            $table->bigInteger('img')->default(0);
            $table->json('data');
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
        Schema::dropIfExists('product_dynamics');

    }
}
