<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Controller;

use Fikretgoktepe\KullaniciOturumTahminSistemi\Services\EstimateTimeService;

class MainController
{
    public static function GetEstimatedTimes()
    {
        $cacheFile = __DIR__ . '/../LocalStorage/cache.json';
        $cacheDuration = 30 * 60;

        //Cache dosyası var ve güncel ise cache üzerinden veri döndürülüyor.
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheDuration)) {
            return file_get_contents($cacheFile);
        } else {
            $result = EstimateTimeService::CalculateEstimatedTimes();
            if($result['status'] == 1){
            $jsonResult = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($cacheFile, $jsonResult);
            return $jsonResult;
            }else{
                return $result;
            }
        }
    }
}
