<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            if (db_driver() === 'sqlite') {
                $table->addColumn('polygon', 'polygon');
                $table->addColumn('point', 'center');
            }
            $table->timestamps();
        });

        if (db_driver() !== 'sqlite') {
            DB::statement('ALTER TABLE cities ADD COLUMN polygon geometry(Polygon,4326);');
            DB::statement('ALTER TABLE cities ADD COLUMN center geometry(Point,4326);');
            Schema::table('cities', function (Blueprint $table) {
                $table->spatialIndex('center');
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
        Schema::dropIfExists('cities');
    }
}
