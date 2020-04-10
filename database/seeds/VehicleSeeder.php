<?php

use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Generate 7 random vehicles
        factory(Vehicle::class, 7)->create();
    }
}
