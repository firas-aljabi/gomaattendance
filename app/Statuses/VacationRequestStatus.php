<?php

namespace App\Statuses;

class VacationRequestStatus
{
    public const HOURLY = 1;
    public const DAILY = 2;
    public const ANNUL = 3;

    public static array $statuses = [self::HOURLY, self::DAILY,self::ANNUL];
}