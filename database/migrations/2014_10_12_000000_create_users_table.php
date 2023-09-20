<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('userName');
            $table->string('fname');
            $table->string('lname');
            $table->integer('rol')->default(2);
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->integer('email_verified_at')->nullable();
            $table->integer('phone_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('token');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
