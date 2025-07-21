<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Repository;

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
            $error = curl_error($ch);
            curl_close($ch);
            return ['status' => 'failed', 'error' => $error];
        }

        curl_close($ch);

        $data = json_decode($response, true);

        $curlResult = [];

        foreach ($data['data']['rows'] as $row) {
            $user = new UserRawData($row['id'], $row['name'], $row['logins']);
            $curlResult[] = $user;
        }

        $result = ['status' => 'success', 'data' => $curlResult];
        return $result;
    }
}
