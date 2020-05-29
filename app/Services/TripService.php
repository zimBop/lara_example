<?php

namespace App\Services;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Exceptions\Google\GoogleApiException;
use App\Exceptions\Trip\TripException;
use App\Http\Requests\TripOrder\StoreTripOrderRequest;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Shift;
use App\Models\Trip;
use App\Models\TripOrder;
use App\Logic\TripPriceCalculator;
use App\Notifications\TripStatusChanged;
use Illuminate\Database\Eloquent\Collection;
use \Illuminate\Validation\ValidationException;

class TripService
{
    protected $order;
    protected $trip;
    protected $cityId;

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
        $driverData = $this->getDriverData($clientData[TripOrder::ORIGIN], $client);

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

        $cityId = $this->getCityId($origin['coordinates']['lng'], $origin['coordinates']['lat']);
        $isRouteBoundsInCity = $cityId && PostgisService::isCityPolygonContainsRouteBounds($cityId, $bounds);
        if (!$isRouteBoundsInCity) {
            throw ValidationException::withMessages(['route' => TripMessages::ROUTE_BOUNDS_VALIDATION_ERROR]);
        }
    }

    protected function getCityId(float $longitude, float $latitude)
    {
        return $this->cityId ?: PostgisService::findClosestCityId($longitude, $latitude);
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
        $drivers = $this->findDrivers($clientOrigin['coordinates']['lng'], $clientOrigin['coordinates']['lat']);
        $closestDriver = $drivers->first();

        $currentTripDuration = 0;
        if ($closestDriver->active_trip) {
            $currentTripDuration = $closestDriver->active_trip->trip_duration_adjusted;
            $driverOrigin = $closestDriver->active_trip->destination['id'];
        } else {
            $driverLocation = $closestDriver->active_shift->driver_location()->withCoordinates()->get();
            $driverOrigin = "{$driverLocation->latitude},{$driverLocation->longitude}";
        }

        //TODO remove $client param
        if ($client && config('app.env') === 'dev' && $client->id === 96) {
            $driverOrigin = '47.935618,33.422442';
        }

        $response = $this->requestDirectionsApi(
            [
                'origin' => $driverOrigin,
                'destination' => $clientOrigin['id'],
            ]
        );

        return [
            TripOrder::DRIVER_DISTANCE => $response['routes'][0]['legs'][0]['distance']['value'],
            TripOrder::WAIT_DURATION => $currentTripDuration + $response['routes'][0]['legs'][0]['duration']['value'],
        ];
    }

    /**
     * @param int $cityId
     * @return bool
     */
    public function isClientsQueueFull(int $cityId): bool
    {
        $activeShiftsNumber = Shift::active()->where(Shift::CITY_ID, $cityId)->count();

        $ordersCount = TripOrder::whereHas('shifts', function ($query) use ($cityId) {
            $query->active()->where(Shift::CITY_ID, $cityId);
        })->count();

        return $ordersCount > $activeShiftsNumber;
    }

    public function findDrivers($longitude, $latitude): Collection
    {
        $cityId = $this->getCityId($longitude, $latitude);

        $drivers = PostgisService::findClosestDrivers($longitude, $latitude, $cityId);

        if ($drivers->isEmpty()) {
            if ($this->isClientsQueueFull($cityId)) {
                throw new TripException(200, TripMessages::CLIENTS_QUEUE_IS_FULL);
            }

            $driversAtWork = PostgisService::findClosestDrivers($longitude, $latitude, $cityId, false);

            if ($driversAtWork->isEmpty()) {
                throw new TripException(200,TripMessages::ALL_DRIVERS_OFFLINE);
            }

            $drivers = $driversAtWork;
        }

        return $drivers;
    }

    /**
     * @param array $params
     * @return array
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
        //TODO remove $client param
        $client = $tripOrder->client;

        $driverData = $this->getDriverData($tripOrder->origin, $client);

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
