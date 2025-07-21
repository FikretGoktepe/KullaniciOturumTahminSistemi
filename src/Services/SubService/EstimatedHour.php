<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService;

use Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\HourFuncs\FindTimeCycle;

use DateTime;
use DateTimeZone;
use Exception;

class EstimatedHour
{
    public static function EstimateHourCycle($logins)
    {
        //En yüksek değerin ne kadar altında bulunan yüzdeliklerin hesaba dahil edileceği belirleniyor.
        $threshold = 8;

        $cycles = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0];
        try {
            //Saat dilimlerine göre giriş sayıları belirleniyor.
            foreach ($logins as $login) {
                $dt = new DateTime($login, new DateTimeZone('UTC'));
                $hour = (int)$dt->format('H');
                $cycleNo = FindTimeCycle::GetTimeCycle($hour);
                $cycles[(string)$cycleNo] += 1;
            }
        } catch (Exception $e) {
            return ['status' => 0, 'data' => 'ERROR_09', 'error-msg' => $e];
        }



        //Toplama göre yüzdeleri hesaplanıyor.
        $total = count($logins);
        $cyclesAverage = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0];
        for ($i = 1; $i <= 6; $i++) {
            $cyclesAverage[(string)$i] = round(($cycles[(string)$i] / $total) * 100, 2);
        }

        //Son girişin hangi saat diliminde yapıldığı belirleniyor.
        $lastLogin = end($logins);
        $dtLast = new DateTime($lastLogin, new DateTimeZone('UTC'));
        $hourLast = (int)$dtLast->format('H');
        $lastCycle = FindTimeCycle::GetTimeCycle($hourLast);

        //Liste yüzdelik değere göre sıralanıyor.
        arsort($cyclesAverage);

        //En yüksek olasılık olan saat dilimleri belirleniyor.
        $predictedCycles = [];
        $higherAverage = null;
        foreach ($cyclesAverage as $key => $value) {
            if ($higherAverage == null) {
                $higherAverage = $value;
                $predictedCycles[] = $key;
            } else if (($higherAverage - $threshold) < $value) {
                $predictedCycles[] = $key;
            }
        }

        sort($predictedCycles);

        $predictedCycle = $predictedCycles[0];
        //Yüksek olasılıklı saat dilimi tahimini giriş olarak belirleniyor.
        //Birden fazla ise son girişe göre sonraki saat dilimi tespit ediliyor ve sonuç olarak belirleniyor.
        if (count($predictedCycles) == 1) {
            return (['status' => 1, 'data' => 1]);
        } else if (count($predictedCycles) > 1) {
            foreach ($predictedCycles as $cycle) {
                if ($cycle > $lastCycle) {
                    $predictedCycle = $cycle;
                    break;
                }
            }
            return (['status' => 1, 'data' => $predictedCycle]);
        } else {
            return (['status' => 0, 'data' => 'ERROR_09']);
        }
    }
}
