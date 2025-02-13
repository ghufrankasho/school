<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Reports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->string('name')->default(null);
        $table->date('date')->default(null);
        $table->string('note')->default(null);
         
        $table->foreignId('user_id')->constrained('users','id');
         
        
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
    Schema::dropIfExists('reports');
}
}