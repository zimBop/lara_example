<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\VerificationCode;
use App\Services\NexmoService;
use App\Services\VerificationCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\RefreshTokenRepository;

class ClientController extends Controller
{
    /**
     * @param Request $request
     * @param NexmoService $nexmo
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(StoreClientRequest $request, NexmoService $nexmo)
    {
        $phone = $request->input('phone');

        $client = Client::firstOrCreate([Client::PHONE => $phone]);

        $verificationCodeService = new VerificationCodeService($client);

        if (!$verificationCodeService->canSend()) {
            $message = 'SMS cannot be sent. Delay between SMS sending ' . VerificationCode::DELAY_MINUTES
                . ' ' . Str::plural('minute', VerificationCode::DELAY_MINUTES);

            return response()->json(['message' => $message]);
        }

        $verificationCode = $verificationCodeService->get();

        $statusMessage = $nexmo->sendSMS($phone, $verificationCode->code);

        return response()->json(['message' => $statusMessage]);
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
     * @param UpdateClientRequest $request
     * @param Client $client
     * @return ClientResource
     */
    public function update(UpdateClientRequest $request, Client $client)
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
}
