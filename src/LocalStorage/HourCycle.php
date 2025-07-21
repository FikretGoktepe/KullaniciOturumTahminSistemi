<?php
namespace Fikretgoktepe\KullaniciOturumTahminSistemi\LocalStorage;

enum HourCycle: string{
    case C1 = '23:00 - 05:59';
    case C2 = '06:00 - 10:59';
    case C3 = '11:00 - 13:59';
    case C4 = '14:00 - 16:59';
    case C5 = '17:00 - 18:59';
    case C6 = '19:00 - 22:59';
}