<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Services\SubService\HourFuncs;

class FindTimeCycle
{
    //Saatin kaç olduğuna göre hangi saat diliminde olduğu geri yollanıyor.
    public static function GetTimeCycle(int $hour): int
    {
        switch (true) {
            case ($hour >= 23 || $hour < 6):
                return 1; // Gece
            case ($hour >= 6 && $hour < 11):
                return 2; // Sabah
            case ($hour >= 11 && $hour < 14):
                return 3; // Olası Öğle Molası aralığı
            case ($hour >= 14 && $hour < 17):
                return 4; // Öğleden sonra
            case ($hour >= 17 && $hour < 19):
                return 5; // Olası iş çıkış aralığı
            default:
                return 6; // Akşam
        }
    }
}
