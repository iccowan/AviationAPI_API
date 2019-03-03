<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VatsimControllers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vatsim_controllers', function(Blueprint $table) {
            $table->increments('id');
            $table->string('callsign')->nullable();
            $table->integer('cid')->nullable();
            $table->string('name')->nullable();
            $table->string('frequency')->nullable();
            $table->text('atis')->nullable();
            $table->timestamp('time_logon')->nullable();
            $table->string('time_online')->nullable();
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
        Schema::dropIfExists('vatsim_controllers');
    }
}
