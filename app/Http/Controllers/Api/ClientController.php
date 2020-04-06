<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ResetPasswordRequest;
use App\Http\Requests\Client\StoreRequest;
use App\Http\Requests\Client\UpdateRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\NexmoService;
use App\Services\ResetPasswordService;
use App\Services\VerificationCodeService;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\RefreshTokenRepository;

class ClientController extends Controller
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
            return response()->json(['message' => VerificationCodeService::getCannotSendMessage()]);
        }

        $verificationCode = $codeService->get();

        $statusMessage = $nexmo->sendSMS($phone, $verificationCode->code);

        return response()->json([
            'message' => $statusMessage,
            'client' => new ClientResource($client),
            'is_registration_completed' => $client->isRegistrationCompleted()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Client $client
     * @return ClientResource
     */
    public function show(Client $client)
    {
        return new ClientResource($client);
    }

    /**
     * @param UpdateRequest $request
     * @param Client $client
     * @return ClientResource
     */
    public function update(UpdateRequest $request, Client $client)
    {
        $input = $request->all();

        $password = $request->input(Client::PASSWORD);

        if ($password) {
            $input[Client::PASSWORD] = Hash::make($password);
        }

        $client->update($input);

        return new ClientResource($client);
    }

    public function logout(Client $client, RefreshTokenRepository $refreshTokenRepository)
    {
        if (!$client->tokens) {
            return response()->json(['message' => 'Already logged out.']);
        }

        foreach ($client->tokens as $token) {
            $token->revoke();

            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
        }

        return response()->json(['message' => 'Tokens revoked.']);
    }

    public function forgotPassword(Client $client, NexmoService $nexmo, ResetPasswordService $passwordService)
    {
        $passwordService->setClient($client);
        $token = $passwordService->create();
        $link = config('app.password_reset.ios_link') . "?token=$token";

        $statusMessage = $nexmo->sendSMS($client->phone, $link);

        return response()->json(['message' => $statusMessage]);
    }

    public function resetPassword(ResetPasswordRequest $request, Client $client, ResetPasswordService $passwordService)
    {
        $password = $request->input(Client::PASSWORD);
        $token = $request->input('token');

        $passwordService->setClient($client);
        if (!$passwordService->exists($token)) {
            return response()->json(['message' => 'Password reset token expired or not exists.'], 422);
        }

        $client->update([
            Client::PASSWORD => Hash::make($password)
        ]);

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
