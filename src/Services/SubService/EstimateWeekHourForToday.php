<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Models\Days;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\WeekHourFuncs\FindTimeCycleWeekHour;
use MathPHP\Statistics\Average;
use MathPHP\Statistics\Descriptive;

class EstimateWeekHourForToday
{
    public static function EstimatedWeakHour($logins)
    {
        $weekTable = [];
        $weekAverageTable = [];

        //Verileri tutmak için Days nesnesi oluşturuluyor.
        for ($i = 1; $i <= 7; $i++) {
            $weekTable[] = new Days($i);
        }

        try {
            //Tüm girişler haftanın günü ve saat dilimine göre gruplanıyor.
            foreach ($logins as $login) {
                $dt = new DateTime($login, new DateTimeZone('UTC'));
                $weekNumber = $dt->format('N');
                $cycle = FindTimeCycleWeekHour::FindWeekHourTimeCycle((int)$dt->format('H'));
                $weekTable[$weekNumber - 1]->cycleData[$cycle][] = $login;
            }
        } catch (Exception $e) {
            return ['status' => 0, 'error-msg' => $e];
        }

        $totalWeekLogins = count($logins);

        //Yüzdelik ortalama listesi oluşturuluyor.
        $weekDayCycleAverage = [];
        $allAverage = [];
        foreach ($weekTable as $dayData) {
            $dayAverage = [];
            foreach ($dayData->cycleData as $cycle => $logins) {
                $count = count($logins);
                if ($totalWeekLogins > 0) {
                    $dayAverage[$cycle] = round(($count / $totalWeekLogins) * 100, 2);
                    $allAverage[] = $dayAverage[$cycle];
                } else {
                    $dayAverage[$cycle] = 0;
                }
            }
            $weekDayCycleAverage[$dayData->weekNumber] = $dayAverage;
        }

        //Yüzdelik listede seçim yapabilmek için bir alt değer oluşturuluyor.
        $threshold = (int)(Average::mean($allAverage) + Descriptive::standardDeviation($allAverage, false));

        //Standart sapma çok düşük ise hata payı yüksek olacağı için tahmin yapılamayacağına göre hata döndürülüyor.
        if (Descriptive::standardDeviation($allAverage, false) <= 1) {
            return ['status' => 0, 'data' => 'ERROR_12'];
        }

        //Oluşturulan yüzdelik listesine göre ortalama ve standart sapmanın toplamından yüksek olan en yakın aralık belirleniyor.
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $hour = (int)$now->format('H');
        $weekNumber = $now->format('N');
        $cycleNo = FindTimeCycleWeekHour::FindWeekHourTimeCycle($hour);
        $firstTurn = true;
        $predictedWeek = null;
        $predictedHourCycle = null;
        $daysPast = 0;
        for ($i = $weekNumber; $i <= 7; $i++) {
            $j = 0;
            if ($firstTurn) {
                $j = $cycleNo + 1;
                $firstTurn = false;
            } else
                $j = 0;

            if ($i == 7)
                $i = 1;

            for ($j; $j < 6; $j++) {
                if ($weekDayCycleAverage[$i][$j] > $threshold) {
                    $predictedWeek = $i;
                    $predictedHourCycle = $j;
                    break 2;
                }
            }
            $daysPast += 1;
        }

        $predictedCycleList = $weekTable[$predictedWeek - 1]->cycleData[$predictedHourCycle];
        $predictedLoginTime = null;

        if (count($predictedCycleList) == 1) {
            //Eğer tek bir geçmiş giriş varsa onun saati alınıyor ve bugünün tarihine göre hafta kaç gün sonra ise tarihe ekleniyor.
            $dt = new DateTime($predictedCycleList[0], new DateTimeZone('UTC'));

            $predictedDate = clone $now;
            $predictedDate->modify("+{$daysPast} days");

            $hour = (int)$dt->format('H');
            $minute = (int)$dt->format('i');
            $second = (int)$dt->format('s');
            $predictedDate->setTime($hour, $minute, $second);
            $predictedLoginTime = $predictedDate;
        } else {
            $secondsList = [];
            //Saat dilimindeki önceki saatlerin ortalaması alınıyor ve buna göre ortalama bir saat tahmini yapılıyor.
            foreach ($predictedCycleList as $time) {
                $dt = new DateTime($time, new DateTimeZone('UTC'));
                $seconds = ($dt->format('H') * 3600) + ($dt->format('i') * 60) + $dt->format('s');
                $secondsList[] = $seconds;
            }
            $timeAverage = Average::mean($secondsList);

            $predictedDate = clone $now;

            $predictedDate->modify("+{$daysPast} days");

            $hour = floor($timeAverage / 3600);
            $minute = floor(($timeAverage % 3600) / 60);
            $second = (int)($timeAverage % 60);

            $predictedDate->setTime($hour, $minute, $second);

            $predictedLoginTime = $predictedDate;
        }

        return ['status' => 1, 'data' => $predictedLoginTime->format('d.m.Y/H:i')];
    }
}
