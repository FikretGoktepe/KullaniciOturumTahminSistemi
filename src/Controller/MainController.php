<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Controller;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Services\EstimateTimeService;

class MainController {
    public static function GetEstimatedTimes(){
        return EstimateTimeService::CalculateEstimatedTimes();
    }
}
