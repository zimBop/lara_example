<?php

namespace App\Services;

use App\Constants\TripOrderStatuses;
use App\Exceptions\Google\GoogleApiException;
use App\Http\Requests\TripOrder\StoreTripOrderRequest;
use App\Models\Client;
use App\Models\TripOrder;
use App\Logic\TripPriceCalculator;

class TripService
{
    public function updateOrCreateTripOrder(StoreTripOrderRequest $request, Client $client)
    {
        $clientData = $this->getClientPartData($request);
        $driverData = $this->getDriverPartData($clientData[TripOrder::ORIGIN]);

        return TripOrder::updateOrCreate(
            [TripOrder::CLIENT_ID => $client->id],
            array_merge(
                $clientData,
                $driverData,
                [
                    TripOrder::PRICE => TripPriceCalculator::calculatePrice(array_merge($clientData, $driverData)),
                    TripOrder::STATUS => TripOrderStatuses::WAITING_FOR_CONFIRMATION,
                ]
            )
        );
    }

    protected function getClientPartData(StoreTripOrderRequest $request): array
    {
        $origin = $request->input('origin');
        $destination = $request->input('destination');

        $params = [
            'origin' => 'place_id:' . $origin,
            'destination' => 'place_id:' . $destination,
        ];

        $waypoints = $request->input('waypoints', null);
        $this->prepareWaypoints($params, $waypoints);

        $response = $this->requestDirectionsApi($params);

        $routes = $response['routes'][0];

        return [
            TripOrder::ORIGIN => $origin,
            TripOrder::DESTINATION => $destination,
            TripOrder::WAYPOINTS =>  $waypoints,
            TripOrder::DISTANCE => $routes['legs'][0]['distance']['value'],
            TripOrder::TRIP_DURATION => $routes['legs'][0]['duration']['value'],
            TripOrder::OVERVIEW_POLYLINE => $routes['overview_polyline'],
        ];
    }

    protected function prepareWaypoints(array &$params, ?array $waypoints): void
    {
        if ($waypoints) {
            $waypoints = array_map(
                static function ($waypoint) {
                    return 'via:place_id:' . $waypoint;
                },
                $waypoints
            );

            $params['waypoints'] = implode('|', $waypoints);
        }
    }

    protected function getDriverPartData($clientOrigin): array
    {
        //TODO find closest driver location
//        $driver = $this->findClosestDriver($clientOrigin);

        $driverLocation = '41.767907,-72.682805';

        $response = $this->requestDirectionsApi(
            [
                'origin' => 'place_id:' . $clientOrigin,
                //Ensure that no space exists between the latitude and longitude values
                'destination' => $driverLocation,
            ]
        );

        return [
            TripOrder::DRIVER_DISTANCE => $response['routes'][0]['legs'][0]['distance']['value'],
            TripOrder::WAIT_DURATION => $response['routes'][0]['legs'][0]['duration']['value'],
        ];
    }

    /**
     * @param array $params
     * @param string $getParam
     * @return array
     * @throws GoogleApiException
     * @throws \ErrorException
     */
    protected function requestDirectionsApi(array $params): array
    {
        $response = \GoogleMaps::load('directions')
            ->setParam($params)
            ->get();

        $response = json_decode($response, true);

        if ($response['status'] !== 'OK') {
            $errorMessage = $response['error_message'] ?? $response['status'];
            throw new GoogleApiException($errorMessage);
        }

        return $response;
    }
}
