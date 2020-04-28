<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('set null');

            $table->unsignedBigInteger('shift_id')->nullable();
            $table->foreign('shift_id')
                ->references('id')
                ->on('shifts')
                ->onDelete('set null');

            $table->tinyInteger('status')->comment('Trip Order Status');
            $table->string('origin')->comment('Origin Google Place Id');
            $table->string('destination')->comment('Destination Google Place Id');
            $table->json('waypoints')->nullable()->comment('Google Place Ids for waypoints');
            $table->json('overview_polyline')->comment('Contains a single points object that holds an encoded polyline representation of the route');
            $table->integer('price')->comment('Trip price in cents');
            $table->integer('wait_duration')->comment('Driver waiting time in seconds');
            $table->integer('trip_duration')->comment('Trip duration in seconds');
            $table->integer('distance')->comment('Trip distance in meters');
            $table->integer('driver_distance')->comment('Distance in meters between diver\' location and origin');
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
        Schema::dropIfExists('trip_orders');
    }
}
