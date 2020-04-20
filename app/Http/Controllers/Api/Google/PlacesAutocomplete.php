<?php

namespace App\Http\Controllers\Api\Google;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Google\PlacesAutocompleteRequest;

class PlacesAutocomplete extends ApiController
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
            ->get('predictions');

        return $this->data($response['predictions']);
    }
}
