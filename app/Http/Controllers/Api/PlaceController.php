<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Place\StorePlaceRequest;
use App\Http\Resources\PlaceResource;
use App\Models\Client;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends ApiController
{
    /**
     * @param Client $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Client $client)
    {
        return $this->data(
            PlaceResource::collection($client->places)
        );
    }

    /**
     * @param StorePlaceRequest $request
     * @param Client $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePlaceRequest $request, Client $client)
    {
        $client->places()->create(
            $request->only([Place::DESCRIPTION, Place::NAME, Place::PLACE_ID])
        );

        return $this->done('New favorite place is successfully added.');
    }

    /**
     * @param Client $client
     * @param Place $place
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Client $client, Place $place)
    {
        return $this->data(new PlaceResource($place));
    }

    /**
     * @param Client $client
     * @param Place $place
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Client $client, Place $place)
    {
        $place->delete();

        return $this->done("Place '{$place->name}' is deleted.");
    }
}
