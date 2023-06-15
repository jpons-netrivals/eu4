<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class
CreateMovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moves', function (Blueprint $table) {
            $table->id();
            $table->integer('UserID')->unsigned();
            $table->integer('ShipID')->unsigned()->nullable();
            $table->integer('FleetID')->unsigned()->nullable();
            $table->dateTime('started_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('will_be_finished_at')->nullable();
            $table->integer('has_arrived')->default(0);
            $table->integer('SolarSystemX')->unsigned()->nullable();
            $table->integer('SolarSystemY')->unsigned()->nullable();
            $table->integer('GalaxyX')->unsigned()->nullable();
            $table->integer('GalaxyY')->unsigned()->nullable();
            $table->integer('SSX')->unsigned()->nullable();
            $table->integer('SSY')->unsigned()->nullable();
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
        Schema::dropIfExists('moves');
    }
}
