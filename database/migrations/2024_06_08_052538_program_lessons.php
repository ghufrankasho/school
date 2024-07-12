<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProgramLessons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons','id');
            $table->foreignId('program_id')->constrained('programs','id');
            $table->foreignId('teacher_id')->constrained('teachers','id');
            $table->time('time')->default(null);
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
        Schema::dropIfExists('program_lessons');
    }
}