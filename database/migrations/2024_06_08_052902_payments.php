<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Payments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('amount')->unsigned()->nullable(true);
            $table->date('date')->nullable(true);
           
           
            $table->string('title')->nullable(true);
            $table->enum('type',['0','1','2','3'])->nullable(true);
            //['activity','exapm','monthly_installment','trip']
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
        Schema::dropIfExists('payments');
    }
}