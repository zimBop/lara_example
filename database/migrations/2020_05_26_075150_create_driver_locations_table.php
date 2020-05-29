<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')
                ->references('id')
                ->on('shifts')
                ->onDelete('cascade');

            if (db_driver() === 'sqlite') {
                $table->addColumn('point', 'location');
            }

            $table->timestamps();
        });


        if (db_driver() !== 'sqlite') {
            DB::statement('ALTER TABLE driver_locations ADD COLUMN location geometry(Point,4326);');
            Schema::table('driver_locations', function (Blueprint $table) {
                $table->spatialIndex('location');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_locations');
    }
}
