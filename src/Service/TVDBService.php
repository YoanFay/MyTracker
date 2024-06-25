<?php

namespace App\Service;

use App\Entity\Serie;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class TVDBService
{

    private const API_URL = "https://api4.thetvdb.com/v4";

    /**
     * @throws InvalidArgumentException
     */
    public function getKey()
    {

        $cache = new FilesystemAdapter();

        $cache->clear();

        return $cache->get('apiKeyTVDB', function (ItemInterface $item, $apiUrl = self::API_URL) {
            $item->expiresAfter(2592000);

            $client = new Client();

            $apiToken = '8f3a7d8f-c61f-4bf7-930d-65eeab4b26ad';

            $response = $client->post($apiUrl."/login", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => ['apiKey' => $apiToken],
            ]);

            $data = json_decode($response->getBody(), true);

            return $data['data']['token'];
        });

    }


    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function updateSerieName(Serie $serie){

        $client = new Client();

        $token = self::getKey();

        $response = $client->get(self::API_URL."/series/".$serie->getTvdbId()."/translations/fra", [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if ($data !== null && $data['status'] === "success"){
            $serie->setName($data['data']['name']);
            $serie->setVfName(true);
        }
    }

}