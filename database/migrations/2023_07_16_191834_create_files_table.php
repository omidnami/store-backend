<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // imge alt
            $table->bigInteger('pid')->index(); // productID, blogID,....
            $table->string('type'); // blog, product, gallery,...
            $table->string('url');
            $table->binary('file');
            $table->string('data'); // json data file (size,format,...)
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
        Schema::dropIfExists('files');

    }
}
