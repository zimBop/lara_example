<?php

namespace App\Services;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Exceptions\Google\GoogleApiException;
use App\Exceptions\Trip\TripException;
use App\Http\Requests\TripOrder\StoreTripOrderRequest;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripOrder;
use App\Logic\TripPriceCalculator;
use App\Notifications\TripStatusChanged;
use \Illuminate\Validation\ValidationException;

class TripService
{
    protected $order;
    protected $trip;

    /**
     * @param mixed $trip
     */
    public function setTrip(Trip $trip): void
    {
        $this->trip = $trip;
    }

    public function setOrder(?TripOrder $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function updateOrCreateTripOrder(StoreTripOrderRequest $request, Client $client)
    {
        $clientData = $this->getClientData($request);
        $driverData = $this->getDriverData($clientData[TripOrder::ORIGIN]['id'], $client);

        return TripOrder::updateOrCreate(
            [TripOrder::CLIENT_ID => $client->id],
            array_merge(
                $clientData,
                $driverData,
                [
                    TripOrder::PRICE => TripPriceCalculator::calculatePrice(array_merge($clientData, $driverData)),
                    TripOrder::STATUS => TripStatuses::WAITING_FOR_CONFIRMATION,
                ]
            )
        );
    }

    protected function getClientData(StoreTripOrderRequest $request): array
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

        $this->checkRouteBounds($response['routes'][0]['bounds'], $origin);

        return [
            TripOrder::ORIGIN => $origin,
            TripOrder::DESTINATION => $destination,
            TripOrder::WAYPOINTS =>  $waypoints,
            TripOrder::DISTANCE => $route['legs'][0]['distance']['value'],
            TripOrder::TRIP_DURATION => $route['legs'][0]['duration']['value'],
            TripOrder::OVERVIEW_POLYLINE => $route['overview_polyline'],
        ];
    }

    protected function checkRouteBounds(array $bounds, array $origin)
    {
        if (app()->runningUnitTests() || config('app.skip_route_bounds_validation')) {
            return true;
        }

        $cityId = PostgisService::findClosestCityId($origin['coordinates']['lng'], $origin['coordinates']['lat']);
        $isRouteBoundsInCity = $cityId && PostgisService::isCityPolygonContainsRouteBounds($cityId, $bounds);
        if (!$isRouteBoundsInCity) {
            throw ValidationException::withMessages(['route' => TripMessages::ROUTE_BOUNDS_VALIDATION_ERROR]);
        }
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

    protected function getDriverData($clientOrigin, $client = null): array
    {
        //TODO remove $client param
        //TODO find closest driver location
//        $driver = $this->findClosestDriver($clientOrigin);

        $driverLocation = '41.767907,-72.682805';

        if ($client && config('app.env') === 'dev' && $client->id === 96) {
            $driverLocation = '47.935618,33.422442';
        }

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

    public function createTrip(TripOrder $tripOrder, Driver $driver): Trip
    {
        //TODO remove this
        $client = $tripOrder->client;

        $driverData = $this->getDriverData($tripOrder->origin['id'], $client);

        $tripData = $tripOrder->toArray();
        $tripData[Trip::DRIVER_DISTANCE] = $driverData[TripOrder::DRIVER_DISTANCE];
        $tripData[Trip::CO2] = $tripOrder->distance;
        $tripData[Trip::WAIT_DURATION] = (int)$driverData[TripOrder::WAIT_DURATION];
        $tripData[Trip::SHIFT_ID] = $driver->activeShift->id;
        $tripData[Trip::STATUS] = TripStatuses::DRIVER_IS_ON_THE_WAY;

        return Trip::create($tripData);
    }

    public function checkTrip(?Trip $trip, int $newStatus)
    {
        if (!$trip) {
            throw new TripException(200, 'Active trip not found.');
        }

        if ($trip->status !== ($newStatus - 1)) {
            throw new TripException(200, 'Incorrect trip status.');
        }
    }

    public function changeStatus(Trip $trip, int $newStatus)
    {
        $data = [Trip::STATUS => $newStatus];

        $trip->update($data);

        if ($newStatus !== TripStatuses::TRIP_ARCHIVED) {
            $trip->client->tripOrder->update($data);
        } else {
            $trip->client->tripOrder->delete();

            $this->updateClientsCo2Sum($trip->client);
        }

        $trip->client->notify(new TripStatusChanged($newStatus, $trip->id));
    }

    protected function updateClientsCo2Sum(Client $client)
    {
        $co2Sum = $client->trips()->archived()->get()->sum('co2');

        $client->update([Client::CO2_SUM => $co2Sum]);
    }
}
