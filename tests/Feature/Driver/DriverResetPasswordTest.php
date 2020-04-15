<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use App\Notifications\ForgotPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DriverResetPasswordTest extends TestCase
{
    public function testForgotPassword()
    {
        $driver = factory(Driver::class)->create();

        Notification::fake();

        $response = $this->postJson(route('drivers.forgot-password'), ['email' => $driver->email]);

        $response
            ->assertStatus(200)
            ->assertJson([
                 'done' => true,
                 'message' => 'Notification sent to driver\'s email.',
             ]);

        Notification::assertSentTo($driver, ForgotPassword::class);
    }

    public function testForgotPasswordForNonexistentEmail()
    {
        $response = $this->postJson(route('drivers.forgot-password'), ['email' => $this->faker->email]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => false,
                'message' => 'Driver with specified email not found.',
            ]);
    }
}
