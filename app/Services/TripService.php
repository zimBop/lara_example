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
        $origin = $request->input('origin');
        $destination = $request->input('destination');

        $directionsApiParams = [
            'origin' => $origin['id'],
            'destination' => $destination['id'],
        ];

        $waypoints = $request->input('waypoints', []);

        $this->prepareWaypoints($directionsApiParams, $waypoints);

        $response = $this->requestDirectionsApi($directionsApiParams);

        $route = $response['routes'][0];

        $this->addCoordinates($route['legs'][0], $origin, $destination, $waypoints);

        return [
            TripOrder::ORIGIN => $origin,
            TripOrder::DESTINATION => $destination,
            TripOrder::WAYPOINTS =>  $waypoints,
            TripOrder::DISTANCE => $route['legs'][0]['distance']['value'],
            TripOrder::TRIP_DURATION => $route['legs'][0]['duration']['value'],
            TripOrder::OVERVIEW_POLYLINE => $route['overview_polyline'],
        ];
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

    protected function addCoordinates(array $routeLeg, array &$origin, array &$destination, array &$waypoints): void
    {
        foreach ($routeLeg['via_waypoint'] as $key => $apiWaypoint) {
            $waypoints[$key]['coordinates'] = $apiWaypoint['location'];
        }

        $origin['coordinates'] = $routeLeg['start_location'];
        $destination['coordinates'] = $routeLeg['end_location'];
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
