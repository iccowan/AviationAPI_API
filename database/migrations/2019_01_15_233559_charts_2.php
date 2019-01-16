<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Charts2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charts_next', function(Blueprint $table) {
            $table->increments('id');
            $table->string('state');
            $table->text('state_full');
            $table->text('city');
            $table->string('volume');
            $table->text('airport_name');
            $table->string('military');
            $table->string('faa_ident');
            $table->string('icao_ident');
            $table->string('chart_seq');
            $table->string('chart_code');
            $table->string('chart_name');
            $table->string('pdf_name');
            $table->text('pdf_path');
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
        Schema::dropIfExists('charts_next');
    }
}
