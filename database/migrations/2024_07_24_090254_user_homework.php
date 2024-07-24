<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserHomework extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_homeworks', function (Blueprint $table) {
            
            $table->id();
            $table->foreignId('user_id')->constrained('users','id');
            $table->foreignId('homework_id')->constrained('homeworks','id');
            $table->integer('result')->default(0);
            $table->integer('rate')->default(0);
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
        Schema::dropIfExists('users_homeworks');
    }
}