<?php

namespace App\Http\Controllers\Api;

use App\Constants\ClientMessages;
use App\Http\Requests\Client\ChangePhoneRequest;
use App\Http\Requests\Client\ResetPasswordRequest;
use App\Http\Requests\Client\StoreRequest;
use App\Http\Requests\Client\UpdateRequest;
use App\Http\Resources\ClientResource;
use App\Models\Avatar;
use App\Models\Client;
use App\Models\Invitation;
use App\Services\AvatarService;
use App\Services\ClientService;
use App\Services\NexmoService;
use App\Services\ResetPasswordService;
use App\Services\VerificationCodeService;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository;

class ClientController extends ApiController
{
    /**
     * @param StoreRequest $request
     * @param NexmoService $nexmo
     * @param VerificationCodeService $codeService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(StoreRequest $request, NexmoService $nexmo, VerificationCodeService $codeService)
    {
        $phone = $request->input('phone');

        $client = Client::firstOrCreate([Client::PHONE => $phone]);

        $codeService->setClient($client);

        if (!$codeService->canSend()) {
            return $this->error(VerificationCodeService::getCannotSendMessage());
        }

        $verificationCode = $codeService->get();
        $message = "Your Electra code {$verificationCode->code}";
        $nexmoResponse = $nexmo->sendSMS($phone, $message);

        if (!$nexmoResponse['sent']) {
            return $this->error($nexmoResponse['message']);
        }

        return $this->data([
            'client' => new ClientResource($client),
            'is_registration_completed' => $client->isRegistrationCompleted()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Client $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Client $client)
    {
        return $this->data([
           'client' => new ClientResource($client),
           'is_registration_completed' => $client->isRegistrationCompleted(),
        ]);
    }

    /**
     * @param UpdateRequest $request
     * @param Client $client
     * @param ClientService $clientService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Client $client, ClientService $clientService)
    {
        $input = $request->all();

        if ($client->isRegistrationCompleted()) {
            unset($input[Client::PASSWORD]);
        } else {
            $clientService->setClient($client)->processInvitation();
        }

        if ($request->hasFile(Avatar::FILE_INPUT_NAME)) {
            AvatarService::update($request->file(Avatar::FILE_INPUT_NAME), $client);
        }

        if ($request->has('delete_avatar')) {
            AvatarService::delete($client);
        }

        if ($request->has('delete_email')) {
            $input[Client::EMAIL] = null;
        }

        $client->update($input);
        $client->refresh();

        return $this->data(new ClientResource($client));
    }

    /**
     * @param Client $client
     * @param RefreshTokenRepository $refreshTokenRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Client $client, RefreshTokenRepository $refreshTokenRepository)
    {
        foreach ($client->tokens() as $token) {
            $token->revoke();

            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
        }

        return $this->done('Tokens revoked.');
    }

    /**
     * @param Client $client
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Client $client)
    {
        foreach ($client->tokens() as $token) {
            $token->delete();

            Passport::refreshToken()->where('access_token_id', $token->id)->delete();
        }

        $client->delete();

        return $this->done("Client with ID = {$client->id} deleted.");
    }

    /**
     * @param Client $client
     * @param NexmoService $nexmo
     * @param ResetPasswordService $passwordService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function forgotPassword(Client $client, NexmoService $nexmo, ResetPasswordService $passwordService)
    {
        $passwordService->setModel($client);
        $token = $passwordService->create();
        $link = config('app.password_reset.ios_link') . "?token=$token";

        $nexmoResponse = $nexmo->sendSMS($client->phone, $link);

        if (!$nexmoResponse['sent']) {
            return $this->error($nexmoResponse['message']);
        }

        return $this->done($nexmoResponse['message']);
    }

    /**
     * @param ResetPasswordRequest $request
     * @param Client $client
     * @param ResetPasswordService $passwordService
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request, Client $client, ResetPasswordService $passwordService)
    {
        $passwordService->setModel($client);
        $passwordService->setNewPassword($request->input());

        return $this->done('Password reset successfully.');
    }

    public function sendCode(Client $client, StoreRequest $request, NexmoService $nexmo, VerificationCodeService $codeService)
    {
        $phone = $request->input('phone');

        $codeService->setClient($client);

        if (!$codeService->canSend()) {
            return $this->error(VerificationCodeService::getCannotSendMessage());
        }

        $verificationCode = $codeService->get();
        $message = "Your verification code for new phone number: {$verificationCode->code}";
        $nexmoResponse = $nexmo->sendSMS($phone, $message);

        if (!$nexmoResponse['sent']) {
            return $this->error($nexmoResponse['message']);
        }

        return $this->done('SMS with verification code has been sent to the new phone.');
    }

    public function changePhone(Client $client, ChangePhoneRequest $request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');

        if (!$client->validateForPassportPasswordGrant($code)) {
            return $this->error('Invalid SMS code.');
        }

        $client->update([Client::PHONE => $phone]);

        return $this->done('Phone number has been successfully changed.');
    }

    /**
     * Here we create invite for friend.
     * This invite will be processed on friend registration - step when he saves his profile info -
     * in $this->update(), ClientService->processInvitation().
     *
     * @param Client $client
     * @param StoreRequest $request
     * @param NexmoService $nexmo
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function inviteFriend(Client $client, StoreRequest $request, NexmoService $nexmo)
    {
        $phone = $request->input('phone');

        if (Client::wherePhone($phone)->exists()) {
            return $this->error(ClientMessages::PHONE_ALREADY_REGISTERED);
        }

        if (Invitation::wherePhone($phone)->whereSmsSent(true)->exists()) {
            return $this->error(ClientMessages::INVITE_ALREADY_SENT);
        }

        $invitation = $client->invitations()->firstOrCreate([Client::PHONE => $phone]);
        if (!$invitation->sms_sent) {
            $message = sprintf(ClientMessages::INVITE_TEXT, $client->full_name, config('app.invites.link'));
            $nexmoResponse = $nexmo->sendSMS($phone, $message);

            if (!$nexmoResponse['sent']) {
                return $this->error($nexmoResponse['message']);
            }

            $invitation->sms_sent = true;
            $invitation->save();
        }

        return $this->done(ClientMessages::INVITE_SENT);
    }
}
