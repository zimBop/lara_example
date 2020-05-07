<?php

namespace Tests\Feature\Device;

use App\Constants\DeviceType;
use App\Models\Device;
use Tests\TestCase;

class DeviceControllerTest extends TestCase
{
    public function testIsDeviceSuccessfullyCreated(): void
    {
        $this->makeAuthUser();

        $data = [
            Device::TOKEN => $this->faker->word,
            Device::TYPE => $this->faker->randomElement(DeviceType::getList())
        ];

        $response = $this->postJson(
            route('devices.store'),
            $data
        );

        $response->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => 'Device successfully added.',
            ]);

        $this->assertDatabaseHas('devices', $data);
    }

    public function testIsDeviceSuccessfullyDeleted(): void
    {
        $user = $this->makeAuthUser();

        $device = $user->devices()->create([
            Device::TOKEN => $this->faker->word,
            Device::TYPE => $this->faker->randomElement(DeviceType::getList())
        ]);

        $response = $this->deleteJson(
            route('devices.delete'),
            [
                Device::TYPE => $device->type,
                Device::TOKEN => $device->token
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => 'Device successfully deleted.',
            ]);

        $this->assertDatabaseMissing('devices', ['id' => $device->id]);
    }
}
