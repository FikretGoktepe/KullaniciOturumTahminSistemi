<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\AverageFuncs;

use Exception;

class ClearOutliers
{
    public static function ClearTheListForOutliers($diffs, $mean, $std){
        try{
        $filteredDiffs = [];
        foreach ($diffs as $v) {
            $z = ($v - $mean) / $std;
            if ($z >= -1.5 && $z <= 1.5) {
                $filteredDiffs[] = $v;
            }
        }
        }catch(Exception $e){
            return ['status' => 0, 'error-no' => 'ERROR_07', 'error-msg' => $e];
        }
        return ['status' => 1, 'data' => $filteredDiffs];
    }
}
