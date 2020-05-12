<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->index('client_id');
            $table->index('shift_id');
        });

        Schema::table('trip_orders', function (Blueprint $table) {
            $table->index('client_id');
        });

        Schema::table('verification_codes', function (Blueprint $table) {
            $table->index('client_id');
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->index('driver_id');
            $table->index('vehicle_id');
        });

        Schema::table('places', function (Blueprint $table) {
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['shift_id']);
        });

        Schema::table('trip_orders', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
        });

        Schema::table('verification_codes', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropIndex(['driver_id']);
            $table->dropIndex(['vehicle_id']);
        });

        Schema::table('places', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
        });
    }
}
