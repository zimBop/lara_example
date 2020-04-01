<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\NexmoService;
use App\Services\VerificationCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\RefreshTokenRepository;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * @param Request $request
     * @param NexmoService $nexmo
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request, NexmoService $nexmo)
    {
        $phone = $request->input('phone');

        $client = Client::firstOrCreate([Client::PHONE => $phone]);

        $verificationCodeService = new VerificationCodeService($client);

        if (!$verificationCodeService->canSend()) {
            return response()->json(['message' => 'SMS cannot be sent. Delay between SMS sending 1 minute']);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        //
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
