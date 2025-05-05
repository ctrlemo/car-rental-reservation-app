<?php

namespace App\Constants;

final class AppConstants
{
    // Session keys
    public const SESSION_RESERVATION_KEY = 'reservation';

    // Route names
    public const ROUTE_HOME = 'app_home';
    public const ROUTE_RESERVATION_INDEX = 'app_reservation_index';
    public const ROUTE_RESERVATION_CONFIRM = 'app_reservation_confirm';
    public const ROUTE_ADMIN_INDEX = 'app_admin_index';

    // Other constants
    public const DEFAULT_PASSENGER_COUNT = 1;
    public const DEFAULT_RESERVATION_DAYS = 1;

    // Validation messages
    public const RESERVATION_NOT_FINALIZED = 'Reservation is not finalized.';
}