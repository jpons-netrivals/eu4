<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipInProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ship_in_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('UserID')->unsigned();
            $table->integer('PlanetID')->unsigned();
            $table->dateTime('will_be_finished_at')->nullable();
            $table->integer('has_finished')->default(0);
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
        Schema::dropIfExists('ship_in_progress');
    }
}
