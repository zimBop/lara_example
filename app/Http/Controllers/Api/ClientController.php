<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Client\ResetPasswordRequest;
use App\Http\Requests\Client\StoreRequest;
use App\Http\Requests\Client\UpdateRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\NexmoService;
use App\Services\ResetPasswordService;
use App\Services\StripeService;
use App\Services\VerificationCodeService;
use Illuminate\Support\Facades\Hash;
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
        $message = "Your Electra code {$verificationCode->code} some text";
        $nexmoResponse = $nexmo->sendSMS($phone, $message);

        if (!$nexmoResponse['sent']) {
            return $this->error($nexmoResponse['message']);
        }

        return $this->data(
            [
                'client' => new ClientResource($client),
                'is_registration_completed' => $client->isRegistrationCompleted()
            ]
        );
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Client $client)
    {
        $input = $request->all();

        $password = $request->input(Client::PASSWORD);

        if ($password && !$client->isRegistrationCompleted()) {
            $input[Client::PASSWORD] = Hash::make($password);
        } else {
            unset($input[Client::PASSWORD]);
        }

        $client->update($input);

        return $this->data(new ClientResource($client));
    }

    /**
     * @param Client $client
     * @param RefreshTokenRepository $refreshTokenRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Client $client, RefreshTokenRepository $refreshTokenRepository)
    {
        $tokens = $client->tokens();

        if ($tokens->isEmpty()) {
            return $this->done('Already logged out.');
        }

        foreach ($tokens as $token) {
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
        $tokens = $client->tokens();

        foreach ($tokens as $token) {
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
        $passwordService->setClient($client);
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
        $password = $request->input(Client::PASSWORD);
        $token = $request->input('token');

        $passwordService->setClient($client);

        if (!$passwordService->exists($token)) {
            return $this->error('Password reset token expired or not exists.');
        }

        $client->update([
            Client::PASSWORD => Hash::make($password)
        ]);

        return $this->done('Password reset successfully.');
    }
}
