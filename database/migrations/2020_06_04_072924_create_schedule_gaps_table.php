<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleGapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_gaps', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('week_day');
            $table->time('start');
            $table->time('end');

            $table->unsignedBigInteger('week_id')->index()->comment('Schedule week ID');
            $table->foreign('week_id')
                ->references('id')
                ->on('schedule_weeks')
                ->onDelete('cascade');

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
        Schema::dropIfExists('schedule_gaps');
    }
}
