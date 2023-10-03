<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\models\Blog_cat;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('mainCat');
            $table->bigInteger('user');
            $table->json('cat'); // blog cat index id
            $table->boolean('status')->default(true); // blog cat index id
            $table->string('uniqueId'); // blog cat index id
            $table->string('lang'); // blog cat index id
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
        Schema::dropIfExists('blogs');

    }
}
