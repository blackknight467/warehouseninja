<?php

namespace WarehouseNinja\ShipBundle\Service;


class GeoLocationService {

    const GOOGLE_API_KEY = 'AIzaSyADTxfk7ZnP1sMfxRI-qc2aJrEYfhDQi-s';
    const BASE_API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function getLatLonForAddress($address)
    {
        $url = GeoLocationService::BASE_API_URL . '?address=' . urlencode($address) . '&key=' . GeoLocationService::GOOGLE_API_KEY;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result, true);
        if(array_key_exists('results', $response)) {
            if (array_key_exists('geometry', $response['results'][0])) {
                $latlon = ['lat' => $response['results'][0]['geometry']['location']['lat'], 'lon' => $response['results'][0]['geometry']['location']['lng']];
                return $latlon;
            }
        }

        return null;
    }


    function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return $miles;
    }

}