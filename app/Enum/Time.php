<?php

namespace App\Enum;

enum Time: int
{
    case secondsInMinute = 60;
    case secondsInHour = 3600;
    case secondsInDay = 86400;
    case secondsInWeek = 604800;
    case secondsIn10Days = 864000;
}
