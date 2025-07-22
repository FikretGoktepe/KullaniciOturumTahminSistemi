<?php
require __DIR__ . '/../vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

use Fikretgoktepe\KullaniciOturumTahminSistemi\Controller\MainController;

$result = MainController::GetEstimatedTimes();
echo $result;
