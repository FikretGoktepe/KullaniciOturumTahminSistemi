<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Repository;

use Exception;
use Fikretgoktepe\KullaniciOturumTahminSistemi\Models\UserRawData;

class GetDataFromAPI
{
    public static function GetRawDataFromApi()
    {
        //CURL ile api üzerinden veri alınıyor.
        $ch = curl_init('https://case-test-api.humanas.io');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $e = curl_error($ch);
            curl_close($ch);
            return ['status' => 0, 'error-no' => 'ERROR_01', 'error-msg' => $e];
        }

        curl_close($ch);

        $data = json_decode($response, true);

        $curlResult = [];
        if (count($data['data']) == 0 || $data['data'] === null) {
            return ['status' => 0, 'error-no' => 'ERROR_11'];
        }

        try {
            foreach ($data['data']['rows'] as $row) {
                $user = new UserRawData($row['id'], $row['name'], $row['logins']);
                $curlResult[] = $user;
            }
        } catch (Exception $e) {
            return ['status' => 0, 'error-no' => 'ERROR_01', 'error-msg' => $e];
        }

        return ['status' => 1, 'data' => $curlResult];
    }
}
