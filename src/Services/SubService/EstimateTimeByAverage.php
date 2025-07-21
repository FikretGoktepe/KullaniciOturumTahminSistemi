<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService;

use Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\AverageFuncs\ClearOutliers;

use DateTimeImmutable;
use DateTimeZone;
use DateTime;
use MathPHP\Statistics\Descriptive;
use MathPHP\Statistics\Average;

class EstimateTimeByAverage
{
    public static function CalculateAverageTime($logins)
    {
        $diffs = [];

        for ($i = 1; $i < count($logins); $i++) {
            $prev = new DateTimeImmutable($logins[$i - 1], new DateTimeZone('UTC'));
            $curr = new DateTimeImmutable($logins[$i], new DateTimeZone('UTC'));

            $diffs[] = $curr->getTimestamp() - $prev->getTimestamp();
        }

        //Ortalama
        $mean = Average::mean($diffs);
        //Standart Sapma (Popülasyon)
        $std = Descriptive::standardDeviation($diffs, false);

        $filteredDiffs = ClearOutliers::ClearTheListForOutliers($diffs, $mean, $std);

        if ($filteredDiffs['status'] == 0)
            return $filteredDiffs;

        sort($filteredDiffs['data']);

        //IQR ile bir alt ve üst aralık belirleniyor
        $q1 = Descriptive::percentile($filteredDiffs['data'], 25);
        $q2 = Descriptive::percentile($filteredDiffs['data'], 75);

        $iqr = $q2 - $q1;

        $lowerLimit = $q1 -  0.02 * $iqr;
        $upperLimit = $q2 +  0.02 * $iqr;

        //IQR ile belirlenen alt ve üst limite göre aralık belirleniyor.
        $r1 = new DateTime($logins[count($logins) - 1], new DateTimeZone('UTC'));
        $r1->setTimestamp((int)round($r1->getTimestamp() + $lowerLimit));
        $r1 = $r1->format('d.m.Y/H:i');

        $r2 = new DateTime($logins[count($logins) - 1], new DateTimeZone('UTC'));
        $r2->setTimestamp((int)round($r2->getTimestamp() + $upperLimit));
        $r2 = $r2->format('d.m.Y/H:i');

        $r = $r1 . ' - ' . $r2;


        return ['status' => 1, 'data' => $r];
    }
}
