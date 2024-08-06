<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('written_average')->default(null);
            $table->float('number_average',4,2,true)->default(0);
             
            $table->foreignId('user_id')->constrained('users','id');
            $table->foreignId('subject_id')->constrained('subjects','id');
            $table->foreignId('report_id')->constrained('reports','id');
            
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
        Schema::dropIfExists('user_subjects');
    }
}