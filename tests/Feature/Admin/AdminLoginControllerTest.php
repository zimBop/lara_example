<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminLoginControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIsLoginFormAvailable()
    {
        $response = $this->get(route(R_ADMIN_LOGIN));

        // get the status
        $response->assertStatus(200);

        // is correct view loaded
        $response->assertViewIs('admin.login');
    }

    public function testIsAuthFailsWithInvalidCredentials()
    {
        $email = $this->faker->safeEmail;
        $password = $this->faker->password(8);

        $response = $this->post(route(R_ADMIN_LOGIN_SUBMIT), ['email' => $email, 'password' => $password]);

        $response->assertStatus(302);
        $response->assertSessionHas('alert_type', 'danger');
    }

    public function testIsAuthSucceedWithValidCredentials()
    {
        $admin = factory(Admin::class)->create();

        $response = $this->post(route(R_ADMIN_LOGIN_SUBMIT), [
            'email' => $admin->email,
            'password' => 'password'
        ]);

        $response->assertRedirect(route(R_ADMIN_DASHBOARD));
        $this->assertAuthenticatedAs($admin, 'admin');
    }
}
