<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\WeekHourFuncs;

class FindTimeCycleWeekHour
{
    public static function FindWeekHourTimeCycle(int $hour): int
    {
        return match (true) {
            $hour >= 0 && $hour <= 3  => 0,
            $hour >= 4 && $hour <= 7 => 1,
            $hour >= 8 && $hour <= 11 => 2,
            $hour >= 12 && $hour <= 15 => 3,
            $hour >= 16 && $hour <= 19 => 4,
            default => 5,
        };
    }
}
