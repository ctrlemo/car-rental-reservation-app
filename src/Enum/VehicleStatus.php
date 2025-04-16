<?php

namespace App\Enum;

enum VehicleStatus: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
    case MAINTENANCE = 'maintenance';
}