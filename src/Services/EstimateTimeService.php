<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Services;

use Fikretgoktepe\KullaniciOturumTahminSistemi\Repository\GetDataFromAPI;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Models\UserRawData;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Models\UserResultData;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\EstimateTimeByAverage;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\EstimatedHour;
use Fikretgoktepe\KullaniciOturumTahminSistemi\LocalStorage\HourCycle;
use Fikretgoktepe\KullaniciOturumTahminSistemi\LocalStorage\Weeks;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\EstimatedWeek;

class EstimateTimeService
{
    public static function CalculateEstimatedTimes()
    {
        $userResults = [];
        $usersRawData = GetDataFromAPI::GetRawDataFromApi();
        //Hata döünüşü varsa hata geri döndürülüyor.
        if ($usersRawData['status'] == 0)
            return $usersRawData;

        foreach ($usersRawData['data'] as $user) {
            $dataSufficiency = 0;
            //50'den az veri olan girişler veri yetersizliğinden kaynaklı güvenli bir hesaplama olmadığına dair bilgilendirme için ekstra veri döndürülüyor.
            if (count($user->GetLogins()) < 50)
                $dataSufficiency = 0;
            else
                $dataSufficiency = 1;

            //Alt servislerden sonuçlar alınıyor
            $r1 = EstimateTimeByAverage::CalculateAverageTime($user->GetLogins());
            $r2 = EstimatedHour::EstimateHourCycle($user->GetLogins());
            $r3 = EstimatedWeek::EstimateWeekDay($user->GetLogins());

            //Hata var ise hata kodu yoksa işlem sonucu dönüşü sağlanıyor.
            if ($r1['status'] == 0 && $r2['status'] == 0 && $r3 == 0)
                return ['status' => 0, 'error-no' => 'ERROR_08', 'error-msg-average' => $r1['error-msg'], 'error-msg-hour' => $r2['error-msg'], 'error-msg-week' => $r3['error-msg']];
            else if ($r1['status'] == 1 && $r2['status'] == 1)
                $userResults[] = new UserResultData($user->GetId(), $user->GetName(), $r1['data'], $r2Data = ($r2['status'] == 1) ? HourCycle::{'C' . (string)$r2['data']}->value : $r2['data'], $r3Data = ($r3['status'] == 1) ? Weeks::{'W' . (string)$r3['data']}->value : $r3['data'], $dataSufficiency);
        }

        return ['status' => 1, 'data' => $userResults];
    }
}
