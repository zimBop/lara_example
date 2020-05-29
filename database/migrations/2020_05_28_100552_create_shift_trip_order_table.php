<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftTripOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_trip_order', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('shift_id')->index();
            $table->foreign('shift_id')
                ->references('id')
                ->on('shifts')
                ->onDelete('cascade');

            $table->unsignedBigInteger('trip_order_id')->index();
            $table->foreign('trip_order_id')
                ->references('id')
                ->on('trip_orders')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_trip_order');
    }
}
