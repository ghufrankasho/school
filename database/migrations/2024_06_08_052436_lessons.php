<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Lessons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default(null);
            $table->string('file')->default(null);
            $table->string('image')->default(null);
            $table->string('video')->default(null);
            $table->string('description')->default(null);
            $table->foreignId('type_id')->constrained('types','id');
            $table->foreignId('subject_id')->constrained('subjects','id')->onDelete('cascade');
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
        Schema::dropIfExists('lessons');
    }
}