<?php

namespace App\Http\Controllers\Api\Google;

use App\Exceptions\Google\GoogleApiException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Google\ReverseGeocodingRequest;

class ReverseGeocodingController extends ApiController
{
    /**
     * @param ReverseGeocodingRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws GoogleApiException
     * @throws \ErrorException
     */
    public function __invoke(ReverseGeocodingRequest $request)
    {
        $response = \GoogleMaps::load('geocoding')
            ->setParamByKey('latlng', $request->input('latlng'))
            ->setParamByKey('result_type', 'street_address')
            ->get();

        $response = json_decode($response, true);

        if ($response['status'] !== 'OK') {
            $errorMessage = $response['error_message'] ?? $response['status'];
            throw new GoogleApiException($errorMessage);
        }

        return $this->data(
            $response['results']
        );
    }
}
