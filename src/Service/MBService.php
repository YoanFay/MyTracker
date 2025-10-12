<?php

namespace App\Service;

class MBService
{

    const URL = "https://musicbrainz.org/ws/2/";

    public function callApi($apiUrl, $query){

        $query = [
            'fmt' => 'json',
            'limit' => 1,
            'query' => $query,
        ];

        $apiUrl = self::URL. $apiUrl . '?' . http_build_query($query);

        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'MyTracker/1.0 (yoanfayolle.yf@gmail.com)');

        $response = curl_exec($curl);

        curl_close($curl);

        if ($response === false) {
            dd('Erreur cURL : ' . curl_error($curl));
        } else {
            return json_decode($response, true);
        }

    }

    public function searchRelease($musicArtist, $musicName){

        $query = "artist:".$musicArtist." AND release:".$musicName;

        return $this->callApi("release", $query);

    }

    public function searchRecording($releaseID){

        $query = "reid:".$releaseID;

        return $this->callApi("recording", $query);

    }



}