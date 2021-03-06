<?php

namespace App\Constants;

class TripMessages
{
    public const REQUEST_NOT_FOUND = '';
    public const TRIP_NOT_FOUND = '';
    public const CITY_NOT_FOUND = 'Your location is outside of the taxi service area.';
    public const REQUEST_ALREADY_CONFIRMED = 'The Trip Request is already confirmed.';
    public const REQUEST_ALREADY_ACCEPTED = 'The Trip Request is already accepted or not confirmed.';
    public const DRIVER_HAS_NOT_SHIFT = 'Driver doesnt have active shift.';
    public const DRIVER_ALREADY_HAS_ACTIVE_TRIP = 'Driver already has active trip.';
    public const CANCELED = 'The Trip has canceled.';
    public const CANNOT_BE_CANCELED = 'The Trip can not be canceled. The Trip is in progress.';
    public const DRIVER_ARRIVED = 'The driver has arrived.';
    public const STARTED = 'The Trip is started.';
    public const FINISHED = 'The Trip has finished.';
    public const DRIVER_RATED = 'The Driver has been rated. The Trip is archived.';
    public const CLIENT_RATED = 'The Client has been rated.';
    public const ARCHIVED = 'The Trip is archived.';
    public const ROUTE_BOUNDS_VALIDATION_ERROR = 'The chosen route is outside of the taxi service area.';
    public const CLIENTS_QUEUE_IS_FULL = 'Clients queue is full.';
    public const ALL_DRIVERS_OFFLINE = 'All drivers are offline.';
    public const NO_FREE_TRIPS = 'You have no free trips left.';
    public const WASH_CAR = 'Please wash the car.';
    public const ACTIVE_TRIP_NOT_FOUND = 'Active trip not found.';
    public const INCORRECT_STATUS = 'Incorrect trip status.';
}
