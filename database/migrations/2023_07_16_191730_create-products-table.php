<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // product image and video gallery or filses -> files_table
        // product description -> article_table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->json('cat');
            $table->string('mainCat')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('type')->nullable();
            $table->boolean('orginal')->default(true);
            $table->json('noghat')->nullable();
            $table->longText('text')->nullable();
            $table->integer('sk'); // sk number or serial generate 8 digits
            $table->string('uniqueId');
            $table->string('lang');
            $table->boolean('status')->default(true);
            $table->json('box')->nullable();
            $table->json('settings')->nullable();
            $table->bigInteger('user');
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
        Schema::dropIfExists('products');

    }
}
