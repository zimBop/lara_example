<?php

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::firstOrCreate(
            [Admin::EMAIL => 'hi@ag.digital'],
            [Admin::NAME => 'AG staff', Admin::PASSWORD => '$2y$10$KGMYIUme6TNInC289sULEeqm8dVncPzZi6wRwuaUTRlcQg62xl2/W']
        );
    }
}
