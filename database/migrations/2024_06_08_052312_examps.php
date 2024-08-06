<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Examps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default(null);
            $table->time('time')->default(null);
            $table->time('duration')->default(null);
            $table->date('day')->default(null);
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
        Schema::dropIfExists('examps');
    }
}