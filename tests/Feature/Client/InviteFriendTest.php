<?php

namespace Tests\Feature\Client;

use App\Constants\ClientMessages;
use App\Models\Client;
use App\Models\Invitation;
use App\Services\ClientService;
use Tests\Feature\Traits\Nexmo;
use Tests\TestCase;

class InviteFriendTest extends TestCase
{
    use Nexmo;

    public function testIsInvitationCreated()
    {
        $nexmoMock = $this->createNexmoMock();
        $this->setupSuccessfulSmsSending($nexmoMock);

        $client = $this->makeAuthClient();
        $phone = ClientService::generatePhoneNumber();

        $response = $this->postJson(
            route('clients.invite-friend', ['client' => $client->id]),
            ['phone' => $phone]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                 'done' => true,
                 'message' => ClientMessages::INVITE_SENT,
             ]);

        $this->assertDatabaseHas('invitations', [
            Invitation::CLIENT_ID => $client->id,
            Invitation::SMS_SENT => true,
            Invitation::ACCEPTED => false,
        ]);
    }

    public function testIsFriendAlreadyInvitedMessageReturned()
    {
        $phone = ClientService::generatePhoneNumber();
        $client = $this->makeAuthClient();

        $client->invitations()->create([
            Invitation::CLIENT_ID => $client->id,
            Invitation::PHONE => $phone,
            Invitation::SMS_SENT => true,
            Invitation::ACCEPTED => false,
        ]);

        $this->postJson(
                route('clients.invite-friend', ['client' => $client->id]),
                ['phone' => $phone]
            )->assertStatus(200)
            ->assertJson([
                 'done' => false,
                 'message' => ClientMessages::INVITE_ALREADY_SENT,
             ]);
    }

    public function testIsAllInvitesAlreadyUsedMessageReturned()
    {
        $phone = ClientService::generatePhoneNumber();
        $client = $this->makeAuthClient();

        factory(Invitation::class, config('app.invites.number'))->create([
            Invitation::CLIENT_ID => $client->id,
        ]);

        $this->postJson(
                route('clients.invite-friend', ['client' => $client->id]),
                ['phone' => $phone]
            )
            ->assertStatus(200)
            ->assertJson([
                 'done' => false,
                 'message' => ClientMessages::ALL_INVITES_ALREADY_USED,
             ]);
    }

    public function testIsInviteAlreadySentMessageReturned()
    {
        $phone = ClientService::generatePhoneNumber();
        $client = $this->makeAuthClient();

        $client->invitations()->create([
            Invitation::CLIENT_ID => $client->id,
            Invitation::PHONE => $phone,
            Invitation::SMS_SENT => true,
            Invitation::ACCEPTED => false,
        ]);

        $this->postJson(
                route('clients.invite-friend', ['client' => $client->id]),
                ['phone' => $phone]
            )
            ->assertStatus(200)
            ->assertJson([
                 'done' => false,
                 'message' => ClientMessages::INVITE_ALREADY_SENT,
             ]);
    }

    public function testIsFreeTripsAdded()
    {
        $phone = ClientService::generatePhoneNumber();
        $freeTripsNumber =  $this->faker->randomNumber(1);

        $sender = factory(Client::class)->create([
            Client::FREE_TRIPS => $freeTripsNumber
        ]);
        $sender->invitations()->create([
            Invitation::CLIENT_ID => $sender->id,
            Invitation::PHONE => $phone,
            Invitation::SMS_SENT => true,
            Invitation::ACCEPTED => false,
        ]);

        $friend = $this->makeAuthClient([
            Client::PHONE => $phone,
            Client::FIRST_NAME => null,
            Client::LAST_NAME => null,
            Client::PASSWORD => null,
            Client::FREE_TRIPS => $freeTripsNumber,
        ]);

        $this->patchJson(
            route('clients.update', ['client' => $friend->id]),
            [
                Client::FIRST_NAME => $this->faker->firstName,
                Client::LAST_NAME => $this->faker->lastName,
                Client::PASSWORD => $this->faker->password(6),
            ]
        );

        $sender->refresh();
        $friend->refresh();

        $this->assertEquals($freeTripsNumber + 1, $sender->free_trips);
        $this->assertEquals($freeTripsNumber + 1, $friend->free_trips);
        $this->assertDatabaseHas('invitations', [Invitation::PHONE => $phone, Invitation::ACCEPTED => true]);
    }
}
