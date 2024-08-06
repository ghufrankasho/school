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
        Schema::create('user_homework', function (Blueprint $table) {
            
            $table->id();
            $table->foreignId('user_id')->constrained('users','id');
            $table->foreignId('homework_id')->constrained('homework','id');
            $table->boolean('answer')->default(false);
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
        Schema::dropIfExists('user_homework');
    }
}