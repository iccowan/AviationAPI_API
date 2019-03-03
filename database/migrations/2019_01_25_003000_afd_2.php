<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Afd2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afd_next', function(Blueprint $table) {
            $table->increments('id');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('airport_name')->nullable();
            $table->string('icao_ident')->nullable();
            $table->text('pdf_name')->nullable();
            $table->text('pdf_path')->nullable();
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
        Schema::dropIfExists('afd_next');
    }
}
