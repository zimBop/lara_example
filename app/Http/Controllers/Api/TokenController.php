<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class TokenController extends ApiController
{
    public function getAccessToken(Request $request)
    {
        try {
            $response = app()->call(
                '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken',
                $request->input()
            );
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }

        return $this->data(json_decode($response->content()));
    }
}
