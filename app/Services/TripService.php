<?php

namespace App\Services;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Exceptions\Google\GoogleApiException;
use App\Exceptions\Trip\AllDriversOfflineException;
use App\Exceptions\Trip\ClientsQueueIsFullException;
use App\Exceptions\Trip\TripException;
use App\Http\Requests\TripOrder\StoreTripOrderRequest;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Shift;
use App\Models\Trip;
use App\Models\TripOrder;
use App\Logic\TripPriceCalculator;
use App\Notifications\NewTripOrder;
use App\Notifications\TripStatusChanged;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use \Illuminate\Validation\ValidationException;

class TripService
{
    protected $cityId;

    /**
     * @param StoreTripOrderRequest $request
     * @param Client $client
     * @return TripOrder|\Illuminate\Database\Eloquent\Model
     * @throws ValidationException
     * @throws \ErrorException
     */
    public function updateOrCreateTripOrder(StoreTripOrderRequest $request, Client $client)
    {
        $clientData = $this->getClientData($request);
        $driverData = $this->getDriverData($clientData[TripOrder::ORIGIN]);

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

    /**
     * @param StoreTripOrderRequest $request
     * @return array
     * @throws ValidationException
     * @throws \ErrorException
     */
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

        $route = $this->requestDirectionsApi($directionsApiParams);

        $this->addCoordinates($route['legs'][0], $origin, $destination, $waypoints);

        $this->checkRouteBounds($route['bounds'], $origin);

        $this->setOriginLabel($origin);

        return [
            TripOrder::ORIGIN => $origin,
            TripOrder::DESTINATION => $destination,
            TripOrder::WAYPOINTS =>  $waypoints,
            TripOrder::DISTANCE => $route['legs'][0]['distance']['value'],
            TripOrder::TRIP_DURATION => $route['legs'][0]['duration']['value'],
            TripOrder::OVERVIEW_POLYLINE => $route['overview_polyline'],
        ];
    }

    protected function setOriginLabel(array &$origin)
    {
        if (!$origin['label'] && strpos($origin['id'], 'place_id') === false) {
            $origin['label'] = $this->requestReverseGeocodingApi($origin['id']);
        }
    }

    /**
     * @param array $bounds
     * @param array $origin
     * @return bool
     * @throws ValidationException
     */
    protected function checkRouteBounds(array $bounds, array $origin)
    {
        if (config('app.skip_route_bounds_validation')) {
            return true;
        }

        $cityId = $this->getCityId($origin['coordinates']['lng'], $origin['coordinates']['lat']);
        $isRouteBoundsInCity = $cityId && PostgisService::isCityPolygonContainsRouteBounds($cityId, $bounds);
        if (!$isRouteBoundsInCity) {
            throw ValidationException::withMessages(['route' => TripMessages::ROUTE_BOUNDS_VALIDATION_ERROR]);
        }
    }

    /**
     * @param float $longitude
     * @param float $latitude
     * @return int
     */
    protected function getCityId(float $longitude, float $latitude): int
    {
        return $this->cityId ?: PostgisService::findClosestCityId($longitude, $latitude);
    }

    /**
     * @param array $directionsApiParams
     * @param array|null $waypoints
     */
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

    /**
     * @param array $routeLeg
     * @param array $origin
     * @param array $destination
     * @param array $waypoints
     */
    protected function addCoordinates(array $routeLeg, array &$origin, array &$destination, array &$waypoints): void
    {
        foreach ($routeLeg['via_waypoint'] as $key => $apiWaypoint) {
            $waypoints[$key]['coordinates'] = $apiWaypoint['location'];
        }

        $origin['coordinates'] = $routeLeg['start_location'];
        $destination['coordinates'] = $routeLeg['end_location'];
    }

    /**
     * @param $clientOrigin
     * @return array
     * @throws \ErrorException
     */
    protected function getDriverData($clientOrigin): array
    {
        $drivers = $this->findDrivers($clientOrigin['coordinates']['lng'], $clientOrigin['coordinates']['lat']);
        $closestDriver = $drivers->first();

        $currentTripDuration = 0;
        if ($closestDriver->active_trip) {
            $currentTripDuration = $closestDriver->active_trip->trip_duration_adjusted;
            $driverOrigin = $closestDriver->active_trip->destination['id'];
        } else {
            $driverLocation = $closestDriver->active_shift->driver_location()->withCoordinates()->first();
            $driverOrigin = "{$driverLocation->latitude},{$driverLocation->longitude}";
        }

        $route = $this->requestDirectionsApi(
            [
                'origin' => $driverOrigin,
                'destination' => $clientOrigin['id'],
            ]
        );

        return [
            TripOrder::DRIVER_DISTANCE => $route['legs'][0]['distance']['value'],
            TripOrder::WAIT_DURATION => $currentTripDuration + $route['legs'][0]['duration']['value'],
        ];
    }

    /**
     * @param int $cityId
     * @param int $activeShiftsNumber
     * @return bool
     */
    public function isClientsQueueFull(int $cityId, int $activeShiftsNumber): bool
    {
        $ordersCount = TripOrder::whereHas('shifts', function ($query) use ($cityId) {
            $query->active()->where(Shift::CITY_ID, $cityId);
        })->count();

        return $ordersCount >= $activeShiftsNumber;
    }

    /**
     * @param $longitude
     * @param $latitude
     * @return Collection
     */
    public function findDrivers($longitude, $latitude): Collection
    {
        $cityId = $this->getCityId($longitude, $latitude);

        $activeShiftsNumber = Shift::active()->where(Shift::CITY_ID, $cityId)->count();

        if ($activeShiftsNumber === 0) {
            throw new AllDriversOfflineException(200);
        }

        $drivers = PostgisService::findClosestDrivers($longitude, $latitude, $cityId);

        if ($drivers->isEmpty()) {
            if ($this->isClientsQueueFull($cityId, $activeShiftsNumber)) {
                throw new ClientsQueueIsFullException(200);
            }

            // drivers with active trips
            $drivers = PostgisService::findClosestDrivers($longitude, $latitude, $cityId, false);
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

        return $response['routes'][0];
    }

    /**
     * @param string $latlng
     * @return string
     * @throws \ErrorException
     */
    protected function requestReverseGeocodingApi(string $latlng): string
    {
        $response = \GoogleMaps::load('geocoding')
            ->setParamByKey('latlng', $latlng)
            ->setParamByKey('result_type', 'street_address')
            ->get();

        $response = json_decode($response, true);

        if ($response['status'] !== 'OK') {
            return '';
        }

        return $response['results'][0]['formatted_address'];
    }

    /**
     * @param TripOrder $tripOrder
     * @param Driver $driver
     * @return Trip
     */
    public function createTrip(TripOrder $tripOrder, Driver $driver): Trip
    {
        $driverData = $this->getDriverData($tripOrder->origin);

        $tripData = $tripOrder->toArray();
        $tripData[Trip::DRIVER_DISTANCE] = $driverData[TripOrder::DRIVER_DISTANCE];
        $tripData[Trip::CO2] = $tripOrder->distance;
        $tripData[Trip::WAIT_DURATION] = (int)$driverData[TripOrder::WAIT_DURATION];
        $tripData[Trip::SHIFT_ID] = $driver->activeShift->id;
        $tripData[Trip::STATUS] = TripStatuses::DRIVER_IS_ON_THE_WAY;

        return Trip::create($tripData);
    }

    /**
     * @param Trip|null $trip
     * @param int $newStatus
     */
    public function checkTrip(?Trip $trip, int $newStatus): void
    {
        if (!$trip) {
            throw new TripException(200, 'Active trip not found.');
        }

        if ($trip->status !== ($newStatus - 1)) {
            throw new TripException(200, 'Incorrect trip status.');
        }
    }

    /**
     * @param Trip $trip
     * @param int $newStatus
     * @throws \Exception
     */
    public function changeStatus(Trip $trip, int $newStatus): void
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

    /**
     * @param TripOrder $tripOrder
     */
    public function informDriversAboutOrder(TripOrder $tripOrder): void
    {
        $longitude = $tripOrder->origin['coordinates']['lng'];
        $latitude = $tripOrder->origin['coordinates']['lat'];

        $drivers = $this->findDrivers($longitude, $latitude);

        foreach ($drivers as $driver) {
            $driver->notify(new NewTripOrder($tripOrder->id));

            $orderShiftRelationExists = $tripOrder->shifts()
                ->where('shifts.id', $driver->activeShift->id)
                ->exists();

            if (!$orderShiftRelationExists) {
                $tripOrder->shifts()->attach($driver->activeShift->id);
            }
        }
    }

    /**
     * @param Client $client
     */
    protected function updateClientsCo2Sum(Client $client): void
    {
        $co2Sum = $client->trips()->archived()->get()->sum('co2');

        $client->update([Client::CO2_SUM => $co2Sum]);
    }

    public function checkFreeTrips(Client $client)
    {
        if (!$client->free_trips) {
            throw new TripException(200, TripMessages::NO_FREE_TRIPS);
        }
    }

    public function processFreeTrip(Trip $trip)
    {
        $client = $trip->client;

        $this->checkFreeTrips($client);

        $client->decrement(Client::FREE_TRIPS);

        Log::channel('free_trips')->info("Client ID: " . $client->id);
    }
}
