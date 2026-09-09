<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Canceled = 'canceled';
}
