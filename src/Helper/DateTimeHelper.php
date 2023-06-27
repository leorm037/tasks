<?php

namespace App\Helper;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class DateTimeHelper
{

    public static function currentDateTimeZone(string $timeZone = 'America/Sao_Paulo'): DateTimeInterface
    {
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone($timeZone));

        return $dateTime;
    }

    public static function currentDateTimeImmutableZone(string $timeZone = 'America/Sao_Paulo'): DateTimeImmutable
    {
        return DateTimeImmutable::createFromMutable(self::currentDateTimeZone($timeZone));
    }
}
