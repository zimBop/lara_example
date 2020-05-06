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
    protected $order;

    public function setOrder(?TripOrder $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function checkTripOrderExists(): void
    {
        if (!$this->order) {
            abort(404, 'Trip Request not found.');
        }
    }

    public function checkDriverAvailable(): void
    {
        if (!$this->isDriverAvailable()) {
            abort(200, 'Driver is not available.');
        }
    }

    // TODO add real check
    public function isDriverAvailable(): bool
    {
        return true;
    }

    public function updateOrCreateTripOrder(StoreTripOrderRequest $request, Client $client)
    {
        $clientData = $this->getClientPartData($request);
        $driverData = $this->getDriverPartData($clientData[TripOrder::ORIGIN]['id']);

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
        $origin = json_decode($request->input('origin'), true);
        $destination = json_decode($request->input('destination'), true);

        $directionsApiParams = [
            'origin' => $origin['id'],
            'destination' => $destination['id'],
        ];

        $waypoints = $this->decodeWaypoints(
            $request->input('waypoints', null)
        );
        $this->prepareWaypoints($directionsApiParams, $waypoints);

        $response = $this->requestDirectionsApi($directionsApiParams);

        $route = $response['routes'][0];

        return [
            TripOrder::ORIGIN => $origin,
            TripOrder::DESTINATION => $destination,
            TripOrder::WAYPOINTS =>  $waypoints,
            TripOrder::COORDINATES =>  $this->prepareCoordinates($route['legs'][0]),
            TripOrder::DISTANCE => $route['legs'][0]['distance']['value'],
            TripOrder::TRIP_DURATION => $route['legs'][0]['duration']['value'],
            TripOrder::OVERVIEW_POLYLINE => $route['overview_polyline'],
        ];
    }

    protected function decodeWaypoints(?array $waypoints = null): ?array
    {
        if ($waypoints === null) {
            return null;
        }

        $decodedWaypoints = [];
        foreach ($waypoints as $waypoint) {
            $decodedWaypoints[] = json_decode($waypoint, true);
        }

        return $decodedWaypoints;
    }

    protected function prepareWaypoints(array &$directionsApiParams, ?array $waypoints): void
    {
        if ($waypoints) {
            $waypoints = array_map(
                static function ($waypoint) {
                    return 'via:' . $waypoint['id'];
                },
                $waypoints
            );

            $directionsApiParams['waypoints'] = implode('|', $waypoints);
        }
    }

    protected function prepareCoordinates(array $routeLeg): array
    {
        $waypoints = [];

        foreach ($routeLeg['via_waypoint'] as $waypoint) {
            $waypoints[] = $waypoint['location'];
        }

        return [
            'origin' => $routeLeg['start_location'],
            'destination' => $routeLeg['end_location'],
            'waypoints' => $waypoints,
        ];
    }

    protected function getDriverPartData($clientOrigin): array
    {
        //TODO find closest driver location
//        $driver = $this->findClosestDriver($clientOrigin);

        $driverLocation = '41.767907,-72.682805';

        $response = $this->requestDirectionsApi(
            [
                'origin' => $clientOrigin,
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
            throw new GoogleApiException($response);
        }

        return $response;
    }
}
