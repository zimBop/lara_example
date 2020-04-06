<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * @param string $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function done(string $message, array $data = [])
    {
        return $this->prepareResponse($message, $data);
    }

    /**
     * @param string $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message, array $data = [])
    {
        return $this->prepareResponse($message, $data, false);
    }

    /**
     * @param string $message
     * @param array $data
     * @param bool $done
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareResponse(string $message, array $data = [], bool $done = true)
    {
        return response()->json(
            array_merge(
                [
                    'done' => $done,
                    'message' => $message,
                ],
                $data
            )
        );
    }

    /**
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function data($data = [])
    {
        return response()->json(
            [
                'done' => true,
                'data' => $data
            ]
        );
    }
}
