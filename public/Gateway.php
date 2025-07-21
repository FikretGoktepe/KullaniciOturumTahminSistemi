<?php
require __DIR__ . '/../vendor/autoload.php';

use Fikretgoktepe\KullaniciOturumTahminSistemi\Controller\MainController;

$result = MainController::GetEstimatedTimes();

