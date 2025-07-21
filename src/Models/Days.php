<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Models;

class Days
{
    public function __construct($_weekNumber)
    {
        $this->weekNumber = $_weekNumber;
    }
    public $weekNumber;
    public $cycleData = [[], [], [], [], [], []];
}
