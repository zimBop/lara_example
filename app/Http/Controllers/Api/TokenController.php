<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Psr7\Response;
use Laravel\Passport\Http\Controllers\AccessTokenController;

use GuzzleHttp\Exception\ClientException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

class TokenController extends AccessTokenController
{
    public function getAccessToken(ServerRequestInterface $request)
    {
        try {
            $response = $this->server->respondToAccessTokenRequest($request, new Response());

            return response()->json([
                'done' => true,
                'data' => json_decode($response->getBody())
            ]);
        } catch (ClientException $exception) {
            $error = json_decode($exception->getResponse()->getBody());

            throw OAuthServerException::invalidRequest('access_token', object_get($error, 'error.message'));
        }
    }
}
