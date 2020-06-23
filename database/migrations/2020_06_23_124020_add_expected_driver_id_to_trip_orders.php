<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpectedDriverIdToTripOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_orders', static function (Blueprint $table) {
            $table->unsignedBigInteger('expected_driver_id')->nullable()->index();
            $table->foreign('expected_driver_id')
                ->references('id')
                ->on('drivers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_orders', static function (Blueprint $table) {
            $table->dropColumn('expected_driver_id');
        });
    }
}
