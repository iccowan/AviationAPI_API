<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VatsimPilots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vatsim_pilots', function(Blueprint $table) {
            $table->increments('id');
            $table->string('callsign')->nullable();
            $table->integer('cid')->nullable();
            $table->string('name')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('altitude')->nullable();
            $table->string('ground_speed')->nullable();
            $table->integer('heading')->nullable();
            $table->integer('transponder')->nullable();
            $table->string('aircraft')->nullable();
            $table->string('departure')->nullable();
            $table->string('nm_from_dep')->nullable();
            $table->string('arrival')->nullable();
            $table->string('nm_from_arr')->nullable();
            $table->string('alternate')->nullable();
            $table->text('route')->nullable();
            $table->string('flight_rules')->nullable();
            $table->string('filed_altitude')->nullable();
            $table->string('filed_tas')->nullable();
            $table->string('filed_time_enroute')->nullable();
            $table->string('filed_fuel_onboard')->nullable();
            $table->text('remarks')->nullable();
            $table->string('stage_of_flight')->nullable();
            $table->timestamp('time_logon')->nullable();
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
        Schema::dropIfExists('vatsim_pilots');
    }
}
