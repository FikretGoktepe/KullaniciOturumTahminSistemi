<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService;

use DateTime;
use DateTimeZone;
use Exception;

class EstimatedWeek
{
    public static function EstimateWeekDay($logins)
    {
        //En yüksek değerin ne kadar altında bulunan yüzdeliklerin hesaba dahil edileceği belirleniyor.
        $threshold = 8;
        $prevLogin = null;
        $weekDays = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0];

        //Hafta günlerine göre giriş sayıları hesaplanıyor.(Aynı gün içerisinde 1 den fazla giriş bulunuyorsa dikkate alınmıyor ve 1 giriş olarak hesaba dahil ediliyor.)
        try {
            foreach ($logins as $login) {
                $datetime = new DateTime($login, new DateTimeZone('UTC'));
                $weekNumber = $datetime->format('N');
                $dt = new DateTime($login, new DateTimeZone('UTC'));
                $nowLogin = $dt->format('m-d');
                if ($nowLogin === $prevLogin) {
                    continue;
                }
                $prevLogin = $nowLogin;

                switch ($weekNumber) {
                    case 1:
                        $weekDays[1] += 1;
                        break;
                    case 2:
                        $weekDays[2] += 1;
                        break;
                    case 3:
                        $weekDays[3] += 1;
                        break;
                    case 4:
                        $weekDays[4] += 1;
                        break;
                    case 5:
                        $weekDays[5] += 1;
                        break;
                    case 6:
                        $weekDays[6] += 1;
                        break;
                    default:
                        $weekDays[7] += 1;
                        break;
                }
            }
        } catch (Exception $e) {
            return ['status' => 0, 'data' => 'ERROR_06', 'error-msg' => $e];
        }


        //Yüzdelik ortalamaları hesaplanıyor.
        $total = count($logins);
        $weekDaysAverage = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0];
        for ($i = 1; $i <= 7; $i++) {
            $weekDaysAverage[(string)$i] = round(($weekDays[(string)$i] / $total) * 100, 2);
        }

        //Son girişin hangi gün yapıldığı tespit ediliyor.
        $lastLogin = end($logins);
        $dtLast = new DateTime($lastLogin, new DateTimeZone('UTC'));
        $lastWeekNumber = (int)$dtLast->format('N');

        arsort($weekDaysAverage);

        //Treshold değerine göre olası günler listeleniyor.
        $predictedWeekDays = [];
        $higherAverage = null;
        foreach ($weekDaysAverage as $key => $value) {
            if ($higherAverage == null) {
                $higherAverage = $value;
                $predictedWeekDays[] = $key;
            } else if (($higherAverage - $threshold) < $value) {
                $predictedWeekDays[] = $key;
            }
        }

        //Eğer sadece 1 gün varsa listede 
        $predictedWeekDay = $predictedWeekDays[0];
        if (count($predictedWeekDays) == 1) {
            return ['status' => 1, 'data' => 1];
        } else if (count($predictedWeekDays) > 1) {
            foreach ($predictedWeekDays as $weekDay) {
                if ($weekDay > $lastWeekNumber) {
                    $predictedWeekDay = $weekDay;
                    break;
                }
            }
            return ['status' => 1, 'data' => $predictedWeekDay];
        } else {
            return ['status' => 0, 'data' => 'ERROR_10'];
        }
    }
}
