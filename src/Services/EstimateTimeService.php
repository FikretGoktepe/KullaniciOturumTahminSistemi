<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Services;

use Fikretgoktepe\KullaniciOturumTahminSistemi\Repository\GetDataFromAPI;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Models\UserRawData;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Models\UserResultData;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\EstimateTimeByAverage;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\EstimateTimeByWeek;

class EstimateTimeService
{
    public static function CalculateEstimatedTimes()
    {
        $userResults = [];
        $usersRawData = GetDataFromAPI::GetRawDataFromApi();
        if ($usersRawData['status'] == 0)
            return $usersRawData;

        foreach ($usersRawData['data'] as $user) {
            $dataSufficiency = 0;
            if (count($user->GetLogins()) < 50)
                $dataSufficiency = 0;
            else
                $dataSufficiency = 1;
            $r1 = EstimateTimeByAverage::CalculateAverageTime($user->GetLogins());
            $r2 = EstimateTimeByWeek::CalculateByWeekTime($user->GetLogins());


            if ($r1['status'] == 0 && $r2['status'] == 0)
                return ['status' => 0, 'error-no' => 'ERROR_08', 'error-msg-average' => $r1['error-msg'], 'error-msg-weekly' => $r2['error-msg']];
            else if ($r1['status'] == 1 && $r2['status'] == 1)
                $userResults[] = new UserResultData($user->GetId(), $user->GetName(), $r1['data'], $r2['data'], $dataSufficiency);
            else if ($r1['status'] == 0)
                $userResults[] = new UserResultData($user->GetId(), $user->GetName(), $r1['error-no'], $r2['data'], $dataSufficiency);
            else if ($r2['status'] == 0)
                $userResults[] = new UserResultData($user->GetId(), $user->GetName(), $r1['data'], $r2['error-no'], $dataSufficiency);
        }

        return ['status' => 1, 'data' => $userResults];
    }
}
