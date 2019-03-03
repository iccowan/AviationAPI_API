<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AirportData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airport_data', function(Blueprint $table) {
            $table->increments('id');
            $table->string('site_number')->nullable();
            $table->string('type')->nullable();
            $table->text('facility_name')->nullable();
            $table->string('faa_ident')->nullable();
            $table->string('icao_ident')->nullable();
            $table->string('region')->nullable();
            $table->string('district_office')->nullable();
            $table->string('state')->nullable();
            $table->text('state_full')->nullable();
            $table->text('county')->nullable();
            $table->text('city')->nullable();
            $table->string('ownership')->nullable();
            $table->string('use')->nullable();
            $table->text('manager')->nullable();
            $table->text('manager_phone')->nullable();
            $table->text('latitude')->nullable();
            $table->text('latitude_sec')->nullable();
            $table->text('longitude')->nullable();
            $table->text('longitude_sec')->nullable();
            $table->string('elevation')->nullable();
            $table->string('magnetic_variation')->nullable();
            $table->string('tpa')->nullable();
            $table->string('vfr_sectional')->nullable();
            $table->string('boundary_artcc')->nullable();
            $table->string('boundary_artcc_name')->nullable();
            $table->string('responsible_artcc')->nullable();
            $table->string('responsible_artcc_name')->nullable();
            $table->string('fss_phone_number')->nullable();
            $table->string('fss_phone_numer_tollfree')->nullable();
            $table->string('notam_facility_ident')->nullable();
            $table->string('status')->nullable();
            $table->string('certification_typedate')->nullable();
            $table->string('customs_airport_of_entry')->nullable();
            $table->string('military_joint_use')->nullable();
            $table->string('military_landing')->nullable();
            $table->string('lighting_schedule')->nullable();
            $table->string('beacon_schedule')->nullable();
            $table->string('control_tower')->nullable();
            $table->string('unicom')->nullable();
            $table->string('ctaf')->nullable();
            $table->string('effective_date')->nullable();
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
        Schema::dropIfExists('airport_data');
    }
}
