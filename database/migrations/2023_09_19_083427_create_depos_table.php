<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depos', function (Blueprint $table) {
            $table->id();
            $table->integer('productSk'); //کد اس کی کالا
            $table->bigInteger('dynamicId')->default(0); //شناسه تنوع
            $table->bigInteger('quty'); //تعداد
            $table->string('partNumber'); // پارت نامبر
            $table->json('depoLoc'); // پارت نامبر
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
        Schema::dropIfExists('depos');
    }
}
