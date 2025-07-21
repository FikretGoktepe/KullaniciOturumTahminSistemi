
<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\LocalStorage;

enum ErrorLib: string
{
    case ERROR_01 = 'CURL Request error!';
    case ERROR_02 = 'Variables Not Correct!';
    case ERROR_03 = 'Not Enough Data For Estimate Time!';
    case ERROR_04 = 'Unexpected Error!';
    case ERROR_05 = 'Average Calculation Error!';
    case ERROR_06 = 'Weekly Calculation Error!';
    case ERROR_07 = 'Clear Outliers Error!';
    case ERROR_08 = 'Average And Weekly Calculation Error!';
}
