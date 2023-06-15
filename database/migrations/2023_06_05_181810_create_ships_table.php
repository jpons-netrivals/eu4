<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ships', function (Blueprint $table) {
            $table->id();
            $table->integer('UserID')->unsigned()->nullable();
            $table->integer('ShipTypeID')->unsigned()->nullable();
            $table->integer('SolarSystemX')->unsigned()->nullable();
            $table->integer('SolarSystemY')->unsigned()->nullable();
            $table->integer('GalaxyX')->unsigned()->nullable();
            $table->integer('GalaxyY')->unsigned()->nullable();
            $table->json('config')->nullable();
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
        Schema::dropIfExists('ships');
    }
}
