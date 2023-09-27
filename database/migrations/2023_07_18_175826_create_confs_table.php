<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('domain');
            $table->string('logoDark')->nullable();
            $table->string('logoLight')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('status')->default(true);
            $table->text('tag');
            $table->text('des');
            $table->json('style')->nullable();
            $table->longText('css')->nullable();
            $table->longText('js')->nullable();
            $table->string('lang');
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
        Schema::dropIfExists('confs');
    }
}
