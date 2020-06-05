<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_shifts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('gap_id')->index()->comment('Schedule shift ID');
            $table->foreign('gap_id')
                ->references('id')
                ->on('schedule_gaps')
                ->onDelete('cascade');

            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles')
                ->onDelete('set null');

            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('set null');

            $table->unsignedBigInteger('driver_id')->nullable()->index();
            $table->foreign('driver_id')
                ->references('id')
                ->on('drivers')
                ->onDelete('set null');

            $table->unique(['gap_id', 'vehicle_id']);

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
        Schema::dropIfExists('schedule_shifts');
    }
}
