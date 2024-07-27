<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Users extends Migration
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
            $table->string('name');
            $table->string('image')->default(null);
            $table->string('phone')->default(null);
            $table->boolean('block')->default(false);
           
            $table->integer('grading')->default(0);
            $table->string('address')->default(null);
            $table->string('fcm_token')->default(null);
            $table->foreignId('account_id')->constrained('accounts','id');
            $table->string('class_name');
            $table->foreignId('type_section_id')->constrained('type_sections','id')->default(null);
           
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