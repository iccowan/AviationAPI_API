<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RoutingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preferred_routes', function(Blueprint $table) {
            $table->increments('id');
            $table->string('origin')->nullable();
            $table->text('route');
            $table->string('destination')->nullable();
            $table->string('hours1')->nullable();
            $table->string('hours2')->nullable();
            $table->string('hours3')->nullable();
            $table->string('type')->nullable();
            $table->text('area')->nullable();
            $table->string('altitude')->nullable();
            $table->text('aircraft')->nullable();
            $table->string('flow')->nullable();
            $table->integer('seq')->nullable();
            $table->string('d_artcc')->nullable();
            $table->string('a_artcc')->nullable();
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
        Schema::dropIfExists('preferred_routes');
    }
}
