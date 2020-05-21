<?php

namespace App\Console\Commands\SingleTime;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupPostgis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup-postgis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install postgis extensions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');
    }
}
