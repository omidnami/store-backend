<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepoLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depo_locations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); //نام انبار
            $table->bigInteger('maxQuty')->default(1000000000); //حجم انباد
            $table->text('address')->nullable(); //ادرس انبار
            $table->string('depo')->nullable(); // شناسه سوله
            $table->string('row')->nullable(); // شناسه ردیف
            $table->string('Shelf')->nullable(); // شناسه قفسه
            $table->bigInteger('depoMan')->nullable(); // شناسه قفسه
            $table->bigInteger('user')->nullable(); // شناسه قفسه
            $table->bigInteger('did')->default(0);
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
        Schema::dropIfExists('depo_locations');
    }
}
