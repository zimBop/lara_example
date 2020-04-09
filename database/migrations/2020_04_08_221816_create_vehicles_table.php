<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Vehicle;
use App\Constants\VehicleConstants;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', static function (Blueprint $table) {
            $table->id();
            $table->string('license_plate', 15)->unique();
            $table->tinyInteger('brand_id');
            $table->tinyInteger('model_id');
            $table->tinyInteger('color_id')->nullable();
            $table->tinyInteger('status_id')->default(VehicleConstants::STATUS_AVAILABLE);
            $table->softDeletes();
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
        Schema::dropIfExists('vehicles');
    }
}
