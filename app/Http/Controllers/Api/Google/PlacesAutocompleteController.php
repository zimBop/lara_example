<?php

namespace App\Http\Controllers\Api\Google;

use App\Exceptions\Google\GoogleApiException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Google\PlacesAutocompleteRequest;

class PlacesAutocompleteController extends ApiController
{
    /**
     * @param PlacesAutocompleteRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function __invoke(PlacesAutocompleteRequest $request)
    {
        $response = \GoogleMaps::load('placequeryautocomplete')
            ->setParamByKey('input', $request->input('input'))
            ->get();

        $response = json_decode($response, true);

        if (!in_array($response['status'],  ['OK', 'ZERO_RESULTS'])) {
            throw new GoogleApiException($response);
        }

        return $this->data($response['predictions']);
    }
}
