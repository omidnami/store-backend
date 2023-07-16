<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pid');
            $table->string('type'); // type pege "blog, product or ..."
            $table->string('page');
            $table->longText('text');
            $table->foreignId('user_id');
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
        Schema::dropIfExists('comments');
    }
}
