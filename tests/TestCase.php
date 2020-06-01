<?php

namespace Tests;

use App\Models\Client;
use App\Models\Driver;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use SMartins\PassportMultiauth\PassportMultiauth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');

        Artisan::call('db:seed');

        DB::beginTransaction();
    }

    public function tearDown(): void
    {
        DB::rollBack();

        parent::tearDown();
    }

    protected function makeAuthUser(string $type = null, array $data = [])
    {
        if (!$type) {
            $type = $this->faker->randomElement(['client', 'driver']);
        }

        $class = $type === 'client' ? Client::class : Driver::class;

        $user = factory($class)->create($data);

        PassportMultiauth::actingAs($user, ['access-' . $type]);

        return $user;
    }

    protected function makeAuthClient(array $data = []): Client
    {
        return $this->makeAuthUser('client', $data);
    }

    protected function makeAuthDriver(array $data = []): Driver
    {
        return $this->makeAuthUser('driver', $data);
    }
}
